var Reader = {
    items: null,
    addsearch: null,
    search_timer: null,

    readHeader: function(header){
        header.addClass('reader_active');

        var item = Items.getItemByHeader(header);
        var fl_content = header.next('.fl_content');

        var tp = View.getTemplate('tp_feed_list_content_'+item.type);
        var html = View.fillTemplate(tp, item);

        fl_content  .html( html )
                    .css('display','block');
                    
        fl_content.find('a').each(function(){
            var $this = $(this);
            $this.attr('target','_blank');
            var href = $this.attr('href');
            if(typeof(href) !== 'undefined' && href.substr(0,3) === 'www'){
                $this.attr('href','http://'+ href);
            }
        });
        
        
        var fl_text = fl_content.find('.fl_text');
        if(fl_text.html() === ''){
            fl_text.remove();
        }

        this.markAsRead( header );
        
        if(!View.isScrolledIntoView(fl_content.find('.fl_actions'))){
            $.scrollTo(header, 350);
        }
    },

    getCurrentActive: function(){
        return $('.reader_active');
    },

    next: function(){
        $('.fl_content:visible').css('display','none');						//hides all open content

        var active = this.getCurrentActive();
        var new_active = active.nextAll('.reader_header:first');	//gets next header
        active.removeClass('reader_active');	//removes all active headers
        
        if(new_active.length > 0){
            this.readHeader(new_active); // only goes to next if item exits
        }else{
            Feeds.openNextFeed();
        }
    },

    toggleMarkAsRead: function(){
        var active = this.getCurrentActive();

        if(active.hasClass('reader_read')){
            this.markAsUnread(active);
        }else{
            this.markAsRead(active);
        }
    },

    markAsRead: function(header){
        if(header.hasClass('reader_read')){
            return false;
        }

        header.addClass('reader_read');
        var item = Items.getItemByHeader(header);

        Lus.decreaseFeedCount( item.feed_id );
        Lus.decreaseFeedCount( 'newsfeed' );
        
        $('.fl_content:visible').find('.btn_read')
                .removeClass('readItem')
                .addClass('unreadItem')
                .html('Mark as unread');

        Items.markAsRead(item.id);

        return true;
    },

    markAsUnread: function(header){
        if(!header.hasClass('reader_read')){
            return false;
        }

        header.removeClass('reader_read');
        var item = Items.getItemByHeader(header);

        Lus.increaseFeedCount( item.feed_id );
        Lus.increaseFeedCount( 'newsfeed' );
        
        $('.fl_content:visible').find('.btn_read')
                .removeClass('unreadItem')
                .addClass('readItem')
                .html('Mark as read');

        Items.markAsUnread(item.id);

        return false;
    },
    

    getAllFeedsInFolder: function(folder_id){
        Model.getFeeds(folder_id);
    },

    readMore: function(){
        if(Model.last_was_empty || Model.xhr_get_feeds !== null){
            return;
        }
        
        Model.getFeeds(Feeds.getSelected(), Model.last_start + 20);
    },
    
    saveNavExpand: function(expanded){
        Ajax.call({
            controller: 'users',
            action: 'saveNavExpand',
            data: {
                expanded: expanded
            }
        });
    },
    
    saveCloseWelcome: function(){
        Ajax.call({
            controller: 'users',
            action: 'saveCloseWelcome',
            data: {
                success: true
            }
        });
    }
};