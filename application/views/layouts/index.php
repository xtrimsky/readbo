<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
        <meta charset="utf-8" />
        <meta name="keywords" content="readbo, news, social, facebook, twitter, aggregator, reader" />
        <meta name="robots" content="all" />
        <meta name="title" content="Readbo.com" />
        <meta name="description" content="Social news website! Navigates RSS feeds, Twitter and Facebook!" />
        <link rel="image_src" href="http://readbo.com/public/images/facebook_newsfeed_logo.png" />
        <meta property="og:image" content="http://readbo.com/public/images/facebook_newsfeed_logo.png" />
        <meta property="og:site_name" content="Readbo"/>
        <meta property="og:title" content="Readbo.com" />
        <meta property="og:description" content="Social news website! Navigates RSS feeds, Twitter and Facebook!" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://readbo.com"/>
	<title>Readbo.com</title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <?php echo $css; ?>
        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-27301228-1']);
          _gaq.push(['_setDomainName', 'readbo.com']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
    </head>
    <body>
        <div id="app">
            <div id="header">
                <h1 id="logo" onclick="window.location = '/';">readbo <span style="font-size: 10px;">beta</span></h1>
                
                <?php /*
                <input id="searchbar" type="search" placeholder="Search in readbo..."> */ ?>

                <ul id="nav">
                    <li><a id="trending_nav" href="#">Trending</a></li>
                    <li><a id="filters" href="#">Filters</a></li>
                    <li><a id="settings" href="#">Settings</a></li>
                    <!-- <li><a id="profile" href="#">Profile</a></li> -->
                    <li><a id="logout" href="#">Logout <?php echo $username; ?></a></li>
                </ul>
                
                
            </div>
            
            <div id="main">
                <div id="leftcolumnwrapper">
                    
                </div>
                
                <?php if($info_panel != ''){ ?>
                <div id="info_panel"><?php echo $info_panel; ?></div>
                <?php } ?>
                <div id="facebook_warning" style="display: none;">
                    Readbo cannot access Facebook properly anymore, in order to keep Readbo functionnal, please connect to Facebook again: <button class="fb_connect">Connect</button>
                </div>
                
                <div id="rightcolumnwrapper">
                    
                </div>
            </div>
        </div>

        <!--
        <div class="hidden">
            <div id="fb_button" class="fb-login-button" scope="email,offline_access,publish_stream,read_stream">
                LogIn
            </div>
        </div> -->
        <div id="fb-root"></div>
        <?php echo $js; ?>
        <script type="text/javascript">
            window.fbAsyncInit = function() {
                FB.init({
                    appId: '<?php echo FACEBOOK_APP_ID; ?>',
                    status: true,
                    channelURL: '//www.readbo.com/channel.html',
                    cookie: true,
                    xfbml: true,
                    oauth: true
                });
                
                setTimeout(function(){
                    if(user.facebook_uid){
                        FB.getLoginStatus(function(response) {
                            if(response.status !== 'connected'){
                                $('#facebook_warning').show();
                            }
                        }, true);
                    }
                }, 5000);
                
                /*
                FB.Event.subscribe('auth.login', function() {
                    Facebook.connect();
                }); */
                //Facebook.connected();
              };
            
            (function(d){
             var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
             js = d.createElement('script'); js.id = id; js.async = true;
             js.src = "//connect.facebook.net/en_US/all.js";
             d.getElementsByTagName('head')[0].appendChild(js);
           }(document));
        </script>
        
        <?php echo $toolbar; ?>
    </body>
</html>