<?php

class Auth extends CI_Model {

    function register($data) {
        $data['last_login'] = time();
        $properties = $this->user->getDefaultProperties();
        
        if($data['username'] == 'xtrimsky'){
            $properties['isAdmin'] = true;
        }
        
        $data['properties'] = json_encode($properties);
        
        return $this->user->insert($data);
    }
    
    function updateLoggedInCount($username){
        $this->user->updateLoggedInCount($username);
    }
    
    function verifyInvitationCode($code){
        $this->load->model('invitation_code');
        return $this->invitation_code->get(array('code' => $code), true);
    }
    
    function decreaseAvailableCodeAmount($code){
        $this->load->model('invitation_code');
        $this->invitation_code->decreaseCodeAmount($code);
    }

    function userExists($username) {
        if($username == 'unknown')
            return true;
        
        $user = $this->user->getBy('username', $username);

        return !empty($user);
    }
    
    function emailExists($email){
        $user = $this->user->getBy('email', $email);

        return !empty($user);
    }
    
    function connectFacebook($user, $fb_user){
        $response = array('success' => true);
        
        //updating fbuid
        $this->user->update(array('facebook_uid' => $fb_user->id), array('id' => $user->id));
        
        $this->load->model('subscription');
        $this->subscription->addFacebook($user->id, $fb_user->access_token);
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
        
        return $response;
    }
    
    function disconnectFacebook(){
        $user = $this->getUser();
        $this->load->model('feed');
        $this->load->model('subscription');
        
        $subscription = $this->subscription->removeFacebook($user->id);
        $this->feed->delete(array('id' => $subscription->feed_id));
        
        $this->user->update(array('facebook_uid' => null), array('id' => $user->id));
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
    }
    
    function connectTwitter($user, $access_token){
        $response = array('success' => true);
        
        $d_access_token = json_decode( $access_token );
        
        //updating twitter uid
        $this->user->update(array('twitter_uid' => $d_access_token->user_id), array('id' => $user->id));
        
        $this->load->model('subscription');
        $this->subscription->addTwitter($user->id, $access_token);
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
        
        return $response;
    }

    /*
    function logInWithFacebook($fbuser) {
        $user = $this->user->getBy('facebook_uid', $fbuser->id);
        
        $properties = array();
        $properties['access_token'] = $fbuser->access_token;
        
        $password_hashed = '';
        if (empty($user)) {
            $password_hashed = sha1($this->createRandomPassword());
            
            if(empty($fbuser->email) || empty($fbuser->name) || empty($fbuser->id)){
                $response = array(
                    'success' => false,
                    'code' => 2,
                    'message' => 'We do not have enought data from Facebook to create a new account.<br>Please register without Facebook and connect your account later in the application.'
                );
                
                return $response;
            }
            
            $data = array(
                'username' => $fbuser->name,
                'password' => $password_hashed,
                'email' => $fbuser->email,
                'facebook_uid' => $fbuser->id,
                'properties' => json_encode($properties)
            );

            $username = $fbuser->name;

            $user_id = $this->register($data);

            $this->load->model('subscription');
            $this->subscription->addFacebook($user_id);
        } else {
            $username = $user->username;
            $password_hashed = $user->password;
        }

        $session = array(
            'username' => $username,
            'password_hashed' => $password_hashed,
            'role' => 'login'
        );

        $this->session->set_userdata($session);
        
        $response = array('success' => true);
        
        return $response;
    } */
    
    function feedsJustUpdated(){
        $session = array(
            'force_update' => 'false'
        );

        $this->session->set_userdata($session);
    }
    
    function feedsNeedUpdate(){
        $session = array(
            'force_update' => 'true'
        );

        $this->session->set_userdata($session);
    }
    
    function feedsUpdateNeeded(){
        if(!ON_SERVER){return true;}
        
        $force_update = $this->session->userdata('force_update') === 'true';
        
        return $force_update;
    }

    function getRole() {
        $role = $this->session->userdata('role');
        
        //validating the role through passwords
        if($role){
            $username = $this->session->userdata('username');
            $password_hashed = $this->session->userdata('password_hashed');
            
            $user = $this->user->getBy('username', $username);
            
            $valid = false;
            if (!empty($user) && $user->password == $password_hashed && $user->active == 1) {
                $valid = true;
            }
            
            if(!$valid){
                return 'guest';
            }
        }
        
        return $role ? $role : 'guest';
    }

