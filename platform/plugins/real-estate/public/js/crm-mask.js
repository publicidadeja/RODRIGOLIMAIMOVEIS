$(document).ready(function() {
    // Função para aplicar máscara de moeda
    function aplicarMascaraMonetaria(input) {
        $(input).on('input', function() {
            // Remove todos os caracteres não numéricos
            let valor = $(this).val().replace(/\D/g, '');
            
            // Converte para número e formata
            if (valor.length > 0) {
                valor = parseInt(valor) / 100;
                $(this).val('R$ ' + valor.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            } else {
                $(this).val('');
            }
        });
    }

    // Aplica a máscara quando o filtro é aberto
    $(document).on('click', '.btn-show-table-options', function() {
        setTimeout(function() {
            aplicarMascaraMonetaria('input[name="property_value"]');
        }, 300);
    });
    
    // Também aplica a máscara quando a página carrega, caso o filtro já esteja aberto
    setTimeout(function() {
        aplicarMascaraMonetaria('input[name="property_value"]');
    }, 500);
});