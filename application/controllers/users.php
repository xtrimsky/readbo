<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');
require_once('application/models/include/common.php');

class Users extends Base_Controller {

    function facebookConnectAjax(){
        $response = array();
        $this->load->model('services/facebook');
        $access_token = $this->input->post('accessToken');
        $fb_user = $this->facebook->getFacebookUser( $access_token );
        $user = $this->auth->getUser();
        
        if(isset($user->facebook_uid)){
            $this->load->model('subscription');
            $saved_access_token = $this->subscription->getFacebookAccessToken($user->id);
            
            if($saved_access_token != $access_token){
                $result = $this->subscription->updateFacebookAccessToken($user->id, $access_token);
                if($result){
                    $response['success'] = true;
                }else{
                    $response['success'] = false;
                    $response['code'] = 7;
                    $response['message'] = 'Could not update Facebook access token to server, please report a bug.';
                }
            }else{
                $response['success'] = true;
            }
        }else{
            if(is_object($fb_user) && isset($fb_user->id)){
                $response = $this->auth->connectFacebook($user, $fb_user);
            }else{
                $response['success'] = false;
                $response['code'] = 5;
                $response['message'] = 'Could not connect to facebook.';
            }
        }

        $this->sendToAjax($response);
    }
    
    function facebookDisconnectAjax(){
        $this->auth->disconnectFacebook();
        
        $this->sendToAjax(array('success' => true));
    }

    function loginAction() {
        $username = $this->input->post('txtLogin');
        $password = $this->input->post('txtPassword');
        $remember = $this->input->post('chkRemember');

        if ($this->auth->login($username, $password, $remember)) {
            $this->auth->updateLoggedInCount($username);
            redirect('/', 'refresh');
        }

        redirect('wrong_password', 'location', 301);
    }

    function registerAjax() {
        $response = array();

        $username = $this->input->post('username');
        $password = $this->input->post('password');
        //$invite_code = $this->input->post('invite_code');
        $email_address = strtolower($this->input->post('email'));

        if (strlen($username) > 0) {
            //$code = $this->auth->verifyInvitationCode($invite_code);
            //$valid = $code && $code->amount > 0;
            
            //if($valid){
                //$this->auth->decreaseAvailableCodeAmount($invite_code);
                
                //when creating new user, making sure there is no old data in the cache
                require_once(APPPATH.'models/cacheManagers/user_cm.php');
                $cm = new user_cm($username);
                $cm->delete();
                
                if ($this->auth->userExists($username)) {
                    $response['error'] = 1; //username exists
                }elseif($this->auth->emailExists($email_address)){
                    $response['error'] = 4; //email exists
                }else{
                    $data = array(
                        'username' => $username,
                        'password' => sha1($password),
                        'email' => $email_address,
                        'active' => '1'
                    );
                    $this->auth->register($data);
                    $response['success'] = true;
                    
                    $this->auth->login($username, $password, 'on');
                    
                    $data = array(
                        'username' => $username
                    );
                    
                    require_once(APPPATH.'models/objects/email.php');
                    $email = new Email();
                    $email->setFrom('noreply@readbo.com');
                    $email->setSubject('Welcome to Readbo!');
                    $email->setTemplate('welcome', $data);
                    $email->send($email_address);
                }
                /*
            }else{
                if(!$code){
                    $response['error'] = 2; //no code exists
                }else{
                    $response['error'] = 3; //code has been used too many times
                }
            } */
        }

        $this->sendToAjax($response);
    }

    function deleteAjax(){
        $this->auth->disable();
        $this->sendToAjax(array('success' => true));
    }

    function getSettingsAjax(){
        $response = array();

        $user = $this->auth->getUser();
        $hide_tips = '0';
        if(isset($user->properties->hide_tips) && $user->properties->hide_tips){
            $hide_tips = '1';
        }
        $show_read = '0';
        if(isset($user->properties->show_read) && $user->properties->show_read){
            $show_read = '1';
        }

        $response['settings'] = array(
            'username' => $user->username,
            'email' => $user->email,
            'facebook_uid' => $user->facebook_uid,
            'twitter_uid' => $user->twitter_uid,
            'hide_tips' => $hide_tips,
            'show_read' => $show_read
        );

        $this->sendToAjax($response);
    }
    
    function getProfileAjax(){
        $response = array();
        
        $username = $this->input->post('username');
        
        $user = $this->user->getBy('username', $username);
        
        if(!$user){
            $this->sendToAjax(array('success' => false));
        }
        
        $profile_pic = false;
        if(!empty($user->facebook_uid)){
            $profile_pic = 'https://graph.facebook.com/'.$user->facebook_uid.'/picture?type=large';
        }
        
        $response = array(
            'success' => true,
            'username' => $user->username,
            'profile_pic' => $profile_pic
        );
        
        $this->sendToAjax($response);
    }
    
