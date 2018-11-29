$("form[action='/consultation.php']").submit(function(event){

    if(validateForm($(this))) {
        return true;
    } else {
        return false;
    }

});

$("form[action='/patient_submission.php']").submit(function(event){

    if(validateCancellationForm($(this))) {
        return true;
    } else {
        return false;
    }


});