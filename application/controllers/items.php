<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Items extends Base_Controller {
    function getAjax() {
        $response = array();

        $feed_ids = $this->input->post('ids');
        $start = $this->input->post('start');
        $start_time = $this->input->post('start_time');
        $user = $this->auth->getUser();
        
        if($start == 0){ $start_time = time(); }
        
        if(count($feed_ids) > 0){
            $this->load->model('item');
            $response['feeds'] = $this->item->getList($feed_ids, $user, $start, $start_time);
        }else{
            $response['feeds'] = array();
        }
        $response['start_time'] = $start_time;

        $this->sendToAjax($response);
    }
	
    function __construct(){
        parent::__construct();
        $this->load->model('item');
    }
    
    function likeAjax(){
        $response = array();
        
        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        
        if($this->item->itemExists($item_sid)){
            $this->load->model('like');
            $this->like->add($user->id, $item_sid);
            $response['success'] = true;
        }else{
            $response['success'] = false;
        }
        
        $this->sendToAjax($response);
    }
    
    function unlikeAjax(){
        $response = array();
        
        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        
        $this->load->model('like');
        $this->like->remove($user->id, $item_sid);
        
        $this->sendToAjax($response);
    }
    
    function markAsUnreadAjax() {
        $response = array();

        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        $feed_id = $this->input->post('feed_id');
        $this->item->markAsUnread($item_sid, $user->id);
        
        require_once(APPPATH.'models/cacheManagers/lu_cm.php');
        $cm = new lu_cm($user->id);
        $cm->decrement($feed_id);

        $this->sendToAjax($response);
    }
    
    function markAsReadAjax() {
        $response = array();

        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        $feed_id = $this->input->post('feed_id');
        $response['success'] = $this->item->markAsRead($item_sid, $user->id, $feed_id);
        
        if($response['success']){
            //on success updating cache
            require_once(APPPATH.'models/cacheManagers/lu_cm.php');
            $cm = new lu_cm($user->id);
            $cm->increment($feed_id);
        }
        
        $this->sendToAjax($response);
    }
    
    function markAllAsReadAjax() {
        $response = array();

        $user = $this->auth->getUser();
        $feed_id = $this->input->post('feed_id');
        
        if ($feed_id == 'newsfeed') {
            $this->load->model('subscription');
            $feed_id = $this->subscription->getSubscriptionIds($user->id);
        }
        
        $items = $this->item->getSIDsInFeeds($feed_id, $user->id);
        $this->item->markAllAsRead($items, $user->id, $feed_id);
        
        require_once(APPPATH.'models/cacheManagers/lu_cm.php');
        $cm = new lu_cm($user->id);
        $cm->delete();

        $this->sendToAjax($response);
    }
    
    function markAllFeedsAsReadAjax(){
        $response = array();

        $user = $this->auth->getUser();
        $this->load->model('subscription');
        $feed_ids = $this->subscription->getSubscriptionIds($user->id);
        
        $items = $this->item->getSIDsInFeeds($feed_ids, $user->id);
        $this->item->markAllAsRead($items, $user->id);
        
        require_once(APPPATH.'models/cacheManagers/lu_cm.php');
        $cm = new lu_cm($user->id);
        $cm->delete();

        $this->sendToAjax($response);
    }
}