    function saveSettingsAjax(){
        $response = array();
        
        $post = $this->input->post();
        $user = $this->auth->getUser();
        
        if(count($post) == 0){
            $response['success'] = false;
            $this->sendToAjax($response);
        }
        
        $data = array();
        $properties = array();
        foreach($post as $field => $value){
            if(substr($field,0,9) === 'property_'){
                $field = substr($field,9);
                if($field == 'isAdmin'){exit;}
                if($value == '1'){$value = true;}
                if($value == '0'){$value = false;}
                $properties[$field] = $value;
            }else{
                $data[$field] = $value;
            }
        }
        
        $this->user->updateProperties($user->id, $properties);
        
        if(!empty($data)){
            $this->user->update($data, array('id' => $user->id));
        }
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
        
        $response['success'] = true;
        $this->sendToAjax($response);
    }

    function logoutAction() {
        $user = $this->auth->getUser();
        $this->auth->logout($user->username);
        redirect('/', 'refresh');
    }
    
    function addInviteAjax(){
        $email = $this->input->post('email');
        $this->load->model('invite');
        
        $count = $this->invite->count(array(
            'email' => $email
        ));
        
        if($count == 0){
            $success = $this->invite->insert(array(
                'email' => $email,
                'timestamp' => time()
            ));
            
            $success = $success !== false ? true : false;
        }else{
            $success = false; //email already there
        }
        
        
        $response = array('success' => $success);
        
        $this->sendToAjax($response);
    }
    
    function saveNavExpandAjax(){
        $expanded = $this->input->post('expanded');
        $user = $this->auth->getUser();
        
        $this->user->updateProperties($user->id, array('nav_expanded' => $expanded));
        
        $response = array('success' => true);
        $this->sendToAjax($response);
    }
    
    function saveLastFeedAjax(){
        $ids = $this->input->post('ids');
        $user = $this->auth->getUser();
        
        $this->user->updateProperties($user->id, array('last_feed' => $ids));
        
        $response = array('success' => true);
        $this->sendToAjax($response);
    }
    
    function saveCloseWelcomeAjax(){
        $user = $this->auth->getUser();
        $this->user->updateProperties($user->id, array('welcome_screen' => false));
        
        $response = array('success' => true);
        $this->sendToAjax($response);
    }
    
    function postFacebookStatusAjax(){
        $user = $this->auth->getUser();
        $message = $this->input->post('message');
        
        $success = false;
        $hasFacebook = $user->facebook_uid == true;
        $error_message = '';
        if($hasFacebook){
            $this->load->model('subscription');
            $access_token = $this->subscription->getFacebookAccessToken($user->id);
            
            if($access_token){
                $this->load->model('services/facebook');
                $response = $this->facebook->postStatus($access_token, $user->facebook_uid, $message);
                
                if(isset($response->error)){
                    $error_message = $response->error->message;
                    $success = false;
                }else{
                    $success = true;
                }
            }
        }
        
        $response = array('success' => $success);
        
        if($error_message != ''){
            $response['error_message'] = $error_message;
        }
        
        $this->sendToAjax($response);
    }
    
    function shareFacebookLinkAjax(){
        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        
        $success = false;
        $hasFacebook = $user->facebook_uid == true;
        if($hasFacebook){
            $this->load->model('subscription');
            $access_token = $this->subscription->getFacebookAccessToken($user->id);
            
            if($access_token){
                $this->load->model('item');
                $item = $this->item->getBySID($item_sid, true);
                
                $this->load->model('services/facebook');
                $this->facebook->shareLink($access_token, $user->facebook_uid, $item);
                
                $this->item->markItemAsShared($item->id);

                $success = true;
            }
        }
        
        $response = array('success' => $success);
        $this->sendToAjax($response);
    }
    
    function postTwitterStatusAjax(){
        $user = $this->auth->getUser();
        $message = $this->input->post('message');
        
        $success = false;
        $hasTwitter = $user->twitter_uid == true;
        if($hasTwitter){
            $this->load->model('subscription');
            $access_token = $this->subscription->getTwitterAccessToken($user->id);
            
            if($access_token){
                $this->load->model('services/twitter');
                $success = $this->twitter->postStatus($access_token, $user->twitter_uid, $message);
            }
        }
        
        $response = array('success' => $success);
        $this->sendToAjax($response);
    }
    
    function shareTwitterLinkAjax(){
        $user = $this->auth->getUser();
        $item_sid = $this->input->post('item_sid');
        
        $success = false;
        $hasTwitter = $user->twitter_uid == true;
        if($hasTwitter){
            $this->load->model('subscription');
            $access_token = $this->subscription->getTwitterAccessToken($user->id);
            
            if($access_token){
                $this->load->model('item');
                $item = $this->item->getBySID($item_sid, true);
                
                $link = 'http://readbo.com/shares/'.$item->id;
                
                $message = Common::short($item->title, 118).': '.$link;
                
                $this->load->model('services/twitter');
                $success = $this->twitter->postStatus($access_token, $user->twitter_uid, $message);
            }
        }
        
        $response = array('success' => $success);
        $this->sendToAjax($response);
    }

}