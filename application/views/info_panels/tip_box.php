<?php

$tips = array(
    array(
        'title' => 'Keyboard Shortcuts',
        'html' => 'Readbo is implementing keyboard shortcuts, try pressing <b>SPACE</b> to navigate faster to the next item. Or press <b>M</b> to mark an item as unread.<br/><br/>More shortcuts to come...'
    )
);

//if user doesn't have facebook
if(!$user->facebook_uid){
    $tips[] = array(
        'title' => 'Facebook',
        'html' => 'You can connect your Facebook account with Readbo, never miss a status anymore!<br/><br/><button class="fb_connect">Connect</button>'
    );
}

//if user doesn't have twitter
if(!$user->twitter_uid){
    $tips[] = array(
        'title' => 'Twitter',
        'html' => 'You can connect your Twitter account with Readbo, never miss tweet anymore!<br/><br/><button class="twitter_connect">Connect</button>'
    );
}

//if user has facebook
if($user->facebook_uid){
    $tips[] = array(
        'title' => 'Like our Facebook page!',
        'html' => '<div class="fb-like-box" data-href="http://www.facebook.com/readbo" data-width="225" data-show-faces="true" data-border-color="white" data-stream="false" data-header="false"></div>'
    );
}
if($user->twitter_uid){
    $tips[] = array(
        'title' => 'Follow Readbo on Twitter!',
        'html' => 'Do you want to know when Twitter is updated, or just follow our news?<br/>Follow our twitter account!<br/><br/>'.
                    '<a href="https://twitter.com/readbo" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @readbo</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>'
    );
}

$key = array_rand($tips);
$tip = $tips[$key];

?>

<h1><?php echo $tip['title']; ?></h1>
<p><?php echo $tip['html']; ?></p>