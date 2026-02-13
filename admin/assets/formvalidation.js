// required jquery
$(document).ready(() => {
    const forms = $('.needs-validation');
    forms.each((index, form) => {
        $(form).on('submit', e => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            $(form).addClass('was-validated');
        });
    });
})