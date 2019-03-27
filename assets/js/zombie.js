function manageZbMission() {
    $('.zbMission .zbForm').off('change').on('change',function(e){
        var succeedZb = 0;
        var gainZb = 0;
        var soldierZb = 0;
        var tankZb = 0;
        var timeZb = 0;
        var zombie = $('.zombieIndicator').text() * 75;
        var zombieTotal = zombie;
        if($('#mission_soldier').attr('max') - $('#mission_soldier').val() < 0) {
            $('#mission_soldier').val($('#mission_soldier').attr('max'));
        }
        if($('#mission_tank').attr('max') - $('#mission_tank').val() < 0) {
            $('#mission_tank').val($('#mission_tank').attr('max'));
        }
        if($('.zbMission .nbrSoldier').val() > 0) {
            soldierZb = $('.zbMission .nbrSoldier').val();
        }
        if($('.zbMission .nbrTank').val() > 0) {
            tankZb = $('.zbMission .nbrTank').val() * 75;
        }
        zombie = zombie - soldierZb - tankZb;
        if (zombie <= 1) {
            zombie = 1;
        } else {
            zombie = 1 + ((100 * zombie) / zombieTotal) / 100;
        }
        if($('.zbMission .nbrTime').val() > 0) {
            timeZb = $('.zbMission .nbrTime').val();
            if (timeZb == 1) {
                gainZb = -1;
                succeedZb = Math.round(90 / zombie);
            } else if (timeZb == 2) {
                gainZb = -5;
                succeedZb = Math.round(70 / zombie);
            } else if (timeZb == 3) {
                gainZb = -8;
                succeedZb = Math.round(50 / zombie);
            } else if (timeZb == 4) {
                gainZb = -15;
                succeedZb = Math.round(30 / zombie);
            }
        }
        if(succeedZb >= 70) {
            $('.zbMission .zombiePercent').html("<span class='text-vert'>" + succeedZb + "%</span>");
        } else if (succeedZb >= 50) {
            $('.zbMission .zombiePercent').html("<span class='text-orange'>" + succeedZb + "%</span>");
        } else {
            $('.zbMission .zombiePercent').html("<span class='text-rouge'>" + succeedZb + "%</span>");
        }
        $('.zbMission .zombieGain').html("<span class='text-vert'>" + gainZb + "</span>");
    });
}

function manageUraMission() {
    $('.uraMission .zbForm').off('change').on('change',function(e){
        var succeedUra = 0;
        var gainUra = 0;
        var soldierUra = 0;
        var tankUra = 0;
        var timeUra = 0;
        var zombie = $('.zombieIndicator').text() * 75;
        var zombieTotal = zombie;
        if($('#mission_ura_soldier').attr('max') - $('#mission_ura_soldier').val() < 0) {
            $('#mission_ura_soldier').val($('#mission_ura_soldier').attr('max'));
        }
        if($('#mission_ura_tank').attr('max') - $('#mission_ura_tank').val() < 0) {
            $('#mission_ura_tank').val($('#mission_ura_tank').attr('max'));
        }
        if($('.uraMission .nbrSoldier').val() > 0) {
            soldierUra = $('.uraMission .nbrSoldier').val();
        }
        if($('.uraMission .nbrTank').val() > 0) {
            tankUra = $('.uraMission .nbrTank').val() * 75;
        }
        zombie = zombie - soldierUra - tankUra;
        if (zombie <= 1) {
            zombie = 1;
        } else {
            zombie = 1 + ((100 * zombie) / zombieTotal) / 100;
        }
        if($('.uraMission .nbrTime').val() > 0) {
            timeUra = $('.uraMission .nbrTime').val();
            if (timeUra == 1) {
                gainUra = 2;
                succeedUra = Math.round(90 / zombie);
            } else if (timeUra == 2) {
                gainUra = 5;
                succeedUra = Math.round(70 / zombie);
            } else if (timeUra == 3) {
                gainUra = 8;
                succeedUra = Math.round(50 / zombie);
            } else if (timeUra == 4) {
                gainUra = 15;
                succeedUra = Math.round(30 / zombie);
            }
        }
        if(succeedUra >= 70) {
            $('.uraMission .uraniumPercent').html("<span class='text-vert'>" + succeedUra + "%</span>");
        } else if (succeedUra >= 50) {
            $('.uraMission .uraniumPercent').html("<span class='text-orange'>" + succeedUra + "%</span>");
        } else {
            $('.uraMission .uraniumPercent').html("<span class='text-rouge'>" + succeedUra + "%</span>");
        }
        $('.uraMission .uraniumGain').html("<span class='text-vert'>+" + gainUra + "</span>");
    });
}

$(document).ready(function() {
    manageZbMission();
    manageUraMission();
});