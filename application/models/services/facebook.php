<?php

require_once('application/models/include/common.php');

class Facebook extends CI_Model {
    function getFacebookUser($access_token){
        $user = $this->getURL( 'https://graph.facebook.com/me?access_token=' . $access_token );
        $user->access_token = $access_token;
        
        return $user;
    }

    function getNewsFeed($feed_id, $last_sid, $access_token){
        $ids = array();
        $newsFeed = array();

        $url = 'https://graph.facebook.com/me/home?access_token='.$access_token;

        $count = 0;
        $break = false;
        do {
            $count++;
            
            $results = $this->getURL($url);

            if(!is_object($results)){
                return null;
            }

            foreach($results->data as $row){
                $id = $row->id.$feed_id;

                if($last_sid == $id){ $break = true; break; } //stop if we already have this sid

                $ids[] = $id;
                $date = strtotime(substr($row->created_time, 0, 10).' '.substr($row->created_time, 11));
                
                //incorrect time
                $time = time();
                if($date > $time){
                    $date = $time;
                }

                if($row->type == 'status'){
                    if(empty($row->message) || !empty($row->to)){
                        continue;
                    }

                    $id_array = explode('_',$row->id);
                    $link = 'https://www.facebook.com/permalink.php?story_fbid='.$id_array['1'].'&id='.$id_array['0'];

                    $newsFeed[$id] = array(
                        'content' => str_replace("\n",'<br>',$row->message),
                        'title' => $row->message,
                        'date' => $date,
                        'link' => $link,
                        'author' => $row->from->name,
                        'picture' => 'https://graph.facebook.com/'.$row->from->id.'/picture'
                    );
                }elseif($row->type == 'photo' || $row->type == 'video'){
                    $album_name = '';
                    if(isset($row->name)){
                        $album_name = '('.$row->name.')';
                    }

                    if(isset($row->caption) && $row->type == 'photo'){
                        $title = 'added '.$row->caption.' '.$album_name;
                    }else{
                        $title = 'added a new '.$row->type.' '.$album_name;
                    }

                    $content = '';
                    if(isset($row->picture)){
                        $content = '<a href="'.$row->link.'"><img src="'.$row->picture.'" alt="facebook photo"/></a>';
                    }
                    if(isset($row->description)){
                        $content = $row->description.'<br><br>'.$content;
                    }
                    
                    if(isset($row->link)){
                        $link = $row->link;
                    }else if(isset($row->source)){
                        $link = $row->source;
                    }else{
                        $key = array_search($id, $ids);
                        unset($ids[$id]);
                        continue;
                    }

                    $newsFeed[$id] = array(
                        'content' => $content,
                        'title' => Common::short($title, 10000),
                        'date' => $date,
                        'link' => $link,
                        'author' => $row->from->name,
                        'picture' => 'https://graph.facebook.com/'.$row->from->id.'/picture'
                    );
                }
                
            }

            if(isset($results->paging)){
                $url = $results->paging->next;
            }

        } while(isset($results->paging) && $count < 10 && !$break);
        

        $newsFeed['ids'] = $ids;

        return $newsFeed;
    }
    
    public function postStatus($access_token, $user_id, $message){
        $url = "https://graph.facebook.com/{$user_id}/feed";
        
        $attachment =  array(   'access_token'  => $access_token,                        
                            //'name'          => 'Title',
                            //'link'          => "www.google.com",
                            //'description'   => $message,
                            'message' => $message
                        );
        
        return $this->send($url, $attachment);
    }
    
    public function shareLink($access_token, $user_id, $item){
        $url = "https://graph.facebook.com/{$user_id}/feed";
        
        $link = 'http://readbo.com/shares/'.$item->id;
        
        $attachment =  array(
                            'access_token'  => $access_token,
                            'link'          => $link,
                            'caption'       => 'This was shared using Readbo.com',
                            'message'       => $item->title
                        );
        
        return $this->send($url, $attachment);
    }
    
    public function send($url, $attachment){
        $ch = curl_init();

        ob_start ();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
        curl_exec($ch);
        $response = ob_get_contents();
        ob_end_clean ();

        curl_close ($ch);
        
        return json_decode($response);
    }

    public function getURL($url){
        $response = @json_decode(file_get_contents($url));
        
        return $response;
    }
}