<?php

require_once('datastore/init.php');
class User extends Datastore_Table {
    
    protected $table = 'users';
    
    function getBy($property, $value){
        if($value == ''){
            return false;
        }
        
        if($property === 'username'){
            require_once(APPPATH.'models/cacheManagers/user_cm.php');
            $cm = new user_cm($value);
            $cache = $cm->fetch();
            if($cache){return $cache;}
        }
        
        $user = $this->get(array($property => $value, 'active' => '1'), true);
        
        if($user){
            $properties = json_decode($user->properties);
            $this->setDefaultProperties($properties);
            $user->properties = $properties;
            
            if($property === 'username'){
                $cm->add($user);
            }
        }
        
        return $user;
    }
    
    function getDefaultProperties(){
        return array(
            'nav_expanded' => true,
            'welcome_screen' => true
        );
    }
    
    function setDefaultProperties(&$properties){
        $default = $this->getDefaultProperties();
        
        foreach($default as $key => $value){
            if(!isset($properties->{$key})){
                $properties->{$key} = $value;
            }
        }
    }
    
    function updateProperties($user_id, $properties){
        if(empty($properties)){return;}
        
        $user = $this->getBy('id', $user_id);
        
        //filling other properties
        foreach($user->properties as $k => $v){
            if(!isset($properties[$k])){
                $properties[$k] = $v;
            }
        }
        
        //cleaning values
        foreach($properties as &$p){
            if($p === 'false'){$p = false;}
            if($p === 'true'){$p = true;}
        }
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
        $this->update(array('properties' => json_encode($properties)), array('id' => $user_id));
    }

    function getFolders($user_id) {
        $folders = array();

        $folders[] = array(
            'id' => 'newsfeed',
            'name' => 'Newsfeed',
            'parent' => 0
        );
        
        return $folders;
        
        /* FOLDERS NOT YET HERE
        $this->startTable('folders');
        $folders_result = $this->get(array('user_id' => $user_id));
        $this->endTable();

        foreach ($folders_result as $folder) {
            $folders[] = array(
                'id' => $folder->id,
                'name' => $folder->name,
                'parent' => $folder->parent_id
            );
        } */

        
    }

    function countReadFeeds($user_id) {
        require_once(APPPATH.'models/cacheManagers/lu_cm.php');
        $cm = new lu_cm($user_id);
     
        $cache = $cm->fetch();
        if($cache){ return $cache; }
        
        $sql = "SELECT count(item_sid) AS count, feed_id ".
                "FROM lus ".
                "WHERE lus.user_id = '{$user_id}' GROUP BY feed_id";

        $result = $this->query($sql);

        $count = array();
        foreach ($result as $row) {
            $count[$row->feed_id] = intVal($row->count);
        }
        
        $cm->add($count);

        return $count;
    }
    
    function countFilteredItems($user_id){
        $sql = "SELECT count(item_sid) AS count, feed_id ".
                "FROM items_filtered AS f ".
                "WHERE f.user_id = '{$user_id}' GROUP BY feed_id";

        $result = $this->query($sql);

        $count = array();
        foreach ($result as $row) {
            $count[$row->feed_id] = intVal($row->count);
        }

        return $count;
    }

    function getFeeds($user_id, $parse = false, $ids = array()) {
        $feeds = array();

        $reads = $this->countReadFeeds($user_id);
        $filtered = $this->countFilteredItems($user_id);

        $this->load->model('subscription');

        if ($parse) { //for now disabling parsing here
            if(empty($ids)){
                $parseIds = $this->subscription->getSubscriptionIds($user_id);
            }else{
                $parseIds = $ids;
            }
        
            if (empty($parseIds)) {
                return $feeds;
            }
            
            $this->load->model('services/feeds');
            $this->feeds->parseFeed($parseIds);
        }

        foreach ($this->subscription->getUserSubscriptions($user_id, $ids) as $feed) {
            $id = intVal($feed->feed_id);
            
            $cur_reads = isset($reads[$id]) ? $reads[$id] : 0;
            $cur_filtered = isset($filtered[$id]) ? $filtered[$id] : 0;

            /*
            $parent_id = intVal($feed->parent_id);
            if ($parent_id == 0) {
                $parent_id = 'newsfeed';
            } */
            $parent_id = 'newsfeed';

            $count = intVal($feed->count) - $cur_reads - $cur_filtered;

            $feeds[] = array(
                'id' => $id,
                'name' => $feed->name,
                'parent' => $parent_id,
                'count' => $count,
                'type' => $feed->type
            );
        }

        return $feeds;
    }
    
    function updateLoggedInCount($username){
        $username = $this->escape($username);
        
        $sql = "UPDATE {$this->table} SET logins = logins + 1 WHERE username = {$username} AND active = 1;";
        $this->query($sql);
    }
    
    function updateLastLogin($user_id){
        $user_id = $this->escape($user_id);
        
        $time = time();
        $sql = "UPDATE {$this->table} SET last_login = {$time} WHERE id = {$user_id};";
        $this->query($sql);
    }
    
    function search($search){
        $search = trim( $this->escape($search), "'" );
        
        $sql =  "SELECT * FROM users ".
                "WHERE ".
                "username LIKE '%{$search}%' OR ".
                "email LIKE '%{$search}%' ";
                
        return $this->query($sql);
    }
    
    function getFirst30(){
        return $this->query("SELECT * FROM {$this->table} WHERE active = 1 LIMIT 30");
    }
    
    /*
     * checks all users that have been disabled, and destroys there feeds if not used
     */
    function ultimateClean(){
        $sql = "SELECT id FROM users WHERE active = '0';";
        $unactive_users = $this->query($sql);
        
        //listing all ids
        $users_ids = '';
        foreach($unactive_users as $u){
            $users_ids.=$u->id.',';
        }
        $users_ids = trim($users_ids,',');
        
        $sql = "SELECT * FROM subscriptions WHERE user_id IN ({$users_ids})";
        $subscriptions = $this->query($sql);
        
        $feed_ids = '';
        $feeds = array();
        foreach($subscriptions as $s){
            $sql = "SELECT * FROM subscriptions WHERE feed_id = {$s->feed_id} AND id <> {$s->id} LIMIT 1";
            $other_feeds = $this->query($sql);
            
            if(!empty($other_feeds)){
                continue;
            }
            
            $feed_ids .= $s->feed_id.',';
            $feeds[] = $s->feed_id;
        }
        $feed_ids = trim($feed_ids,',');
        
        foreach($feeds as $f){
            $this->load->model('lu');
            $this->lu->delete(array('feed_id' => $f));

            $this->load->model('items_filtered');
            $this->items_filtered->delete(array('feed_id' => $f));
            
            $this->load->model('item');
            $this->item->delete(array('feed_id' => $f));
        }
        $sql = "DELETE FROM feeds WHERE id IN ({$feed_ids})";
        $this->query($sql);
    }

}