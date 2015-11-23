var App = {
    init: function(){
        Folders.init();
        Feeds.add(feeds);
        this.initKeys();
        this.tabs();
        this.initWindowScrolling();
        this.initDevToolbar();
        Interface.init();
        
        if(update_feeds){
            Feeds.updateData(false, true); //force parsing
        }
        
        if(typeof(profile_username) === 'undefined'){
            Feeds.openLastFeed();
        }else{
            Profile.open(profile_username, function(){
                Feeds.openLastFeed();
            });
        }
        
        if(typeof(error_message) !== 'undefined'){
            Dialog.message(error_message);
        }
        
        setTimeout(function(){
            Feeds.updateData(false, false);
        },300000); //refreshing list with new counts every 5 minutes
    },
    
    initDevToolbar: function(){
        if(typeof(show_toolbar) === 'undefined'){
            return;
        }
        
        var profiler = $('#code' + 'ign'+'iter_profiler');
        
        profiler.children().each(function(){
            var id = $(this).attr('id');
            
            if(typeof(id) === 'undefined'){
                id = 'profiler_queries';
            }
            
            var sections = id.split('_');
            $('#dev_' + sections[sections.length - 1]).append($(this).html());
        });
        
        $(document).on('click','.show_dev', function(){
            var item = $(this).attr('item');
            $('#'+item).toggle();
        }).on('click','#hide_toolbar', function(){
            $('#dev_toolbar_wrapper').remove();
        });
        
        profiler.remove();
    },

    initKeys: function(){
        $(document).keydown(function(e){
            var code = e.which;

            if(e.currentTarget.activeElement.tagName === 'INPUT' || e.currentTarget.activeElement.tagName === 'TEXTAREA'){
                return true;
            }

            if(code === 32) { //space bar
                Reader.next();
                return false;
            }else if(code === 77){ //M key
                Reader.toggleMarkAsRead();
            }

            return true;
        });
        
        //Search.init();
    },

    initWindowScrolling: function(e){
        $(window).scroll(function(e){
            var elem = $(window)[0];

            if(elem.scrollY > (document.documentElement.scrollHeight - document.documentElement.clientHeight) - 200){
                Reader.readMore();
            }
        });
    },

    tabs: function(){
        $(document).on('click', '.tabbed_list li', function(){
            var list = $(this).parent().parent();
            var tabs = list.next('.tabs-list');
            
            list.find('.sel-tab').removeClass('sel-tab');

            var index = list.find('li').index( $(this) );

            tabs.children().hide();
            tabs.children().eq(index).show();
            
            $(this).addClass('sel-tab');
        });
    }
};

$(function(){
    App.init();
});

//remove this from here
String.prototype.ucwords = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};