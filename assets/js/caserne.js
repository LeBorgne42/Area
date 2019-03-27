function manageTotalCaserne() {
    $('.nbrProduct').off('change').on('change',function(e){
        var niobium = 0;
        var product = 0;
        var worker = 0;
        var bitcoin = 0;
        if($('#caserne_recruit_soldier').attr('max') - $('#caserne_recruit_soldier').val() < 0) {
            $('#caserne_recruit_soldier').val($('#caserne_recruit_soldier').attr('max'));
        }
        if($('#caserne_recruit_tank').attr('max') - $('#caserne_recruit_tank').val() < 0) {
            $('#caserne_recruit_tank').val($('#caserne_recruit_tank').attr('max'));
        }
        if($('#caserne_recruit_scientist').attr('max') - $('#caserne_recruit_scientist').val() < 0) {
            $('#caserne_recruit_scientist').val($('#caserne_recruit_scientist').attr('max'));
        }
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
                var delPoint = $(this).find('.bitcoinProduct').text().replace('.', '');
                if (bitcoin == 0) {
                    bitcoin = parseFloat($(this).find('.nbrProduct').val() * delPoint);
                } else {
                    bitcoin = bitcoin + parseFloat($(this).find('.nbrProduct').val() * delPoint);
                }
            }
        });
        var re = new RegExp(',', 'g')
        if(niobium <= parseFloat($('#niobium').text().replace(re, ''))) {
            $('#niobiumProduct').text(niobium);
        } else {
            $('#niobiumProduct').html("<span class='text-rouge'>" + niobium + "</span>");
        }
        if(worker <= parseFloat($('#worker').text().replace(re, ''))) {
            $('#workerProduct').text(worker);
        } else {
            $('#workerProduct').html("<span class='text-rouge'>" + worker + "</span>");
        }
        if(bitcoin <= parseFloat($('#bitcoin').text().replace(re, ''))) {
            $('#bitcoinProduct').text(bitcoin);
        } else {
            $('#bitcoinProduct').html("<span class='text-rouge'>" + bitcoin + "</span>");
        }
    });
}
$(document).ready(function() {
    manageTotalCaserne();
});