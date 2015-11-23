/*
 * Calculates amount of items left to read, marks items as read
 */
var Lus = {
    setFeedCount: function(feed_id, count){
        var feed = $('#feed_' + feed_id);
        
        Feeds.set(feed_id,'count',count);
        this.setItemCount(feed, count);
    },
    
    increaseFeedCount: function(feed_id, count){
        var feed = $('#feed_' + feed_id);
        var feed_data = Feeds.get(feed_id);

        if(!count){ count = 1; }
        
        var new_count = feed_data.count + count;
        Feeds.set(feed_id,'count',new_count);

        this.setItemCount(feed, new_count);
    },
    
    decreaseFeedCount: function(feed_id, count){
        var feed = $('#feed_' + feed_id);
        var feed_data = Feeds.get(feed_id);

        if(!count){ count = 1; }
        
        var new_count = feed_data.count - count;
        Feeds.set(feed_id,'count',new_count);

        this.setItemCount(feed, new_count);
    },
    
    getItemCount: function(item){
        item = item.find('.item_count');
        
        if(item.length === 0){
            return 0;
        }

        return parseInt(item.html().substr(1,item.html().length - 2), 10);
    },
    
    getItemsCount: function(feed_id){
        var obj = $('#feed_'+feed_id);
        var fol = $('#hfolder_'+feed_id);
        if(obj.length > 0){
            return Lus.getItemCount(obj);
        }else if(fol.length > 0){
            return Lus.getItemCount(fol);
        }
        
        return 0;
    },
    
    setItemCount: function(item, count){
        var current = item;
        var previousCount = Lus.getItemCount(item);
        item = item.find('.item_count');

        if(count < 0){
            count = 0;
        }
        
        var diff = count - previousCount;
		
        if(count === 0 && !user.properties.show_read){
            item.hide();
            item.parents('.feed_link').hide();
        }else{
            item.show();
            if(count === 1){
                item.parents('.feed_link').show();
            }
        }

        var s_count = count.toString();
        item.html('('+s_count+')');
        
        while(current.parent().hasClass('nav_content')){
            current = current.parent();
            var id = current.attr('id').substr(7);
            var hfolder = $('#hfolder_'+id);
            this.setItemCount(hfolder, Lus.getItemCount(hfolder) + diff);
        }
    }
};