var Panels = {
    
    /*
     * options:
     * display: left|right|full
     * name: panel type
     */
    open: function(options){
        if(!options.name){return false;}
        if(!options.display){options.display = 'full';}
        
        var html = this.getHTML(options.name);
        
        if(options.display === 'left'){
            $('#leftcolumnwrapper').html(html);
        }else if(options.display === 'right'){
            $('#rightcolumnwrapper').html(html);
        }
        
        return true;
    },
    
    getHTML: function(name){
        return View.getTemplate('tp_panel_'+name);
    }
    
};