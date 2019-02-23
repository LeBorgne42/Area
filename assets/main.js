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

function manageAttackFleetForm() {
    $('#fleet_attack_attack').off('change').on('change',function(e){
        e.preventDefault();

        var formContent = $(this).closest('form')[0][1].checked;
            content = formContent ? 1 : 0;
        $form = $(this).closest('form');
        $.ajax({
            url: $form.attr('action'),
            type: "POST",
            data: {name: 'attack', data: content},
            success: function(response){
                if(response.war == true) {
                    location = location;
                }
            }
        });
        //$(this).closest('form').submit();
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

function manageFleetListChoiceForm() {
    $('.fleetList_add').off('change').on('change',function(e){
        e.preventDefault();

        var actionForm = $(this).attr('action');
        fleetChoice = $(this)[0][0].value;

        actionForm = actionForm.replace(/\/\d$/, '/' + fleetChoice);
        console.log(actionForm);
        $(this).attr('action', actionForm)
        $(this).closest('form').submit();
    });
}

function manageRenameFleetForm() {
    $('#fleet_rename_sendForm').off('click').on('click',function(e){
        e.preventDefault();

        var content = $(this).closest('form')[0][0].value;
        $form = $(this).closest('form');
        $.ajax({
            url: $form.attr('action'),
            type: "POST",
            data: {name: 'name', data: content}
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

function manageMaxClick() {
    $('.maxInput').off('click').on('click',function(e){
        var parent = $(this).parent().parent();
        parent.find('input:first').val(parent.find('input:first').attr('max'));
    });
    $('.maxInputLess').off('click').on('click',function(e){
        var parent = $(this).parent().parent();
        parent.find('input:eq(1)').val(parent.find('input:eq(1)').attr('max'));
    });
    $('.maxInputR').off('click').on('click',function(e){
        var parent = $(this).parent().parent();
        parent.find('input:first').val(parent.find('input:first').attr('max'));
    });
    $('.maxInputLessR').off('click').on('click',function(e){
        var parent = $(this).parent().parent();
        parent.find('input:eq(1)').val(parent.find('input:eq(1)').attr('max'));
    });
    $('.addAllShip').off('click').on('click',function(e){
        $('.maxInput').each( function(){
            var parent = $(this).parent().parent();
            parent.find('input:first').val(parent.find('input:first').attr('max'));
        });
    });
    $('.removeAllShip').off('click').on('click',function(e){
        $('.maxInputLess').each( function(){
            var parent = $(this).parent().parent();
            parent.find('input:eq(1)').val(parent.find('input:eq(1)').attr('max'));
        });
    });
    $('.addAllShipR').off('click').on('click',function(e){
        $('.maxInputR').each( function(){
            var parent = $(this).parent().parent();
            parent.find('input:first').val(parent.find('input:first').attr('max'));
        });
    });
    $('.removeAllShipR').off('click').on('click',function(e){
        $('.maxInputLessR').each( function(){
            var parent = $(this).parent().parent();
            parent.find('input:eq(1)').val(parent.find('input:eq(1)').attr('max'));
        });
    });
}

function manageTotalShip() {
    $('.nbrProduct').off('change').on('change',function(e){
        var niobium = 0;
        var water = 0;
        var worker = 0;
        var soldier = 0;
        var bitcoin = 0;
        var pdg = 0;
        var product = 0;
        $('.nbrProduct').each( function(){
            if($(this).val() > 0) {
                if (product == 0) {
                    product = $(this).val();
                } else {
                    product = parseFloat(product) + parseFloat($(this).val());
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.niobiumProduct').text().replace('.', '');
                if (niobium == 0) {
                    niobium = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    niobium = niobium + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.waterProduct').text().replace('.', '');
                if (water == 0) {
                    water = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    water = water + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.workerProduct').text().replace('.', '');
                if (worker == 0) {
                    worker = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    worker = worker + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.soldierProduct').text().replace('.', '');
                if (soldier == 0) {
                    soldier = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    soldier = soldier + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.bitcoinProduct').text().replace('.', '');
                if (bitcoin == 0) {
                    bitcoin = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    bitcoin = bitcoin + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        $('tr').each( function(){
            if($(this).find('.nbrProduct').val() > 0) {
                var delPoint = $(this).find('.pdgProduct').text().replace('.', '');
                if (product == 0) {
                    pdg = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    pdg = pdg + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        var re = new RegExp(',', 'g')
        if(niobium <= parseFloat($('#niobium').text().replace(re, ''))) {
            $('#niobiumProduct').text(niobium);
        } else {
            $('#niobiumProduct').html("<span class='text-rouge'>" + niobium + "</span>");
        }
        if(water <= parseFloat($('#water').text().replace(re, ''))) {
            $('#waterProduct').text(water);
        } else {
            $('#waterProduct').html("<span class='text-rouge'>" + water + "</span>");
        }
        if(worker <= parseFloat($('#worker').text().replace(re, ''))) {
            $('#workerProduct').text(worker);
        } else {
            $('#workerProduct').html("<span class='text-rouge'>" + worker + "</span>");
        }
        if(soldier <= parseFloat($('#soldier').text().replace(re, ''))) {
            $('#soldierProduct').text(soldier);
        } else {
            $('#soldierProduct').html("<span class='text-rouge'>" + soldier + "</span>");
        }
        if(bitcoin <= parseFloat($('#bitcoin').text().replace(re, ''))) {
            $('#bitcoinProduct').text(bitcoin);
        } else {
            $('#bitcoinProduct').html("<span class='text-rouge'>" + bitcoin + "</span>");
        }
        if(pdg <= parseFloat($('#pdg').text().replace(re, ''))) {
            $('#pdgProduct').text(pdg);
        } else {
            $('#pdgProduct').html("<span class='text-rouge'>" + pdg + "</span>");
        }
        $('#nbrProduct').text(product);
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
    $('#spatial_edit_fleet_moreCargoI').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCargoI').attr('max') - $('#spatial_edit_fleet_moreCargoI').val() < 0) {
            $('#spatial_edit_fleet_moreCargoI').val($('#spatial_edit_fleet_moreCargoI').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreCargoV').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCargoV').attr('max') - $('#spatial_edit_fleet_moreCargoV').val() < 0) {
            $('#spatial_edit_fleet_moreCargoV').val($('#spatial_edit_fleet_moreCargoV').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreCargoX').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCargoX').attr('max') - $('#spatial_edit_fleet_moreCargoX').val() < 0) {
            $('#spatial_edit_fleet_moreCargoX').val($('#spatial_edit_fleet_moreCargoX').attr('max'));
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
    $('#spatial_edit_fleet_moreHunterHeavy').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreHunterHeavy').attr('max') - $('#spatial_edit_fleet_moreHunterHeavy').val() < 0) {
            $('#spatial_edit_fleet_moreHunterHeavy').val($('#spatial_edit_fleet_moreHunterHeavy').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreCorvet').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCorvet').attr('max') - $('#spatial_edit_fleet_moreCorvet').val() < 0) {
            $('#spatial_edit_fleet_moreCorvet').val($('#spatial_edit_fleet_moreCorvet').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreCorvetLaser').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCorvetLaser').attr('max') - $('#spatial_edit_fleet_moreCorvetLaser').val() < 0) {
            $('#spatial_edit_fleet_moreCorvetLaser').val($('#spatial_edit_fleet_moreCorvetLaser').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreFregate').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreFregate').attr('max') - $('#spatial_edit_fleet_moreFregate').val() < 0) {
            $('#spatial_edit_fleet_moreFregate').val($('#spatial_edit_fleet_moreFregate').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreFregatePlasma').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreFregatePlasma').attr('max') - $('#spatial_edit_fleet_moreFregatePlasma').val() < 0) {
            $('#spatial_edit_fleet_moreFregatePlasma').val($('#spatial_edit_fleet_moreFregatePlasma').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreCroiser').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreCroiser').attr('max') - $('#spatial_edit_fleet_moreCroiser').val() < 0) {
            $('#spatial_edit_fleet_moreCroiser').val($('#spatial_edit_fleet_moreCroiser').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreIronClad').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreIronClad').attr('max') - $('#spatial_edit_fleet_moreIronClad').val() < 0) {
            $('#spatial_edit_fleet_moreIronClad').val($('#spatial_edit_fleet_moreIronClad').attr('max'));
        }
    });
    $('#spatial_edit_fleet_moreDestroyer').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_moreDestroyer').attr('max') - $('#spatial_edit_fleet_moreDestroyer').val() < 0) {
            $('#spatial_edit_fleet_moreDestroyer').val($('#spatial_edit_fleet_moreDestroyer').attr('max'));
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
    $('#spatial_edit_fleet_lessCargoI').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCargoI').attr('max') - $('#spatial_edit_fleet_lessCargoI').val() < 0) {
            $('#spatial_edit_fleet_lessCargoI').val($('#spatial_edit_fleet_lessCargoI').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessCargoV').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCargoV').attr('max') - $('#spatial_edit_fleet_lessCargoV').val() < 0) {
            $('#spatial_edit_fleet_lessCargoV').val($('#spatial_edit_fleet_lessCargoV').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessCargoX').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCargoX').attr('max') - $('#spatial_edit_fleet_lessCargoX').val() < 0) {
            $('#spatial_edit_fleet_lessCargoX').val($('#spatial_edit_fleet_lessCargoX').attr('max'));
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
    $('#spatial_edit_fleet_lessHunterHeavy').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessHunterHeavy').attr('max') - $('#spatial_edit_fleet_lessHunterHeavy').val() < 0) {
            $('#spatial_edit_fleet_lessHunterHeavy').val($('#spatial_edit_fleet_lessHunterHeavy').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessCorvet').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCorvet').attr('max') - $('#spatial_edit_fleet_lessCorvet').val() < 0) {
            $('#spatial_edit_fleet_lessCorvet').val($('#spatial_edit_fleet_lessCorvet').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessCorvetLaser').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCorvetLaser').attr('max') - $('#spatial_edit_fleet_lessCorvetLaser').val() < 0) {
            $('#spatial_edit_fleet_lessCorvetLaser').val($('#spatial_edit_fleet_lessCorvetLaser').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessFregate').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessFregate').attr('max') - $('#spatial_edit_fleet_lessFregate').val() < 0) {
            $('#spatial_edit_fleet_lessFregate').val($('#spatial_edit_fleet_lessFregate').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessFregatePlasma').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessFregatePlasma').attr('max') - $('#spatial_edit_fleet_lessFregatePlasma').val() < 0) {
            $('#spatial_edit_fleet_lessFregatePlasma').val($('#spatial_edit_fleet_lessFregatePlasma').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessCroiser').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessCroiser').attr('max') - $('#spatial_edit_fleet_lessCroiser').val() < 0) {
            $('#spatial_edit_fleet_lessCroiser').val($('#spatial_edit_fleet_lessCroiser').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessIronClad').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessIronClad').attr('max') - $('#spatial_edit_fleet_lessIronClad').val() < 0) {
            $('#spatial_edit_fleet_lessIronClad').val($('#spatial_edit_fleet_lessIronClad').attr('max'));
        }
    });
    $('#spatial_edit_fleet_lessDestroyer').off('change').on('change',function(e){
        if($('#spatial_edit_fleet_lessDestroyer').attr('max') - $('#spatial_edit_fleet_lessDestroyer').val() < 0) {
            $('#spatial_edit_fleet_lessDestroyer').val($('#spatial_edit_fleet_lessDestroyer').attr('max'));
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
    $('#spatial_fleet_cargoI').off('change').on('change',function(e){
        if($('#spatial_fleet_cargoI').attr('max') - $('#spatial_fleet_cargoI').val() < 0) {
            $('#spatial_fleet_cargoI').val($('#spatial_fleet_cargoI').attr('max'));
        }
    });
    $('#spatial_fleet_cargoV').off('change').on('change',function(e){
        if($('#spatial_fleet_cargoV').attr('max') - $('#spatial_fleet_cargoV').val() < 0) {
            $('#spatial_fleet_cargoV').val($('#spatial_fleet_cargoV').attr('max'));
        }
    });
    $('#spatial_fleet_cargoX').off('change').on('change',function(e){
        if($('#spatial_fleet_cargoX').attr('max') - $('#spatial_fleet_cargoX').val() < 0) {
            $('#spatial_fleet_cargoX').val($('#spatial_fleet_cargoX').attr('max'));
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
    $('#spatial_fleet_hunterHeavy').off('change').on('change',function(e){
        if($('#spatial_fleet_hunterHeavy').attr('max') - $('#spatial_fleet_hunterHeavy').val() < 0) {
            $('#spatial_fleet_hunterHeavy').val($('#spatial_fleet_hunterHeavy').attr('max'));
        }
    });
    $('#spatial_fleet_corvet').off('change').on('change',function(e){
        if($('#spatial_fleet_corvet').attr('max') - $('#spatial_fleet_corvet').val() < 0) {
            $('#spatial_fleet_corvet').val($('#spatial_fleet_corvet').attr('max'));
        }
    });
    $('#spatial_fleet_corvetLaser').off('change').on('change',function(e){
        if($('#spatial_fleet_corvetLaser').attr('max') - $('#spatial_fleet_corvetLaser').val() < 0) {
            $('#spatial_fleet_corvetLaser').val($('#spatial_fleet_corvetLaser').attr('max'));
        }
    });
    $('#spatial_fleet_fregate').off('change').on('change',function(e){
        if($('#spatial_fleet_fregate').attr('max') - $('#spatial_fleet_fregate').val() < 0) {
            $('#spatial_fleet_fregate').val($('#spatial_fleet_fregate').attr('max'));
        }
    });
    $('#spatial_fleet_fregatePlasma').off('change').on('change',function(e){
        if($('#spatial_fleet_fregatePlasma').attr('max') - $('#spatial_fleet_fregatePlasma').val() < 0) {
            $('#spatial_fleet_fregatePlasma').val($('#spatial_fleet_fregatePlasma').attr('max'));
        }
    });
    $('#spatial_fleet_croiser').off('change').on('change',function(e){
        if($('#spatial_fleet_croiser').attr('max') - $('#spatial_fleet_croiser').val() < 0) {
            $('#spatial_fleet_croiser').val($('#spatial_fleet_croiser').attr('max'));
        }
    });
    $('#spatial_fleet_ironClad').off('change').on('change',function(e){
        if($('#spatial_fleet_ironClad').attr('max') - $('#spatial_fleet_ironClad').val() < 0) {
            $('#spatial_fleet_ironClad').val($('#spatial_fleet_ironClad').attr('max'));
        }
    });
    $('#spatial_fleet_destroyer').off('change').on('change',function(e){
        if($('#spatial_fleet_destroyer').attr('max') - $('#spatial_fleet_destroyer').val() < 0) {
            $('#spatial_fleet_destroyer').val($('#spatial_fleet_destroyer').attr('max'));
        }
    });
    $('#spatial_fleet_barge').off('change').on('change',function(e){
        if($('#spatial_fleet_barge').attr('max') - $('#spatial_fleet_barge').val() < 0) {
            $('#spatial_fleet_barge').val($('#spatial_fleet_barge').attr('max'));
        }
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
        setInterval(function() {
            if (build < now) {
                area.html("<a style='cursor: pointer;' onclick='window.location.reload(false)'>Terminée</a>");
                area.removeAttr('hidden');
                setTimeout(function () {
                }, 2000);
            } else {
                if (date_now > 0) {
                    if (jours > 0) {
                        area.text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's');
                        area.removeAttr('hidden');
                    } else if (heures > 0) {
                        area.text(heures + 'heures ' + minutes + 'mins ' + secondes + 's');
                        area.removeAttr('hidden');
                    } else if (minutes > 0) {
                        area.text(minutes + 'mins ' + secondes + 's');
                        area.removeAttr('hidden');
                    } else if (secondes > 0) {
                        area.text(secondes + ' secondes');
                        area.removeAttr('hidden');
                    }
                    secondes = secondes - 1;
                    if (secondes == 0 || secondes < 0) {
                        if (minutes == 0 && heures == 0 && jours == 0) {
                            area.html("<a style='cursor: pointer;' onclick='window.location.reload(false)'>Terminée</a>");
                            setTimeout(function () {
                                //window.location.href = window.location.href;
                            }, 2000);
                        } else {
                            secondes = 60;
                            minutes = minutes - 1;
                            if (minutes == 0 && heures != 0) {
                                minutes = 60;
                                heures = heures - 1;
                            }
                        }
                    }
                }
            }
        }, 1000);
    });
}

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

function manageCoordonate(){
    $('#fleet_send_planet').off('change').on('change',function(e){
        if($('#fleet_send_planet').val() != '') {
            $('#fleet_send_planete').attr('disabled', 'disabled');
            $('#fleet_send_sector').attr('disabled', 'disabled');
        } else {
            $('#fleet_send_planete').removeAttr('disabled', 'disabled');
            $('#fleet_send_sector').removeAttr('disabled', 'disabled');
        }
    });
}

function manageFlightTime(){
    var position = $('#positionFleet').text();
    var speed = $('#speedFleet').text();
    var base = 1000;
    var price = 1;
    var carburant = 1;
    $('#fleet_send_planete').off('change').on('change',function(e){
        var newPosition = $('#fleet_send_sector').val();
        newPosition = newPosition.toString();
        var planete = $('#planeteFleet').text();
        var newPlanete = $('#fleet_send_planete').val();
        newPlanete = newPlanete.toString();
        var x1 = (position % 10) * 5 + (planete % 5);
        var x2 = (newPosition % 10) * 5 + (newPlanete % 5);
        var y1 = (position / 10) * 5 + (planete % 5);
        var y2 = (newPosition / 10) * 5 + (newPlanete % 5);
        var base = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
        var price = base / 3;
        carburant = Math.round(price * ($('#signatureFleet').text() / 200));
        var now = new Date();
        var travel = new Date();
        travel.setSeconds(travel.getSeconds() + (base * speed * 500));
        var date_now = Math.abs((travel - now) / 1000);
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        if (jours > 0) {
            $('#flightTime').text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (heures > 0)
        {
            $('#flightTime').text(heures + 'heures ' + minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (minutes > 0)
        {
            $('#flightTime').text(minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (secondes > 0)
        {
            $('#flightTime').text(secondes + ' secondes' + '   Prix : ' + carburant + ' bitcoin');
        }
    });

    $('#fleet_send_sector').off('change').on('change',function(e){
        var newPosition = $('#fleet_send_sector').val();
        newPosition = newPosition.toString();
        var planete = $('#planeteFleet').text();
        var newPlanete = $('#fleet_send_planete').val();
        newPlanete = newPlanete.toString();
        var x1 = (position % 10) * 5 + (planete % 5);
        var x2 = (newPosition % 10) * 5 + (newPlanete % 5);
        var y1 = (position / 10) * 5 + (planete % 5);
        var y2 = (newPosition / 10) * 5 + (newPlanete % 5);
        var base = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
        var price = base / 3;
        carburant = Math.round(price * ($('#signatureFleet').text() / 200));
        var now = new Date();
        var travel = new Date();
        travel.setSeconds(travel.getSeconds() + (base * speed * 500));
        var date_now = Math.abs((travel - now) / 1000);
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        if (jours > 0) {
            $('#flightTime').text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (heures > 0)
        {
            $('#flightTime').text(heures + 'heures ' + minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (minutes > 0)
        {
            $('#flightTime').text(minutes + 'mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (secondes > 0)
        {
            $('#flightTime').text(secondes + ' secondes' + '   Prix : ' + carburant + ' bitcoin');
        }
    });
    var now = new Date();
    var travel = new Date();
    travel.setSeconds(travel.getSeconds() + (base * speed));
    var date_now = Math.abs((travel - now) / 1000);
    var jours = Math.floor(date_now / (60 * 60 * 24));
    var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
    var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
    var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
    if (jours > 0) {
        $('#flightTime').text(jours + 'j ' + heures + 'heures ' + minutes + 'mins ' + secondes + 's');
    } else if (heures > 0)
    {
        $('#flightTime').text(heures + 'heures ' + minutes + 'mins ' + secondes + 's');
    } else if (minutes > 0)
    {
        $('#flightTime').text(minutes + 'mins ' + secondes + 's');
    } else if (secondes > 0)
    {
        $('#flightTime').text(secondes + ' secondes');
    }
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

    $(document).click(function() {
        $('.tipFleet').tooltip('hide');
        $("body").find(".modal-backdrop").css("display", "none");
        $("body").find(".popover.confirmation").attr('hidden', 'hidden');
    });

    manageImageForm();
    manageAllyImageForm();
    manageModalContact();
    manageMaxShip();
    manageMaxClick();
    manageTotalShip();
    manageCoordonate();
    manageFlightTime();
    manageTime();
    manageDisplaySalon();
    manageSalon();
    manageAttackFleetForm();
    manageRegistrationCheck();
    manageRenameFleetForm();
    managePlanetChoiceForm();
    manageFleetListChoiceForm();
    console.log("Toute utilisation de scripts sur le jeu seront puni d'un ban permanent, merci.");
});