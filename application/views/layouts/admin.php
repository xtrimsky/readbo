<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <base href="<?php echo base_url(); ?>" />
        <meta http-equiv="X-UA-Compatible" content="chrome=1">
        <meta charset="utf-8" />
        <title>Readbo</title>
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
        <?php echo $css; ?>
    </head>
    <body>
        <div id="header">
            <h1>Readbo Admin</h1>
            
            <ul id="nav">
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/admin/invites">Invitation Codes</a></li>
                <li><a href="/admin/releases">Releases</a></li>
                <li><a href="/admin/cache">Cache</a></li>
                <li><a href="/">Main Website</a></li>
            </ul>
        </div>
        
        <div id="app">
            <?php echo $content; ?>
        </div>
        
        
        <?php echo $js; ?>
    </body>
</html>