<?php

require_once('datastore/init.php');
class Invitation_code extends Datastore_Table {

    protected $table = 'invitation_codes';
    
    function decreaseCodeAmount($code){
        $code = $this->escape($code);
        
        $sql = "UPDATE {$this->table} SET amount = amount - 1 WHERE code = {$code}";
        $this->query($sql);
    }
    
    function getAll(){
        return $this->query("SELECT * FROM {$this->table}");
    }
    
    function generateSingleCode(){
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"; 
        srand((double)microtime()*1000000); 
        $i = 0; 
        $pass = '' ; 

        while ($i <= 4) { 
            $num = rand() % 33; 
            $tmp = substr($chars, $num, 1); 
            $pass = $pass . $tmp; 
            $i++; 
        }
        
        $pass = 'RBO'.$pass;
        
        $this->insert(array(
            'code' => $pass,
            'amount' => 1
        ));

        return $pass; 
    }

}