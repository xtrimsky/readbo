var Interface = {
    wait: false,
    
    //clicking on the menu bar
    '#nav li': function(){
        $(this).children('a').click();
        
        return false;
    },
    
    //clicking on "Settings"
    '#settings': function(){
        Settings.show();
        return false;
    },
    
    //clicking on "Settings"
    '#profile': function(){
        window.location = '/profile/' + user.username;
        return false;
    },
    
    //clicking on Filters
    '#filters': function(){
        Filters.show();
        return false;
    },
    
    //clicking on Reporting
    '#btnReportBug': function(){
        Reporting.show();
        return false;
    },
    
    //clicking on Trending
    '#trending_nav': function(){
        window.location = '/trending';
        return false;
    },
    
    //Clicking on logout
    '#logout': function(){
        Dialog.confirm('Are you sure you want to logout?', function(){
            window.location = '/users/logout';
        });
        return false;
    },
    
    '#closeprofile': function(){
        Profile.close();
    },
    
    //Clicking on the button next to a feed, diplays dropdown
    '.edit_feed': function(){
        var feed_link = $(this).parent();
        var id = feed_link.attr('id').substr(5);
        var type = Feeds.get(id).type;
        
        var list = ['Rename'];
        if(type === 'rss' || type === 'twitter_search'){
            list = ['Rename','Unsubscribe'];
        }
        
        Dropdown.show( $(this),
            list,
            [function(){
                Dialog.open('rename', function(d){
                    d.find('.input_rename').val( $('#feed_'+id).data('name') );
                    
                    d.find('.rename-subscription-yes').click(function(){
                        Dialog.close('rename');
                        Feeds.rename(id, d.find('.input_rename').val());
                    });
                    
                    d.find('.rename-subscription-cancel').click(function(){
                        Dialog.close('rename');
                    });
                });
            }, function(){
                Dialog.confirm('Are you sure you want to unsubscribe?', function(){
                    feed_link.remove();
                    Feeds.remove(id);
                });
            }, null, null]
        );
        return false;
    },
    
    '#btnMarkListAsRead': function(){
        var html = 'Are you sure you want to mark all as read ?<br/>'+
                    '<br/>'+
                    '<input type="checkbox" id="dontaskagain"/> <label for="dontaskagain" style="font-size: 12px">Do not ask me again</label>';
        
        if(user['properties']['dont_confirm_mark_as_read']){
            
            if(typeof(Feeds.selected) == 'number'){
                Feeds.markAllAsRead(Feeds.selected);
            }else if(Feeds.selected == 'newsfeed'){
                Feeds.markAllFeedsAsRead();
            }
            
        }else{
            Dialog.confirm(html, function(dialog){
                if(typeof(Feeds.selected) == 'number'){
                    Feeds.markAllAsRead(Feeds.selected);
                }else if(Feeds.selected == 'newsfeed'){
                    Feeds.markAllFeedsAsRead();
                }

                var dontAskAgain = dialog.find('#dontaskagain').is(':checked');
                if(dontAskAgain){
                    Settings.saveData('dont_confirm_mark_as_read',true, true);
                }
            });
        }
    },
    
    '#btnRefreshList': function(){
        Model.getFeeds(Feeds.getSelected());
        return false;
    },
    
    //clicking on Post status
    '.btnPostStatus': function(){
        Dialog.open('post_status', function(d){
            Settings.getSettingsData(function(data){
                if(!data.settings.facebook_uid){
                    $('#post_facebook').remove();
                }
                if(!data.settings.twitter_uid){
                    $('#post_facebook').remove();
                }
            });
        });
        return false;
    },
    
    '#postStatusSend': function(){
        var message = $('#statusText').val();
        var list = $('#checkBoxesStatus');
        
        list.find('input[type="checkbox"]:checked').each(function(){
            switch($(this).val()){
                case 'facebook':
                    Facebook.postStatus(message);
                    break;
                case 'twitter':
                    Twitter.postStatus(message);
                    break;
            }
        });
        
        Dialog.close('post_status');
    },
    
    '.fb_connect': function(){
        Facebook.connect();
        return false;
    },
    
    '.fb_disconnect': function(){
        Dialog.confirm('Are you sure you want to disconnect from Facebook?', function(){
            Facebook.disconnect();
        });
        return false;
    },
    
    '.twitter_connect': function(){
        Twitter.connect();
        return false;
    },
    
    '.twitter_disconnect': function(){
        Dialog.confirm('Are you sure you want to disconnect from Twitter?', function(){
            Twitter.disconnect();
        });
        return false;
    },
    
    //clicking on Send Report in Reporting
    '#sendReport': function(){
        Reporting.send($(this));
        return false;
    },
    
    //clicking on an item
    '.reader_header': function(e){
        //if user clicked on link instead, canceling action, opening link
        if(e.target.tagName == 'A'){return true;}

        var h = $(this); //h
            
        //if header is already active, hides it
        if(h.hasClass('reader_active')){
            h.removeClass('reader_active')
             .next('.fl_content').css('display','none');

            return false;
        }

        $('.reader_header').removeClass('reader_active');
        $('.fl_content').css('display','none');

        Reader.readHeader( h );

        return false;
    },
    
    //button Add News on the left
    '.#btnAddFeed': function(){
        Dialog.open('add_feed', function(d){
            d.css('padding','0px');

            var search = d.find('.txtFeedSearch');
            search.focus();

            search.keyup(function(){
                if(Reader.search_timer !== null){
                    clearInterval(Reader.search_timer);
                    Reader.search_timer = null;

                    if(Reader.addsearch !== null){
                        Reader.addsearch.abort();
                        Reader.addsearch = null;
                    }
                }

                Reader.search_timer = setTimeout(function(){
                    var loading = d.find('.loading-search');
                    var field = d.find('.add-feed-search-result');

                    field.empty();
                    loading.show();

                   Reader.addsearch =  Ajax.call({
                        controller: 'reader',
                        action: 'searchFeeds',
                        data: {
                            search: search.val()
                        },
                        error: function(data){
                            loading.hide();
                        },
                        success: function(data){
                            loading.hide();
                            View.addFeedSearchResults(data, field);
                        }
                    });
                }, 500);
            });
        });

        return false;
    },
    
    '.btnAddFeedByURL': function(){
        var url = $(this).parent().children('.txtFeedAddURL').val();
            
        Feeds.subscribeRssByUrl(url);
        return false;
    },
    
    '#addTwitterSearch': function(){
        Feeds.addTwitterSearch( $('#txtFeedAddTwitter').val() );
        return false;
    },
    
    '.add-feed-link': function(){
        var url = $(this).attr('href');
        Feeds.addSubscription(url);

        return false;
    },
    
    '.feed_link': function(e){
        if($(e.target).hasClass('edit_feed')){
            return false;
        }
        Dropdown.removeDropdown();

        var fl = $(this);
        var id = parseInt( fl.attr('id').substr(5),10 );
        $('.selected_nav').removeClass('selected_nav');
        fl.addClass('selected_nav');
        Model.getFeeds(id);

        fl.children('.edit_feed').addClass('always_visible');

        return false;
    },
    
    '.likeItem': function(){
        var id = $(this).attr('rel');
            
        Items.like(id);

        $(this).removeClass('likeItem')
                .addClass('unlikeItem')
                .html('Unlike');

        return false;
    },
    
    '.unlikeItem': function(){
        var id = $(this).attr('rel');
            
        Items.unlike(id);

        $(this).removeClass('unlikeItem')
                .addClass('likeItem')
                .html('Like');

        return false;
    },
    
    '.readItem': function(){
        var header = '#item_'+$(this).attr('rel');
        Reader.markAsRead($(header));

        return false;
    },
    
    '.unreadItem': function(){
        var header = '#item_'+$(this).attr('rel');
        Reader.markAsUnread($(header));

        return false;
    },
    
    '.share_facebook': function(){
        var $this = $(this);
        var id = $this.attr('rel');
        $this.html('Shared!');
        $this.fadeOut(500);
        
        var item_sid = Items.get(id).item_sid;
        Facebook.shareLink(item_sid, function(){
            $this.html('Shared!');
            $this.fadeOut(500);
        });

        return false;
    },
    
    '.share_twitter': function(){
        var $this = $(this);
        var id = $this.attr('rel');
        
        var item_sid = Items.get(id).item_sid;
        Twitter.shareLink(item_sid, function(){
            $this.html('Shared!');
            $this.fadeOut(500);
        });

        return false;
    },
    
    '#close_panel': function(){
        $('#info_panel').slideUp(250, function(){
            $(this).remove();
        });
        
        Reader.saveCloseWelcome();
    },
    
    
    //expanding or collapsing folder
    '#nav_feeds .nav_expand': function(){
        var header = $(this).parent();
        if(header.hasClass('expand')){
            Folders.expand(header, true);
        }else{
            Folders.collapse(header, true);
        }
        
        return false;
    },
    '#nav_feeds .folder_header': function(){
        $('.selected_nav').removeClass('selected_nav');
        $(this).addClass('selected_nav');

        var id = $(this).attr('id').substr(8);
        Reader.getAllFeedsInFolder(id);
        
        return false;
    },
    
    init: function(){
        var d = $(document);
        for(var i in this){
            if(this.hasOwnProperty(i) && i !== 'init'){
                var fn = this[i];
                d.on('click', i, fn);
            }
        }
    }
};