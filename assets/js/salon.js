function manageSalon() {
    /*$('#salon_sendForm').click(function(e) {
        e.preventDefault();

        var content = $('#salon_content').val();
        $form = $('#salon_content').closest('form');
        if (content != "") {
            $.ajax({
               url: $form.attr('action'),
               type: "POST",
                data: 'newMessage=' + content,
            });
        }
    });*/
    if(document.location.href.match('/salon(/|$)')) {
        $('.chat-defil').scrollTop(2000);
        $('#salon_content').select();
    }
}


function manageFocusSalon() {
    $('.nameSalon').off('click').on('click',function(e){
        if ($('#salon_content').val().indexOf($(this).text() + " > ") == -1) {
            $('#salon_content').val($(this).text() + " > "  + $('#salon_content').val());
        }
    });
}

function manageDisplaySalon(){
    if(document.location.href.match('/salon(/|$)')) {
        setTimeout( function(){
            $.ajax({
                url : document.location.href,
                type : "GET",
                success : function(data){
                    if(data.has_error === false && $('#salon_content').val() === "") {
                        location = location;
                    }
                }
            });
            manageDisplaySalon();
        }, 2500);
    }
}
$(document).ready(function() {
    manageDisplaySalon();
    manageSalon();
    manageFocusSalon();
});