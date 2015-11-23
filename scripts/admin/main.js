var timeout = null;
$(function(){
    $(document).on('click','#nav li', function(){
        window.location = $(this).children('a').attr('href');
    }).on('keyup', '#searchbox_users', function(){
        if(timeout !== null){
            clearTimeout(timeout);
        }
        
        timeout = setTimeout(function(){
            refreshUsersTable();
        }, 500);
    }).on('click','#btnCreateRelease',function(){
        var name = $('#name').val();
        if(name === ''){alert('you need a name!'); return;}
        Dialog.confirm('Are you sure you want to create a new release name "'+name+'" ?', function(){
           Ajax.call({
                controller: 'admin',
                action: 'createRelease',
                data: {
                    name: name
                },
                error: function(){
                    Dialog.message('An error occured!');
                },
                success: function(data){
                    window.location = '/admin/releases';
                }
            });
        });
    }).on('click','#sendInvites',function(){
        window.location = '/admin/sendInvites';
    });
    
    if(current_page == 'users'){
        if($('#searchbox_users').val() != ''){
            refreshUsersTable();
        }
    }
});

function refreshUsersTable(){
    Ajax.call({
        controller: 'admin',
        action: 'fetchUsersSearch',
        data: {
            search: $('#searchbox_users').val()
        },
        error: function(){
            Dialog.message('An error occured!');
        },
        success: function(data){
            $('#users_table').replaceWith($(data.html));
        }
    });
}

function makeAdmin(id){
    Ajax.call({
        controller: 'admin',
        action: 'makeAdmin',
        data: {
            id: id
        },
        error: function(){
            Dialog.message('An error occured!');
        },
        success: function(data){
            window.location = '/admin/users?search='+$('#searchbox_users').val();
        }
    });
}