var View = {
    templates: null,
    
    listItems: function(items, multifeed){
        var fl = $('#feed_list');

        var i = 0;
        var html = '';
        var items_showed = 0;
        while(i < items.length){
			
            var classes = '';
            if(items[i].isRead){
                classes += ' reader_read';
            }
            
            if(items[i].picture === null){
                items[i].picture = MEDIA_SERVER+'/images/news_icon.png';
            }
            
            var picture_html = '';
            var feed_title_html = '';
            
            if(multifeed || items[i].type !== 'rss'){
                picture_html = '<img width="32" height="32" class="feed_picture" src="'+ items[i].picture +'" alt="..." />';
                feed_title_html = '<span class="main_title">' + items[i].author + '</span> <span class="fl_likes">+25</span> <br>';
            }else{
                classes += ' no_img_fd';
            }

            if(items[i].id == 4304){
                var t = '<h3 id="item_' + items[i].id + '" class="reader_header'+ classes +'">' +
                    '<a href="#" class="fl_title">' +
                        picture_html +
                        feed_title_html +
                        '<span class="fl_title_text">' + items[i].title + '</span>' + 
                    '</a>' + 
                    '<span class="fl_date">' + Common.formatDate(items[i].date, time_display === 12) + '</span>' +
                '</h3>' +
                '<div class="fl_content"></div>';
            }
            html +=
            '<h3 id="item_' + items[i].id + '" class="reader_header'+ classes +'">' +
                '<div href="#" class="fl_title">' +
                    picture_html +
                    feed_title_html +
                    '<span class="fl_title_text">' + items[i].title + '</span>' + 
                '</div>' + 
                '<span class="fl_date">' + Common.formatDate(items[i].date, time_display === 12) + '</span>' +
            '</h3>' +
            '<div class="fl_content"></div>';
            i++;
            items_showed++;
        }

        fl.append(html);
        
        //if last item is visible in list, loads more data
        if(items[i-1] && View.isScrolledIntoView($('#item_'+items[i-1].id))){
            Reader.readMore();
        }
        
        if(items.length == 0 && fl.html() === ''){
            Items.showNoItemsAvailable();
        }
        
        return items_showed; //amount of items showed, to fetch more if not enought
    },

    listFolders: function(folders){
        var nav = $('#nav_feeds');

        var tp = this.getTemplate('tp_navigate_folders');
        for(var i in folders){
            folders[i].id2 = folders[i].id;
            var li = this.fillTemplate(tp,folders[i]);

            nav.append(li);
        }
    },

    clearNavPanel: function(){
        $('#nav_feeds').empty();
    },
    
    updateFeed: function(feed_data){
        var link = $('#feed_'+feed_data.id);
        if(feed_data.count === 0){
            link.hide();
        }else{
            link.show();
        }
        Lus.setItemCount(link, feed_data.count);
    },

    listFeeds: function(feeds){
        var i = 0;
        var nav = $('#nav_feeds');
        
        Folders.refreshFolders();
        
        var sorting = function(a, b){
            var tmp = [a.name.toLowerCase(),b.name.toLowerCase()].sort();
            
            if(tmp[0] == b.name.toLowerCase()){
                return 1;
            }
            
            return -1;
        }
        feeds.sort(sorting);
        
        var tp = this.getTemplate('tp_navigate_feeds');
        while(i < feeds.length){
            
            var name = feeds[i].name;
            if(feeds[i].name.length > 27){
                feeds[i].name = feeds[i].name.substr(0, 24) + '...';
            }
            
            if(feeds[i].type == 'twitter_search'){
                feeds[i].icon = 'twitter';
            }else if(feeds[i].type == 'facebook'){
                feeds[i].icon = 'facebook';
            }else if(feeds[i].type == 'twitter'){
                feeds[i].icon = 'twitter';
            }else{
                feeds[i].icon = 'rss';
            }

            var li = this.fillTemplate(tp,feeds[i]);

            if(feeds[i].parent === 0){
                nav.append(li);
            }else{
                $('#folder_'+feeds[i].parent).append(li);
            }
            
            var feed_link = $('#feed_'+feeds[i].id);
            feed_link.data('name',name);
            
            if(feeds[i].id === 0){
                feed_link.children('.edit_feed').remove();
            }

            if(feeds[i].count > 0){
                Lus.setFeedCount(feeds[i].id, feeds[i].count);
            }else{
                if(!user.properties.show_read){
                    feed_link.hide();
                }
                
            }

            i++;
        }
    },
    
    loadTemplates: function(){
        this.templates = {};
        
        var html = $('<div>'+js_templates+'</div>');
        
        html.children().each(function(){
            var name = $(this).attr('id');
            var html = $(this).html();
            html = html.replace('img:delay','img');
            
            View.templates[name] = html;
        });
    },

    getTemplate: function(name){
        if(this.templates === null){
            this.loadTemplates();
        }
        
        return this.templates[name];
    },

    fillTemplate: function(content, args){
        var i;
        for(i in args){
            if(args.hasOwnProperty(i)) {
                var string = '['+i+']';
                var replaced = new RegExp(string.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, "\\$&"), "gi");
                content = content.replace(replaced, args[i]);
            }
        }

        return content;
    },

    addFeedSearchResults: function(response, field){
        var i = 0;

        var list = '';
        while(i < response.length){
            var name = response[i].name;
            if(name.length > 55){
                name = name.substr(0,55) + '...';
            }

            list += '<li><a class="add-feed-link" href="'+response[i].url+'" title="'+response[i].content+'">' + name + '</a></li>';

            i++;
        }

        field.append(list);
    },
    
    isScrolledIntoView: function(elem)
    {
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();

        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();

        return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom)
          && (elemBottom <= docViewBottom) &&  (elemTop >= docViewTop) );
    }
};