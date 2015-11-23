<div id="tp_panel_nav_feed">
    <div id="nav_panel">
        <div id="button_bar" class="button_bar">
            <button id="btnAddFeed">Add news</button>
            <?php if($hasFacebook || $hasTwitter){ ?>
            <button class="btnPostStatus" style="margin-left: 5px;">Post status</button>
            <?php } ?>
        </div>
        <div id="nav_feeds">

        </div>
        <div class="updating">updating... <img src="<?php echo MEDIA_SERVER; ?>/images/ajax-loader-horizontal.gif" alt=""/></div>
    </div>
    <div id="tip_box"><?php echo $tip_box; ?></div>
    <button id="btnReportBug">Report a bug</button>
    <div class="clear" id="footer">

    <ul id="bottom_text">

        <li><a href="/terms">Terms of Use</a>&nbsp;|</li>
        <li>&nbsp;<a href="/privacy">Privacy</a>&nbsp;|</li>
        <li>&nbsp;<a href="/contact">Contact Us</a>&nbsp;|</li>
        <li>&nbsp;<a href="/unsubscribe">Unsubscribe</a></li>
    </ul>
        <br/><br/>
        © Readbo <?php echo date('Y'); ?></div>
</div>

<div id="tp_panel_list">
    <div id="feed_title">
        <button id="btnMarkListAsRead">Mark all as read</button>
        <button id="btnRefreshList">Refresh</button>
    </div>
    <div id="feed_list" class="reader"></div>
</div>

<div id="tp_panel_profile">
    
    <div id="userprofile">
        <button id="closeprofile">Close profile</button>
        <span class="data username" name="username"></span>
        <img class="data profile_picture" name="profile_pic" src="" alt="profile_picture"/>
        <br style="clear: both;"/>
    </div>
</div>