@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="widget meta-boxes">
                    <div class="widget-title">
                        <h4><i class="fas fa-tasks"></i> {{ trans('plugins/real-estate::crm.name') }}</h4>
                        <div class="d-flex">
                            <a href="{{ route('public.account.crm.create') }}" class="btn btn-primary me-2">
                                <i class="fas fa-plus"></i> {{ trans('plugins/real-estate::crm.create') }}
                            </a>
                            <a href="{{ route('public.account.crm.index') }}" class="btn btn-info" title="Visualizar em Kanban">
                                <i class="fas fa-columns"></i> Kanban
                            </a>
                        </div>
                    </div>
                    
                    <div class="widget-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Contato</th>
                                        <th>Tipo de Imóvel</th>
                                        <th>Faixa de Preço</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="leads-table-body">
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                            <p class="mt-2">Carregando leads...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
<script>
    $(document).ready(function() {
        // Carregar leads para a tabela
        loadLeadsTable();
        
        // Função para carregar leads para a tabela
        function loadLeadsTable() {
            $.ajax({
                url: '{{ route('public.account.crm.index') }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    _ajax: 'get_leads'
                },
                success: function(res) {
                    if (res && res.data && res.data.length > 0) {
                        renderLeadsTable(res.data);
                    } else {
                        $('#leads-table-body').html(`
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p>Nenhum lead encontrado.</p>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(err) {
                    console.error('Error loading leads:', err);
                    $('#leads-table-body').html(`
                        <tr>
                            <td colspan="8" class="text-center">
                                <p class="text-danger">Erro ao carregar leads. Por favor, tente novamente.</p>
                            </td>
                        </tr>
                    `);
                }
            });
        }
        
        // Função para renderizar leads na tabela
        function renderLeadsTable(leads) {
            let tableContent = '';
            
            // Ordenar leads por data de criação (mais recentes primeiro)
            leads.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            leads.forEach(function(lead, index) {
                // Determinar a classe de cor para o status
                let statusClass = '';
                let statusName = '';
                
                switch(lead.lead_color) {
                    case 'red':
                        statusClass = 'danger';
                        statusName = 'Lead Quente';
                        break;
                    case 'yellow':
                        statusClass = 'warning';
                        statusName = 'Em Negociação';
                        break;
                    case 'blue':
                        statusClass = 'primary';
                        statusName = 'Lead Frio';
                        break;
                    case 'gray':
                        statusClass = 'secondary';
                        statusName = 'Venda Perdida';
                        break;
                    default:
                        statusClass = 'primary';
                        statusName = 'Lead Frio';
                }
                
                // Formatar valor
                let priceRange = formatPriceRange(lead);
                
                // Formatando a data
                let createdDate = new Date(lead.created_at);
                let formattedDate = createdDate.toLocaleDateString('pt-BR');
                
                // Criar linha da tabela
                tableContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${lead.name}</td>
                        <td>
                            ${lead.phone ? `<div><i class="fas fa-phone-alt me-1"></i> ${lead.phone}</div>` : ''}
                            ${lead.email ? `<div><i class="fas fa-envelope me-1"></i> ${lead.email}</div>` : ''}
                        </td>
                        <td>${lead.category_name || 'Não especificado'}</td>
                        <td>${priceRange}</td>
                        <td><span class="badge bg-${statusClass}">${statusName}</span></td>
                        <td>${formattedDate}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('public.account.crm.edit', ['id' => ':id']) }}".replace(':id', lead.id) class="btn btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger delete-lead" data-id="${lead.id}" title="Deletar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-success find-properties" data-id="${lead.id}" data-category="${lead.category || ''}" data-min="${lead.min_price || 0}" data-max="${lead.max_price || 0}" title="Buscar imóveis compatíveis">
                                    <i class="fas fa-home"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            $('#leads-table-body').html(tableContent);
            
            // Adicionar handlers para os botões
            $('.delete-lead').on('click', function() {
                const leadId = $(this).data('id');
                if (confirm('Tem certeza que deseja excluir este lead?')) {
                    deleteLead(leadId);
                }
            });
            
            $('.find-properties').on('click', function() {
                const leadId = $(this).data('id');
                const category = $(this).data('category');
                const minPrice = $(this).data('min');
                const maxPrice = $(this).data('max');
                findCompatibleProperties(leadId, category, minPrice, maxPrice);
            });
        }
        
        // Função para exclusão de lead
        function deleteLead(leadId) {
            $.ajax({
                url: '{{ route('public.account.crm.destroy', ['id' => ':id']) }}'.replace(':id', leadId),
                type: 'DELETE',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.error === false) {
                        // Recarregar a tabela
                        loadLeadsTable();
                        // Mostrar mensagem de sucesso
                        alert('Lead excluído com sucesso.');
                    } else {
                        alert(res.message || 'Erro ao excluir lead.');
                    }
                },
                error: function() {
                    alert('Erro ao excluir lead.');
                }
            });
        }
        
        // Format price range
        function formatPriceRange(lead) {
            if (lead.min_price || lead.max_price) {
                if (lead.min_price && lead.max_price) {
                    return `R$ ${formatNumber(lead.min_price)} - R$ ${formatNumber(lead.max_price)}`;
                } else if (lead.min_price) {
                    return `A partir de R$ ${formatNumber(lead.min_price)}`;
                } else {
                    return `Até R$ ${formatNumber(lead.max_price)}`;
                }
            } else if (lead.property_value) {
                return `R$ ${formatNumber(lead.property_value)}`;
            }
            return 'Não informado';
        }
        
        // Format number for currency display
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('pt-BR');
        }
        
        // Find compatible properties
        function findCompatibleProperties(leadId, category, minPrice, maxPrice) {
            // Create loading modal
            if (!$('#compatible-properties-modal').length) {
                $('body').append(`
                    <div class="modal fade" id="compatible-properties-modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Imóveis Compatíveis</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p class="mt-2">Buscando imóveis compatíveis...</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
            }
            
            // Show modal with loading indicator
            const modal = new bootstrap.Modal(document.getElementById('compatible-properties-modal'));
            modal.show();
            
            // Add simple fetch logic that uses the backend route
            $.ajax({
                url: '{{ route('public.account.crm.match-properties', ['id' => ':id']) }}'.replace(':id', leadId),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res.error && res.data) {
                        const properties = res.data;
                        let modalContent = '';
                        
                        if (properties.length === 0) {
                            modalContent = `
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Nenhum imóvel cadastrado corresponde aos critérios do lead:
                                    <ul class="mt-2 mb-0">
                                        <li><strong>Categoria:</strong> ${category ? getCategoryName(category) : 'Não especificada'}</li>
                                        <li><strong>Faixa de preço:</strong> ${formatPriceRange({min_price: minPrice, max_price: maxPrice})}</li>
                                    </ul>
                                </div>
                            `;
                        } else {
                            modalContent = `
                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle me-2"></i> Encontrados <strong>${properties.length}</strong> imóveis compatíveis com os critérios do lead.
                                </div>
                                <div class="row">
                            `;
                            
                            properties.forEach(function(property) {
                                modalContent += `
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-img-top text-center pt-2">
                                                ${property.image ? 
                                                    `<img src="${property.image}" class="img-fluid" style="height: 150px; object-fit: cover;">` : 
                                                    `<div class="p-3 text-center bg-light text-muted"><i class="fas fa-home fa-3x"></i><br>Sem imagem</div>`
                                                }
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title">${property.name}</h5>
                                                <p class="card-text">
                                                    <strong>Categoria:</strong> ${property.category || 'N/A'}<br>
                                                    <strong>Preço:</strong> ${property.price}<br>
                                                    <strong>Localização:</strong> ${property.location || 'N/A'}
                                                </p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <div class="btn-group btn-group-sm w-100">
                                                    <a href="${property.public_url}" class="btn btn-outline-success" target="_blank">
                                                        <i class="fas fa-eye"></i> Visualizar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            
                            modalContent += '</div>'; // Close row
                        }
                        
                        $('#compatible-properties-modal .modal-body').html(modalContent);
                        
                        // Update modal title with more info
                        $('#compatible-properties-modal .modal-title').html(
                            `Imóveis Compatíveis - ${getCategoryName(category) || 'Todas categorias'} - ${formatPriceRange({min_price: minPrice, max_price: maxPrice})}`
                        );
                    } else {
                        $('#compatible-properties-modal .modal-body').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i> Erro ao buscar imóveis compatíveis: ${res.message || 'Erro desconhecido'}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#compatible-properties-modal .modal-body').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> Erro ao buscar imóveis compatíveis. Tente novamente mais tarde.
                        </div>
                    `);
                }
            });
        }
        
        // Get category name from code
        function getCategoryName(code) {
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
    });
</script>
@endpush