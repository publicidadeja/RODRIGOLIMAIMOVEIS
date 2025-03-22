<?php

return [
    'name'                => 'Imóveis',
    'create'              => 'Novo imóvel',
    'edit'                => 'Editar imóvel',
    'form'                => [
        'main_info'        => 'Informações gerais',
        'basic_info'       => 'Informações básicas',
        'name'             => 'Título',
        'type'             => 'Tipo',
        'images'           => 'Imagens',
        'location'         => 'Localização do imóvel',
        'number_bedroom'   => 'Número de quartos',
        'number_bathroom'  => 'Número de banheiros',
        'number_floor'     => 'Número de andares',
        'square'           => 'Área :unit',
        'price'            => 'Preço',
        'features'         => 'Características',
        'project'          => 'Projeto',
        'date'             => 'Informações de data',
        'currency'         => 'Moeda',
        'city'             => 'Cidade',
        'period'           => 'Período',
        'category'         => 'Categoria',
        'latitude'         => 'Latitude',
        'latitude_helper'  => 'Clique aqui para obter a Latitude do endereço.',
        'longitude'        => 'Longitude',
        'longitude_helper' => 'Clique aqui para obter a Longitude do endereço.',
        'categories'       => 'Categorias',
        'images_upload_placeholder' => 'Arraste arquivos aqui ou clique para fazer upload.',
    ],
    'statuses'            => [
        'not_available' => 'Não disponível',
        'pre_sale'      => 'Preparando venda',
        'selling'       => 'À venda',
        'sold'          => 'Vendido',
        'renting'       => 'Para alugar',
        'rented'        => 'Alugado',
        'building'      => 'Em construção',
    ],
    'types'               => [
        'sale' => 'Venda',
        'rent' => 'Aluguel',
    ],
    'periods'             => [
        'day'   => 'Dia',
        'month' => 'Mês',
        'year'  => 'Ano',
    ],
    'moderation_status'   => 'Status de moderação',
    'moderation-statuses' => [
        'pending'  => 'Pendente',
        'approved' => 'Aprovado',
        'rejected' => 'Rejeitado',
    ],
    'renew_notice'        => 'Renovar automaticamente (você será cobrado novamente em :days dias)?',
    'distance_key'        => 'Distância entre comodidades',
    'never_expired'       => 'Nunca expira?',
    'select_project'      => 'Selecione um projeto...',
    'account'             => 'Conta',
    'select_account'      => 'Selecione uma conta..',
    'expire_date'         => 'Data de expiração',
    'never_expired_label' => 'Nunca expira',
    'active_properties'   => 'Imóveis ativos',
    'pending_properties'  => 'Imóveis pendentes',
    'expired_properties'  => 'Imóveis expirados',
    
    // ZAP Imóveis integration
    'zap_imoveis_integration' => 'Integração com ZAP Imóveis',
    'zap_imoveis_description' => 'Gere um feed XML para integração com o portal ZAP Imóveis.',
    'zap_imoveis_tip_1' => 'O feed XML contém todos os seus imóveis aprovados.',
    'zap_imoveis_tip_2' => 'Copie a URL do XML e cole-a na sua conta do portal ZAP Imóveis.',
    'zap_imoveis_tip_3' => 'O feed XML é atualizado quando você clica em "Gerar XML".',
    'zap_imoveis_xml_feed' => 'URL do Feed XML',
    'zap_imoveis_xml_not_generated' => 'O feed XML ainda não foi gerado.',
    'zap_imoveis_xml_generated_successfully' => 'Feed XML gerado com sucesso!',
    'zap_imoveis_xml_not_found' => 'Arquivo XML não encontrado. Por favor, gere-o primeiro.',
    'generate_xml' => 'Gerar XML',
    'generating' => 'Gerando...',
    'download_xml' => 'Baixar XML',
    'copy_url' => 'Copiar URL',
    'url_copied' => 'URL copiada para a área de transferência!',
    'last_updated' => 'Última atualização',
];