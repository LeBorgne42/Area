var $ = require('jquery');
        require('bootstrap-sass');
        require('bootstrap-confirmation2');


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

function manageRegistrationCheck() {
    $('#registration-check').off('click').on('click',function(e){
        $('#email').removeAttr('hidden');
        $(this).hide();
        $('#email').attr('required', 'required');
        $('form').attr('action', '/enregistrement');
    });
}

function managePlanetChoiceForm() {
    $('#planet_choice').off('change').on('change',function(e){
        e.preventDefault();

        var actionForm = $(this).attr('action');
            planetChoice = $(this)[0][0].value;

        actionForm = actionForm.replace(/\/\d+$/, '/' + planetChoice);
        $(this).attr('action', actionForm)
        $(this).closest('form').submit();
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

function manageTime() {
    $('.timerArea').each( function(){
        var build = new Date($(this).text());
        var area = $(this);
        var now = new Date();
        var date_now = Math.abs(build - now) / 1000;
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        jours = (jours > 10) ? jours : '0' + jours;
        heures = (heures > 10) ? heures : '0' + heures;
        minutes = (minutes > 10) ? minutes : '0' + minutes;
        secondes = (secondes > 10) ? secondes : '0' + secondes;
        setInterval(function() {
            if (build < now) {
                area.html("<a style='cursor: pointer;' onclick='window.location.reload(false)'>Terminée</a>");
                area.removeAttr('hidden');
                setTimeout(function () {
                }, 2000);
            } else {
                if (date_now > 0) {
                    if (jours > 0) {
                        area.text('(' + jours + 'j ' + heures + ':' + minutes + ':' + secondes + ')');
                        area.removeAttr('hidden');
                    } else if (heures > 0) {
                        area.text('(' + heures + ':' + minutes + ':' + secondes + ')');
                        area.removeAttr('hidden');
                    } else if (minutes > 0) {
                        area.text('(' + minutes + ':' + secondes + ')');
                        area.removeAttr('hidden');
                    } else if (secondes > 0) {
                        area.text('(' + secondes + ')');
                        area.removeAttr('hidden');
                    }
                    secondes = secondes - 1;
                    secondes = (secondes > 10) ? secondes : '0' + secondes;
                    if (secondes == 0 || secondes < 0) {
                        if (minutes == 0 && heures == 0 && jours == 0) {
                            area.html("<a style='cursor: pointer;' onclick='window.location.reload(false)'>Terminée</a>");
                            setTimeout(function () {
                                //window.location.href = window.location.href;
                            }, 2000);
                        } else {
                            secondes = 60;
                            minutes = minutes - 1;
                            minutes = (minutes > 10) ? minutes : '0' + minutes;
                            if (minutes == 0 && heures != 0) {
                                minutes = 60;
                                heures = heures - 1;
                                heures = (heures > 10) ? heures : '0' + heures;
                            }
                        }
                    }
                }
            }
        }, 1000);
    });
}

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="confirmation"]').confirmation();
    $("body").scrollspy({
        target: "#navbar-rules",
        offset: 70
    });

    $('.tipFleet').on("mouseover", function() {
        $(this).tooltip('show');
    })

    $('.tipProduct').on("mouseover", function() {
        $(this).tooltip('show');
    })

    $('.tipProduct').on("mouseout", function() {
        $(this).tooltip('hide');
    })

    $(document).click(function() {
        $('.tipFleet').tooltip('hide');
        $("body").find(".modal-backdrop").css("display", "none");
        $("body").find(".popover.confirmation").attr('hidden', 'hidden');
    });

    manageImageForm();
    manageAllyImageForm();
    manageModalContact();
    manageTime();
    manageRegistrationCheck();
    managePlanetChoiceForm();
    console.log("Toute utilisation de scripts sur le jeu seront puni d'un ban permanent, merci.");
});