var Profile = {
    open: function(username, onFailure){
        if(!username){username = user.username;}
        
        this.loadData(username, function(data){
            Panels.open({
                'name': 'profile',
                'display': 'right'
            });
            
            $('#userprofile .data').each(function(){
                $this = $(this);
                var name = $this.attr('name');

                if(data[name]){
                    if($this.is('img')){
                        $this.attr('src', data[name] );
                    }else{
                        $this.html( data[name] );
                    }
                }
            });
        }, onFailure);
    },
    
    close: function(){
        window.location = '/';
    },
    
    loadData: function(username, callback, onFailure){
        Ajax.call({
            controller: 'users',
            action: 'getProfile',
            data: {
                username: username
            },
            error: function(){
                onFailure();
            },
            success: function(data){
                if(data.success){
                
                    if(callback){
                        callback(data);
                    }
                }else{
                    onFailure();
                }
            }
        });
    }
};