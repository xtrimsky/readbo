<div id="tp_dialog_add_feed">
    <div class="tabbed_list">
        <ul>
            <li class="sel-tab">Search feeds</li>
            <li>Add feed by URL</li>
            <li>Twitter search</li>
        </ul>
    </div>
    <div class="tabs-list">
        <div>
            Search feeds:<br>
            <input type="search" class="txtFeedSearch" placeholder="Search..." />
            <ul class="add-feed-search-result">
            </ul>
            <img class="loading-search" src="<?php echo MEDIA_SERVER; ?>/images/ajax-loader.gif" alt="loading"/>
        </div>
        <div>
            URL:<br>
            <input type="search" class="txtFeedAddURL" placeholder="http://example.com/feed.xml" ><br>
            <input type="button" class="btnAddFeedByURL" value="Add">
            <br style="clear: both;"/>
        </div>
        <div>
            <span style="font-size: 12px;">You can add here a specific twitter search, this is not your twitter account feed. To add your Twitter feed please go to your Settings, and connect Twitter to your account.</span><br />
            <br />
            <input id="txtFeedAddTwitter" type="search" placeholder="#readbo"><button id="addTwitterSearch">Add</button>
        </div>
    </div>
    
</div>

<div id="tp_dialog_rename">
    Rename to: <input class="input_rename" type="text" value=""/>
    <br><br>
    <div class="action_buttons">
        <button class="rename-subscription-yes">Save</button><button class="rename-subscription-cancel">Cancel</button>
    </div>
</div>

<div id="tp_dialog_settings">
    <div class="tabbed_list">
        <ul>
            <li class="sel-tab">General Settings</li>
            <?php /* <li>Reader</li> */ ?>
            <li>Linked Accounts</li>
            <li>Import / Export</li>
            <li>Delete Account</li>
        </ul>
    </div>
    <div class="tabs-list">
        <div>
            <table class="settings_list">
                <tr>
                    <td style="width: 100px;">Username:</td><td><span class="username-data"></span></td>
                </tr>
                <tr>
                    <td>Email:</td><td><input class="email-data" type="email" value=""></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td colspan="2">Show Read and Unread Items in feeds <input class="show_read-data property" type="checkbox"></td>
                </tr>
            </table>
        </div>
        <?php /*
        <div>
            Only show unread news <input class="hide_read-data" type="checkbox">
        </div> */ ?>
        <div>
            <p style="font-weight: bold;">You can connect your favorite social networks to Readbo:</p>
            
            <table style="margin-top: 30px;">
                <tr>
                    <td style="width: 100px;">Facebook: </td>
                    <td><button class="fb_connect">Connect</button><button class="fb_disconnect">Disconnect</button></td>
                </tr>
                <tr>
                    <td>Twitter: </td>
                    <td><button class="twitter_connect">Connect</button><button class="twitter_disconnect">Disconnect</button></td>
                </tr>
            </table>
        </div>
        <div>
            <p>
            If you are switching from another RSS reader, you can import your subscriptions into Readbo.<br>
            You need to find how to export them in a standard OPML file from your previous reader.</p>
            <br>
            <span id="finalUploadMessage" style="display: none; color: green;"></span>
            <form name="form" id="uploadForm" action="/import_export/import" method="post" enctype="multipart/form-data">
                Import: <input id="importfield" type="file" name="import" /><button id="btnUploadImport">Upload</button>
                <!-- <iframe id="upload_target" name="upload_target" class="uploadFrame" src=""></iframe> -->
            </form>
            <br>
            <p style="line-height: 1.3">Export: You can download an OPML format file here: <a href="/export" target="_blank" class="export_link">Download OPML File</a></p>
        </div>
        <div>
            <p>Very dangerous button that will delete all your account. We advise you not to use it.<br><br>
            If you do use it, please email us at support@readbo.com to tell us why and what we could have done better.<br><br>
            </p>
            <button class="btnDeleteAccount">Delete account</button>
        </div>
    </div>
</div>

<div id="tp_dialog_reporting">
    <div style="width: 400px;">
        <p>If you have a bug to report, or some important feature you think is needed for the application, you should explain it here. Every report is read!</p>
        <br>
        <label for="report_select">Action:</label> <select name="report_type" id="report_select">
            <option value="bug">Report a bug</option>
            <option value="feature">I want a feature!</option>
            <option value="comment">Write a comment</option>
            <option value="other">Other</option>
        </select><br><br>
        <form action="" method="post">
            <table>
                <tr>
                    <td><label for="report_email">Email</label></td>
                    <td><input id="report_email" name="email" type="email"/></td>
                </tr>
                <tr>
                    <td><label for="report_name">Name</label></td>
                    <td><input id="report_name" name="name" type="text"/></td>
                </tr>
                <tr>
                    <td><label for="report_comment">Comment</label></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><textarea id="report_comment" name="comment"></textarea></td>
                </tr>
            </table>
            <input id="sendReport" type="button" value="Send"/>
        </form>
    </div>
</div>

<div id="tp_dialog_filters">
    <div class="tabbed_list">
        <ul id="tabs_filters">
            <li class="sel-tab">Filters</li>
            <li>Add filter</li>
        </ul>
    </div>
    <div class="tabs-list">
        <div>
            <p id="nofiltersdefined" style="display: none;">You have no filters defined.</p>
            <table id="filter-list">
                <tr>
                    <th>
                        Field
                    </th>
                    <th>
                        Property
                    </th>
                    <th>
                        Affects
                    </th>
                    <th>
                        
                    </th>
                </tr>
            </table>
        </div>
        <div>
            <span>Add a filter to your news, hide topics you don't want to see.</span><br><br>
            <table class="settings_list">
                <tr>
                    <td style="width: 100px;">Field:</td>
                    <td>
                        <select id="filter_select_field">
                            <option value="title">Title</option>
                            <option value="content">Content</option>
                            <option value="author">User/Author</option>
                            <option value="link">Link</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Value:</td>
                    <td>
                        <select id="filter_select_value_compare">
                            <option value="contains">Contains</option>
                            <option value="equals">Equals</option>
                            <option value="starts_with">Starts with</option>
                            <option value="ends_with">Ends With</option>
                        </select>
                        <input id="filter_input_property" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>Affects:</td>
                    <td>
                        <select id="filter_select_affects"></select>
                    </td>
                </tr>
            </table>
            <button id="btnAddFilter">Add filter</button>
            <span><br><br>Note: Filters only work on incoming items, not items already listed to you</span>
        </div>
    </div>
</div>

<div id="tp_dialog_post_status">
    <div style="width: 400px;">
        Post a status to your social networks:<br/><br/>
        <table>
            <tr>
                <td><textarea id="statusText" style="width: 365px; margin-right: 10px;" rows="3"></textarea></td>
                <td style="vertical-align: top;"><input id="postStatusSend" type="button" value="Post"></td>
            </tr>
        </table>
        <br/>
        <table id="checkBoxesStatus">
            <tr id="post_facebook">
                <td>Facebook </td><td><input type="checkbox" value="facebook"></td>
            </tr>
            <tr id="post_twitter">
                <td>Twitter </td><td><input type="checkbox" value="twitter"></td>
            </tr>
        </table>
    </div>
</div>