<?php

require_once('datastore/init.php');
class Feed extends Datastore_Table {
    protected $table = 'feeds';
    
    const _STATUS_FREE_ = 'FREE'; //available
    const _STATUS_LOCKED_ = 'LOCKED'; //is currently parsed
    const _STATUS_WAITING_ = 'WAITING'; //will be parsed
    
    function getCountItems($feed_id){
        $feed_id = $this->escape($feed_id);
        
        $sql = "SELECT count(feed_id) AS count ".
               "FROM items ".
               "WHERE feed_id = {$feed_id} ".
               "GROUP BY feed_id";
        
        $result = $this->query($sql);
        
        if(empty($result)){
            return 0;
        }
        
        return current($result)->count;
    }
    
    function delete($data){
        $feed_id = $data['id'];
        
    	$this->load->model('item');
        $this->item->delete(array('feed_id' => $feed_id));

        return parent::delete($data);
    }
    
    function lock($feed_id){
        $feed_id = $this->escape($feed_id);
        $sql = "UPDATE {$this->table} SET status = '".self::_STATUS_LOCKED_."' WHERE id = {$feed_id}";
        
        return $this->query($sql);
    }
    
    function getIdsForParsingWithOffset($chunck_size = 10){
        $chunck_size = intVal($chunck_size);
        
        $next_update = time();
        
        $sql =  "SELECT id ".
                "FROM {$this->table} ".
                "WHERE status = '".self::_STATUS_FREE_."' ".
                "AND next_update <= {$next_update} ".
                "ORDER BY next_update ASC ".
                "LIMIT 0, ".$chunck_size;
                
        $result = $this->query($sql);
        $ids = array();
        
        if(!empty($result)){
            foreach($result as $r){
                $ids[] = $r->id;
            }
            
            $string_ids = implode(',',$ids);
            $sql2 = "UPDATE {$this->table} SET status = '".self::_STATUS_WAITING_."' WHERE id IN ({$string_ids})";
            $this->query($sql2);
        }
        
        return $ids;
    }
    
    /*
     * updates date of last update of feed. If no new items found, reducing priority
     */
    function feedParsed($feed){
        $next_update = time();
        
        if($feed->avg_priority >= -2){
            $next_update += 60*15;
        }else if($feed->avg_priority >= -4){
            $next_update += 60*30;
        }else if($feed->avg_priority == -5){
            $next_update += 3600;
        }else if($feed->avg_priority == -6){
            $next_update += 3600 * 5;
        }else if($feed->avg_priority == -7){
            $next_update += 3600 * 12;
        }else if($feed->avg_priority == -8){
            $next_update += 3600 * 24;
        }else if($feed->avg_priority == -9){
            $next_update += 3600 * 48;
        }else{
            $next_update += 3600 * 24 * 7;
            $feed->avg_priority = -10; //keep it at -10
        }
        
        $sql =  "UPDATE {$this->table} ".
                "SET avg_priority = {$feed->avg_priority},".
                "next_update = {$next_update},".
                "status = '".self::_STATUS_FREE_."' ".
                "WHERE id = {$feed->id}";
        
        return $this->query($sql);
    }
    
    /*
     * returns an array of feed ids
     */
    function getAllFeedIds(){
        $ids = array();
        
        $sql = "SELECT id FROM feeds";
        $result = $this->query($sql);
        
        foreach($result as $row){
            $ids[] = $row->id;
        }
        
        return $ids;
    }
}