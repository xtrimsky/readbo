<?php
require_once(APPPATH.'models/datastore/cache.php');

class user_cm extends Cache{
    private $username = null;
    
    function __construct($username){
        $this->username = $username;
    }
    
    /* saving data into cache, saves associative array with feeds as key, count as value
     */
    function add($data){
        return Cache::set('','u'. $this->username,$data,3600); //stores for 1h
    }
    
    function fetch(){
        return Cache::get('','u'. $this->username);
    }
    
    function delete(){
        Cache::dirty('u'.$this->username);
    }
    
}