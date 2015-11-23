<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Pages extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->load->model('services/frontend');
        $this->frontend->addVariable('MEDIA_SERVER', MEDIA_SERVER );
    }
    
    function indexAction(){
        $this->__default();
    }
    
    function termsAction(){
        $this->__default();
        
        $this->setLayoutVar('title', 'Terms of Use');
        $this->setLayoutVar('url', 'terms');
        $this->setView('terms','pages');
    }
    
    function whatIsReadboAction(){
        $this->__default();
        
        $this->setLayoutVar('title', 'What is Readbo ?');
        $this->setLayoutVar('url', 'what_is_readbo');
        $this->setView('what_is_readbo','pages');
    }
    
    function trendingAction(){
        $this->__default();
        $data = array();
        
        $this->load->model('item');
        $data['items'] = $this->item->get20TrendingItems();
        
        $this->setLayoutVar('title', 'Trending');
        $this->setLayoutVar('url', 'trending');
        $this->setView('trending','pages', $data);
    }
    
    function privacyAction(){
        $this->__default();
        
        $this->setLayoutVar('title', 'Privacy');
        $this->setLayoutVar('url', 'privacy');
        $this->setView('privacy','pages');
    }
    
    function forgotPasswordAction(){
        $view = 'forgot_password';
        $email = $this->input->post('email');
        $error = '';
        if($email){
            if(!$this->auth->emailExists($email)){
                $error = 'This email address is not in our database!';
            }else{
                $this->auth->sendResetPasswordEmail($email);
                $view = 'reset_password_sent';
            }
        }
        
        $data = array(
            'error' => $error
        );
        
        $this->__default();
        
        $this->setLayoutVar('title', 'Forgot my password');
        $this->setLayoutVar('url', 'forgot_password');
        $this->setView($view,'pages', $data);
    }
    
    function resetPasswordAction(){
        $code = $this->getParam('code');
        $password = $this->input->post('password');
        
        $this->load->model('reset_password');
        $user = $this->reset_password->fetchAssociatedUser($code);
        
        $this->__default();
        
        $this->setLayoutVar('title', 'Reset Password');
        $this->setLayoutVar('url', 'reset_password');
        
        if(!$user){
            $this->setView('reset_password_expired','pages');
        }else{
            if(!empty($password)){
                $sha1 = sha1($password);
                $this->user->update(array('password' => $sha1),array('id' => $user->id));
                $this->reset_password->delete(array('email' => $user->email));
                
                //reset cache
                require_once(APPPATH.'models/cacheManagers/user_cm.php');
                $cm = new user_cm($user->username);
                $cache = $cm->delete();
                
                $this->setView('password_was_reset','pages');
            }else{
                $data = array(
                    'user' => $user
                );
                $this->setView('reset_password','pages', $data);
            }
        }
    }
    
    function unsubscribeAction(){
        $email = $this->input->post('email');
        $message = '';
        
        if($email){
            $this->load->model('invite');
            $this->load->model('unsubscribe');
            $this->unsubscribe->delete(array('email' => $email));
            $this->invite->delete(array('email' => $email));
            $this->unsubscribe->insert(array('email' => $email));
            
            $message = 'Your email address '.$email.' is now unsubscribed from Readbo.com!';
        }
        
        $this->__default();
        
        $data = array(
            'message' => $message
        );
        
        $this->setLayoutVar('url', 'unsubscribe');
        $this->setLayoutVar('title', 'Unsubscribe');
        $this->setView('unsubscribe','pages', $data);
    }
    
    function contactAction($error = '', $message = false){
        $this->__default();
        
        $this->setLayoutVar('url', 'contact');
        $this->setLayoutVar('title', 'Contact us');
        
        if(!$message){
            $data = array(
                'user' => $this->auth->getUser(),
                'error' => $error
            );
            $this->setView('contact','pages', $data);
        }else{
            $this->setView('email_sent','pages');
        }
    }
    
    function sendEmailAction(){
        $form = $this->input->post('form');
        
        $message = $form['text']."\n\n\n";
        foreach($_SERVER as $k => $v){
            $message .= $k.': '.$v."\n";
        }
        
        require_once(APPPATH.'models/objects/email.php');
        $email = new Email();
        $email->setFrom($form['email']);
        $email->setSubject('readbo.com - Contact US - '.$form['name']);
        $email->setBody($message);
        $response = $email->send('support@readbo.com');
        
        if(!$response && !empty($form)){
            $this->contactAction('This email could not be sent, there must be a bug with our mailing system!');
        }else{
            $this->contactAction('', true);
        }
    }
    
    function __default(){
        $this->setLayout('pages');
        
        $this->frontend->addExtGroup('pages', $this->isIE());
    }
    
}