<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');
require_once('application/models/include/URLManager.php');

class Shares extends Base_Controller {
    function __construct(){
        parent::__construct();
        
        $this->load->model('services/frontend');
        $this->frontend->addVariable('MEDIA_SERVER', MEDIA_SERVER );
    }
    
    function indexAction(){
        $this->setLayout('shares');
        
        $id = intVal($this->getParam('id'));
        $this->load->model('item');
        
        $item = $this->item->get(array('id' => $id), true);
        if(!$item) $this->forward404($id);
        
        $this->frontend->addExtGroup('toolbar', $this->isIE());
        
        $user_id = 0;
        $ip = $_SERVER['REMOTE_ADDR'];
        if($this->auth->isLogged()){
            $this->setLayoutVar('logged_in', true);
            
            $user = $this->auth->getUser();
            $user_id = $user->id;
            
            $this->load->model('viewed_post');
            $newView = $this->viewed_post->addViewedPost($item->item_sid, $user_id, $ip);
            
            if($newView){
                $this->item->increaseItemViewed($id);
            }
            
            header('Location: '.$item->link);
        }else{
            $this->setLayoutVar('logged_in', false);
        }
        
        $URLManager = new URLManager();
        $og_metas = $URLManager->grabOGMeta($item->link);
        
        $this->setLayoutVar('og_metas', $og_metas);
        $this->setLayoutVar('item', $item);
    }
}