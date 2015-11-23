var Settings = {
    xhr: {},
    uploadClick: false,
    
    show: function(){
        Dialog.open('settings', function(d){
            d.css('padding','0px');
            
            Settings.getSettingsData(function(data){
                Settings.loadSettings(data, d);
            });

            d.find('.btnDeleteAccount').click(function(){
                Settings.deleteUser();
            });
            
            $('#btnUploadImport').click(function(){
                Settings.uploadClick = true;
                $('#uploadForm').submit();
            });
        });
    },

    loadSettings: function(data, dialog){
        var settings = data.settings;

        var i;
        for(i in settings){
            if(settings.hasOwnProperty(i)) {
                var field = dialog.find('.'+ i + '-data');

                if(field.length === 0){
                    continue;
                }

                if(field.is('input[type="checkbox"]')){
                    if(parseInt(settings[i],10) === 1){
                        field.attr('checked','checked');
                    }
                    this.autoSave(i, field, 'checkbox');
                }else{
                    if(field.is('input')){
                        field.val(settings[i]);
                    }else{
                        field.html(settings[i]);
                    }
                    
                    this.autoSave(i, field);
                }
            }
        }
        
        if(settings.facebook_uid){
            $('.fb_disconnect').show();
            $('.fb_connect').hide();
        }else{
            $('.fb_disconnect').hide();
            $('.fb_connect').show();
        }
        
        if(settings.twitter_uid){
            $('.twitter_disconnect').show();
            $('.twitter_connect').hide();
        }else{
            $('.twitter_disconnect').hide();
            $('.twitter_connect').show();
        }
    },
    
    autoSave: function(name, obj, type){
        if(!type){
            type = 'text';
        }
        var that = this;
        
        obj.change(function(){
            var $this = $(this);
            if(that.xhr[name] && that.xhr[name] !== null){
                that.xhr[name].abort();
                that.xhr[name] = null;
            }
            
            var value = '';
            if(type === 'text'){
                value = obj.val();
            }else if(type === 'checkbox'){
                value = obj.is(':checked') ? '1' : '0';
            }
            
            var data = {};
            if($this.hasClass('property')){
                data['property_'+name] = value;
            }else{
                data[name] = value;
            }
            
            that.xhr[name] = Ajax.call({
                controller: 'users',
                action: 'saveSettings',
                data: data,
                success: function(data){
                    that.xhr[name] = null;
                }
            });
        });
    },
    
    saveData: function(name, value, property){
        var data = {};
        
        var db_value = value;
        if(db_value === true){ db_value = '1'; }
        if(db_value === false){ db_value = '0'; }
        
        if(!property){
            data[name] = db_value;
        }else{
            data['property_'+name] = db_value;
        }
        
        var that = this;
        that.xhr[name] = Ajax.call({
            controller: 'users',
            action: 'saveSettings',
            data: data,
            success: function(){
                that.xhr[name] = null;
                if(!property){
                    user[name] = value;
                }else{
                    user['properties'][name] = value;
                }
            }
        });
    },
    
    deleteUser: function(){
        Dialog.confirm('Are you sure you want to delete your user and all your data ?', function(){
            Ajax.call({
                controller: 'users',
                action: 'delete',
                data: {
                    OK: true
                },
                success: function(){
                    window.location = '/';
                }
            });
        });
    },
    
    getSettingsData: function(callback){
        Ajax.call({
            controller: 'users',
            action: 'getSettings',
            data: {
                OK: true
            },
            success: function(data){
                if(callback){
                    callback(data);
                }
            }
        });
    }
};