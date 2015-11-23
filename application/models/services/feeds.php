<?php

require_once('application/models/include/common.php');

class Feeds extends CI_Model {
    public $settings;
    
    function __construct(){
        $this->load->model('feed');
    }

    function parseFeed($ids_to_parse) {
        $parsedAmount = 0;
        $feeds = $this->feed->getIn('id',$ids_to_parse);

        foreach ($feeds as $feed) {
            if($feed->status === Feed::_STATUS_LOCKED_){continue;}
            
            $this->feed->lock($feed->id);

            if ($feed->type == 'rss') {
                $this->load->model('services/rss');
                $new_items = $this->rss->getFeed($feed->url);
            }else if($feed->type == 'facebook'){
                $this->load->model('item');
                $last_sid = $this->item->getLastSID($feed->id);

                $this->load->model('services/facebook');
                $new_items = $this->facebook->getNewsFeed($feed->id, $last_sid, $feed->url);
            }else if($feed->type == 'twitter'){
                $this->load->model('item');
                $last_sid = $this->item->getLastSID($feed->id);

                $this->load->model('services/twitter');
                $new_items = $this->twitter->getFeed($feed->id, $last_sid, $feed->url);
            }else if($feed->type == 'twitter_search'){
                $this->load->model('services/twitter');
                $new_items = $this->twitter->search($feed->url);
            }

            $to_filter = array();
            $new_items_found = false;
            if(isset($new_items['ids'])){

                $ids = $new_items['ids'];
                unset($new_items['ids']);

                $this->load->model('item');
                $old_items = $this->item->getBySID($ids);

                if (count($old_items) > 0) {
                    foreach ($old_items as $old_item) {
                        unset($new_items[$old_item->item_sid]);
                    }
                }

                if(!empty($new_items)){
                    $latest = 0;

                    foreach ($new_items as $item_sid => $new_item) {
                        $data = array(
                            'item_sid' => $item_sid,
                            'feed_id' => $feed->id,
                            'title' => Common::sanitize( Common::utf8( Common::truncateHtml($new_item['title']) ) ),
                            'content' => Common::sanitize( Common::utf8( $new_item['content'] ) ),
                            'date' => $new_item['date'],
                            'link' => Common::sanitize( Common::utf8( Common::short($new_item['link']) ) ),
                            'author' => Common::sanitize( Common::utf8( Common::short($new_item['author'], 100) ) ),
                            'picture' => isset($new_item['picture']) ? $new_item['picture'] : null
                        );

                        $to_filter[] = (object) $data;
                        $this->item->insert($data);

                        if($new_item['date'] > $latest){
                            $latest = $new_item['date'];
                        }
                    }

                    $new_items_found = $latest;
                }

                $parsedAmount++;
            }
            
            if($new_items_found){
                $feed->avg_priority = $feed->avg_priority + 2;
                if($feed->avg_priority > 0){
                    $feed->avg_priority = 0;
                }
            }else{
                $feed->avg_priority--;
            }

            $this->feed->feedParsed($feed);

            $this->load->model('filter');
            $this->filter->markItemsAsFiltered($feed->id, $to_filter);
        }
    }

    /*
     * name: name of the feed that is being added
     * url: url of the feed
     * user_id: id of the user that wants the feed
     * lid: contains the base url of the website
     */
    function addSubscription($url, $user_id, $name = '', $base_url = '') {
        $feed_count = $this->feed->count(array('url' => $url));
        
        if($name == '' || $base_url == ''){
            $this->load->model('services/rss');
            $feed_info = $this->rss->getFeedInfo($url);
            
            if(!$feed_info){
                return array(
                    'not_added' => true,
                    'error' => 'This feed cannot be added. Their rss is protected.'
                );
            }
            
            $name = $feed_info['title'];
            $base_url = $feed_info['base_url'];
        }
        
        if ($feed_count == 0) {
            $data = array(
                'type' => 'rss',
                'url' => $url,
                'base_url' => $base_url
            );
            
            $feed_id = $this->feed->insert($data);

            $this->parseFeed($feed_id);
        }else{
            $feed = $this->feed->get(array('url' => $url), true);
            $feed_id = $feed->id;
        }

        $this->load->model('subscription');
        if($this->subscription->addSubscription($feed_id, $name, $user_id, 'rss')){
            $this->load->model('feed');
            $count = $this->feed->getCountItems($feed_id);

            $response = array(
                'feed_id' => $feed_id,
                'count' => $count,
                'name' => $name
            );
        }else{
            $response = array(
                'not_added' => true,
                'error' => 'Error adding feed to the database'
            );
        }

        return $response;
    }
    
    function addTwitterSearchSubscription($user_id, $search){
        $feed_count = $this->feed->count(array('url' => $search));
        
        if($feed_count == 0){
            $data = array(
                'type' => 'twitter_search',
                'url' => $search
            );
            
            $feed_id = $this->feed->insert($data);

            $this->parseFeed($feed_id);
        }else{
            $feed = $this->feed->get(array('url' => $search), true);
            $feed_id = $feed->id;
        }
        
        $this->load->model('subscription');
        $name = 'Twitter: '.$search;
        if($this->subscription->addSubscription($feed_id, $name, $user_id, 'twitter_search')){
            $this->load->model('feed');
            $count = $this->feed->getCountItems($feed_id);

            $response = array(
                'feed_id' => $feed_id,
                'count' => $count,
                'name' => $name
            );
        }else{
            $response = array(
                'not_added' => true,
                'error' => 'Error adding feed to the database'
            );
        }

        return $response;
    }
    
    function importOPML($user_id, $file){
        $this->config->set_item('log_threshold', '0'); 
        libxml_use_internal_errors(true);
        $xml = @simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->config->set_item('log_threshold', '1'); 
        
        if(!$xml){return false;}
        
        $response = array();
        foreach($xml->body as $key => $element){
            
            $this->readOutline($response, current($element));
        }
        
        foreach($response as $r){
            if(isset($r['title'])){
                $title = $r['title'];
            }else if(isset($r['text'])){
                $title = $r['text'];
            }

            $rss_url = $r['xmlUrl'];
            $base_url = $r['htmlUrl'];
            
            $this->addSubscription($r['xmlUrl'], $user_id, $title, $base_url);
        }
    }
    
    function readOutline(& $response, $elements){
        foreach($elements as $element){
            
            if(property_exists($element, 'outline')){
                $this->readOutline($response, $element);
            }else{
                $rss = current($element->attributes());
                
                if(!isset($rss['type']) || $rss['type'] != 'rss'){
                    continue;
                }
                
                $response[] = $rss;
            }
        }
    }
}