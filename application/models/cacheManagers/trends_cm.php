<?php
require_once(APPPATH.'models/datastore/cache.php');

class trends_cm extends Cache{
    /* saving data into cache, saves associative array with feeds as key, count as value
     */
    function add($data){
        return Cache::set('','trends',$data,2592000); //stores for a month
    }
    
    function fetch(){
        return Cache::get('','trends');
    }
}