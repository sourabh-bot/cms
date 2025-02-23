function hideModal(myModal=null){
    if(myModal){
        myModal.modal('hide')
    }else{
        $('.modal').modal('hide');
    }
}

function showModal(myModal=null){
    if(myModal){
        myModal.modal('show')
    }else{
        $('.modal').modal('show');
    }
}

function resetForm(form = null) {
    let targetForm = form ? form : $("form");

    targetForm[0].reset(); // Resets form elements (default behavior)

    // Handle checkboxes and radio buttons separately
    targetForm.find("input[type='checkbox'], input[type='radio']").prop("checked", false);

    // Reset Select2 or other custom dropdowns
    targetForm.find("select").val("").trigger("change");
}


function showValidationErrors(errors) {
    for (const field in errors) {
        let input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add("is-invalid");
            let errorElement = input.nextElementSibling;
            if (!errorElement || !errorElement.classList.contains("invalid-feedback")) {
                errorElement = document.createElement("div");
                errorElement.classList.add("invalid-feedback");
                input.parentNode.appendChild(errorElement);
            }
            errorElement.innerHTML = errors[field][0];
        }
    }
}

function clearValidationErrors() {
    document.querySelectorAll(".is-invalid").forEach(input => {
        input.classList.remove("is-invalid");
    });

    document.querySelectorAll(".invalid-feedback").forEach(error => {
        error.remove();
    });
}

function setFormValues(formSelector, data) {
    $.each(data, function(name, value) {
        let field = $(formSelector).find('[name="' + name + '"]');
        console.log(field);
        if (field.length) {
            if (field.is(':checkbox')) {
                field.prop('checked', value);
            } else if (field.is(':radio')) {
                $(formSelector).find('[name="' + name + '"][value="' + value + '"]').prop('checked', true);
            } else if(field.attr('type') != 'file'){
                field.val(value);
            }
        }
    });
}


