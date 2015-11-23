var Facebook = {
    accessToken: null,
    
    connect: function(){
        FB.getLoginStatus(function(response) {
            if(response.status !== 'connected'){
                
                FB.login(function(response) {
                    if (response.authResponse) {
                        Facebook.accessToken = FB.getAuthResponse().accessToken;
                        Facebook.connected();
                    }
                }, {scope: 'email,offline_access,publish_stream,read_stream'});
          
                //$('#fb_button a').trigger('click');
            }else{
                Facebook.accessToken = FB.getAuthResponse().accessToken;
                Facebook.connected();
            }
        }, true);
    },
    
    disconnect: function(){
        Ajax.call({
            controller: 'users',
            action: 'facebookDisconnect',
            data: {
                OK: true
            },
            success: function(data){
                if(data.success){
                    window.location = '/';
                }else{
                    Dialog.message(data.message);
                }
            }
        });
    },

    connected: function(){
        if(this.accessToken === null){return false;}
        
        Ajax.call({
            controller: 'users',
            action: 'facebookConnect',
            data: {
                accessToken: Facebook.accessToken
            },
            success: function(data){
                if(data.success){
                    window.location = '/';
                }else{
                    if(data.code != '6'){
                        Dialog.message(data.message);
                    }
                }
            }
        });
        
        return true;
    },
    
    postStatus: function(message){
        Ajax.call({
            controller: 'users',
            action: 'postFacebookStatus',
            data: {
                message: message
            },
            success: function(data){
                if(!data.success){
                    Dialog.message('Posting facebook status failed!');
                }
            },
            error: function(){
                Dialog.message('Posting facebook status failed!');
            }
        });
    },
    
    shareLink: function(item_sid, onSuccess){
        Ajax.call({
            controller: 'users',
            action: 'shareFacebookLink',
            data: {
                'item_sid': item_sid
            },
            success: function(data){
                if(!data.success){
                    Dialog.message('Sharing facebook post failed!');
                }else{
                    if(onSuccess){
                        onSuccess();
                    }
                }
            },
            error: function(){
                Dialog.message('Sharing facebook post failed!');
            }
        });
    }
};