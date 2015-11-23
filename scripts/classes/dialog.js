var Dialog = {
    init: function(){
        $(document).on('click', '.close', function(){
            var d = $(this).parent().parent();
            d   .prev()
                .children('modal-screen')
                .show();
            d.remove();
        });
    },
    
    message: function(message){
        var html = '<div><p>'+message+'</p><br><br><div class="action_buttons"><button class="btnNo">OK</button></div>';

        var confirmDialog = Dialog.createDialog('confirm-dialog', html);

        confirmDialog.find('.btnNo').click(function(){
            Dialog.close('confirm-dialog');
        });

        var close = confirmDialog.find('.close');
        close.unbind('click');
        close.click(function(){
            confirmDialog.find('.btnNo').trigger('click');
        });
    },

    confirm: function(message, fnyes, fnno){
        var html = '<div>'+message+'<br><br><div class="action_buttons"><button class="btnYes">Yes</button><button class="btnNo">No</button></div>';

        var confirmDialog = Dialog.createDialog('confirm-dialog', html);

        confirmDialog.find('.btnYes').click(function(){
            if(fnyes){fnyes(confirmDialog);}
            Dialog.close('confirm-dialog');
        });

        confirmDialog.find('.btnNo').click(function(){
            if(fnno){fnno(confirmDialog);}
            Dialog.close('confirm-dialog');
        });

        var close = confirmDialog.find('.close');
        close.unbind('click');
        close.click(function(){
            confirmDialog.find('.btnNo').trigger('click');
        });
    },

    get: function(name){
        return $('.dialog-'+name);
    },

    close: function(name){
        var dialog = $('.dialog-'+name).parent();

        dialog.prev().children('modal-screen').show();
        dialog.remove();
    },

    open: function(name, callback){
        var html = View.getTemplate('tp_dialog_'+name);

        var dialog = this.createDialog(name, html);

        if(callback){
            callback(dialog);
        }
    },

    createModal: function(content, hideScreen){
        $('.modal-screen').hide();

        var screen = $('<div class="modal-screen"></div>');
        var modal = $('<div class="modal-wrapper"></div>');

        if(!hideScreen){
            modal.append(screen);
        }

        modal.append(content);

        $('body').append( modal );
    },

    createDialog: function(name, html){
        var dialog = $('<div class="dialog dialog-'+ name +'"><div class="close"></div>' + html + '</div>');

        this.createModal(dialog);
        this.centerDialog(dialog);

        return dialog;
    },

    centerDialog: function(dialog){
        var left = ($('body').width() / 2) - (dialog.width() / 2);

        var padding_left = dialog.css('padding-left').substr(0, dialog.css('padding-left').length - 2);
        var padding_right = dialog.css('padding-right').substr(0, dialog.css('padding-right').length - 2);
        var padding = parseInt(padding_left, 10) + parseInt(padding_right, 10);
        left = left - (padding / 2);
        
        var s_left = left.toString();
        dialog.css('left', s_left+'px');
    }
};

$(function(){
    Dialog.init();
});