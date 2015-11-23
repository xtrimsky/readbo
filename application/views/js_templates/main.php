<div id="tp_feed_list_content_rss">
    <div class="fl_content_insidewrap">
        <div class="fl_text">[content]</div>
        <div class="fl_actions">
            <a href="/shares/[id]" alt="" target="_blank">View Post</a> 
            <a class="[likeClass] btn_like" rel="[id]" href="#" alt="">[likeButton]</a>
            <a class="[readClass] btn_read" rel="[id]" href="#" alt="">[readButton]</a>
            <?php if($hasFacebook){ ?>
                <a class="share_facebook" rel="[id]" href="#" alt="">Share on Facebook</a>
            <?php } ?>
            <?php if($hasTwitter){ ?>
                <a class="share_twitter" rel="[id]" href="#" alt="">Share on Twitter</a>
            <?php } ?>
        </div>
    </div>
</div>

<div id="tp_feed_list_content_facebook">
    <div class="fl_content_insidewrap">
        <div class="fl_text">[content]</div>
        <div class="fl_actions">
            <a href="[link]" alt="" target="_blank">View Status</a> 
            <a class="[readClass] btn_read" rel="[id]" href="#" alt="">[readButton]</a>
        </div>
    </div>
</div>

<div id="tp_feed_list_content_twitter_search">
    <div class="fl_content_insidewrap">
        <div class="fl_actions">
            <a href="[link]" alt="" target="_blank">View Status</a> 
            <a class="[readClass] btn_read" rel="[id]" href="#" alt="">[readButton]</a>
        </div>
    </div>
</div>

<div id="tp_feed_list_content_twitter">
    <div class="fl_content_insidewrap">
        <div class="fl_actions">
            <a href="[link]" alt="" target="_blank">View Status</a> 
            <a class="[readClass] btn_read" rel="[id]" href="#" alt="">[readButton]</a>
        </div>
    </div>
</div>

<div id="tp_navigate_folders">
    <h3 id="hfolder_[id]" class="navigate_header folder_header expand">
        <a href="#" class="nav_expand"></a><a href="#" class="folder_link nav_title">[name] <span class="item_count">(0)</span></a>
    </h3>
    <div id="folder_[id2]" class="nav_content"></div>
</div>

<div id="tp_navigate_feeds">
    <h3 id="feed_[id]" class="navigate_header feed_link">
        <a href="#" class="edit_feed"></a>
        <img:delay class="feed_icon" src="<?php echo MEDIA_SERVER; ?>/images/[icon]_14.png" alt=""/>
        <a href="#" class="nav_title"><span class="feed_title">[name]</span> <span class="item_count">(0)</span></a>
    </h3>
</div>