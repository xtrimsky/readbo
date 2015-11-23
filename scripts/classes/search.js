var Search = {
    typeTimeout: null,
    ajax: null,
    
    init: function(){
        $('#searchbar').keyup(function(){
            var text = $(this).val();
            
            //adding a small delay to typing, making sure its up to date
            if(Search.typeTimeout != null){
                clearTimeout(Search.typeTimeout);
            }
            Search.typeTimeout = setTimeout(function(){
                Search.search(text);
            },250);
        });
    },
    
    search: function(text){
        if(this.ajax != null){
            this.ajax.abort();
        }
        
        this.ajax = Ajax.call({
            controller: 'search',
            action: 'get',
            data: {
                feed_ids: 'newsfeed',
                search: text
            },
            success: function(data){
                if(data.success){
                    
                }
            }
        });
    }
    
};