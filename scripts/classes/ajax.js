var Ajax = {
    call: function(args){
        
        return $.ajax({
            url: args.controller + '/' + args.action,
            type: 'POST',
            data: args.data,
            dataType: 'json',
            error: function(data){
                
                if(data.status !== 0){
                    Ajax.error(args.controller, args.action, data.statusText);

                    if(args.error){
                        args.error();
                    }
                }
            },
            success: function(data){
                if(args.success){
                    args.success(data);
                }
            }
        });
        
    },
    
    error: function(controller, action, data){
        Readbo_Error.log('An ajax call failed, controller: ' + controller + ' action: ' + action + ' data: ' + data);
    }
};