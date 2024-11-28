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
            'roles' => [
                'root' => 'Root',
                'manager' => 'Gerente',
                'client' => 'Cliente',
            ],
            'fields' => [
                'name' => 'Nome',
                'lastName' => 'Sobrenome',
                'pictureUrl' => 'URL da Foto',
                'password' => 'Senha',
                'passwordToken' => 'Token de Redefinição de Senha',
                'role' => 'Função',
                'active' => 'Ativo',
            ]
        ],

        'Client' => [
            'name' => 'Cliente',
            'gender' => [
                'male' => 'Masculino',
                'female' => 'Feminino',
            ],
            'fields' => [
                'phone' => 'Telefone',
                'gender' => 'Gênero',
                'birthdate' => 'Data de Nascimento',
                'height' => 'Altura',
            ]
        ],
    ]
];
