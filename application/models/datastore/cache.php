<?php
define('_CACHE_HOST_', '127.0.0.1');

if(!defined('MEMCACHE_COMPRESSED')){
    define('MEMCACHE_COMPRESSED', true);
}

class Cache{
    private static $memcache = null;
    
    static function isRunning(){
        $obj = self::mem();
        if(get_class($obj) === 'CacheUnavailable'){
            return false;
        }
        return true;
    }
    
    static function get($key, $namespace){
        $val = self::mem()->get($namespace);
        if(!$val){
            return false;
        }
        $key = $namespace.$val.'_'.$key;
        
        return self::mem()->get($key);
    }
    
    static function set($key, $namespace, $value, $expiration = 1000){
        $val = self::mem()->get($namespace);
        if(!$val){
            $val = rand(1,9999);
            self::mem()->set($namespace, $val, MEMCACHE_COMPRESSED, $expiration);
        }
        
        $key = $namespace.$val.'_'.$key;
        
        return self::mem()->set($key, $value, MEMCACHE_COMPRESSED, $expiration);
    }
    
    static function dirty($namespace){
        self::mem()->increment($namespace);
    }
    
    static function flush(){
        self::mem()->flush();
    }
    
    static function mem(){
        if(is_null(self::$memcache) && class_exists('Memcache')){
            self::$memcache = new Memcache();
            $con = @self::$memcache->connect(_CACHE_HOST_, 11211);
            
            if(!$con){
                self::$memcache = new CacheUnavailable();
            }
        }else if(!class_exists('Memcache')){
            self::$memcache = new CacheUnavailable();
        }
        
        return self::$memcache;
    }
}

class CacheUnavailable{
    function get(){
        return false;
    }
    
    function set(){
        return false;
    }
    
    function increment(){
        return false;
    }
}