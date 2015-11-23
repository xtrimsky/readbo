<?php

require_once('datastore/init.php');
class Subscription extends Datastore_Table {
    protected $table = 'subscriptions';

    function getSubscriptionIds($user_id){
        $user_id = $this->escape($user_id);

        $result = $this->query(
                "SELECT feed_id " .
                "FROM subscriptions " .
                "WHERE subscriptions.user_id = {$user_id} "
        ,'get_sub_id_'.$user_id);

        $ids = array();
        foreach($result as $subscription){
            $ids[] = $subscription->feed_id;
        }

        return $ids;
    }

    function getSubscriptionsByFeed($user, $feed_ids){
        $user_id = $this->escape($user->id);

        if (is_array($feed_ids)){
            $ids = implode(',', $this->escapeArray($feed_ids));
        }else{
            $ids = $feed_ids;
        }
        
        if(empty($ids)){return array();}

        $query =    "SELECT name, user_id, feed_id, type ".
                    "FROM subscriptions ".
                    "WHERE user_id = {$user_id} AND feed_id IN ({$ids})";

        $result = $this->query($query);

        $subscriptions = array();
        foreach($result as $subscription){
            $subscriptions[$subscription->feed_id] = $subscription;
        }

        return $subscriptions;
    }

    function getUserSubscriptions($user_id, $feed_ids = array()) {
        $user_id = $this->escape($user_id);
        
        $feed_string = '';
        if (!empty($feed_ids)){
            if(!is_array($feed_ids)){$feed_ids = array($feed_ids);}
            
            $ids = implode(',', $this->escapeArray($feed_ids));
            $feed_string = "AND s.feed_id IN ({$ids})";
        }
        
        $sql1 =
            "SELECT s.feed_id, s.type, s.parent_id, s.name, '0' AS count ".
            "FROM subscriptions AS s " .
            "WHERE s.user_id = {$user_id} ".
            $feed_string;
            
        $subscriptions = $this->query($sql1);
        $result = array();
		
		if(!empty($subscriptions)){
			$ids = '';
			foreach($subscriptions as $s){
				$result[$s->feed_id] = $s;
				$ids .= $s->feed_id.',';
			}
			$ids = trim($ids,',');

			$sql2 = "SELECT COUNT(feed_id) AS count, feed_id ".
					"FROM items ".
					"WHERE feed_id IN ({$ids}) ".
					"GROUP BY feed_id";

			$counts = $this->query($sql2);
			foreach($counts as $c){
				$result[$c->feed_id]->count = $c->count;
			}
		}

        return $result;
    }

    function addFacebook($user_id, $access_token){
        $data = array(
            'type' => 'facebook',
            'url' => $access_token
        );
        
        $this->load->model('feed');
        $id = $this->feed->insert($data);

        $this->addSubscription($id, 'Facebook', $user_id, 'facebook');
    }
    
    function addTwitter($user_id, $access_token){
        $data = array(
            'type' => 'twitter',
            'url' => $access_token
        );
        
        $this->load->model('feed');
        $id = $this->feed->insert($data);

        $this->addSubscription($id, 'Twitter', $user_id, 'twitter');
    }
    
    function removeFacebook($user_id){
        $facebook_sub = $this->get(array('type' => 'facebook', 'user_id' => $user_id),true);
        
        $this->delete(array('type' => 'facebook', 'user_id' => $user_id));
        
        return $facebook_sub;
    }
    
    function removeTwitter($user_id){
        $twitter_sub = $this->get(array('type' => 'twitter', 'user_id' => $user_id),true);
        
        $this->delete(array('type' => 'twitter', 'user_id' => $user_id));
        
        return $twitter_sub;
    }

    function addSubscription($feed_id, $name, $user_id, $type) {
        $count = $this->count(array(
            'feed_id' => $feed_id,
            'user_id' => $user_id
        ));

        if ($count > 0) {
            return false;
        }

        $data = array(
            'name' => $name,
            'user_id' => $user_id,
            'feed_id' => $feed_id,
            'parent_id' => 0,
            'type' => $type
        );
        
        $this->insert($data);
        
        $this->auth->feedsNeedUpdate();

        return true;
    }
    
    function removeSubscription($feed_id, $user_id){

        $rows_affected = count($this->delete(array('feed_id' => $feed_id, 'user_id' => $user_id)));

        $this->load->model('lu');
        $this->lu->delete(array('feed_id' => $feed_id, 'user_id' => $user_id));

        $this->load->model('items_filtered');
        $this->items_filtered->delete(array('feed_id' => $feed_id, 'user_id' => $user_id));

        /*
         * 
         * this has been commented while likes don't have column feed_id
         * 
        $this->load->model('like');
        $this->like->delete(array('feed_id' => $feed_id, 'user_id' => $user_id)); */

        //checking if other users are using this feed, if not deleting it
        if($this->count(array('feed_id' => $feed_id)) === 0){
                $this->load->model('feed');
                $this->feed->delete(array('id' => $feed_id));
                
                $this->load->model('item');
                $this->item->delete(array('feed_id' => $feed_id));
        }

        return $rows_affected;
    }
    
    function renameSubscription($feed_id, $user_id, $name){
        $where = array('feed_id' => $feed_id, 'user_id' => $user_id);
        $data = array('name' => $name);
        
        $this->update($data, $where);
    }
    
    function getExport($user_id){
        $user_id = $this->escape($user_id);
        
        $sql =
            "SELECT s.feed_id, s.type, s.name, f.url, f.base_url ".
            "FROM subscriptions AS s " .
            "LEFT JOIN feeds AS f " .
            "ON f.id = s.feed_id " .
            "WHERE s.user_id = {$user_id}";
            
        return $this->query($sql);
    }
    
    function getFacebookAccessToken($user_id){
        $sql = 
            "SELECT f.url AS access_token ".
            "FROM subscriptions AS s ".
            "JOIN feeds AS f ".
            "ON s.feed_id = f.id ".
            "WHERE s.type = 'facebook' AND s.user_id = {$user_id}";
        
        $result = $this->query($sql);
        
        if(empty($result)){
            return false;
        }
        
        return current($result)->access_token;
    }
    
    /*
     * updating access token
     */
    function updateFacebookAccessToken($user_id, $access_token){
        $sql = 
            "SELECT f.id, f.url AS access_token ".
            "FROM subscriptions AS s ".
            "JOIN feeds AS f ".
            "ON s.feed_id = f.id ".
            "WHERE s.type = 'facebook' AND s.user_id = {$user_id}";
        
        $result = $this->query($sql);
        
        if(empty($result)){
            return false;
        }else{
            $feed = current($result);
            $sql = "UPDATE feeds SET url = '{$access_token}' WHERE id = '{$feed->id}'";
            $this->query($sql);
        }
        
        return true;
    }
    
    function getTwitterAccessToken($user_id){
        $sql = 
            "SELECT f.url AS access_token ".
            "FROM subscriptions AS s ".
            "JOIN feeds AS f ".
            "ON s.feed_id = f.id ".
            "WHERE s.type = 'twitter' AND s.user_id = {$user_id}";
        
        $result = $this->query($sql);
        
        if(empty($result)){
            return false;
        }
        
        return current($result)->access_token;
    }

}