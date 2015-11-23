<?php
require_once(APPPATH."models/include/common.php");

class Twitter extends CI_Model{
        function getConnection($access_token){
            require_once(APPPATH . 'models/libs/twitteroauth/twitteroauth.php');
            
            return new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $access_token->oauth_token, $access_token->oauth_token_secret);
        }
        
        function getFeed($feed_id, $last_sid, $access_token){
            $access_token = json_decode($access_token);
            
            $connection = $this->getConnection($access_token);
            $timeline = $connection->get('statuses/home_timeline');
            if(empty($timeline)){return array();}
            
            $ids = array();
            $items = array();
            foreach($timeline as $twit){
                if(!isset($twit->text)){continue;}
                
                $id = sha1($feed_id.$twit->id_str);
                
                if($id === $last_sid){
                    break;
                }

                $ids[] = $id;
                
                $twit->text = Common::createLinksInText($twit->text);
                
                if(isset($twit->retweeted_status)){
                    $twit = $twit->retweeted_status;
                }
                
                //incorrect timestamp ?
                $date = strtotime($twit->created_at);
                $time = time();
                if($date > $time){
                    $date = $time;
                }
                
                $items[$id] = array(
                        'content' => $twit->text,
                        'title' => $twit->text,
                        'date' => $date,
                        'link' => "https://twitter.com/#!/{$twit->user->screen_name}/status/$twit->id_str",
                        'author' => $twit->user->name.' ('.$twit->user->screen_name.')',
                        'picture' => $twit->user->profile_image_url
                );
            }

            $items['ids'] = $ids;

            return $items;
        }
        
        public function postStatus($access_token, $user_id, $message){
            $access_token = json_decode($access_token);
            
            $connection = $this->getConnection($access_token);
            $result = $connection->post('statuses/update', array('status' => $message));
            
            if(isset($result->error)){
                return false;
            }
            
            return true;
        }
    
	function search($query, $count = 100){
		$query = urlencode($query);
		
		$url = "http://search.twitter.com/search.json".
                "?q={$query}".
                "&rpp={$count}";

                $response = $this->curl($url);
                $response = json_decode( $response );

                if(!is_object($response)){
                    return null;
                }else if(!empty($response->error)){
                        return null;
                }
		
                $ids = array();
		$items = array();
		foreach($response->results as $twit){
                        $id = sha1(base64_encode($query).base64_encode($twit->from_user).base64_encode($twit->text));
                        
                        $ids[] = $id;
                        
                        $twit->text = Common::createLinksInText($twit->text);
                        
                        //incorrect timestamp ?
                        $date = strtotime($twit->created_at);
                        $time = time();
                        if($date > $time){
                            $date = $time;
                        }
                    
			$items[$id] = array(
				'content' => $twit->text,
				'title' => $twit->text,
				'date' => $date,
				'link' => "https://twitter.com/#!/{$twit->from_user}/status/$twit->id_str",
				'author' => $twit->from_user,
				'picture' => $twit->profile_image_url
			);
		}
                
                $items['ids'] = $ids;
                
		return $items;
	}
	
	function curl($url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            return $response;
        }
}