<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once('base.php');

class Twitter_route extends Base_Controller {

    function redirectAction() {
        require_once(APPPATH . 'models/libs/twitteroauth/twitteroauth.php');

        $connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET);

        $request_token = $connection->getRequestToken(TWITTER_OAUTH_CALLBACK);

        $session = array(
            'oauth_token' => $token = $request_token['oauth_token'],
            'oauth_token_secret' => $request_token['oauth_token_secret']
        );
        $this->session->set_userdata($session);

        switch ($connection->http_code) {
            case 200:
                $url = $connection->getAuthorizeURL($token);
                header('Location: ' . $url);
                break;
            default:
                echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
        
        exit;
    }

    function callbackAction() {
        require_once(APPPATH . 'models/libs/twitteroauth/twitteroauth.php');

        /* If the oauth_token is old redirect to the connect page. */
        $token = $this->session->userdata('oauth_token');
        $token_secret = $this->session->userdata('oauth_token_secret');

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, $token, $token_secret);

        /* Request access tokens from twitter */
        $access_token = json_encode( $connection->getAccessToken($_REQUEST['oauth_verifier']) );

        /* Remove no longer needed request tokens */
        $this->session->unset_userdata('oauth_token');
        $this->session->unset_userdata('oauth_token_secret');

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
            /* The user has been verified and the access tokens can be saved for future use */
            //connected
            $user = $this->auth->getUser();
            $this->auth->connectTwitter($user,$access_token);
        }
        redirect('/');
        exit;
    }
    
    function removeAjax(){
        $user = $this->auth->getUser();
        $this->load->model('feed');
        $this->load->model('subscription');
        
        $subscription = $this->subscription->removeTwitter($user->id);
        $this->feed->delete(array('id' => $subscription->feed_id));
        
        $this->user->update(array('twitter_uid' => null), array('id' => $user->id));
        require_once(APPPATH.'models/cacheManagers/user_cm.php');
        $cm = new user_cm($user->username);
        $cache = $cm->delete();
        
        $this->sendToAjax(array('success' => true));
    }

}