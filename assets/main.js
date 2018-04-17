var $ = require('jquery');
require('bootstrap-sass');

function manageImageForm() {
    $('.modify').off('click').on('click',function(e){
        $('#user_image_imageFile_file').click();
        $('#user_image_imageFile_file').on('change',function(){
            $(this).closest('form').submit();
        });
    });
}

function manageAllyImageForm() {
    $('.modify-allyImage').off('click').on('click',function(e){
        $('#ally_image_imageFile_file').click();
        $('#ally_image_imageFile_file').on('change',function(){
            $(this).closest('form').submit();
        });
    });
}

function manageModalContact() {
    $('#contactModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var recipient = button.data('whatever');
        var title = $('#contactModalLabel').text();
        var modal = $(this);
        modal.find('.modal-title').text(title);
        modal.find('.modal-body input.form-control').val(recipient);
    });
}

niobium = parseInt($('div.bg-top span.ressource.niobium span').text());
water = parseInt($('div.bg-top span.ressource.water span').text());
function manageReloadR() {
    setTimeout(function(){
        niobium = niobium + 4;
        $('div.bg-top span.ressource.niobium span').text(niobium);
        water = water + 2;
        $('div.bg-top span.ressource.water span').text(water);
        manageReloadR();
        }, 1000);
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    $("body").scrollspy({
        target: "#navbar-rules",
        offset: 70
    });
    manageImageForm();
    manageAllyImageForm();
    manageModalContact();
    manageReloadR();
    console.log("Toute utilisation de scripte sur le jeu seront puni d'un ban permanent");
});