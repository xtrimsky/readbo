<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Admin extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->load->model('services/frontend');
        $this->frontend->addExtGroup('admin', $this->isIE());
        $this->frontend->addVariable('current_page', $this->router->method );
        $this->frontend->addVariable('MEDIA_SERVER', MEDIA_SERVER );
    }
    
    function indexAction(){
        $this->setLayout('admin');
        
        $this->load->model('admin_data');
        $data = $this->admin_data->getDashboardData();
        
        $this->setView('dashboard','admin', $data);
    }
    
    function sendInvitesAction(){
        $this->load->model('invite');
        $this->load->model('invitation_code');
        $invites = $this->invite->get();
        
        foreach($invites as $i){
            $code = $this->invitation_code->generateSingleCode();
            
            require_once(APPPATH.'models/objects/email.php');
            $email = new Email();
            $email->setFrom('support@readbo.com');
            $email->setSubject('Invitation to try Readbo!');
            $email->setTemplate('invitation', array('code' => $code));
            $success = $email->send($i->email);
            
            if($success){
                $this->invite->delete(array('id' => $i->id));
            }
        }
        
        header('Location: /admin/');
    }
    
    function usersAction(){
        $this->setLayout('admin');
        
        $users = $this->user->getFirst30();
        $data = array(
            'table' => $this->load->view('admin/users_table', array('users' => $users), true),
            'search' => $this->input->get('search')
        );
        
        
        $this->setView('users','admin', $data);
    }
    
    function cacheAction(){
        $this->setLayout('admin');
        
        $this->setView('cache','admin');
    }
    
    function clearMemcacheAction(){
        require_once(APPPATH.'models/datastore/cache.php');
        
        Cache::flush();
        $this->redirect('cache');
    }
    
    function releasesAction(){
        $this->setLayout('admin');
        
        $this->load->model('release');
        $data = array();
        $data['releases'] = $this->release->getAll();
        
        $this->setView('releases','admin', $data);
    }
    
    function setProdAction(){
        if(!ON_SERVER){die('your on local! WTF???');}
        $id = $this->input->post('id');
        
        if(empty($id)){exit;}
        
        $this->load->model('release');
        $release = $this->release->get(array('id' => $id), true);
        $folder = '/var/www/releases/'.$release->name;
        
        if(!is_dir($folder)){
            die('folder doesn\'t exist');
        }
        
        $current_dir = getcwd();
        
        chdir($folder);
        $output = shell_exec('rm -rf /var/www/current') . "\n";
        $output .= shell_exec('ln -s '.$folder.' /var/www/current') . "\n";
        chdir($current_dir);
        
        header('Location: /admin/releases');
    }
    
    function setStageAction(){
        if(!ON_SERVER){die('your on local! WTF???');}
        $id = $this->input->post('id');
        
        if(empty($id)){exit;}
        
        $this->load->model('release');
        $release = $this->release->get(array('id' => $id), true);
        $folder = '/var/www/releases/'.$release->name;
        
        if(!is_dir($folder)){
            die('folder doesn\'t exist');
        }
        
        $current_dir = getcwd();
        
        chdir($folder);
        $output = shell_exec('rm -rf /var/www/dev') . "\n";
        $output .= shell_exec('ln -s '.$folder.' /var/www/dev') . "\n";
        chdir($current_dir);
        
        header('Location: /admin/releases');
    }
    
    function createReleaseAjax(){
        if(!ON_SERVER){die('your on local! WTF???');}
        
        $name = $this->input->post('name');
        $name = trim($name);
        $new_folder = '/var/www/releases/'.$name;
        
        if(is_dir($new_folder)){
            $this->sendToAjax(array('success' => false,'error' => 'folder already exists, please create new one'));
        }
        
        $current_dir = getcwd();
        chdir('/var/www/releases/trunk');
        
        $output = shell_exec('/bin/mkdir '.$new_folder.' 2>&1') . "\n";
        
        $output .= shell_exec('/usr/local/bin/git stash') . "\n";
        $output .= shell_exec('/usr/local/bin/git stash clear') . "\n";
        $output .= shell_exec('/usr/local/bin/git pull') . "\n";
        $output .= shell_exec('/usr/local/bin/git archive master | tar -x -C '.$new_folder.'') . "\n";
        
        chdir($new_folder);
        $output .= shell_exec('rm -rf work') . "\n";
        $output .= shell_exec('rm -rf styles') . "\n";
        $output .= shell_exec('rm -rf scripts') . "\n";
        $output .= shell_exec('/bin/cp -fr server_files/* ./') . "\n";
        $output .= shell_exec('rm -rf server_files') . "\n";
        $output .= shell_exec('rm -rf /var/www/dev') . "\n";
        $output .= shell_exec('ln -s '.$new_folder.' /var/www/dev') . "\n";
        chdir($current_dir);
        
        $this->load->model('release');
        $this->release->insert(array(
            'name' => $name,
            'timestamp' => time()
        ));
        
        $this->sendToAjax(array('success' => true, 'output' => $output));
    }
    
    function invitesAction(){
        $this->setLayout('admin');
        $this->load->model('invitation_code');
        
        $code = $this->input->post('code');
        if(!empty($code)){
            $amount = intVal( $this->input->post('amount') );
            
            if($amount != 0){
                $this->invitation_code->insert(array(
                    'code' => $code,
                    'amount' => $amount
                ));
            }
        }
        
        $codes = $this->invitation_code->getAll();
        $data = array(
            'codes' => $codes
        );
        
        
        $this->setView('invites','admin', $data);
    }
    
    function fetchUsersSearchAjax(){
        $users = $this->user->search( $this->input->post('search') );
        
        $response = array(
            'html' => $this->load->view('admin/users_table', array('users' => $users), true)
        );
        
        $this->sendToAjax($response);
    }
    
    function logAsAction(){
        $this->setLayout('admin');
        
        $user_id = $this->getParam('user');
        
        $this->auth->logAs($user_id);
        
        redirect('/', 'refresh');
    }
    
    function makeAdminAjax(){
        $id = intVal($this->input->post('id'));
        
        $this->user->updateProperties($id,array('isAdmin' => true));
        
        $this->sendToAjax(array('success' => true));
    }
    
    function refreshDataAction(){
        require_once(APPPATH.'models/cacheManagers/admin_cm.php');
        $cm = new admin_cm();
        $cache = $cm->delete();
        
        redirect('/admin', 'refresh');
    }
    
    function compileAction(){
        $this->load->model('services/frontend');
        $this->frontend->useGoogleClosure();
        
        //combining css/js
        define('FORCE_SERVER_MEDIA', true);
        try{
            include(APPPATH.'vars/ext_groups.php');
            foreach($ext_groups as $k => $v){
                echo 'compiling '.$k."\n";
                $this->frontend->addExtGroup($k, false, true);
                echo 'compiling '.$k." with IE\n";
                $this->frontend->addExtGroup($k,true, true);
            }
            
            die('finished compiling');
        }catch(Exception $e){
            echo 'Error: '.$e->getMessage()."\n";
            
            die('compiling failed');
        }
        
        
    }
}