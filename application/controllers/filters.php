<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Filters extends Base_Controller {
    
    function __construct(){
        parent::__construct();
        
        $this->load->model('filter');
        $this->load->model('items_filtered');
    }
    
    function addFilterAjax(){
        $response = array();
        
        $user = $this->auth->getUser();
        
        $field = $this->input->post('field');
        $value_compare = $this->input->post('value_compare');
        $value = $this->input->post('value');
        $affects = $this->input->post('affects');
        
        if($this->filter->addFilter($user->id, $field, $value_compare, $value, $affects)){
            $response['success'] = true;
        }else{
            $response['success'] = false;
        }
        
        $this->sendToAjax($response);
    }
    
    function deleteFilterAjax(){
        $response = array();
        
        $filter_id = $this->input->post('filter_id');
        $user = $this->auth->getUser();
        
        $this->filter->delete(array(
            'user_id' => $user->id,
            'id' => $filter_id
        ));
        $this->items_filtered->delete(array(
            'user_id' => $user->id,
            'filter_id' => $filter_id
        ));
        
        $this->sendToAjax($response);
    }
    
    function getFiltersAjax(){
        $response = array();

        $user = $this->auth->getUser();
        
        $filters = $this->filter->getAllUserFilters($user->id);

        $response['filters'] = $filters;

        $this->sendToAjax($response);
    }
    
}