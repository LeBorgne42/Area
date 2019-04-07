function manageClickShip() {
    $('.hunterClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="hunter"]').removeClass('hidden');
    });
    $('.hunterHeavyClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="hunterHeavy"]').removeClass('hidden');
    });
    $('.hunterWarClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="hunterWar"]').removeClass('hidden');
    });
    $('.corvetClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="corvet"]').removeClass('hidden');
    });
    $('.corvetLaserClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="corvetLaser"]').removeClass('hidden');
    });
    $('.corvetWarClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="corvetWar"]').removeClass('hidden');
    });
    $('.fregateClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="fregate"]').removeClass('hidden');
    });
    $('.fregatePlasmaClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="fregatePlasma"]').removeClass('hidden');
    });
    $('.croiserClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="croiser"]').removeClass('hidden');
    });
    $('.ironCladClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="ironClad"]').removeClass('hidden');
    });
    $('.destroyerClick').off('click').on('click',function(e){
        $('.persoFleet tr').addClass('hidden');
        $('tr[name="destroyer"]').removeClass('hidden');
    });
}

function managePointShip() {
    $('tr[name="hunter"] input').off('change').on('change',function(e){

        var total = $('tr[name="hunter"] .totalExec').text();
        var points = 0;
        $('tr[name="hunter"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="hunter"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="hunter"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="hunter"] td:eq(1)').text()) {
                if ($('tr[name="hunter"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="hunter"] td:eq(7)').text()) {
                if ($('tr[name="hunter"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="hunter"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="hunterHeavy"] input').off('change').on('change',function(e){

        var total = $('tr[name="hunterHeavy"] .totalExec').text();
        var points = 0;
        $('tr[name="hunterHeavy"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="hunterHeavy"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="hunterHeavy"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="hunterHeavy"] td:eq(1)').text()) {
                if ($('tr[name="hunterHeavy"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="hunterHeavy"] td:eq(7)').text()) {
                if ($('tr[name="hunterHeavy"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="hunterHeavy"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="hunterWar"] input').off('change').on('change',function(e){

        var total = $('tr[name="hunterWar"] .totalExec').text();
        var points = 0;
        $('tr[name="hunterWar"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="hunterWar"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="hunterWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = Math.abs($(this).parent().prev().find('.addPoint').text()) - 1;
            } else {
                cumul = Math.abs($(this).parent().prev().find('.addPoint').text()) + 1;
            }
            if ($(this).parent().prev().text() == $('tr[name="hunterWar"] td:eq(1)').text()) {
                if ($('tr[name="hunterWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="hunterWar"] td:eq(7)').text()) {
                if ($('tr[name="hunterWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="hunterWar"] td:eq(9)').text()) {
                if ($('tr[name="hunterWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="hunterWar"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="corvet"] input').off('change').on('change',function(e){

        var total = $('tr[name="corvet"] .totalExec').text();
        var points = 0;
        $('tr[name="corvet"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="corvet"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="corvet"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="corvet"] td:eq(1)').text()) {
                if ($('tr[name="corvet"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvet"] td:eq(7)').text()) {
                if ($('tr[name="corvet"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvet"] td:eq(9)').text()) {
                if ($('tr[name="corvet"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="corvet"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="corvetLaser"] input').off('change').on('change',function(e){

        var total = $('tr[name="corvetLaser"] .totalExec').text();
        var points = 0;
        $('tr[name="corvetLaser"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="corvetLaser"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="corvetLaser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="corvetLaser"] td:eq(1)').text()) {
                if ($('tr[name="corvetLaser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvetLaser"] td:eq(7)').text()) {
                if ($('tr[name="corvetLaser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvetLaser"] td:eq(9)').text()) {
                if ($('tr[name="corvetLaser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="corvetLaser"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="corvetWar"] input').off('change').on('change',function(e){

        var total = $('tr[name="corvetWar"] .totalExec').text();
        var points = 0;
        $('tr[name="corvetWar"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="corvetWar"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="corvetWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="corvetWar"] td:eq(1)').text()) {
                if ($('tr[name="corvetWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvetWar"] td:eq(7)').text()) {
                if ($('tr[name="corvetWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="corvetWar"] td:eq(9)').text()) {
                if ($('tr[name="corvetWar"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="corvetWar"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="fregate"] input').off('change').on('change',function(e){

        var total = $('tr[name="fregate"] .totalExec').text();
        var points = 0;
        $('tr[name="fregate"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="fregate"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="fregate"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="fregate"] td:eq(1)').text()) {
                if ($('tr[name="fregate"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="fregate"] td:eq(7)').text()) {
                if ($('tr[name="fregate"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="fregate"] td:eq(9)').text()) {
                if ($('tr[name="fregate"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="fregate"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="fregatePlasma"] input').off('change').on('change',function(e){

        var total = $('tr[name="fregatePlasma"] .totalExec').text();
        var points = 0;
        $('tr[name="fregatePlasma"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="fregatePlasma"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="fregatePlasma"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="fregatePlasma"] td:eq(1)').text()) {
                if ($('tr[name="fregatePlasma"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="fregatePlasma"] td:eq(7)').text()) {
                if ($('tr[name="fregatePlasma"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="fregatePlasma"] td:eq(9)').text()) {
                if ($('tr[name="fregatePlasma"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="fregatePlasma"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="croiser"] input').off('change').on('change',function(e){

        var total = $('tr[name="croiser"] .totalExec').text();
        var points = 0;
        $('tr[name="croiser"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="croiser"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="croiser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="croiser"] td:eq(1)').text()) {
                if ($('tr[name="croiser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="croiser"] td:eq(7)').text()) {
                if ($('tr[name="croiser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="croiser"] td:eq(9)').text()) {
                if ($('tr[name="croiser"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="croiser"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="ironClad"] input').off('change').on('change',function(e){

        var total = $('tr[name="ironClad"] .totalExec').text();
        var points = 0;
        $('tr[name="ironClad"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="ironClad"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="ironClad"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="ironClad"] td:eq(1)').text()) {
                if ($('tr[name="ironClad"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="ironClad"] td:eq(7)').text()) {
                if ($('tr[name="ironClad"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="ironClad"] td:eq(9)').text()) {
                if ($('tr[name="ironClad"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="ironClad"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
    $('tr[name="destroyer"] input').off('change').on('change',function(e){

        var total = $('tr[name="destroyer"] .totalExec').text();
        var points = 0;
        $('tr[name="destroyer"] input[type="number"]').each(function() { points = points + Math.abs($(this).val()); });

        if (total - points < 0) {
            $('tr[name="destroyer"] .totalPoints').text(0);
            $(this).val($(this).val() - 1);
        } else {
            var val = $(this).val() != 0 ? $(this).val() : 1;
            if ($('tr[name="destroyer"] .totalPoints').text() < total - points || $(this).val() == 0) {
                cumul = -1 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
            } else {
                cumul = 1 * val + Math.abs($(this).parent().prev().find('.basis').text());
            }
            if ($(this).parent().prev().text() == $('tr[name="destroyer"] td:eq(1)').text()) {
                if ($('tr[name="destroyer"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -5 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 5 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="destroyer"] td:eq(7)').text()) {
                if ($('tr[name="destroyer"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -3 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 3 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            } else if ($(this).parent().prev().text() == $('tr[name="destroyer"] td:eq(9)').text()) {
                if ($('tr[name="destroyer"] .totalPoints').text() < total - points || $(this).val() == 0) {
                    cumul = -2 * val + Math.abs($(this).parent().prev().find('.addPoint').text());
                } else {
                    cumul = 2 * val + Math.abs($(this).parent().prev().find('.basis').text());
                }
            }
            $('tr[name="destroyer"] .totalPoints').text(total - points);
            $(this).parent().prev().find('.addPoint').html("<span class='text-vert'>" + cumul + "</span>");
        }
    });
}

$(document).ready(function() {
    managePointShip();
    manageClickShip();
});