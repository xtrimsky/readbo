<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Search extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->loadService('rsearch');
    }
    
    function getAjax(){
        $search = $this->input->post('search');
        $feed_ids = $this->input->post('feed_ids');
        
        $user = $this->auth->getUser();
        if ($feed_ids == 'newsfeed') {
            $this->load->model('subscription');
            $feed_ids = $this->subscription->getSubscriptionIds($user->id);
        }
        
        $result = $this->rsearch->get($search, $feed_ids, $user);
        $this->sendToAjax($result);
    }
    
}