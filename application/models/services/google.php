<?php

class Google extends CI_Model {
    private $api_key = 'ABQIAAAALCxxeow0igFMzLHJNe1KTxRH8PGNi_h3nPvk9q1F8hvo8faI9RRUdHFqTTpoMsIhbDHAKgoJdHzPGw';

    function search($search) {
        $results = array();
        $ip = getenv("REMOTE_ADDR");

        $url = "http://ajax.googleapis.com/ajax/services/feed/find?v=1.0".
        "&userip={$ip}".
        "&rsz=8".
        "&key={$this->api_key}".
        "&q=%22{$search}%22";

        $response = $this->curl($url);
        $response = json_decode( $response );

        if(!is_object($response) || $response->responseStatus != 200){
            return null;
        }

        foreach($response->responseData->entries as $result){
            
            $new_entry = array(
                'name' => strip_tags($result->title),
                'url' => $result->url,
                'content' => strip_tags($result->contentSnippet)
            );

            $results[] = $new_entry;
        }

        return $results;
    }
    
    function curl($url) {
        $header = array();
        $header[] = 'Accept: application/json, text/javascript, */*; q=0.01';
        $header[] = 'Accept-Language: en-us,en;q=0.5';
        $header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
        $header[] = 'Connection: Keep-Alive';
        $header[] = 'Keep-Alive: 115';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

}