var Folders = {
    folders: {
        'newsfeed':{
            'id': 'newsfeed',
            'name': 'Newsfeed',
            'parent': 0
        }
    },
    
    init: function(){
        View.listFolders(this.folders);
        
        if(user.properties.nav_expanded){
            Folders.expand($('#hfolder_newsfeed'));
        }else{
            Folders.collapse($('#hfolder_newsfeed'));
        }
        
        this.adjustSize();
        setTimeout(function(){
            Folders.adjustSize();
        },1000);
    },
    
    //ajusting size of main app if no items
    adjustSize: function(){
        var mainh = $('#main').height();
        var left = $('#leftcolumnwrapper').height();
        if(mainh <= left){
            $('#main').css('min-height', (left + 50) + 'px');
        }
    },
    
    refreshFolders: function(){
        View.clearNavPanel();
        this.init();
    },
    
    getFeedsInFolder: function(folder_name, force_visible){
        var ids = [];
        
        for(var i in Feeds.feeds){
            if(Feeds.feeds.hasOwnProperty(i) && Feeds.feeds[i].parent === folder_name){
                
                if(!force_visible || Feeds.feeds[i].count > 0){
                    
                    ids.push(Feeds.feeds[i].id);
                }
            }
        }
        
        return ids;
    },
    
    expand: function(header, save){
        header.removeClass('expand');
        header.addClass('collapse');

        header.next().css('display','block');
        if(save){
            Reader.saveNavExpand(true);
        }
        user.properties.nav_expanded = true;
    },
    
    collapse: function(header, save){
        header.removeClass('collapse');
        header.addClass('expand');

        header.next().css('display','none');
        if(save){
            Reader.saveNavExpand(false);
        }
        user.properties.nav_expanded = false;
    }
    
};