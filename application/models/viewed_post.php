<?php

require_once('datastore/init.php');
class Viewed_Post extends Datastore_Table {
    protected $table = 'viewed_posts';

    function addViewedPost($item_id, $user_id, $ip){
        //making sure that item was not already added to db
        $count1 = $this->count(array(
            'item_sid' => $item_id,
            'user_id' => $user_id
        ));
        
        $count2 = $this->count(array(
            'item_sid' => $item_id,
            'ip' => $ip
        ));
        
        if ($count1 > 0 || $count2 > 0) {
            return false;
        }
        
        $data = array(
            'item_sid' => $item_id,
            'user_id' => $user_id,
            'ip' => $ip,
            'timestamp' => time()
        );
        
        $this->insert($data);
        
        return true;
    }

}