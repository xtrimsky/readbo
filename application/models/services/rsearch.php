<?php

require_once('application/models/include/common.php');

class Rsearch extends CI_Model {
    
    function get($search, $ids, $user){
        
        $searchArr = $this->getArrayOfSearch($search);
        
        if(empty($searchArr)){
            return $this->result(false, 'No valid search found.');
        }
        
        //return $this->item->getList($ids, $user, 0, $searchArr);
    }
    
    function result($success, $message){
        $template = array(
            'success' => false,
            'message' => $message
        );
        
        if(!$success){
            $template['message'] = $message;
            return $template;
        }
        
    }
    
    function getArrayOfSearch($search){
        $pass1 = explode('"',$search); //separating guillemet
        
        $pass2 = array();
        $index = 1;
        foreach($pass1 as $p){
            $mod = $index / 2;
            if(intVal($mod) == $mod){
                $result = $p;
                if($result != ''){
                    $pass2[] = $result;
                }
            }else{
                $p2 = explode(' ',$p);
                foreach($p2 as $pp2){
                    $result = str_replace(' ', '', $pp2);
                    if($result != ''){
                        $pass2[] = $result;
                    }
                }
            }
            $index++;
        }

        return $pass2;
    }
    
    function filter($search, $item){
        $properties = array('content', 'title', 'link', 'type', 'author');
        
        foreach($search as $s){
            $t = strtolower($s);
            
            foreach($properties as $p){
                $value = strtolower($item->{$p});
                
                if($value != ''){
                    //stoping after 20
                    if(strpos($value, $t) !== false){
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
    
}