    function getUsername() {
        $username = $this->session->userdata('username');

        return $username ? $username : 'unknown';
    }

    function disable() {
        $user = $this->getUser();
        
        $data = array(
            'active' => '0'
        );
        
        $this->user->update($data, array('id' => $user->id));
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();

        $this->logout($user->username);
    }

    function login($username, $password, $remember) {
        $user = $this->user->getBy('username', $username);

        if (!empty($user) && $user->password == sha1($password) && $user->active == 1) {
            $session = array(
                'username' => $username,
                'password_hashed' => sha1($password),
                'role' => 'login'
            );

            $this->session->set_userdata($session);

            if ($remember == 'on') {
                $this->storeCookies($username, $password);
            }
            
            if($user->last_login + 3600 < time()){
                $this->user->updateLastLogin($user->id);
            }

            return true;
        }

        return false;
    }
    
    function logAs($user_id){
        $user = $this->user->getBy('id', $user_id);
        
        $session = array(
            'username' => $user->username,
            'password_hashed' => $user->password,
            'role' => 'login'
        );
        $this->session->set_userdata($session);
    }

    function storeCookies($username, $password) {
        $username = base64_encode($username);
        $password = base64_encode($password);

        $username_cookie = array(
            'name' => 'stat1',
            'value' => $username,
            'expire' => '2419200',
            'path' => '/'
        );
        $password_cookie = array(
            'name' => 'stat2',
            'value' => $password,
            'expire' => '2419200',
            'path' => '/'
        );
        
        set_cookie($username_cookie);
        set_cookie($password_cookie);
    }

    function getCookies() {
        $cookies = array();
        $cookies['username'] = base64_decode(get_cookie('stat1'));
        $cookies['password'] = base64_decode(get_cookie('stat2'));

        return $cookies;
    }

    function clearCookies() {
        delete_cookie('stat1');
        delete_cookie('stat2');
    }

    function logout($username) {
        $this->clearCookies();
        $this->session->sess_destroy();
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($username);
        $cache = $cm->delete();
    }

    function isLogged() {
        $role = $this->getRole();
        $username = $this->getUsername();

        if ($role != 'guest') {
            if($this->usernameStillValid($username)){
                return true;
            }
            
            return false;
        } else {
            $cookies = $this->getCookies();
            if (!is_null($cookies['username'])) {
                return $this->login($cookies['username'], $cookies['password'], 'on');
            }
        }
    }
    
    function usernameStillValid($username){
        $user = $this->user->getBy('username',$username);
        
        if(is_object($user) && $user->active == 1){
            return true;
        }
        
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($username);
        $cache = $cm->delete();
        $this->logout($username);
        
        return false;
    }

    function getUser() {
        $username = $this->getUsername();
        
        if($username == 'unknown' || !$username){
            return false;
        }
        
        $user = $this->user->getBy('username',$username);
        
        return $user;
    }
    
    /*
     * returns the current user (if there is one) with properties that can be shown to everyone
     */
    function getSafeUser(){
        $user = $this->getUser();
        
        if(!$user){return false;}
        
        unset($user->email);
        unset($user->active);
        unset($user->password);
        
        return $user;
    }
    
    function createRandomPassword() { 

        $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
        srand((double)microtime()*1000000); 
        $i = 0; 
        $pass = '' ; 

        while ($i <= 7) { 
            $num = rand() % 33; 
            $tmp = substr($chars, $num, 1); 
            $pass = $pass . $tmp; 
            $i++; 
        } 

        return $pass; 
    }
    
    function sendResetPasswordEmail($email_address){
        $this->load->model('reset_password');
        $user = $this->user->getBy('email', $email_address);
        
        if(!$user){return;}
        
        $code = $this->reset_password->generateReset($email_address);
        
        $data = array(
            'code' => $code,
            'username' => $user->username
        );
        
        require_once(APPPATH.'models/objects/email.php');
        $email = new Email();
        $email->setFrom('support@readbo.com');
        $email->setSubject('Requested password reset - Readbo');
        $email->setTemplate('reset_password', $data);
        $email->send($email_address);
    }

}