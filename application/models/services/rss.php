<?php

require_once('application/models/include/common.php');

class Rss extends CI_Model {

    protected $rss;

    public function getFeedInfo($url) {
        if($this->is404($url)){return false;}
        
        $this->config->set_item('log_threshold', '0'); 
        libxml_use_internal_errors(true);
        $feed = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->config->set_item('log_threshold', '1'); 
        
        if ($feed === FALSE){
            return false;
        }
        
        if(isset($feed->channel)){
            $info = array(
                'title' => trim(current($feed->channel->title)),
                'base_url' => trim(current($feed->channel->link))
            );
        }else if(isset($feed->title)){
            $info = array(
                'title' => trim(next($feed->title)),
                'base_url' => trim($this->getBaseUrl($url))
            );
        }
        
        if(!isset($info['title'])){
            $info['title'] = $url;
        }
        if(!isset($info['base_url'])){
            $info['base_url'] = $url;
        }
        
        return $info;
    }

    public function getFeed($url) {
        $feed = array();
        $ids = array();
        
        $url = trim(prep_url($url));
        $limit = 40;
        
        if($this->is404($url)){return array();}

        $this->config->set_item('log_threshold', '0'); 
        libxml_use_internal_errors(true);
        $feed_stream = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);
        $this->config->set_item('log_threshold', '1'); 
        if ($feed_stream === FALSE)
            return array();

        // Detect the feed type. RSS 1.0/2.0 and Atom 1.0 are supported.
        if(isset($feed_stream->channel)){
            $items = $feed_stream->xpath('//item');
            $lastBuildDate = strtotime(current($feed_stream->channel->lastBuildDate));
        }else{
            $items = $feed_stream->entry;
            $lastBuildDate = strtotime(current($feed_stream->updated));
        }
        
        $base_url = null;
        
        if(!$lastBuildDate){
            //some rss feed don't even have a last updated (ycombinator)
            $lastBuildDate = time();
        }

        $i = 0;
        foreach ($items as $item) {
            $item = (array) $item;
            
            if($i == $limit){
                break;
            }
            
            $name = 'Unknown';
            if (isset($item['author'])) {
                $name = $item['author'];

                if (is_object($name)) {
                    $name = $name->name;
                }
            }

            $link = null;
            if(isset($item['link'])){
                $link = $item['link'];
                if (is_array($link)) {
                    $link = current($link['0']->attributes()->href);
                }elseif(is_object($link)){
                    $href = $link->attributes()->href;
                    if(is_object($href) || is_array($href)){
                        $link = current($href);
                    }else{
                        print_r($href);
                    }
                }
            }else if(isset($item['guid']) && strpos($item['guid'],'http://') !== false ){
                $link = $item['guid'];
            }
            
            if(is_null($link) || !is_string($link)){
                if(is_null($base_url)){
                    $info = $this->getFeedInfo($url);
                    $base_url = $info['base_url'];
                }
                
                $link = $base_url;
            }
            
            //notitle
            if(is_array($item['title'])){
                foreach($item['title'] as $t){
                    if(is_string($t)){
                        $item['title'] = $t;
                    }
                }
            }
            if(!isset($item['title']) || !is_string($item['title'])){
                $item['title'] = 'No Title';
            }

            $date = 0;
            $usingLastBuildDate = false;
            if (isset($item['published'])) {
                $date = strtotime(substr($item['published'], 0, 10) . ' ' . substr($item['published'], 11));
            } else if (isset($item['pubDate'])) {
                if(is_array($item['pubDate'])){
                    foreach($item['pubDate'] as $pubDate){
                        if(is_string($pubDate)){
                            $date = $pubDate;
                        }
                    }
                }else{
                    $date = strtotime($item['pubDate']);
                }
            } else {
                //no date!
                $lastBuildDate--; //reducing timestamp by one second to keep them in order
                $date = $lastBuildDate;
                $usingLastBuildDate = true;
            }
            
            //incorrect timestamp ?
            $time = time();
            if($date > $time){
                $date = $time;
            }

            if (isset($item['id'])) {
                $id = $item['id'];
            } else if (isset($item['guid'])) {
                $id = $item['guid'];
            } else {
                if(!$usingLastBuildDate){
                    $id = $date.$item['title'];
                }else{
                    $id = $item['title'];
                }
            }
            $id = md5($url.$id);

            $content = '';
            if (isset($item['content'])) {
                $content = $item['content'];
                if(is_object($content)){
                    $content = $content->asXML();
                }
            } else if (isset($item['description'])) {
                $content = $item['description'];
            } else {
                continue;
            }
            
            if(is_array($content)){
                foreach($content as $c){
                    if(is_string($c)){
                        $content = $c;
                        break;
                    }
                }
            }

            $ids[] = $id;
            $feed[$id] = array(
                'content' => $content,
                'title' => $item['title'],
                'date' => (int) $date,
                'link' => $link,
                'author' => $name
            );
            
            $i++;
        }
        
        $feed['ids'] = $ids;

        return $feed;
    }
    
    public function getBaseUrl($url){
        $parsed_url = parse_url($url);
        $url = $parsed_url['scheme'].'://'.$parsed_url['host'].'/';
        return $url;
    }
    
    function validUrl($url){
        return (bool) filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

    function is404($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 4);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);

        /* Get the HTML or whatever is linked in $url. */
        $response = curl_exec($ch);

        /* Check for 404 (file not found). */
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpCode >= 400 || $httpCode === 0) {
            curl_close($ch);
            return true;
        }

        curl_close($ch);
        return false;
    }
}