<?php
require_once(APPPATH.'models/datastore/cache.php');

class admin_cm extends Cache{
    /* saving data into cache, saves associative array with feeds as key, count as value
     */
    function add($data){
        return Cache::set('','adm',$data,86400); //stores for 1 day
    }
    
    function fetch(){
        return Cache::get('','adm');
    }
    
    function delete(){
        Cache::dirty('adm');
    }
    
}