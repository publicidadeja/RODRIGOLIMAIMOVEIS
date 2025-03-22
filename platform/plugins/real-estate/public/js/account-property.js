/**
 * Manipula a funcionalidade de toggle de marca d'água na área do corretor
 */
$(document).ready(function () {
    console.log('Account property script loaded!');
    
    // Toggle de marca d'água - permite ativar/desativar a marca d'água sem sair da página
    // Quando alterado, envia uma requisição AJAX para atualizar a configuração media_watermark_enabled
    // Esta é a mesma configuração que existe em settings/media
    $(document).on('change', '#watermark-toggle', event => {
        console.log('Toggle clicked: ' + $(event.currentTarget).is(':checked'));
        let enabled = $(event.currentTarget).is(':checked');
        $.ajax({
            url: $('#admin_panel_url').data('url') + '/real-estate/toggle-watermark', // Usa a mesma URL da área admin
            type: 'POST',
            data: {
                enabled: enabled ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: res => {
                console.log('Success response:', res);
                if (res.error) {
                    if (typeof Srapid !== 'undefined') {
                        Srapid.showError(res.message);
                    } else if (typeof Botble !== 'undefined') {
                        Botble.showError(res.message);
                    } else {
                        alert(res.message);
                    }
                } else {
                    if (typeof Srapid !== 'undefined') {
                        Srapid.showSuccess(res.message);
                    } else if (typeof Botble !== 'undefined') {
                        Botble.showSuccess(res.message);
                    } else {
                        alert(res.message);
                    }
                }
            },
            error: res => {
                console.log('Error response:', res);
                if (typeof Srapid !== 'undefined') {
                    Srapid.handleError(res);
                } else if (typeof Botble !== 'undefined') {
                    Botble.handleError(res);
                } else {
                    alert('Ocorreu um erro ao atualizar a configuração.');
                }
            }
        });
    });
});