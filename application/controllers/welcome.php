<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Welcome extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->load->model('services/frontend');
        $this->frontend->addVariable('MEDIA_SERVER', MEDIA_SERVER );
    }

    function loginAction() {
        if ($this->auth->isLogged()) {
            $this->indexAction();
            return false;
        }
        
        $this->setLayout('login');

        $tp_dialogs = $this->load->view('js_templates/login', null, true);
        $this->frontend->addVariable('js_templates', $tp_dialogs);

        $this->frontend->addExtGroup('login', $this->isIE());

        $this->setLayoutVar('incorrect', $this->getParam('error') != null);
        
        if ($this->getParam('signup') != null) {
            $this->frontend->addVariable('signup_code', $this->getParam('signup'));
        }
    }

    function indexAction() {
        if (!$this->auth->isLogged()) {
            $this->loginAction();
            return false;
        }
        $user = $this->auth->getUser();
        
        //devtoolbar
        $toolbar = '';
        if($this->showDebugToolbar){
            $toolbar = $this->load->view('dev/toolbar', null, true);
        }
        $this->setLayoutVar('toolbar', $toolbar);
        $this->frontend->addVariable('show_toolbar', $this->showDebugToolbar);
        
        //###### INFO PANEL #######
        $info_panel = '';
        if($user->properties->welcome_screen){
            $info_panel = $this->load->view('info_panels/welcome.php', array('user' => $user), true);
        }
        //#########################
        
        //###### TIP BOX #######
        $tip_box = $this->load->view('info_panels/tip_box.php', array('user' => $user), true);
        //#########################
        
        //loading templates (used by JS later)
        $tp_main = $this->load->view('js_templates/main', array(
            'hasFacebook' => !empty($user->facebook_uid),
            'hasTwitter' => !empty($user->twitter_uid)
        ), true);
        $tp_dialogs = $this->load->view('js_templates/dialogs', null, true);
        $tp_panels = $this->load->view('js_templates/panels', array(
            'hasFacebook' => !empty($user->facebook_uid),
            'hasTwitter' => !empty($user->twitter_uid),
            'tip_box' => $tip_box,
            'info_panel' => $info_panel
        ), true);
        $js_templates = $tp_main . $tp_dialogs. $tp_panels;
        $this->frontend->addVariable('js_templates', $js_templates);

        $this->frontend->addExtGroup('app', $this->isIE());
        
        $feeds = $this->user->getFeeds($user->id);
        
        if(ON_SERVER){
            $this->frontend->addVariable('update_feeds', $this->auth->feedsUpdateNeeded());
        }else{
            $this->frontend->addVariable('update_feeds', false);
        }
        
        $this->frontend->addVariable('feeds', $feeds );
        $this->frontend->addVariable('time_display', 12);
        $this->frontend->addVariable('user', $this->auth->getSafeUser());
        
        if ($this->getParam('profile') != null) {
            $this->frontend->addVariable('profile_username', $this->getParam('profile') );
        }
        
        
        //if has a parameter error, shows the error
        if ($this->getParam('error') != null) {
            $error_type = $this->getParam('error');
            
            if($error_type == 'importing'){
                $this->frontend->addVariable('error_message', 'The file failed importing.<br/><br/>If you don\'t know why, please send us an email with your file at<br/>support@readbo.com and we will help you.');
            }
        }
        
        
        //$this->setLayoutVar('hasFacebook', !empty($user->facebook_uid) );
        //$this->setLayoutVar('hasTwitter', !empty($user->twitter_uid) );
    }

}