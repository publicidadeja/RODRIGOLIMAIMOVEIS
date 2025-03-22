<?php

return [
    'settings' => [
        'title' => 'Configurações de login social',
        'description' => 'Configurar opções de login redes sociais',
        'facebook' => [
            'title' => 'Configurações de login do Facebook',
            'description' => 'Ativar/desativar e configurar credenciais de aplicativo para login no Facebook',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'helper' => 'Acesse https://developers.facebook.com para criar uma nova ID do aplicativo de atualização do aplicativo, App Secret. URL de retorno é :callback',
        ],
        'google' => [
            'title' => 'Configurações de login do Google',
            'description' => 'Ativar/desativar e configurar credenciais de aplicativo para login do Google',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'helper' => 'Acesse https://console.developers.google.com/apis/dashboard para criar uma nova atualização do aplicativo App ID, App Secret. URL de retorno é :callback',
        ],
        'github' => [
            'title' => 'Github login settings',
            'description' => 'Ativar/desativar e configurar credenciais de aplicativo para login do Github',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'helper' => 'Acesse https://github.com/settings/developers para criar um novo ID do aplicativo de atualização do aplicativo, App Secret. URL de retorno é :callback',
        ],
        'linkedin' => [
            'title' => 'Configurações de login do Linkedin',
            'description' => 'Ativar/desativar e configurar credenciais de aplicativos para login no Linkedin',
            'app_id' => 'App ID',
            'app_secret' => 'App Secret',
            'helper' => 'Acesse https://www.linkedin.com/developers/apps/new para criar uma nova ID do aplicativo de atualização do aplicativo, App Secret. URL de retorno é :callback',
        ],
        'enable' => 'Habilitado?',
    ],
    'menu' => 'Login Redes Sociais',
];
