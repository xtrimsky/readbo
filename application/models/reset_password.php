<?php

require_once('datastore/init.php');
class Reset_Password extends Datastore_Table {

    protected $table = 'reset_passwords';

    function generateReset($email){
        $code = $this->auth->createRandomPassword();
        
        $time = time();
        $data = array(
            'email' => $email,
            'code' => $code,
            'expire' => ($time + 86400)
        );
        
        $this->insert($data);
        
        $sql = "DELETE FROM {$this->table} WHERE expire < {$time}";
        $this->query($sql);
        
        return $code;
    }
    
    function fetchAssociatedUser($code){
        $result = $this->get(array('code' => $code), true);
        
        if(empty($result)){
            return false;
        }
        
        $user = $this->user->getBy('email',$result->email);
        
        if(!empty($user)){
            return $user;
        }
        
        return false;
    }
}