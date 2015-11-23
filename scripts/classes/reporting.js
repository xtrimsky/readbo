var Reporting = {
    show: function(){
        Dialog.open('reporting');
        
        return this;
    },
    
    close: function(){
        Dialog.close('reporting');
        
        return this;
    },
    
    send: function(btn){
        var form = btn.parent();
        var results = {};
        form.find('input,textarea,select').each(function(){
            var el = $(this);
            if(typeof(el.attr('name')) !== 'undefined'){
                results[el.attr('name')] = el.val();
            }
        });
        
        Ajax.call({
            controller: 'reporting',
            action: 'send',
            data: results,
            success: function(data){
                if(!data.success){
                    Dialog.message('The report could not be submited, please try again later. Sorry about this :(.');
                    return;
                }
                
                Reporting.close();
            }
        });
        
        return this;
    }
};