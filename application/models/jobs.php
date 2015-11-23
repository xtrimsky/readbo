<?php

require_once('datastore/init.php');
class Jobs extends Datastore_Table {
    protected $table = 'jobs';
    
    const _JOB_PARSE_FEEDS_ = '_PARSE_FEEDS_';
    
    const _STATUS_FREE_ = '_FREE_';
    const _STATUS_RUNNING = '_RUNNING_';
    
    function run($name){
        $job = $this->get(array('name' => $name),true);
        if($job){
            if($job->status === self::_STATUS_FREE_){
                $time = time();
                $sql =  "UPDATE {$this->table} ".
                        "SET ".
                        "status = '".self::_STATUS_RUNNING."', ".
                        "last_run = '{$time}' ".
                        "WHERE name = '{$name}'";
                        
                $this->query($sql);
            }else{
                $started_time = $job->last_run;
                $diff = time() - $started_time;
                
                
                //if running more than 10 minutes kill
                if($diff > 600){
                    $this->end($name);
                    exit;
                }
                
                return false;
            }
        }else{
            $data = array(
                'name' => $name,
                'last_run' => time(),
                'last_duration' => 0,
                'status' => self::_STATUS_RUNNING
            );
            
            $this->insert($data);
        }
        return true;
    }
    
    function end($name){
        $job = $this->get(array('name' => $name),true);
        if($job){
            $started_time = $job->last_run;
            $diff = time() - $started_time;
            
            $sql =  "UPDATE {$this->table} ".
                    "SET ".
                    "status = '".self::_STATUS_FREE_."', ".
                    "last_duration = '{$diff}' ".
                    "WHERE name = '{$name}'";
                    
            $this->query($sql);
        }
    }
}