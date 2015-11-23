<?php

require_once('datastore/init.php');
class Admin_Data extends Datastore_Table {
    function getDashboardData(){
        //caching data once a day, too much data
        require_once(APPPATH.'models/cacheManagers/admin_cm.php');
        $cm = new admin_cm();
        $cache = $cm->fetch();
        
        if($cache) return $cache;
        
        $data = array(
            'active_users' => $this->getActiveUsers(),
            'active_last_week' => $this->getActiveLastWeekUsers(),
            'active_last_day' => $this->getActiveLastDayUsers(),
            'loggedin_once' => $this->getUsersLoggedInOnce(),
            'users_with_facebook' => $this->usersWithFacebook(),
            'users_with_twitter' => $this->usersWithTwitter(),
            'waiting_list' => $this->getWaitingList(),
            'memcache_running' => Cache::isRunning()
        );
        
        $cm->add($data);
        
        return $data;
    }
    
    function getWaitingList(){
        $sql =  "SELECT count(id) AS count ".
                "FROM invites";
        
        return current($this->query($sql))->count;
    }
    
    function usersWithFacebook(){
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE facebook_uid IS NOT NULL";
        
        return current($this->query($sql))->count;
    }
    
    function usersWithTwitter(){
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE twitter_uid IS NOT NULL";
        
        return current($this->query($sql))->count;
    }
    
    function getUsersLoggedInOnce(){
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE logins = 1";
        
        return current($this->query($sql))->count;
    }
    
    function getActiveUsers(){
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE active = 1";
        
        return current($this->query($sql))->count;
    }
    
    function getActiveLastWeekUsers(){
        $last_week = time() - 60*60*24*7;
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE active = 1 ".
                "AND last_login > {$last_week}";
        
        return current($this->query($sql))->count;
    }
    
    function getActiveLastDayUsers(){
        $last_day = time() - 60*60*24;
        $sql =  "SELECT count(id) AS count ".
                "FROM users ".
                "WHERE active = 1 ".
                "AND last_login > {$last_day}";
        
        return current($this->query($sql))->count;
    }
}