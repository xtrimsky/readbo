var iFrame = {
    init: function(){
        this.resize();
        
        $(window).resize(function(){
            iFrame.resize();
        });
    },
    
    resize: function(){
        var height = $(window).height() - 49;
        $('#website_display').css('height', height + 'px');
    }
};

$(function(){
    iFrame.init();
});

