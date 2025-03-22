<?php

return [
    'cache_management' => 'Gerenciamento de cache',
    'cache_commands' => 'Limpar comandos de cache',
    'commands' => [
        'clear_cms_cache' => [
            'title' => 'Limpe todo o cache do CMS',
            'description' => 'Limpar cache do CMS: cache do banco de dados, blocos estáticos... Execute este comando quando não vir as alterações após a atualização dos dados.',
            'success_msg' => 'Cache limpo',
        ],
        'refresh_compiled_views' => [
            'title' => 'Atualizar visualizações compiladas',
            'description' => 'Limpe as visualizações compiladas para atualizar as visualizações.',
            'success_msg' => 'Visualização de cache atualizada',
        ],
        'clear_config_cache' => [
            'title' => 'Limpar cache de configuração',
            'description' => 'Talvez seja necessário atualizar o cache de configuração ao alterar algo no ambiente de produção.',
            'success_msg' => 'Cache de configuração limpo',
        ],
        'clear_route_cache' => [
            'title' => 'Limpar cache de rota',
            'description' => 'Limpe o roteamento de cache.',
            'success_msg' => 'O cache da rota foi limpo',
        ],
        'clear_log' => [
            'title' => 'Limipar log',
            'description' => 'Limpar arquivos de log do sistema',
            'success_msg' => 'O log do sistema foi limpo',
        ],
    ],
];
