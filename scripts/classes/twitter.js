var Twitter = {
    accessToken: null,
    
    connect: function(){
        window.location = '/twitter_route/redirect';
    },
    
    disconnect: function(){
        Ajax.call({
            controller: 'twitter_route',
            action: 'remove',
            data: {
                OK: true
            },
            success: function(data){
                if(data.success){
                    Readbo_Error.log('twitter added refresh');
                    window.location = '/';
                }else{
                    Dialog.message(data.message);
                }
            }
        });
    },
    
    postStatus: function(message){
        Ajax.call({
            controller: 'users',
            action: 'postTwitterStatus',
            data: {
                message: message
            },
            success: function(data){
                if(!data.success){
                    Dialog.message('Posting twitter status failed!');
                }
            },
            error: function(){
                Dialog.message('Posting twitter status failed!');
            }
        });
    },
    
    shareLink: function(item_sid, onSuccess){
        Ajax.call({
            controller: 'users',
            action: 'shareTwitterLink',
            data: {
                'item_sid': item_sid
            },
            success: function(data){
                if(!data.success){
                    Dialog.message('Sharing twitter post failed!');
                }else{
                    if(onSuccess){
                        onSuccess();
                    }
                }
            },
            error: function(){
                Dialog.message('Sharing twitter post failed!');
            }
        });
    }
};