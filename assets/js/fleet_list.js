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

$(document).ready(function() {
    manageFleetListChoiceForm();
});