<xml version="1.0" encoding="UTF-8">
<opml version="1.0">
    <head>
        <title><?php echo $username; ?> subscriptions in Readbo.com</title>
    </head>
    <body>
        <?php foreach($feeds as $f){ ?>
        <outline text="<?php echo $f->name; ?>" title="<?php echo $f->name; ?>" type="rss"
                xmlUrl="<?php echo $f->url ?>" htmlUrl="<?php echo $f->base_url ?>"/>
        <?php } ?>
    </body>
</opml>
