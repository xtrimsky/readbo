<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Link extends Base_Controller {
    function indexAction(){
        $id = intVal($this->getParam('id'));
        $this->load->model('item');
        
        $item = $this->item->get(array('id' => $id), true);
        if(!$item) $this->forward404($id);
        
        $user_id = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        if($this->auth->isLogged()){
            $user = $this->auth->getUser();
            $user_id = $user->id;
            
            $this->load->model('viewed_post');
            $newView = $this->viewed_post->addViewedPost($item->item_sid, $user_id, $ip);
            
            if($newView){
                $this->item->increaseItemViewed($id);
            }
        }
        
        $url = $item->link;
        header('Location: '.$url);
        exit;
    }
}