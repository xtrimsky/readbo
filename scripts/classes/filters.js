var Filters = {
    show: function(){
        Dialog.open('filters', function(d){
            d.css('padding','0px');
            
            Filters.refreshLists();
            
            Ajax.call({
                controller: 'filters',
                action: 'getFilters',
                data: {
                    OK: true
                },
                success: function(data){
                    Filters.load(data, d);
                }
            });
        });
        
        return this;
    },
    
    refreshLists: function(){
        var feeds = Feeds.getAll();
        var options = '<option value="0">All feeds</option>';
        
        var i = 0;
        while(i < feeds.length){
            options += '<option value="'+feeds[i].id+'">'+feeds[i].name+'</option>';
            i++;
        }
        
        $('#filter_select_affects').empty().append(options);
    },
    
    close: function(){
        Dialog.close('filters');
        
        return this;
    },
    
    load: function(data, d){
        var i = null;
        
        var filters = data.filters;
        for(i in filters){
            if(filters.hasOwnProperty(i)) {
                var field = filters[i].object.ucwords();
                var property = filters[i].property;
                var name = 'All feeds';
                var deleteLink = '<a class="deleteFilterLink" href="" filterid="'+filters[i].id+'" title="Delete filter"></a>';
                
                if(filters[i].feed_id !== null){
                    name = Feeds.get(filters[i].feed_id).name;
                }
                
                var row = $('<tr><td>'+field+'</td><td>'+property+'</td><td>'+name+'</td><td>'+deleteLink+'</td></tr>');
                
                $('#filter-list > tbody:last').append(row);
            }
        }
        
        if(filters.length == 0){
            $('#nofiltersdefined').show();
            $('#filter-list').hide();
        }else{
            $('#filter-list').show();
        }
        
        $('.deleteFilterLink').click(function(){
            var filterid = $(this).attr('filterid');
            
            Dialog.confirm('Are you sure you want to delete this filter ?', function(){
                Ajax.call({
                    controller: 'filters',
                    action: 'deleteFilter',
                    data: {
                        filter_id: filterid
                    },
                    success: function(){
                        Filters.close()
                                .show();
                    }
                });
            });
            
            return false;
        });
        
        $('#btnAddFilter').click(function(){
            Ajax.call({
                controller: 'filters',
                action: 'addFilter',
                data: {
                    field: $('#filter_select_field').val(),
                    value_compare: $('#filter_select_value_compare').val(),
                    value: $('#filter_input_property').val(),
                    affects: $('#filter_select_affects').val()
                },
                success: function(data){
                    if(!data.success){
                        Dialog.message('This feed could not be added. Please contact an administrator');
                    }else{
                        Filters.close()
                                .show();
                    }
                }
            });
            
            return false;
        });
    }
};