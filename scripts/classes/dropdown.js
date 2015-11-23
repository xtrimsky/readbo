var Dropdown = {
    show: function(obj, list, actions){
        this.removeDropdown();
        
        var li = '';
        var i = 0;
        var si = '';
        while(i < list.length){
            var name = list[i];
            
            if(name === 'separator'){
                li += '</ul><div class="dropdown_separator"></div><ul>';
            }else{
                si = i.toString();
                li += '<li id="action'+si+'">'+name+'</li>';
            }
            
            i++;
        }
        
        var dropd = $('<div class="dropdown"><ul>'+li+'</ul></div>');
        $('body').append( dropd );
        this.positionOnObject(obj);
        
        i = 0;
        while(i < actions.length){
            if(typeof(actions[i]) === 'function'){
                si = i.toString();
                $('#action'+si).click(function(){
                    var id = $(this).attr('id').substr(6);
                    actions[id]();
                });
            }
            
            i++;
        }
        
        $(document).on('click', function(){
            Dropdown.removeDropdown();
        })
    },
    
    positionOnObject: function(obj){
        obj.addClass('always_visible');
        
        var offset = obj.offset();
        var top = obj.height() + offset.top;
        var left = offset.left;
        $('.dropdown').css('top',top+'px')
                      .css('left',left+'px');
    },
    
    removeDropdown: function(){
        $('.always_visible').removeClass('always_visible');
        $('.dropdown').remove();
    }
};