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

function manageAttackFleetForm() {
    $('#fleet_attack_attack').off('change').on('change',function(e){
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

/*function setNoDecimalDisplay() {
    console.log($('div.bg-top span.ressource.niobium span.reload').text()+ ($('div.bg-top span.ressource.niobium span.takeProd').text() / 300));
    setInterval(function() {
        var niobium = Math.trunc($('div.bg-top span.ressource.niobium span.reload').text());
        var water =   Math.trunc($('div.bg-top span.ressource.water span.reload').text());
        $('div.bg-top span.ressource.niobium span.reload').text(Math.trunc(niobium + ($('div.bg-top span.ressource.niobium span.takeProd').text() / 300)));
        $('div.bg-top span.ressource.water span.reload').text(Math.trunc(water + ($('div.bg-top span.ressource.water span.takeProd').text() / 300)));
    }, 12000);
}*/

function manageMaxShip() {
    $('#spatial_edit_fleet_moreSonde').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreSonde').attr('max') - $('#spatial_edit_fleet_moreSonde').val() < 0) {
            $('#spatial_edit_fleet_moreSonde').val($('#spatial_edit_fleet_moreSonde').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreColonizer').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreColonizer').attr('max') - $('#spatial_edit_fleet_moreColonizer').val() < 0) {
            $('#spatial_edit_fleet_moreColonizer').val($('#spatial_edit_fleet_moreColonizer').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreRecycleur').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreRecycleur').attr('max') - $('#spatial_edit_fleet_moreRecycleur').val() < 0) {
            $('#spatial_edit_fleet_moreRecycleur').val($('#spatial_edit_fleet_moreRecycleur').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreHunter').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreHunter').attr('max') - $('#spatial_edit_fleet_moreHunter').val() < 0) {
            $('#spatial_edit_fleet_moreHunter').val($('#spatial_edit_fleet_moreHunter').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreFregate').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreFregate').attr('max') - $('#spatial_edit_fleet_moreFregate').val() < 0) {
            $('#spatial_edit_fleet_moreFregate').val($('#spatial_edit_fleet_moreFregate').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreBarge').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreBarge').attr('max') - $('#spatial_edit_fleet_moreBarge').val() < 0) {
            $('#spatial_edit_fleet_moreBarge').val($('#spatial_edit_fleet_moreBarge').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessSonde').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessSonde').attr('max') - $('#spatial_edit_fleet_lessSonde').val() < 0) {
            $('#spatial_edit_fleet_lessSonde').val($('#spatial_edit_fleet_lessSonde').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessColonizer').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessColonizer').attr('max') - $('#spatial_edit_fleet_lessColonizer').val() < 0) {
            $('#spatial_edit_fleet_lessColonizer').val($('#spatial_edit_fleet_lessColonizer').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessRecycleur').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessRecycleur').attr('max') - $('#spatial_edit_fleet_lessRecycleur').val() < 0) {
            $('#spatial_edit_fleet_lessRecycleur').val($('#spatial_edit_fleet_lessRecycleur').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessHunter').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessHunter').attr('max') - $('#spatial_edit_fleet_lessHunter').val() < 0) {
            $('#spatial_edit_fleet_lessHunter').val($('#spatial_edit_fleet_lessHunter').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessFregate').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessFregate').attr('max') - $('#spatial_edit_fleet_lessFregate').val() < 0) {
            $('#spatial_edit_fleet_lessFregate').val($('#spatial_edit_fleet_lessFregate').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessBarge').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessBarge').attr('max') - $('#spatial_edit_fleet_lessBarge').val() < 0) {
            $('#spatial_edit_fleet_lessBarge').val($('#spatial_edit_fleet_lessBarge').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreNiobium').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreNiobium').attr('max') - $('#spatial_edit_fleet_moreNiobium').val() < 0) {
            $('#spatial_edit_fleet_moreNiobium').val(Math.trunc($('#spatial_edit_fleet_moreNiobium').attr('max')));
        }
    });
    $('#spatial_edit_fleet_lessNiobium').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessNiobium').attr('max') - $('#spatial_edit_fleet_lessNiobium').val() < 0) {
            $('#spatial_edit_fleet_lessNiobium').val(Math.trunc($('#spatial_edit_fleet_lessNiobium').attr('max')));
        }
    });
    $('#spatial_edit_fleet_moreWater').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreWater').attr('max') - $('#spatial_edit_fleet_moreWater').val() < 0) {
            $('#spatial_edit_fleet_moreWater').val(Math.trunc($('#spatial_edit_fleet_moreWater').attr('max')));
        }
    });
    $('#spatial_edit_fleet_lessWater').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessWater').attr('max') - $('#spatial_edit_fleet_lessWater').val() < 0) {
            $('#spatial_edit_fleet_lessWater').val(Math.trunc($('#spatial_edit_fleet_lessWater').attr('max')));
        }
    });
    $('#spatial_edit_fleet_moreSoldier').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreSoldier').attr('max') - $('#spatial_edit_fleet_moreSoldier').val() < 0) {
            $('#spatial_edit_fleet_moreSoldier').val(Math.trunc($('#spatial_edit_fleet_moreSoldier').attr('max')));
        }
    });
    $('#spatial_edit_fleet_lessSoldier').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessSoldier').attr('max') - $('#spatial_edit_fleet_lessSoldier').val() < 0) {
            $('#spatial_edit_fleet_lessSoldier').val(Math.trunc($('#spatial_edit_fleet_lessSoldier').attr('max')));
        }
    });
    $('#spatial_edit_fleet_moreWorker').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreWorker').attr('max') - $('#spatial_edit_fleet_moreWorker').val() < 0) {
            $('#spatial_edit_fleet_moreWorker').val(Math.trunc($('#spatial_edit_fleet_moreWorker').attr('max')));
        }
    });
    $('#spatial_edit_fleet_lessWorker').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessWorker').attr('max') - $('#spatial_edit_fleet_lessWorker').val() < 0) {
            $('#spatial_edit_fleet_lessWorker').val(Math.trunc($('#spatial_edit_fleet_lessWorker').attr('max')));
        }
    });
    $('#spatial_edit_fleet_moreScientist').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreScientist').attr('max') - $('#spatial_edit_fleet_moreScientist').val() < 0) {
            $('#spatial_edit_fleet_moreScientist').val(Math.trunc($('#spatial_edit_fleet_moreScientist').attr('max')));
        }
    });
    $('#spatial_edit_fleet_lessScientist').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessScientist').attr('max') - $('#spatial_edit_fleet_lessScientist').val() < 0) {
            $('#spatial_edit_fleet_lessScientist').val(Math.trunc($('#spatial_edit_fleet_lessScientist').attr('max')));
        }
    });
    $('#spatial_fleet_sonde').off('change').on('change',function(e){
        if($('#spatial_fleet_sonde').attr('max') - $('#spatial_fleet_sonde').val() < 0) {
            $('#spatial_fleet_sonde').val($('#spatial_fleet_sonde').attr('max'));
        }
    });
    $('#spatial_fleet_colonizer').off('change').on('change',function(e){
        if($('#spatial_fleet_colonizer').attr('max') - $('#spatial_fleet_colonizer').val() < 0) {
            $('#spatial_fleet_colonizer').val($('#spatial_fleet_colonizer').attr('max'));
        }
    });
    $('#spatial_fleet_recycleur').off('change').on('change',function(e){
        if($('#spatial_fleet_recycleur').attr('max') - $('#spatial_fleet_recycleur').val() < 0) {
            $('#spatial_fleet_recycleur').val($('#spatial_fleet_recycleur').attr('max'));
        }
    });
    $('#spatial_fleet_hunter').off('change').on('change',function(e){
        if($('#spatial_fleet_hunter').attr('max') - $('#spatial_fleet_hunter').val() < 0) {
            $('#spatial_fleet_hunter').val($('#spatial_fleet_hunter').attr('max'));
        }
    });
    $('#spatial_fleet_fregate').off('change').on('change',function(e){
        if($('#spatial_fleet_fregate').attr('max') - $('#spatial_fleet_fregate').val() < 0) {
            $('#spatial_fleet_fregate').val($('#spatial_fleet_fregate').attr('max'));
        }
    });
    $('#spatial_fleet_barge').off('change').on('change',function(e){
        if($('#spatial_fleet_barge').attr('max') - $('#spatial_fleet_barge').val() < 0) {
            $('#spatial_fleet_barge').val($('#spatial_fleet_barge').attr('max'));
        }
    });
}

