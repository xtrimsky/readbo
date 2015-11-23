<?php

class Datastore_Table extends CI_Model {

    protected $table = '';
    protected $originalTable = '';
    
    function insert($data) {
        $this->db->insert($this->table, $data);

        return $this->db->insert_id();
    }

    function delete($data) {
        return $this->db->delete($this->table, $data);
    }

    function update($data, $where) {
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }

        return $this->db->update($this->table, $data);
    }

    function count($where) {
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->from($this->table);

        return $this->db->count_all_results();
    }

    function get($where, $singlerow = false) {
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->from($this->table);
        
        $q = $this->db->get();
        
        if(!$singlerow){
            $result = $q->result();
            return $result;
        }
        
        $result = current($q->result());
        return $result;
    }
    
    function getIn($property, $ids, $singlerow = false) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where_in($property, $ids);

        $q = $this->db->get();

        if(!$singlerow){
            return $q->result();
        }
        
        return current($q->result());
    }
    
    function escape($string){
        return $this->db->escape($string);
    }
    
    function query($query, $cache_id = false){
        $start = time();
        $result = $this->db->query($query);
        $diff = time() - $start;
        if($diff > 2){
            $file = APPPATH."logs/queries.php";
            shell_exec("echo \"{$query} took {$diff} seconds\" >> {$file}");
        }
        
        if(substr($query,0,6) == 'SELECT'){
            return $result->result();
        }
        
        return $result;
    }
    
    function startTable($table){
        $this->originalTable = $this->table;
        $this->table = $table;
    }
    
    function endTable(){
        $this->table = $this->originalTable;
    }
    
    function escapeArray($array){
        if(!is_array($array)){return $this->escape($array);}
        
        $result = array();
        foreach($array as $i){
            $result[] = $this->escape($i);
        }
        
        return $result;
    }
}