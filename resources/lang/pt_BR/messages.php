<?php

return [
    'helpers' => [
        'modelValidation' => [
            'idFieldNotFound' => ':modelLabel não encontrado!',
            'invalidField' => 'O campo ":attribute" contém um valor inválido!',
            'verifyBeforeSave' => 'Verifique os dados antes de salvar!',
            'validateSuccess' => 'Dados validados com sucesso!',
        ],
    ],

    'models' => [
        'User' => [
            'name' => 'Usuário',
            'fields' => [
                'name' => 'Nome',
                'lastName' => 'Sobrenome',
                'pictureUrl' => 'URL da Foto',
                'password' => 'Senha',
                'passwordToken' => 'Token de Redefinição de Senha',
                'role' => 'Função',
                'active' => 'Ativo',
            ]
        ]
    ]
];
