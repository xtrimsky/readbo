<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Reader extends Base_Controller {
    
    function renameSubscriptionAjax(){
        $response = array();
        
        $feed_id = $this->input->post('id');
        $name = $this->input->post('name');
        
        $user = $this->auth->getUser();
        
        $this->load->model('subscription');
        $response['success'] = $this->subscription->renameSubscription($feed_id, $user->id, $name);
        $response['new_name'] = $name;
        
        $this->sendToAjax($response);
    }

    function removeSubscriptionAjax() {
        $response = array();

        $feed_id = $this->input->post('id');

        $user = $this->auth->getUser();
        $this->load->model('subscription');
        $response['success'] = $this->subscription->removeSubscription($feed_id, $user->id) > 0;

        $this->sendToAjax($response);
    }

    function updateFeedsAjax() {
        $response = array();
        
        $ids = $this->input->post('ids');
        $parse = $this->input->post('parse') == 'true';

        $user = $this->auth->getUser();

        //$response['folders'] = $this->user->getFolders($user->id);
        //parsing feeds only if not on server
        $response['feeds'] = $this->user->getFeeds($user->id, $parse, $ids);

        $this->sendToAjax($response);
    }
    
    function feedsFinishedUpdatingAjax(){
        $this->auth->feedsJustUpdated();
        
        $this->sendToAjax(array('success' => true));
    }

    function searchFeedsAjax() {
        $response = array();

        $search = urlencode($this->input->post('search'));

        $this->load->model('services/google');
        $response = $this->google->search($search);

        $this->sendToAjax($response);
    }

    function addSubscriptionAjax() {
        $response = array();

        $url = $this->input->post('url');
        $user = $this->auth->getUser();

        $this->load->model('services/feeds');
        $response = $this->feeds->addSubscription($url, $user->id);
        
        $this->auth->feedsNeedUpdate();

        $this->sendToAjax($response);
    }
    
    function addFeedByUrlAjax(){
        $response = array();
        
        $user = $this->auth->getUser();
        $url = $this->input->post('url');
        
        $this->load->model('services/feeds');
        $response = $this->feeds->addSubscription($url, $user->id);
        
        $this->auth->feedsNeedUpdate();
        
        $this->sendToAjax($response);
    }
    
    function addTwitterSearchAjax(){
        $response = array();

        $search = $this->input->post('search');
        $user = $this->auth->getUser();

        $this->load->model('services/feeds');
        $response = $this->feeds->addTwitterSearchSubscription($user->id, $search);
        
        $this->auth->feedsNeedUpdate();

        $this->sendToAjax($response);
    }
}