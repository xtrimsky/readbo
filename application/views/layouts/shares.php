<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
        <meta charset="utf-8" />
        
        <?php foreach($og_metas as $og_meta){ ?>
            <meta property="<?php echo $og_meta['property']; ?>" content="<?php echo $og_meta['content']; ?>" />
        <?php } ?>
        
        <title><?php echo $item->title; ?></title>
        <meta name="title" content="<?php echo $item->title; ?>" />
        <meta name="description" content="<?php echo strip_quotes( strip_tags( $item->content ) ); ?>" />
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
                <h1 id="logo">readbo <span style="font-size: 10px;">beta</span></h1>
                
                <ul id="nav">
                    
                    <li class="noback" style="padding: 17px 0px">
                        <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                    </li>
                    <li  class="noback"><a name="fb_share" type="button" share_url="http://readbo.com/shares/<?php echo $item->id; ?>">Share on Facebook</a> 
                    <script src="http://static.ak.fbcdn.net/connect.php/js/FB.Share" type="text/javascript"></script></li>
                    <li><a href="/">Login</a></li>
                    <li><a href="/signup">Signup</a></li>
                    <li><a href="<?php echo $item->link; ?>">X</a></li>
                </ul>
                
                
            </div>
            
            <div id="main">
                <iframe id="website_display" src="<?php echo $item->link; ?>"></iframe>
            </div>
        </div>

        <?php echo $js; ?>
    </body>
</html>