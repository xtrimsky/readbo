<?php
/*
 * Lu_cm caches the amount of items a user read per feed
 * example, if feed id 1 has 140 items, and user read 50 of them
 * Lu_cm cache for user will look like
 * array(
 *  '1' => 50
 * );
 */
require_once(APPPATH.'models/datastore/cache.php');

class lu_cm extends Cache{
    private $user_id = null;
    
    function __construct($user_id){
        $this->user_id = $user_id;
    }
    
    /* saving data into cache, saves associative array with feeds as key, count as value
     */
    function add($data){
        return false;
        
        return Cache::set('','lu'. $this->user_id,$data,3600); //stores for 1h
    }
    
    function fetch(){
        return false;
        
        return Cache::get('','lu'. $this->user_id);
    }
    
    function update($feed_id, $count){
        return false;
        
        $cache = Cache::get('','lu'. $this->user_id);
        
        if($cache){
            if(!isset($cache[$feed_id])){
                $this->delete();
            }else{
                $cache[$feed_id] = $count;
                $this->add($cache);
            }
        }
    }
    
    function increment($feed_id){
        return false;
        
        $cache = Cache::get('','lu'. $this->user_id);
        
        if($cache){
            if(!isset($cache[$feed_id])){
                $this->delete();
            }else{
                $cache[$feed_id]++;
                $this->add( $cache);
            }
        }
    }
    
    function decrement($feed_id){
        return false;
        
        $cache = Cache::get('','lu'. $this->user_id);
        
        if($cache && isset($cache[$feed_id])){
            if(!isset($cache[$feed_id])){
                $this->delete();
            }else{
                $cache[$feed_id]--;
                $this->add($cache);
            }
        }
    }
    
    function delete(){
        return false;
        
        Cache::dirty('lu'.$this->user_id);
    }
    
}