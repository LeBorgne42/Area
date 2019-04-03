function manageFlightTime(){
    var position = $('#positionFleet').text();
    var galaxy = $('#galaxyFleet').text();
    var speed = $('#speedFleet').text();
    var carburant = 1;

    $('#fleet_send_planete').off('change').on('change',function(e){
        var newPosition = $('#fleet_send_sector').val();
        var newGalaxy = $('#fleet_send_galaxy').val();
        newPosition = newPosition.toString();
        var planete = $('#planeteFleet').text();
        var newPlanete = $('#fleet_send_planete').val();
        newPlanete = newPlanete.toString();
        if (galaxy != newGalaxy) {
            var base = 18;
            var price = 25;
        } else {
            if (position == newPosition) {
                var x1 = (planete - 1) % 5;
                var x2 = (newPlanete - 1) % 5;
                var y1 = (planete - 1) / 5;
                var y2 = (newPlanete - 1) / 5;
            } else {
                var x1 = ((position - 1) % 10) * 3;
                var x2 = ((newPosition - 1) % 10) * 3;
                var y1 = ((position - 1) / 10) * 3;
                var y2 = ((newPosition - 1) / 10) * 3;
            }
            var base = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
            var price = base / 3;
        }
        carburant = Math.round(price * ($('#signatureFleet').text() / 200));
        var now = new Date();
        var travel = new Date();
        travel.setSeconds(travel.getSeconds() + (base * speed * 100)); // 1000 MODE NORMAL
        var date_now = Math.abs((travel - now) / 1000);
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        if (jours > 0) {
            $('#flightTime').text(jours + ' jours ' + heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (heures > 0)
        {
            $('#flightTime').text(heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (minutes > 0)
        {
            $('#flightTime').text(minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (secondes > 0)
        {
            $('#flightTime').text(secondes + ' secondes' + '   Prix : ' + carburant + ' bitcoin');
        }
    });

    $('#fleet_send_sector').off('change').on('change',function(e){
        var newPosition = $('#fleet_send_sector').val();
        var newGalaxy = $('#fleet_send_galaxy').val();
        newPosition = newPosition.toString();
        var planete = $('#planeteFleet').text();
        var newPlanete = $('#fleet_send_planete').val();
        newPlanete = newPlanete.toString();
        if (galaxy != newGalaxy) {
            var base = 18;
            var price = 25;
        } else {
            if (position == newPosition) {
                var x1 = (planete - 1) % 5;
                var x2 = (newPlanete - 1) % 5;
                var y1 = (planete - 1) / 5;
                var y2 = (newPlanete - 1) / 5;
            } else {
                var x1 = ((position - 1) % 10) * 3;
                var x2 = ((newPosition - 1) % 10) * 3;
                var y1 = ((position - 1) / 10) * 3;
                var y2 = ((newPosition - 1) / 10) * 3;
            }
            var base = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
            var price = base / 3;
        }
        carburant = Math.round(price * ($('#signatureFleet').text() / 200));
        var now = new Date();
        var travel = new Date();
        travel.setSeconds(travel.getSeconds() + (base * speed * 100)); // 1000 MODE NORMAL
        var date_now = Math.abs((travel - now) / 1000);
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        if (jours > 0) {
            $('#flightTime').text(jours + ' jours ' + heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (heures > 0)
        {
            $('#flightTime').text(heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (minutes > 0)
        {
            $('#flightTime').text(minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (secondes > 0)
        {
            $('#flightTime').text(secondes + ' secondes' + '   Prix : ' + carburant + ' bitcoin');
        }
    });

    $('#fleet_send_galaxy').off('change').on('change',function(e){
        var newPosition = $('#fleet_send_sector').val();
        var newGalaxy = $('#fleet_send_galaxy').val();
        newPosition = newPosition.toString();
        var planete = $('#planeteFleet').text();
        var newPlanete = $('#fleet_send_planete').val();
        newPlanete = newPlanete.toString();
        if (galaxy != newGalaxy) {
            var base = 18;
            var price = 25;
        } else {
            if (position == newPosition) {
                var x1 = (planete - 1) % 5;
                var x2 = (newPlanete - 1) % 5;
                var y1 = (planete - 1) / 5;
                var y2 = (newPlanete - 1) / 5;
            } else {
                var x1 = ((position - 1) % 10) * 3;
                var x2 = ((newPosition - 1) % 10) * 3;
                var y1 = ((position - 1) / 10) * 3;
                var y2 = ((newPosition - 1) / 10) * 3;
            }
            var base = Math.sqrt(Math.pow((x2 - x1), 2) + Math.pow((y2 - y1), 2));
            var price = base / 3;
        }
        carburant = Math.round(price * ($('#signatureFleet').text() / 200));
        var now = new Date();
        var travel = new Date();
        travel.setSeconds(travel.getSeconds() + (base * speed * 100)); // 1000 MODE NORMAL
        var date_now = Math.abs((travel - now) / 1000);
        var jours = Math.floor(date_now / (60 * 60 * 24));
        var heures = Math.floor((date_now - (jours * 60 * 60 * 24)) / (60 * 60));
        var minutes = Math.floor((date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
        var secondes = Math.floor(date_now - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
        if (jours > 0) {
            $('#flightTime').text(jours + ' jours ' + heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (heures > 0)
        {
            $('#flightTime').text(heures + ' heures ' + minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (minutes > 0)
        {
            $('#flightTime').text(minutes + ' mins ' + secondes + 's' + '   Prix : ' + carburant + ' bitcoin');
        } else if (secondes > 0)
        {
            $('#flightTime').text(secondes + ' secondes' + '   Prix : ' + carburant + ' bitcoin');
        }
    });
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

$(document).ready(function() {
    manageFlightTime();
    manageCoordonate();
    manageAttackFleetForm();
    manageMaxShip();
    manageMaxClick();
    manageRenameFleetForm();
});