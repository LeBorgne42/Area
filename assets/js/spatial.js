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
    manageTotalShip();
});