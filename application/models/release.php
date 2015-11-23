<?php

require_once('datastore/init.php');
class Release extends Datastore_Table{
    protected $table = 'releases';
    
    function getAll(){
        $sql = "SELECT * FROM releases ORDER BY timestamp DESC";
        
        return $this->query($sql);
    }
}