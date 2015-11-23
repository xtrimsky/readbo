var Model = {
    last_start: 0,
    xhr_get_feeds: null,
    queue: [], //queues feeds request in case there is too much
    last_was_empty: false,
    showed_to_user: 0,
    maximum_count: 0,
    start_time: 0,
    
    read_first_on_load: false,
    
    getFeeds: function(ids, start){
        if(!start){ start = 0; }
        
        if(start === 0){
            this.maximum_count = Lus.getItemsCount(ids);
            this.start_time = 0;
            this.showed_to_user = 0;
        }
        
        if(typeof(ids) !== 'object' && start === 0){
            Feeds.setSelected(ids, true);
        }
        
        //if folder, getting all folder ids
        if(typeof(ids) === 'string'){
            ids = Folders.getFeedsInFolder(ids, true)
        }
        
        if(this.xhr_get_feeds !== null && start <= this.last_start){
            this.queue = [];
            this.xhr_get_feeds.abort();
        }else if(this.xhr_get_feeds !== null){
            this.queue.push(function(){
                Model.getFeeds(ids,start);
            });
            return;
        }
        Model.last_was_empty = false;
        
        this.xhr_get_feeds = Ajax.call({
            controller: 'items',
            action: 'get',
            data: {
                ids: ids,
                start: start,
                start_time: Model.start_time
            },
            error: function(){
                Model.xhr_get_feeds = null;
            },
            success: function(data){
                Model.xhr_get_feeds = null;
                if(!data){
                    Model.checkForQueue();
                    return;
                }
            
                Model.start_time = data.start_time;
                Model.last_start = start;

                var clear = false;
                if(start === 0){
                    Panels.open({
                        'name': 'list',
                        'display': 'right'
                    });
                    
                    $('#feed_list').empty();
                    clear = true;
                }
                
                var multifeed = ids.length > 0;
                var showed_items = View.listItems(data.feeds, multifeed);
                
                Items.addList(data.feeds, clear);
                
                if(start === 0){
                    if(Model.read_first_on_load){
                        Model.read_first_on_load = false;
                        
                        $('.reader_header:first').trigger('click');
                    }
                }
                
                if(data.feeds.length === 0){
                    Model.last_was_empty = true;
                }

                Model.showed_to_user += showed_items;
                if(Model.maximum_count === Model.showed_to_user){
                    Model.checkForQueue();
                    return;
                }
                
                if(data.feeds.length !== 20){
                    //finished
                    if(Model.maximum_count < Model.showed_to_user){
                        Feeds.updateData(ids, false);
                    }
                    if(Model.showed_to_user === 0){
                        Items.showNoItemsAvailable();
                    }
                }
				
                Model.checkForQueue();
            }
        });
    },
    
    checkForQueue: function(){
        if(Model.queue.length > 0){
            Model.queue.shift();
        }
    }
};