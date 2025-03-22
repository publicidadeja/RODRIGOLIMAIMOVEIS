@extends('core/base::layouts.master')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="widget meta-boxes">
                <div class="widget-title">
                    <h4><i class="fas fa-tasks"></i> {{ trans('plugins/real-estate::crm.name') }}</h4>
                    <div class="d-flex">
                        <div class="search-box me-auto">
                            <input type="text" id="lead-search" class="form-control" placeholder="Buscar leads...">
                        </div>
                        <a href="{{ route('crm.create') }}" class="btn btn-primary me-2">
                            <i class="fas fa-plus"></i> {{ trans('plugins/real-estate::crm.create') }}
                        </a>
                        <a href="{{ route('crm.index') }}?view=table" class="btn btn-info" title="Visualizar em tabela">
                            <i class="fas fa-table"></i> Tabela
                        </a>
                    </div>
                </div>
                
                <div class="widget-body kanban-container">
                    <!-- Visualização móvel -->
                    <div class="kanban-mobile d-md-none">
                        <div class="form-group mb-3">
                            <label for="lead-status-filter">Filtrar por Status</label>
                            <select id="lead-status-filter" class="form-control">
                                <option value="all">Todos</option>
                                <option value="red">Leads Quentes</option>
                                <option value="yellow">Em Negociação</option>
                                <option value="blue">Leads Frios</option>
                                <option value="gray">Vendas Perdidas</option>
                            </select>
                        </div>
                        
                        <div id="kanban-leads-mobile">
                            <!-- Lista de leads mobile - preenchida via JavaScript -->
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <p class="mt-2">Carregando leads...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Visualização desktop (kanban) -->
                    <div class="kanban-board d-none d-md-flex">
                        <!-- Coluna: Leads Quentes -->
                        <div class="kanban-column" id="kanban-red">
                            <div class="kanban-column-header bg-danger text-white">
                                <h5>Leads Quentes</h5>
                                <span class="badge bg-light text-danger lead-counter" id="red-counter">0</span>
                            </div>
                            <div class="kanban-column-content" data-status="red">
                                <!-- Conteúdo preenchido via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-danger" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coluna: Em Negociação -->
                        <div class="kanban-column" id="kanban-yellow">
                            <div class="kanban-column-header bg-warning text-dark">
                                <h5>Em Negociação</h5>
                                <span class="badge bg-light text-warning lead-counter" id="yellow-counter">0</span>
                            </div>
                            <div class="kanban-column-content" data-status="yellow">
                                <!-- Conteúdo preenchido via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-warning" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coluna: Leads Frios -->
                        <div class="kanban-column" id="kanban-blue">
                            <div class="kanban-column-header bg-primary text-white">
                                <h5>Leads Frios</h5>
                                <span class="badge bg-light text-primary lead-counter" id="blue-counter">0</span>
                            </div>
                            <div class="kanban-column-content" data-status="blue">
                                <!-- Conteúdo preenchido via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coluna: Vendas Perdidas -->
                        <div class="kanban-column" id="kanban-gray">
                            <div class="kanban-column-header bg-secondary text-white">
                                <h5>Vendas Perdidas</h5>
                                <span class="badge bg-light text-secondary lead-counter" id="gray-counter">0</span>
                            </div>
                            <div class="kanban-column-content" data-status="gray">
                                <!-- Conteúdo preenchido via JavaScript -->
                                <div class="text-center py-3">
                                    <div class="spinner-border spinner-border-sm text-secondary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('header')
    <!-- jQuery Mask Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <style>
        /* Estilos para Kanban */
        .kanban-container {
            padding: 15px;
            overflow-x: auto;
        }
        
        .kanban-board {
            display: flex;
            gap: 15px;
            min-height: calc(100vh - 200px);
        }
        
        .kanban-column {
            flex: 1;
            min-width: 300px;
            background-color: #f8f9fa;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 200px);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .kanban-column-header {
            padding: 12px 16px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .kanban-column-content {
            padding: 12px;
            flex: 1;
            overflow-y: auto;
        }
        
        /* Lead card */
        .lead-card {
            background-color: white;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            cursor: grab;
            transition: all 0.2s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .lead-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }
        
        .lead-card-header {
            margin-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .lead-name {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            max-width: 80%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .lead-category-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
        }
        
        .lead-category-badge {
            font-size: 11px;
            font-weight: normal;
            padding: 4px 8px;
        }
        
        .lead-card-body {
            display: flex;
            flex-direction: column;
        }
        
        .lead-info {
            flex: 1;
        }
        
        .lead-contacts {
            font-size: 13px;
            color: #666;
        }
        
        .contact-item {
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .contact-item i {
            width: 15px;
            margin-right: 5px;
            color: #777;
        }
        
        .lead-price-container {
            color: #444;
            padding: 5px 0;
            font-size: 14px;
        }
        
        .lead-price {
            font-weight: 600;
            color: #28a745;
        }
        
        .lead-date {
            font-size: 11px;
            color: #999;
        }
        
        /* Mobile card styles */
        .lead-card-mobile {
            background-color: white;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .lead-card-mobile-header {
            margin-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 12px;
        }
        
        .lead-status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .lead-card-mobile-body {
            padding-top: 6px;
        }
        
        .status-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        /* Drag styles */
        .dragging {
            opacity: 0.8;
            transform: rotate(1deg);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        
        .drag-over {
            background-color: rgba(0,0,0,0.02);
        }
        
        /* Responsive adjustments */
        @media (max-width: 767px) {
            #kanban-leads-mobile {
                margin-top: 15px;
            }
        }
    </style>
@endpush

@push('footer')
    <script>
        // Função de compatibilidade para o kanban
        function calculateCompatibility(lead, propertyPrice) {
            // If no price ranges are defined, return 100% match
            if (!lead.min_price && !lead.max_price) {
                return 100;
            }
            
            propertyPrice = parseFloat(propertyPrice);
            const minPrice = parseFloat(lead.min_price) || 0;
            const maxPrice = parseFloat(lead.max_price) || Infinity;
            
            // If property price is within the range, it's 100% compatible
            if (propertyPrice >= minPrice && propertyPrice <= maxPrice) {
                return 100;
            }
            
            // Calculate how far the price is from the range
            if (propertyPrice < minPrice) {
                // Property is cheaper than minimum
                const diff = minPrice - propertyPrice;
                const percentage = Math.max(0, 100 - (diff / minPrice * 100));
                return Math.round(percentage);
            }
            
            if (propertyPrice > maxPrice && maxPrice !== Infinity) {
                // Property is more expensive than maximum
                const diff = propertyPrice - maxPrice;
                const percentage = Math.max(0, 100 - (diff / maxPrice * 100));
                return Math.round(percentage);
            }
            
            return 100;
        }
    </script>
    <script>
        $(document).ready(function() {
            // Remover referência à verificação automática de correspondências
            console.log('Kanban carregando...');
            
            let allLeads = [];
            let sortedLeads = {
                'red': [],
                'yellow': [],
                'blue': [],
                'gray': []
            };
            
            // Loader function
            function loadLeads() {
                // Fetch all leads
                $.ajax({
                    url: '{{ route('crm.index') }}',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        _ajax: 'get_leads'
                    },
                    success: function(res) {
                        if (res && res.data) {
                            allLeads = res.data;
                            
                            // Sort leads by status
                            sortLeadsByStatus();
                            
                            // Render leads in desktop kanban and mobile list
                            renderKanbanLeads();
                            renderMobileLeads('all');
                        }
                    },
                    error: function(err) {
                        console.error('Error loading leads:', err);
                        alert('Erro ao carregar leads. Por favor, tente novamente.');
                    }
                });
            }
            
            // Sort leads by status
            function sortLeadsByStatus() {
                sortedLeads = {
                    'red': [],
                    'yellow': [],
                    'blue': [],
                    'gray': []
                };
                
                allLeads.forEach(lead => {
                    if (sortedLeads[lead.lead_color]) {
                        sortedLeads[lead.lead_color].push(lead);
                    }
                });
                
                // Update counters
                updateLeadCounters();
            }
            
            // Update lead counters in each column
            function updateLeadCounters() {
                for (const status in sortedLeads) {
                    $(`#${status}-counter`).text(sortedLeads[status].length);
                }
            }
            
            // Render leads in kanban board
            function renderKanbanLeads() {
                // Clear previous content (except loaders)
                $('.kanban-column-content').empty();
                
                // Render leads in each column
                for (const status in sortedLeads) {
                    const $column = $(`#kanban-${status} .kanban-column-content`);
                    
                    if (sortedLeads[status].length === 0) {
                        $column.html('<div class="text-center py-3 text-muted">Nenhum lead nesta coluna</div>');
                    } else {
                        sortedLeads[status].forEach(lead => {
                            const card = createLeadCard(lead);
                            $column.append(card);
                        });
                    }
                }
                
                // Set up drag-and-drop after rendering
                setupDragAndDrop();
            }
            
            // Render leads in mobile view
            function renderMobileLeads(filter) {
                const $container = $('#kanban-leads-mobile');
                $container.empty();
                
                let filteredLeads = [];
                
                if (filter === 'all') {
                    // Flatten all leads
                    for (const status in sortedLeads) {
                        filteredLeads = filteredLeads.concat(sortedLeads[status]);
                    }
                } else {
                    filteredLeads = sortedLeads[filter] || [];
                }
                
                if (filteredLeads.length === 0) {
                    $container.html('<div class="alert alert-info">Nenhum lead encontrado.</div>');
                    return;
                }
                
                // Sort by created_at (newest first)
                filteredLeads.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                
                filteredLeads.forEach(lead => {
                    const card = createMobileLeadCard(lead);
                    $container.append(card);
                });
            }
            
            // Create a lead card for kanban
            function createLeadCard(lead) {
                // Create card element
                const card = document.createElement('div');
                card.className = 'lead-card';
                card.setAttribute('data-id', lead.id);
                card.setAttribute('data-status', lead.lead_color);
                card.setAttribute('draggable', 'true');
                
                // Create card content
                let cardContent = `
                    <div class="lead-card-header">
                        <div class="d-flex justify-content-between">
                            <h6 class="lead-name">${lead.name}</h6>
                            <button class="btn btn-sm btn-outline-success find-properties" title="Buscar imóveis compatíveis" data-id="${lead.id}" data-category="${lead.category || ''}" data-min="${lead.min_price || 0}" data-max="${lead.max_price || 0}">
                                <i class="fas fa-home"></i>
                            </button>
                        </div>
                        <div class="lead-category-wrap">
                            <span class="badge bg-info lead-category-badge">${lead.category_name || 'Sem categoria'}</span>
                            <small class="lead-date">${formatDate(lead.created_at)}</small>
                        </div>
                    </div>
                    <div class="lead-card-body">
                        <div class="lead-info">
                            <div class="lead-contacts mb-2">
                `;
                
                // Add phone if available
                if (lead.phone) {
                    cardContent += `<div class="contact-item"><i class="fas fa-phone-alt"></i> ${lead.phone}</div>`;
                }
                
                // Add email if available
                if (lead.email) {
                    cardContent += `<div class="contact-item"><i class="fas fa-envelope"></i> ${lead.email}</div>`;
                }
                
                // Complete the card
                cardContent += `
                            </div>
                            <div class="lead-price-container">
                                <i class="fas fa-tag"></i> <span class="lead-price">${formatPriceRange(lead)}</span>
                            </div>
                        </div>
                        <div class="lead-actions mt-2 text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="${'{{ route('crm.edit', ['crm' => ':id']) }}'.replace(':id', lead.id)}" class="btn btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger delete-lead" title="Deletar" data-id="${lead.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                card.innerHTML = cardContent;
                
                // Add event listeners
                card.querySelector('.delete-lead').addEventListener('click', function() {
                    const leadId = this.getAttribute('data-id');
                    if (confirm('Tem certeza que deseja excluir este lead?')) {
                        deleteLead(leadId);
                    }
                });
                
                // Add event listener for finding properties
                card.querySelector('.find-properties').addEventListener('click', function() {
                    const leadId = this.getAttribute('data-id');
                    const category = this.getAttribute('data-category');
                    const minPrice = this.getAttribute('data-min');
                    const maxPrice = this.getAttribute('data-max');
                    findCompatibleProperties(leadId, category, minPrice, maxPrice);
                });
                
                return card;
            }
            
            // Create a lead card for mobile view
            function createMobileLeadCard(lead) {
                // Create mobile card element
                const card = document.createElement('div');
                card.className = 'lead-card-mobile';
                card.setAttribute('data-id', lead.id);
                card.setAttribute('data-status', lead.lead_color);
                
                // Create status class
                let statusClass = 'bg-primary'; // Default blue
                if (lead.lead_color === 'red') statusClass = 'bg-danger';
                else if (lead.lead_color === 'yellow') statusClass = 'bg-warning';
                else if (lead.lead_color === 'gray') statusClass = 'bg-secondary';
                
                // Create card content
                let cardContent = `
                    <div class="lead-card-mobile-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="lead-name mb-0">${lead.name}</h6>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-success me-2 find-properties" title="Buscar imóveis compatíveis" data-id="${lead.id}" data-category="${lead.category || ''}" data-min="${lead.min_price || 0}" data-max="${lead.max_price || 0}">
                                    <i class="fas fa-home"></i>
                                </button>
                                <span class="lead-status-indicator ${statusClass}"></span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span class="badge bg-info lead-category-badge">${lead.category_name || 'Sem categoria'}</span>
                            <small class="lead-date text-muted">${formatDate(lead.created_at)}</small>
                        </div>
                    </div>
                    <div class="lead-card-mobile-body">
                        <div class="lead-info">
                            <div class="contact-details">
                `;
                
                // Add phone if available
                if (lead.phone) {
                    cardContent += `<div class="contact-item mb-1"><i class="fas fa-phone-alt"></i> ${lead.phone}</div>`;
                }
                
                // Add email if available
                if (lead.email) {
                    cardContent += `<div class="contact-item mb-1"><i class="fas fa-envelope"></i> ${lead.email}</div>`;
                }
                
                // Complete the card
                cardContent += `
                            </div>
                            <div class="lead-price-container mt-2">
                                <i class="fas fa-tag"></i> <span class="lead-price">${formatPriceRange(lead)}</span>
                            </div>
                        </div>
                        <div class="lead-actions mt-3 d-flex justify-content-between">
                            <div class="btn-group btn-group-sm">
                                <a href="${'{{ route('crm.edit', ['crm' => ':id']) }}'.replace(':id', lead.id)}" class="btn btn-primary" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-danger delete-lead" title="Deletar" data-id="${lead.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Status
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item change-status" data-status="red" href="#"><span class="status-dot bg-danger"></span> Lead Quente</a></li>
                                    <li><a class="dropdown-item change-status" data-status="yellow" href="#"><span class="status-dot bg-warning"></span> Em Negociação</a></li>
                                    <li><a class="dropdown-item change-status" data-status="blue" href="#"><span class="status-dot bg-primary"></span> Lead Frio</a></li>
                                    <li><a class="dropdown-item change-status" data-status="gray" href="#"><span class="status-dot bg-secondary"></span> Venda Perdida</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                
                card.innerHTML = cardContent;
                
                // Add event listeners
                card.querySelector('.delete-lead').addEventListener('click', function() {
                    const leadId = this.getAttribute('data-id');
                    if (confirm('Tem certeza que deseja excluir este lead?')) {
                        deleteLead(leadId);
                    }
                });
                
                // Add change status event listeners
                card.querySelectorAll('.change-status').forEach(item => {
                    item.addEventListener('click', function(e) {
                        e.preventDefault();
                        const newStatus = this.getAttribute('data-status');
                        const leadId = this.closest('.lead-card-mobile').getAttribute('data-id');
                        updateLeadStatus(leadId, newStatus);
                    });
                });
                
                // Add event listener for finding properties
                card.querySelector('.find-properties').addEventListener('click', function() {
                    const leadId = this.getAttribute('data-id');
                    const category = this.getAttribute('data-category');
                    const minPrice = this.getAttribute('data-min');
                    const maxPrice = this.getAttribute('data-max');
                    findCompatibleProperties(leadId, category, minPrice, maxPrice);
                });
                
                return card;
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
                return 'Valor não informado';
            }
            
            // Format number for currency display
            function formatNumber(num) {
                return parseFloat(num).toLocaleString('pt-BR');
            }
            
            // Format date
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('pt-BR');
            }
            
            // Delete a lead
            function deleteLead(leadId) {
                $.ajax({
                    url: '{{ route('crm.destroy', ['crm' => ':id']) }}'.replace(':id', leadId),
                    type: 'DELETE',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.error === false) {
                            // Remove from local data
                            allLeads = allLeads.filter(lead => lead.id != leadId);
                            // Re-sort and render
                            sortLeadsByStatus();
                            renderKanbanLeads();
                            renderMobileLeads($('#lead-status-filter').val());
                            // Show success message
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
            
            // Set up drag and drop for kanban cards
            function setupDragAndDrop() {
                // Get all cards
                const cards = document.querySelectorAll('.lead-card');
                const columns = document.querySelectorAll('.kanban-column-content');
                
                // Add event listeners to cards
                cards.forEach(card => {
                    card.addEventListener('dragstart', dragStart);
                    card.addEventListener('dragend', dragEnd);
                });
                
                // Add event listeners to columns
                columns.forEach(column => {
                    column.addEventListener('dragover', dragOver);
                    column.addEventListener('dragenter', dragEnter);
                    column.addEventListener('dragleave', dragLeave);
                    column.addEventListener('drop', drop);
                });
                
                function dragStart(e) {
                    this.classList.add('dragging');
                    
                    // Better drag-and-drop experience by setting drag image and data
                    if (e.dataTransfer) {
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', this.getAttribute('data-id'));
                        
                        // Add a slight delay to change appearance after drag starts
                        setTimeout(() => {
                            this.style.opacity = '0.6';
                            this.style.transform = 'rotate(2deg)';
                        }, 10);
                    }
                }
                
                function dragEnd() {
                    this.classList.remove('dragging');
                    this.style.opacity = '';
                    this.style.transform = '';
                }
                
                function dragOver(e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = 'move';
                }
                
                function dragEnter(e) {
                    e.preventDefault();
                    this.classList.add('drag-over');
                }
                
                function dragLeave() {
                    this.classList.remove('drag-over');
                }
                
                function drop(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over');
                    
                    const card = document.querySelector('.dragging');
                    
                    if (card) {
                        const leadId = card.getAttribute('data-id');
                        const newStatus = this.getAttribute('data-status');
                        const oldStatus = card.getAttribute('data-status');
                        
                        if (oldStatus !== newStatus) {
                            // First visually move the card (for better UX)
                            this.appendChild(card);
                            card.setAttribute('data-status', newStatus);
                            
                            // Show a temporary indicator of success
                            const indicator = document.createElement('div');
                            indicator.className = 'alert alert-success p-2 mb-2 text-center';
                            indicator.textContent = 'Atualizando status...';
                            this.prepend(indicator);
                            
                            // Then update in database
                            updateLeadStatus(leadId, newStatus, indicator);
                        }
                    }
                }
            }
            
            // Update lead status via AJAX
            function updateLeadStatus(leadId, newStatus, indicator = null) {
                $.ajax({
                    url: '{{ route('crm.index') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _action: 'update_status',
                        id: leadId,
                        status: newStatus
                    },
                    success: function(res) {
                        if (res.success) {
                            // Update local data
                            updateLocalLeadStatus(leadId, newStatus);
                            // Resort and update counters
                            sortLeadsByStatus();
                            
                            // Remove indicator if provided
                            if (indicator) {
                                indicator.textContent = 'Status atualizado!';
                                setTimeout(() => {
                                    indicator.remove();
                                }, 2000);
                            }
                            
                            // Update UI for mobile view
                            const mobileCard = document.querySelector(`.lead-card-mobile[data-id="${leadId}"]`);
                            if (mobileCard) {
                                mobileCard.setAttribute('data-status', newStatus);
                                
                                const indicator = mobileCard.querySelector('.lead-status-indicator');
                                if (indicator) {
                                    indicator.className = 'lead-status-indicator';
                                    if (newStatus === 'red') indicator.classList.add('bg-danger');
                                    else if (newStatus === 'yellow') indicator.classList.add('bg-warning');
                                    else if (newStatus === 'blue') indicator.classList.add('bg-primary');
                                    else if (newStatus === 'gray') indicator.classList.add('bg-secondary');
                                }
                            }
                        } else {
                            alert(res.message || 'Erro ao atualizar status do lead.');
                            // Revert the visual change if there was an error
                            renderKanbanLeads();
                        }
                    },
                    error: function() {
                        alert('Erro ao atualizar status do lead.');
                        // Revert the visual change if there was an error
                        renderKanbanLeads();
                    }
                });
            }
            
            // Update lead status in local data
            function updateLocalLeadStatus(leadId, newStatus) {
                for (let i = 0; i < allLeads.length; i++) {
                    if (allLeads[i].id == leadId) {
                        allLeads[i].lead_color = newStatus;
                        break;
                    }
                }
            }
            
            // Mobile status filter
            $('#lead-status-filter').on('change', function() {
                const filter = $(this).val();
                renderMobileLeads(filter);
            });
            
            // Add search functionality for leads
            $('#lead-search').on('keyup', function() {
                const searchText = $(this).val().toLowerCase().trim();
                
                if (searchText === '') {
                    // If search is empty, show all leads
                    renderKanbanLeads();
                    return;
                }
                
                // Search in all lead cards
                $('.lead-card').each(function() {
                    const card = $(this);
                    const leadName = card.find('.lead-name').text().toLowerCase();
                    const leadCategory = card.find('.lead-category-badge').text().toLowerCase();
                    const leadContacts = card.find('.lead-contacts').text().toLowerCase();
                    const leadContent = leadName + ' ' + leadCategory + ' ' + leadContacts;
                    
                    if (leadContent.includes(searchText)) {
                        card.show();
                    } else {
                        card.hide();
                    }
                });
                
                // Hide "no leads" message if search is active
                $('.text-muted:contains("Nenhum lead nesta coluna")').hide();
            });
            
            // Load leads on page load
            loadLeads();
            
            // Function to find compatible properties
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
                    url: '{{ route('crm.match-properties', ['id' => ':id']) }}'.replace(':id', leadId),
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
                                                        <a href="${property.edit_url}" class="btn btn-outline-primary" target="_blank">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
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
    
    <style>
        /* Estilos para a caixa de busca */
        .search-box {
            max-width: 300px;
            margin-right: 15px;
        }
        
        /* Estilos para o botão de busca de imóveis */
        .find-properties {
            padding: 2px 5px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .find-properties:hover {
            transform: scale(1.1);
            box-shadow: 0 0 4px rgba(0,0,0,0.1);
        }
        
        /* Estilos para o modal de imóveis compatíveis */
        #compatible-properties-modal .modal-body {
            max-height: 500px;
            overflow-y: auto;
        }
        
        #compatible-properties-modal .card {
            transition: all 0.2s ease;
            border: 1px solid #eee;
        }
        
        #compatible-properties-modal .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-color: #ddd;
        }
    </style>
@endpush