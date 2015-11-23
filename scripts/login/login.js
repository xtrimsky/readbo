var App = {
    init: function(){
        var that = this;

        $('#btnRegister').click(function(){
            Dialog.open('register');

            return false;
        });
        
        $('#btnInvite').click(function(){
            var email = $('#inviteemail').val();
            if(!App.isEmailValid(email)){
                $('#inviteemail').css('background-color','#ab0024');
                $('#inviteerror').show();
                return false;
            }
            $('#inviteemail').css('background-color','white');
            $('#inviteerror').hide();
            
            App.addInviteEmail(email, function(success){
                if(!success){
                    $('#invitefail').show();
                }else{
                    $('#invitebox').html('Your email has been added to the waiting list!');
                }
            });
            
            return true;
        });
        
        $(document).on('click', '.register-link-facebook', function(){
            Facebook.connect();
            
            return false;
        }).on('click', '#btnCreateAccount', function(){
            var dialog = $(this).parents('.dialog');

            that.register(dialog);

            return false;
        });
        
        if(typeof(signup_code) != 'undefined'){
            Dialog.open('register');
            //$('#txtInviteCode').val(signup_code);
        }
    },
    
    addInviteEmail: function(email, callback){
        Ajax.call({
            controller: 'users',
            action: 'addInvite',
            data: {
                email: email
            },
            error: function(){
                callback(false);
            },
            success: function(data){
                callback(true);
            }
        });
    },

    register: function(dialog){
        var username = dialog.find('#txtUsername').val();
        var email = dialog.find('#txtEmail').val();
        var password = dialog.find('#txtPassword').val();
        //var invite_code = dialog.find('#txtInviteCode').val();
        var error = dialog.find('.form-error');

        if(!this.isEmailValid(email)){
            error.show();
            error.html('The email address you entered is not valid!');

            dialog.find('label[for="txtEmail"]').addClass('input-error');
            return;
        }
        
        Ajax.call({
            controller: 'users',
            action: 'register',
            data: {
                username: username,
                email: email,
                password: password
                //invite_code: invite_code
            },
            error: function(){
                error.html('An unknown error occured while trying to register! Please contact us to let us know.');
            },
            success: function(data){
                if(data.success){
                    Dialog.close('register');
                    window.location = '/';
                }else{
                    error.show();
                    switch(data.error){
                        case 1:
                            error.html('This username already exists, please enter a new one.');
                            dialog.find('label[for="txtUsername"]').addClass('input-error');
                            break;
                        case 2:
                            error.html('The invitation code is not valid.');
                            dialog.find('label[for="txtInviteCode"]').addClass('input-error');
                            break;
                        case 3:
                            error.html('This invitation code cannot be used anymore.');
                            dialog.find('label[for="txtInviteCode"]').addClass('input-error');
                            break;
                        case 4:
                            error.html('This email is already in use. Try clicking "forgot password", you probably already have an account here.');
                            dialog.find('label[for="txtEmail"]').addClass('input-error');
                            break;
                    }
                }
            }
        });
    },

    isEmailValid: function(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

};

$(function(){
    App.init();
});