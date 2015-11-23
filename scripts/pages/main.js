$(function(){
    $('#pages_contact_f').submit(function(){
        var errors = false;
        
        $('.required_input').css('background-color', 'white');
        
        $('.required_input').each(function(){
            if($(this).val() == ''){
                $(this).css('background-color', '#FFD6D7');
                errors = true;
            }
        });
        
        return !errors;
    });
});