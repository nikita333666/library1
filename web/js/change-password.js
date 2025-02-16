$(document).ready(function() {
    $('#change-password-form').on('beforeSubmit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var errorAlert = form.find('.alert-danger');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    errorAlert.html(response.message);
                    if (response.errors) {
                        $.each(response.errors, function(attribute, errors) {
                            var input = form.find('[name="ChangePasswordForm[' + attribute + ']"]');
                            input.closest('.form-group').addClass('has-error');
                            var errorHtml = errors.join('<br>');
                            input.closest('.form-group').find('.help-block').html(errorHtml);
                        });
                    }
                    errorAlert.show();
                }
            },
            error: function() {
                errorAlert.html('Произошла ошибка при отправке формы').show();
            }
        });
        return false;
    });
    
    $('#changePasswordModal').on('hidden.bs.modal', function() {
        var form = $('#change-password-form');
        form.find('.alert-danger').hide();
        form.find('.has-error').removeClass('has-error');
        form.find('.help-block').html('');
        form[0].reset();
    });
});
