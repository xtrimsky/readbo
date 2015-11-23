var Global = {
    init: function(){
        $(document).on('click', 'button', function(){
            $(this).blur();
        });
    }
};

$(function(){
    Global.init();
});