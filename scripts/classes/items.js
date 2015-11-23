var Items = {
    items: {},
    //all_read_items: {},
    
    addList: function(items, clear){
        if(clear){
            this.items = {};
        }
        
        var i = null;
        for(i in items){
            if (items.hasOwnProperty(i)) {
                var id = items[i].id;

                this.items[id] = items[i];

                this.items[id].likeClass = 'likeItem';
                this.items[id].likeButton = 'Like';
                if(this.items[id].is_liked === '1'){
                    this.items[id].likeClass = 'unlikeItem';
                    this.items[id].likeButton = 'Unlike';
                }
                
                this.items[id].readClass = 'readItem';
                this.items[id].readButton = 'Mark as read';
                if(this.items[id].isRead){
                    this.items[id].readClass = 'unreadItem';
                    this.items[id].readButton = 'Mark as unread';
                }
            }
        }
    },
    
    getItemByHeader: function(header){
        var id = header.attr('id').substr(5);
        
        return this.get(id);
    },
    
    get: function(id){
        return this.items[id];
    },
    
    set: function(id, field, value){
        this.items[id][field] = value;
    },
    
    like: function(id){
        this.items[id].is_liked = '1';
        this.items[id].likeClass = 'unlikeItem';
        this.items[id].likeButton = 'Unlike';
        
        Ajax.call({
            controller: 'items',
            action: 'like',
            data: {
                item_sid: this.items[id].item_sid
            }
        });
    },
    
    unlike: function(id){
        this.items[id].is_liked = '0';
        this.items[id].likeClass = 'likeItem';
        this.items[id].likeButton = 'Like';
        
        Ajax.call({
            controller: 'items',
            action: 'unlike',
            data: {
                item_sid: this.items[id].item_sid
            }
        });
    },
    
    markAsRead: function(id){
        this.items[id].isRead = '1';
        this.items[id].readClass = 'unreadItem';
        this.items[id].readButton = 'Mark as unread';
        
        Ajax.call({
            controller: 'items',
            action: 'markAsRead',
            data: {
                item_sid: this.items[id].item_sid,
                feed_id: this.items[id].feed_id
            }
        });
        
        //this.all_read_items[this.items[id].item_sid] = 1;
    },
    
    markAsUnread: function(id){
        this.items[id].isRead = '0';
        this.items[id].readClass = 'readItem';
        this.items[id].readButton = 'Mark as read';
        
        Ajax.call({
            controller: 'items',
            action: 'markAsUnread',
            data: {
                item_sid: this.items[id].item_sid,
                feed_id: this.items[id].feed_id
            }
        });
        
        //delete this.all_read_items[this.items[id].item_sid];
    },
    
    showNoItemsAvailable: function(){
        $('#feed_list').html('<p style="padding: 30px;">No items available</p>');
    }
};