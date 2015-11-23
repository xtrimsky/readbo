<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
<head>
        <base href="<?php echo base_url(); ?>" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
	<meta charset="utf-8" />
	<meta name="keywords" content="readbo, news, social, facebook, twitter, aggregator, reader" />
        <meta name="robots" content="all" />
        <meta name="title" content="Readbo.com - <?php echo $title; ?>" />
        <meta name="description" content="Social news website! Navigates RSS feeds, Twitter and Facebook!" />
        <link rel="image_src" href="http://readbo.com/public/images/facebook_newsfeed_logo.png" />
        <meta property="og:image" content="http://readbo.com/public/images/facebook_newsfeed_logo.png" />
        <meta property="og:site_name" content="Readbo"/>
        <meta property="og:title" content="Readbo.com - <?php echo $title; ?>" />
        <meta property="og:description" content="Social news website! Navigates RSS feeds, Twitter and Facebook!" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="http://readbo.com/<?php echo $url; ?>"/>
	<title>Readbo.com - <?php echo $title; ?></title>
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
    <div id="pages_header">
        <div id="pages_header_content">
            <div id="pages_logo" onclick="window.location = '/';" title="Go back">
                readbo<span class="smalltext">.com</span>
            </div>
            <ul id="pages_menu">
                <li><a href="/what_is_readbo" class="<?php if($url == 'what_is_readbo'){echo 'current';} ?>">What is Readbo ?</a></li>
                <li><a href="/terms" class="<?php if($url == 'terms'){echo 'current';} ?>">Terms of Use</a></li>
                <li><a href="/privacy" class="<?php if($url == 'privacy'){echo 'current';} ?>">Privacy</a></li>
                <li><a href="/contact" class="<?php if($url == 'contact'){echo 'current';} ?>">Contact us</a></li>
                <li><a href="/unsubscribe" class="<?php if($url == 'unsubscribe'){echo 'current';} ?>">Unsubscribe</a></li>
            </ul>
        </div>
    </div>
    <div id="pages_box">
        <h2><?php echo $title; ?></h2>
        
        <div id="pages_content">
            <?php echo $content; ?>
        </div>
    </div>
    <div id="pages_footer">
        &copy; Readbo <?php echo date('Y'); ?>
    </div>
    <?php echo $js; ?>
</body>
</html>