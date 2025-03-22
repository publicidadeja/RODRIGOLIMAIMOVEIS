<?php

return [
    // Informações básicas do módulo
    'name'                => 'CRM',
    'create'              => 'Adicionar Lead',
    'edit'                => 'Editar Lead',
    
    // Campos do formulário
    'form'                => [
        'name'                     => 'Nome',
        'email'                    => 'Email',
        'phone'                    => 'Telefone',
        'subject'                  => 'Assunto',
        'content'                  => 'Conteúdo',
        'property'                 => 'Imóvel relacionado',
        'select_property'          => 'Selecione um imóvel',
        'phone_placeholder'        => 'Digite o telefone',
        'email_placeholder'        => 'Digite o email',
        'content_placeholder'      => 'Digite o conteúdo',
        'status'                   => 'Status',
        'property_value'           => 'Valor do Imóvel',
        'property_value_placeholder' => 'Digite o valor do imóvel',
        'category'                 => 'Categoria do Imóvel',
    ],
    
    // Categorias de imóveis
    'categories'          => [
        'casa'              => 'Casa',
        'casa_condominio'   => 'Casa em Condomínio',
        'sobrado'           => 'Sobrado',
        'apartamento'       => 'Apartamento',
        'studio'            => 'Studio/Kitnet',
        'cobertura'         => 'Cobertura',
        'flat'              => 'Flat',
        'loft'              => 'Loft',
        'chacara'           => 'Chácara',
        'sitio'             => 'Sítio',
        'fazenda'           => 'Fazenda',
        'rancho'            => 'Rancho',
        'terreno'           => 'Terreno',
        'terreno_cond'      => 'Terreno em Condomínio',
        'lote'              => 'Lote',
        'area_rural'        => 'Área Rural',
        'comercial_sala'    => 'Sala Comercial',
        'comercial_loja'    => 'Loja',
        'comercial_galpao'  => 'Galpão',
        'comercial_predio'  => 'Prédio Comercial',
        'aluguel'           => 'Aluguel',
        'temporada'         => 'Temporada',
        'industrial'        => 'Área Industrial',
        'hotel_pousada'     => 'Hotel/Pousada',
        'imovel_na_planta'  => 'Imóvel na Planta',
        'outros'            => 'Outros',
    ],
    
    // Traduções específicas para a tabela CRM
    'phone'               => 'Telefone',
    'email'               => 'Email',
    'content'             => 'Conteúdo',
    
    // Mensagens
    'messages'            => [
        'request'              => [
            'name_required'        => 'Nome é obrigatório',
            'email_required'       => 'Email é obrigatório',
            'email_valid'          => 'Email inválido',
            'phone_required'       => 'Telefone é obrigatório',
            'content_required'     => 'Conteúdo é obrigatório',
            'category_required'    => 'Categoria do imóvel é obrigatória',
            'property_value_required' => 'Valor do imóvel é obrigatório',
        ],
        'create_success'       => 'Lead criado com sucesso',
        'update_success'       => 'Lead atualizado com sucesso',
        'delete_success'       => 'Lead excluído com sucesso',
    ],
    
    // Status possíveis
    'statuses'            => [
        'read'                 => 'Lido',
        'unread'               => 'Não lido',
        'pending'              => 'Pendente',
        'processing'           => 'Em processamento',
        'completed'            => 'Concluído',
    ],
  
  'form' => [
    'lead_status' => 'Status do Lead',
    'lead_status_hot' => 'Lead Quente',
    'lead_status_cold' => 'Lead Frio',
    'lead_status_negotiating' => 'Em negociação',
    'lead_status_lost' => 'Venda Perdida',
    'lead_status_completed' => 'Venda Concluída',
    // ... outras traduções existentes ...
],
    
    // Outras traduções
    'lead_information'    => 'Informações do Lead',
    'last_updated'        => 'Última atualização',
    'created_at'          => 'Criado em',
    'updated_at'          => 'Atualizado em',
    'no_lead'             => 'Nenhum lead encontrado',
    'lead_details'        => 'Detalhes do Lead',
    'mark_as_read'        => 'Marcar como lido',
    'mark_as_unread'      => 'Marcar como não lido',
    'assign_to'           => 'Atribuir para',
    'notes'               => 'Notas',
    'add_note'            => 'Adicionar nota',
    
    // Campos adicionais
    'property_details'    => 'Detalhes do Imóvel',
    'property_location'   => 'Localização do Imóvel',
    'property_type'       => 'Tipo de Imóvel',
    'property_status'     => 'Status do Imóvel',
    'contact_preference'  => 'Preferência de Contato',
    'best_time_to_contact' => 'Melhor Horário para Contato',
    'lead_source'         => 'Origem do Lead',
    'lead_priority'       => 'Prioridade do Lead',
    'follow_up_date'      => 'Data de Acompanhamento',
    'budget_range'        => 'Faixa de Orçamento',
    'additional_notes'    => 'Observações Adicionais',
];