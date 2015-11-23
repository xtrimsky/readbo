<?php

require_once('datastore/init.php');
class Filter extends Datastore_Table {
    
    protected $table = 'filters';
    //private $filters = null;
    
    /*
     * When an item is added to the database, we compare it to all the filters, and filtering it to some users
     */
    function markItemsAsFiltered($feed_id, $items, $user_id = null){
        if(empty($items)){return;}
        
        if(is_null($user_id)){
            $filters = $this->getAllFilters($feed_id);
        }else{
            $filters = $this->getAllUserFiltersByFeed($feed_id, $user_id);
        }
        
        foreach($items as $item){
            foreach($filters as $f){
                $object = strtolower($item->{$f->object});
                $properties = explode(';',$f->property);

                $passed = true;
                foreach($properties as $property){
                    $temp = explode(':',$property);
                    $event = $temp[0];
                    $value = strtolower($temp[1]);


                    switch($event){
                        case 'contains':
                            if(strpos($object, $value) !== false){
                                $passed = false;
                            }
                            break;
                        case 'equals':
                            if($object == $value){
                                $passed = false;
                            }
                            break;
                        case 'starts_with': //timestamp
                            if(substr($object,0,strlen($value)) == $value){
                                $passed = false;
                            }
                            break;
                        case 'ends_with': //timestamp
                            if(substr($object, strlen($value) * -1) == $value){
                                $passed = false;
                            }
                            break;
                    }
                }

                if(!$passed && $f->action == 'hide'){
                    $this->load->model('items_filtered');
                    
                    //if item has already been filtered
                    if($this->items_filtered->count(array(
                        'item_sid' => $item->item_sid,
                        'user_id' => $f->user_id,
                        'feed_id' => $item->feed_id
                    )) > 0){
                        continue;
                    }
                    
                    $this->items_filtered->insert(array(
                        'item_sid' => $item->item_sid,
                        'user_id' => $f->user_id,
                        'feed_id' => $item->feed_id,
                        'filter_id' => $f->id
                    ));
                }
            }
        }
    }
    
    function getAllFilters($feed_id){
        $sql =  "SELECT * ".
                "FROM filters ".
                "WHERE feed_id = {$feed_id} OR feed_id IS NULL";
                
        return $this->query($sql);
    }
    
    function getAllUserFiltersByFeed($user_id, $feed_id){
        $user_id = $this->escape($user_id);
        
        $result = $this->query(
                "SELECT * " .
                "FROM {$this->table} " .
                "WHERE user_id = {$user_id} ".
                "feed_id = {$feed_id} OR feed_id IS NULL"
        );

        return $result;
    }
    
    function addFilter($user_id, $field, $value_compare, $value, $affects){
        if($affects == 0){
            $affects = null;
        }
        
        $data = array(
            'user_id' => $user_id,
            'feed_id' => $affects,
            'object' => $field,
            'property' => $value_compare.':'.$value,
            
        );
        
        return $this->insert($data);
    }
    
    function getAllUserFilters($user_id){
        $user_id = $this->escape($user_id);
        
        $result = $this->query(
                "SELECT * " .
                "FROM {$this->table} " .
                "WHERE user_id = {$user_id}"
        );

        return $result;
    }
    
    //#### OLD CODE ###

    /*
    function getFilters($user_id, $feed_id){
        $user_id = $this->escape($user_id);
        $feed_id = $this->escape($feed_id);

        $result = $this->query(
                "SELECT object, property, action " .
                "FROM {$this->table} " .
                "WHERE filters.user_id = {$user_id} AND (filters.feed_id = {$feed_id} OR filters.feed_id IS NULL)"
        );

        return $result;
    }
    
    function mustFilter($user_id, $item){
        if(is_null($this->filters)){
            $this->filters = $this->getFilters($user_id, $item->feed_id);
        }
        
        foreach($this->filters as $f){
            $object = strtolower($item->{$f->object});
            $properties = explode(';',$f->property);
            
            $passed = true;
            foreach($properties as $property){
                $temp = explode(':',$property);
                $event = $temp[0];
                $value = strtolower($temp[1]);
                
                
                switch($event){
                    case 'contains':
                        if(strpos($object, $value) !== false){
                            $passed = false;
                        }
                        break;
                    case 'equals':
                        if($object == $value){
                            $passed = false;
                        }
                        break;
                    case 'starts_with': //timestamp
                        if(substr($object,0,strlen($value)) == $value){
                            $passed = false;
                        }
                        break;
                    case 'ends_with': //timestamp
                        if(substr($object, strlen($value) * -1) == $value){
                            $passed = false;
                        }
                        break;
                }
            }
            
            if(!$passed && $f->action == 'hide'){
                return true;
            }
        }
        
        return false;
    }
    */
}