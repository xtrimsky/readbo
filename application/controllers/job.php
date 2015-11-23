<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Job extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->load->model('jobs');
        
        if(!$this->input->is_cli_request())
        {
            redirect('/', 'refresh');
        }
    }
    
    function backupSQLAction(){
        $job_name = '_BACKUP_SQL_';
        $started = $this->jobs->run($job_name);
        
        if(!$started){
            $this->triggerErrorEmail('Could not execute cron backup sql');
        }
        
        $host = $this->db->hostname;
        $user = $this->db->username;
        $password = $this->db->password;
        $table = 'readbo';
        $path = '/var/www/releases/backups/';
        
        $day = date('Y-m-d_H-i');
        // SQL Outout (.sql)
        $backup = $path.$table.'_'.$day.'.sql';
        $c1 = "/usr/bin/mysqldump --host={$host} --user={$user} --password={$password} {$table} --quick --lock-tables --add-drop-table --routines > {$backup}";
        shell_exec($c1);
        shell_exec('gzip '.$backup);
        
        $this->jobs->end($job_name);
        exit;
    }
    
    function ultimateCleanAction(){
        $this->user->ultimateClean();
    }
    
    //recalculate trending items
    function renewTrendingAction(){
        $job_name = '_RENEW_TRENDING_';
        $started = $this->jobs->run($job_name);
        
        if(!$started){
            $this->triggerErrorEmail('Could not start cron renew trending');
        }
        
        $this->load->model('item');
        $this->item->get20TrendingItems(true);
        
        $this->jobs->end($job_name);
        exit;
    }
    
    function parseFeedsAction(){
        $service = $this->getParam('service');
        
        $job_name = "_PARSE_FEEDS_SERVICE_{$service}_";
        $chunk_size = 20;
        $offset = $this->getParam('offset');
        if(!is_null($offset)){$offset = intVal($offset);}else{$offset = -1;}
        
        $started = $this->jobs->run($job_name);
        
        if($offset === -1 && !$started){
            //exit;
            $this->triggerErrorEmail('parsing feeds could not be started, service '.$service);
        }
        
        if($offset === -1){
            exec('php index.php job parseFeeds offset 0 service '.$service.' > /dev/null &'."\n");
            exit;
        }
        
        $this->load->model('feed');
        $ids = $this->feed->getIdsForParsingWithOffset($chunk_size);
        
        if(!empty($ids)){
            //$ids_string = implode(',',$ids);
            
            $this->load->model('services/feeds');
            
            //not checking time as it was already checked
            $this->feeds->parseFeed($ids, false);
        }
        
        if(count($ids) == $chunk_size){
            exec('php index.php job parseFeeds offset 0 service '.$service.' > /dev/null &');
        }else{
            $this->jobs->end($job_name);
        }
        exit;
    }
    
    function triggerErrorEmail($message){
        //if job is running and we are trying to start it too, quitting, emailing support
        require_once(APPPATH.'models/objects/email.php');
        $email = new Email();
        $email->setFrom('error_cron@readbo.com');
        $email->setSubject('cron error');
        $email->setBody($message);
        $response = $email->send('support@readbo.com');
        exit;
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
    
    //removing all older items
    function clearOldItemsAction(){
        //$job_name = '_CLEAR_OLD_ITEMS_';
        //$started = $this->jobs->run($job_name);
        
        //if(!$started){
        //    $this->triggerErrorEmail('Could not start cron clear old items');
        //}
        
        $this->load->model('item');
        $this->item->clearOldItemData();
        
        //$this->jobs->end($job_name);
        exit;
    }
    
}