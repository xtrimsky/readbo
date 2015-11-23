<?php

require_once('datastore/init.php');
class Like extends Datastore_Table{
    protected $table = 'likes';
    
    function add($user_id, $item_sid){
        if($this->hasLiked($user_id, $item_sid)){
            return;
        }
        
        $data = array(
            'user_id' => $user_id,
            'item_sid' => $item_sid,
            'timestamp' => time()
        );
        
        $this->insert($data);
    }
    
    function remove($user_id, $item_sid){
        $this->delete(array('user_id' => $user_id, 'item_sid' => $item_sid));
    }
    
    function hasLiked($user_id, $item_sid){
        $count = $this->count(array(
                'user_id' => $user_id,
                'item_sid' => $item_sid
        ));
        
        return $count > 0; //return true if contains this in db
    }
}