function manageConstructTime() {
    var build = new Date($('#timeConstruct').text());
    var now = new Date();
    var date_now = Math.abs(build - now) / 1000;
    var jours = Math.floor(date_now / (60 * 60 * 24));
    var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
    var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
    var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
    if (date_now > 0) {
        setInterval(function() {
            if (jours > 0) {
                $('#timeDisplay').text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's');
            } else if (heures > 0)
            {
                $('#timeDisplay').text(heures + 'heures ' + minutes + 'mins ' + secondes + 's');
            } else if (minutes > 0)
            {
                $('#timeDisplay').text(minutes + 'mins ' + secondes + 's');
            } else if (secondes > 0)
            {
                $('#timeDisplay').text(secondes + ' secondes');
            }
            secondes = secondes - 1;
            if(secondes == 0) {
                if(minutes == null) {
                    $('#timeDisplay').text('Terminée');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    secondes = 60;
                    minutes = minutes - 1;
                    if(minutes == 0 && heures != 0) {
                        minutes = 60;
                        heures = heures - 1;
                    } else if (minutes == 0) {
                        minutes = null;
                    }
                }
            }
        }, 1000);
    }
}

function manageResearchTime() {
    var build = new Date($('#timeResearch').text());
    var now = new Date();
    var date_now = Math.abs(build - now) / 1000;
    var jours = Math.floor(date_now / (60 * 60 * 24));
    var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
    var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
    var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
    if (date_now > 0) {
        setInterval(function() {
            if (jours > 0) {
                $('#timeDisplayR').text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's');
            } else if (heures > 0)
            {
                $('#timeDisplayR').text(heures + 'heures ' + minutes + 'mins ' + secondes + 's');
            } else if (minutes > 0)
            {
                $('#timeDisplayR').text(minutes + 'mins ' + secondes + 's');
            } else if (secondes > 0)
            {
                $('#timeDisplayR').text(secondes + ' secondes');
            }
            secondes = secondes - 1;
            if(secondes == 0) {
                if(minutes == null) {
                    $('#timeDisplayR').text('Terminée');
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                } else {
                    secondes = 60;
                    minutes = minutes - 1;
                    if(minutes == 0 && heures != 0) {
                        minutes = 60;
                        heures = heures - 1;
                    } else if (minutes == 0) {
                        minutes = null;
                    }
                }
            }
        }, 1000);
    }
}

function manageSalon() {
/*    $('#salon_sendForm').click(function(e) {
        e.preventDefault();

        var content = $('#salon_content').val();
        $form = $('#salon_content').closest('form');
        if (content != "") {
            $.ajax({
               url: $form.attr('action'),
               type: "POST",
                data: 'newMessage=' + content,
            });
            window.location.reload();
        }
    });*/
    $('.chat-defil').scrollTop(2000);
}

/*function manageDisplaySalon(){

   setTimeout( function(){

        $.ajax({
            url : document.location.href,
            type : "GET",
            success : function(data){
                console.log(data.name);
                console.log(data.id);
            }
        });

        manageDisplaySalon();

    }, 1000);

}*/

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
    manageMaxShip();
    manageConstructTime();
    manageResearchTime();
    manageSalon();
    /*    manageDisplaySalon();
        setNoDecimalDisplay();*/
    manageAttackFleetForm();
    console.log("Toute utilisation de scripts sur le jeu seront puni d'un ban permanent, merci.");
});