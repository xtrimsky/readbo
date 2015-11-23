<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once('base.php');

class Reporting extends Base_Controller{
    
    function sendAjax(){
        $response = array();
        
        $comment = $this->input->post('comment');
        $email_address = $this->input->post('email');
        $name = $this->input->post('name');
        $type = $this->input->post('report_type');
        
        $message = $comment."\n\n\n";
        foreach($_SERVER as $k => $v){
            $message .= $k.': '.$v."\n";
        }
        
        require_once(APPPATH.'models/objects/email.php');
        $email = new Email();
        $email->setFrom($name." <{$email_address}>");
        $email->setSubject('readbo.com - Reporting '.$type.' - '.$name);
        $email->setBody($message);
        $response['success'] = $email->send('support@readbo.com');
        
        $this->sendToAjax($response);
    }
    
}