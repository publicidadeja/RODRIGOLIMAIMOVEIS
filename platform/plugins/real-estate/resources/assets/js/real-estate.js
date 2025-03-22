$(document).ready(function () {
    $(document).on('change', '#type', event => {
        if ($(event.currentTarget).val() === 'rent') {
            $('#period').closest('.period-form-group').removeClass('hidden').fadeIn();
        } else {
            $('#period').closest('.period-form-group').addClass('hidden').fadeOut();
        }
    });

    $(document).on('change', '#never_expired', event => {
        if ($(event.currentTarget).is(':checked') === true) {
            $('#auto_renew').closest('.auto-renew-form-group').addClass('hidden').fadeOut();
        } else {
            $('#auto_renew').closest('.auto-renew-form-group').removeClass('hidden').fadeIn();
        }
    });
    
    // Toggle de marca d'água - permite ativar/desativar a marca d'água sem sair da página
    // Quando alterado, envia uma requisição AJAX para atualizar a configuração media_watermark_enabled
    // Esta é a mesma configuração que existe em settings/media
    $(document).on('change', '#watermark-toggle', event => {
        let enabled = $(event.currentTarget).is(':checked');
        $.ajax({
            url: route('toggle-watermark'),
            type: 'POST',
            data: {
                enabled: enabled ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: res => {
                if (res.error) {
                    Botble.showError(res.message);
                } else {
                    Botble.showSuccess(res.message);
                }
            },
            error: res => {
                Botble.handleError(res);
            }
        });
    });
});

