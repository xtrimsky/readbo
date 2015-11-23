var Feeds = {
    feeds: [],
    selected: 'newsfeed',
    
    add: function(feeds, merge){
        if(!merge){
            Panels.open({
                'name': 'nav_feed',
                'display': 'left'
            });
            
            this.feeds = feeds;
        }else{
            this.feeds = $.merge(this.feeds, feeds);
        }
        
        View.listFeeds(this.feeds);
    },
    
    refresh: function(){
        Panels.open({
            'name': 'nav_feed',
            'display': 'left'
        });
        
        this.add(this.feeds, false);
    },
    
    update: function(feeds){
        for(var i = feeds.length - 1; i >= 0; i--){
            
            var found = false;
            for(var j = this.feeds.length - 1; j >= 0 ; j--){
                var a = feeds[i];
                var b = this.feeds[j];
                
                if(a.id == b.id){
                    found = true;
                    if(feeds[i].count != this.feeds[j].count){
                        View.updateFeed(feeds[i]);
                        this.feeds[j] = feeds[i];
                    }
                    break;
                }
            }
            
            if(!found){
                this.feeds.push(feeds[i]);
            }
        }
    },
    
    set: function(feed_id, field, value){
        for(var i in this.feeds){
            if(this.feeds.hasOwnProperty(i) && this.feeds[i].id == feed_id){
                this.feeds[i][field] = value;
            }
        }
    },
    
    get: function(feed_id){
        for(var i in this.feeds){
            if(this.feeds.hasOwnProperty(i) && this.feeds[i].id == feed_id){
                return this.feeds[i];
            }
        }
        
        return false;
    },
    
    rename: function(id, name){
        Ajax.call({
            controller: 'reader',
            action: 'renameSubscription',
            data: {
                id: id,
                name: name
            },
            success: function(data){
                var title = $('#feed_' + id).find('.feed_title');
                title.html(data.new_name);
            }
        });
    },
    
    addTwitterSearch: function(name){
        Ajax.call({
            controller: 'reader',
            action: 'addTwitterSearch',
            data: {
                search: name
            },
            success: function(data){
                if(data.not_added){
                    Readbo_Error.log(data.error);
                    return;
                }

                Dialog.close('add_feed');
                
                Feeds.add([{
                    id: data.feed_id,
                    parent: 'newsfeed',
                    count: parseInt(data.count,10),
                    name: data.name,
                    type: 'twitter_search'
                }], true);
            }
        });
    },
    
    remove: function(id){
        Ajax.call({
            controller: 'reader',
            action: 'removeSubscription',
            data: {
                id: id
            },
            success: function(){
                //cleaning feeds var
                for(var i in Feeds.feeds){
                    if(Feeds.feeds.hasOwnProperty(i) && Feeds.feeds[i].id == id){
                        Feeds.feeds.splice(i,1);
                    }
                }
            }
        });
    },
    
    getAll: function(){
        return this.feeds;
    },
    
    markAllFeedsAsRead: function(){
        Ajax.call({
            controller: 'items',
            action: 'markAllFeedsAsRead',
            success: function(){
                Readbo_Error.log('refreshing, all feeds marked as read');
                window.location = '/';
            }
        });
    },
    
    getAllFeedIds: function(){
        var result = [];
        var i = 0;
        while(i < this.feeds.length){
            result.push(this.feeds[i].id);
            i++;
        }
        
        return result;
    },
    
    updateData: function(ids, parse){
        $('#nav_panel .updating').show();
        
        if(!ids){
            ids = this.getAllFeedIds();
        }
        if(!parse){parse = false;}
        
        if(ids.length != null){
            var toFetch = ids;
            ids = ids.splice(10);
        }else{
            toFetch = ids;
            ids = [];
        }
        
        Ajax.call({
            controller: 'reader',
            action: 'updateFeeds',
            data: {
                'ids': toFetch,
                parse: parse
            },
            error: function(){
                $('#nav_panel .updating').hide();
            },
            success: function(data){
                $('#nav_panel .updating').hide();
                
                Feeds.update(data.feeds);
                
                if(ids.length !== 0){
                    Feeds.updateData(ids, parse);
                }else{
                    Feeds.updateFinished();
                }
            }
        });
    },
    
    updateFinished: function(){
        Ajax.call({
            controller: 'reader',
            action: 'feedsFinishedUpdating',
            data: {
                OK: true
            }
        });
    },
    
    markAllAsRead: function(feed_id){
        var feed = $('#feed_'+feed_id);
        Lus.setItemCount(feed, 0);

        if(feed_id == Feeds.selected){
            Items.showNoItemsAvailable();
        }
        
        Ajax.call({
            controller: 'items',
            action: 'markAllAsRead',
            data: {
                feed_id: feed_id
            }
        });
    },
    
    subscribeRssByUrl: function(url){
        Ajax.call({
            controller: 'reader',
            action: 'addFeedByUrl',
            data: {
                url: url
            },
            success: function(data){
                if(data.not_added){
                    Dialog.message('Your feed could not be added, please verify that the url is correct.<br/>This url has to be the direct link to an RSS feed, not the website it is on.<br/>Otherwise please use search feeds function.');
                    return;
                }

                Dialog.close('add_feed');
                Readbo_Error.log('refreshing, subscribed rss');
                window.location = '/';
            }
        });
    },

    addSubscription: function(url){
        Ajax.call({
            controller: 'reader',
            action: 'addSubscription',
            data: {
                url: url
            },
            success: function(data){
                if(data.not_added){
                    Readbo_Error.log(data.error);
                    return;
                }

                Dialog.close('add_feed');
                
                Feeds.add([{
                    id: data.feed_id,
                    parent: 'newsfeed',
                    count: parseInt(data.count,10),
                    name: data.name
                }], true);
            }
        });
    },
    
    setSelected: function(ids, save){
        //ids += ''; //making it a string
        
        if(this.selected !== ids && save){
            this.saveLastClicked(ids);
        }
        this.selected = ids;
    },
    
    getSelected: function(){
        return this.selected;
    },
    
    //saves last feed clicked, when user refreshes the page, the feed is kept selected
    saveLastClicked: function(ids){
        Ajax.call({
            controller: 'users',
            action: 'saveLastFeed',
            data: {
                ids: ids
            }
        });
    },
    
    getFromFolder: function(name){
        var parent_id = Folders.getId(name);
        var result = {};
        
        for(var i in this.feeds){
            if(this.feeds.hasOwnProperty(i) && this.feeds[i].parent_id === parent_id){
                result[this.feeds[i].id] = this.feeds[i];
            }
        }
        
        return result;
    },
    
    openLastFeed: function(){
        var last_feed = user.properties.last_feed ? user.properties.last_feed : 'newsfeed';
        this.setSelected(last_feed,false);
        if(!this.get(last_feed)){
            last_feed = 'newsfeed';
        }
        
        if(last_feed === 'newsfeed'){
            $('#hfolder_newsfeed .folder_link').trigger('click');
        }else{
            $('#feed_'+last_feed).trigger('click');
        }
    },
    
    openNextFeed: function(){
        var selected = Feeds.getSelected();
        if(typeof(selected) === 'number'){
            var feed = $('#feed_'+selected);
            var next = feed.next();

            while(next.length > 0 && !next.is(':visible')){
                next = next.next();
            }

            if(next.length > 0){
                Model.read_first_on_load = true;
                next.trigger('click');
                $.scrollTo($('#logo'), 0);
            }
        }
    }
};