$(document).ready(function() {
    // Verificar correspondências quando a página carrega
    function checkPropertyMatches() {
        console.log('Verificando correspondências com leads...');
        
        // Tentar garantir que a tabela esteja completamente carregada
        setTimeout(function() {
            $.ajax({
                url: route('crm.check-property-matches'),
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    console.log('Resposta de verificação:', res);
                    if (!res.error) {
                        if (res.data && res.data.match_count > 0) {
                            toastr.success(res.message);
                            // Recarregar a página para mostrar os badges
                            window.location.reload();
                        } else {
                            console.log('Nenhuma correspondência encontrada');
                        }
                    } else {
                        console.error('Erro ao verificar correspondências:', res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição AJAX:', error);
                }
            });
        }, 1000); // Aguardar 1 segundo para garantir que a tabela esteja carregada
    }
    
    // Click em badge de correspondência para abrir modal
    $(document).on('click', '.property-match', function() {
        const leadId = $(this).data('id');
        const category = $(this).data('category');
        const minPrice = $(this).data('min');
        const maxPrice = $(this).data('max');
        
        // Carregar imóveis correspondentes
        $.ajax({
            url: route('crm.match-properties', {id: leadId}),
            type: 'GET',
            success: function(res) {
                if (!res.error && res.data) {
                    const properties = res.data;
                    let modalContent = '';
                    
                    if (properties.length === 0) {
                        modalContent = '<div class="alert alert-info">Nenhum imóvel compatível encontrado.</div>';
                    } else {
                        modalContent += '<div class="match-properties-list">';
                        properties.forEach(function(property) {
                            modalContent += `
                                <div class="property-item">
                                    <div class="row">
                                        <div class="col-md-3">
                                            ${property.image ? `<img src="${property.image}" class="img-thumbnail">` : '<div class="no-image">Sem imagem</div>'}
                                        </div>
                                        <div class="col-md-9">
                                            <h5>${property.name}</h5>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Categoria:</strong> ${property.category}</p>
                                                    <p><strong>Preço:</strong> ${property.price}</p>
                                                    <p><strong>Localização:</strong> ${property.location || 'N/A'}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    ${property.compatibility ? `
                                                    <p>
                                                        <i class="fas fa-percentage text-success"></i> 
                                                        <strong>Compatibilidade:</strong> 
                                                        <span class="badge ${getCompatibilityBadgeClass(property.compatibility)}">${property.compatibility}%</span>
                                                    </p>` : ''}
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <a href="${property.edit_url}" class="btn btn-info btn-sm" target="_blank">Editar</a>
                                                <a href="${property.public_url}" class="btn btn-primary btn-sm" target="_blank">Visualizar</a>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            `;
                        });
                        modalContent += '</div>';
                    }
                    
                    // Mostrar modal
                    const modal = $('#property-match-modal');
                    $('.modal-body', modal).html(modalContent);
                    $('.modal-title', modal).html(`Imóveis compatíveis - ${getCategory(category)} - ${getPriceRange(minPrice, maxPrice)}`);
                    modal.modal('show');
                } else {
                    toastr.error(res.message || 'Erro ao carregar imóveis compatíveis');
                }
            },
            error: function() {
                toastr.error('Erro ao carregar imóveis compatíveis');
            }
        });
    });
    
    // Detectar submit do form para verificar novamente
    $(document).on('submit', 'form', function() {
        setTimeout(function() {
            checkPropertyMatches();
        }, 2000);
    });
    
    // Obter nome da categoria
    function getCategory(code) {
        const categories = {
            'casa': 'Casa',
            'casa_condominio': 'Casa em Condomínio',
            'sobrado': 'Sobrado',
            'apartamento': 'Apartamento',
            'studio': 'Studio/Kitnet',
            'cobertura': 'Cobertura',
            'flat': 'Flat',
            'loft': 'Loft',
            'chacara': 'Chácara',
            'sitio': 'Sítio',
            'fazenda': 'Fazenda',
            'rancho': 'Rancho',
            'terreno': 'Terreno',
            'terreno_cond': 'Terreno em Condomínio',
            'lote': 'Lote',
            'area_rural': 'Área Rural',
            'comercial_sala': 'Sala Comercial',
            'comercial_loja': 'Loja',
            'comercial_galpao': 'Galpão',
            'comercial_predio': 'Prédio Comercial',
            'aluguel': 'Aluguel',
            'temporada': 'Temporada',
            'industrial': 'Área Industrial',
            'hotel_pousada': 'Hotel/Pousada',
            'imovel_na_planta': 'Imóvel na Planta',
            'outros': 'Outros'
        };
        
        return categories[code] || code;
    }
    
    // Obter faixa de preço formatada
    function getPriceRange(min, max) {
        min = parseFloat(min);
        max = parseFloat(max);
        
        const formatPrice = (value) => {
            return new Intl.NumberFormat('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }).format(value);
        };
        
        if (min && max) {
            return `${formatPrice(min)} - ${formatPrice(max)}`;
        } else if (min) {
            return `A partir de ${formatPrice(min)}`;
        } else if (max) {
            return `Até ${formatPrice(max)}`;
        }
        
        return '';
    }
    
    // Adicionar modal ao body
    $('body').append(`
        <div class="modal fade" id="property-match-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Imóveis compatíveis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- conteúdo gerado dinamicamente -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `);
    
    // Get compatibility badge class based on score
    function getCompatibilityBadgeClass(score) {
        if (score >= 90) return 'bg-success';
        if (score >= 70) return 'bg-info';
        if (score >= 50) return 'bg-warning';
        return 'bg-danger';
    }
    
    // Adicionar estilo personalizado
    $('head').append(`
        <style>
            .property-match {
                cursor: pointer;
            }
            .property-match:hover {
                opacity: 0.8;
            }
            .match-properties-list {
                max-height: 500px;
                overflow-y: auto;
            }
            .property-item {
                margin-bottom: 15px;
                padding: 10px;
                border-radius: 5px;
                border: 1px solid #eee;
            }
            .property-item:hover {
                background-color: #f9f9f9;
                border-color: #ddd;
            }
            .property-item .no-image {
                width: 100%;
                height: 100px;
                background: #f0f0f0;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #999;
                border-radius: 4px;
            }
            .property-item img {
                width: 100%;
                height: 150px;
                object-fit: cover;
                border-radius: 4px;
            }
        </style>
    `);
    
    // Verificar correspondências ao carregar a página
    checkPropertyMatches();
});