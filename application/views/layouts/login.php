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
    <div id ="background"></div>
    <div id="app">
		<a style="text-decoration: none; background-color: white; padding: 2px; border: 1px solid red; position: absolute; top: 10px; left: 10px; font-size: 20px; color: red;" href="/shut_down">Readbo.com is shutting down September 7th, read more...</a>
        <div id="top">
            readbo
        </div>
        <div id="leftsidewrapper">
            <div id="cool_info">
                <p>Readbo is a news reading application, that also integrates with Facebook and Twitter.</p>
                <ul id="find_out_more">
                    <li><a href="/what_is_readbo">What is readbo ?</a></li>
                    <li><a href="/trending">Trending</a></li>
                </ul>
            </div>
            
            <div id="leftside">
                <div id="maintext">
                <p>
                Readbo is now out of private beta. Anyone can register!<br/>
                Please note that there are still some bugs, we are working very hard on fixing as many as we can.
                </p>
                <br>
                <p>
                Thank you!
                </p>
                </div>
                
                <!--
                <div id="invitebox">
                    <span id="invitefail" style="display: none; color: red;">You have not been added to the list, our server failed, sorry!<br><br></span>
                    <span id="inviteerror" style="display: none; color: red;">Email is not valid, please try again...<br><br></span>
                    <input id="inviteemail" type="email" placeholder="enter your email address here..."><input id="btnInvite" type="button" value="Invite me">
                </div> -->
            </div>
            <ul id="bottom_text">
                <li>&nbsp;<a href="/terms">Terms of Use</a>&nbsp;&nbsp;&nbsp;&nbsp;|</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/privacy">Privacy</a>&nbsp;&nbsp;&nbsp;&nbsp;|</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/contact">Contact Us</a>&nbsp;&nbsp;&nbsp;&nbsp;|</li>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/unsubscribe">Unsubscribe</a></li>
            </ul>
            <span style="float: right;">&copy; Readbo <?php echo date('Y'); ?></span>
        </div>
        <div id="rightside">
            <div id="loginbox">
                <?php if($incorrect){ ?>
                <span class="login-error">
                    Login failed, please try again!<br><br>
                </span>
                <?php } ?>
                <form action="users/login" method="post">
                    <table>
                        <tr>
                            <td><label for="txtLogin">Username</label></td><td><input type="text" name="txtLogin" id="txtLogin" ></td>
                        </tr>
                        <tr>
                            <td><label for="txtPassword">Password</label></td><td><input type="password" name="txtPassword" id="txtPassword" ></td>
                        </tr>
                    </table>
                    Remember me<input type="checkbox" name="chkRemember"/><br>
                    <button id="btnLogin" type="submit">Login</button>
                </form>
                <?php /*
                <br>
                Or use facebook: <a href="#" class="register-link-facebook"><span class="icon"></span>Facebook</a> */ ?>
                <br>
                <a href="/forgot_password">Forgot your password ?</a>
            </div>
            <br>
            <p>What are you waiting for ? Try it now!</p><br>
            <a id="btnRegister" href="#"></a>
            <br/>
            <p>Accounts are FREE, and can be deleted in the Settings in a few seconds!</p>
        </div>
    </div>

    <div class="hidden">
        <fb:login-button id="fb_button" scope="email,offline_access,publish_stream,read_stream"></fb:login-button>
    </div>
    <div id="fb-root"></div>
    <script type="text/javascript">
      window.fbAsyncInit = function() {
        FB.init({appId: '<?php echo FACEBOOK_APP_ID; ?>', status: true, cookie: true,
                 xfbml: true, oauth: true});
      };
     (function() {
      var e = document.createElement('script'); e.async = true;
      e.src = document.location.protocol 
        + '//connect.facebook.net/en_US/all.js';
      document.getElementById('fb-root').appendChild(e);
    }());
    </script>
    <?php echo $js; ?>
</body>
</html>