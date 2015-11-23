<?php

class Email{
    private $subject = '';
    private $from = '';
    private $body = '';
    private $html = '';
    
    function setSubject($subject){
        $this->subject = $subject;
    }
    
    function setFrom($email){
        //if sending from a readbo.com mail, adding Name
        if(strpos(strtolower($email),'readbo.com') !== false){
            $email = "Readbo.com <{$email}>";
        }
        
        $this->from = $email;
    }
    
    function setBody($body){
        $this->body = $body;
    }
    
    function setHTMLOn(){
        $this->html .= "MIME-Version: 1.0\r\n";
        $this->html .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    }
    
    function setTemplate($name, $data = array()){
        if($this->subject === ''){
            throw new Exception('You must define the subject before using a template');
        }
        
        require_once(APPPATH.'models/objects/view.php');
        $this->setHTMLOn();
        
        $view = new View();
        
        $layout_data = array(
            'content' => $view->render(APPPATH.'views/emails/'.$name.'.php', $data),
            'subject' => $this->subject
        );
        $this->body = $view->render(APPPATH.'views/layouts/email.php',$layout_data);
    }
    
    function send($to){
        $headers = "Reply-To: \r\n"; 
        $headers .= "From: {$this->from}\r\n";
        $headers .= "Return-Path: {$this->from}\r\n";
        $headers .= "Organization: Readbo.com\r\n"; 
        $headers .= $this->html;
        $headers .= "MIME-Version: 1.0\r\n";
	$headers .= "X-Mailer: php";
        if(@mail($to,$this->subject,$this->body,$headers)){
            return true;
        }
        
        return false;
    }
}