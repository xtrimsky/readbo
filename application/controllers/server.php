<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Server extends Base_Controller {
    function compileAction(){
        $this->load->model('services/frontend');
        
        //combining css/js
        define('FORCE_SERVER_MEDIA', true);
        try{
            include(APPPATH.'vars/ext_groups.php');
            foreach($ext_groups as $k => $v){
                echo 'compiling '.$k."\n";
                $this->frontend->addExtGroup($k, false, true);
                echo 'compiling '.$k." with IE\n";
                $this->frontend->addExtGroup($k,true, true);
            }
            
            die('finished compiling');
        }catch(Exception $e){
            echo 'Error: '.$e->getMessage()."\n";
            
            die('compiling failed');
        }
        
        
    }
}