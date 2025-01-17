<?php

return [
    'thousandSeparator' => '.',
    'decimalSeparator' => ',',
    'dateFormat' => 'd/m/Y',
    'selectEmptyOption' => 'Selecione...',
    'dontHavePermission' => 'Você não tem acesso a esse conteúdo! Faça o login novamente.',
    'userNameDash' => 'Usuário',
    'logout' => 'Sair',
    'buttonSave' => 'Salvar',
    'buttonBackToList' => 'Voltar para lista',

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
            ],
            'fLogin' => [
                'invalidEmail' => 'Informe um e-mail válido!',
                'emptyPassword' => 'Preencha a senha!',
                'invalidCredentials' => 'Usuário ou senha inválido(s)!',
                'loginUserError' => 'Erro ao tentar logar o usuário!',
                'loginSuccess' => 'Usuário logado com sucesso!',
            ],
        ],

        'Client' => [
            'name' => 'Cliente',
            'gender' => [
                'male' => 'Masculino',
                'female' => 'Feminino',
            ],
            'fields' => [
                'first_name' => 'Nome',
                'last_name' => 'Sobrenome',
                'phone' => 'Telefone',
                'gender' => 'Sexo Biológico',
                'birthdate' => 'Data de Nascimento',
                'height' => 'Altura',
                'weight' => 'Peso',
            ],
            'clientNotFound' => 'Cliente não encontrado para edição!',
            'errorSavingClient' => 'Ocorreu um problema ao salvar o Cliente, tente novamente.',
            'errorSavingOtherClient' => 'Você não tem permissão para salvar este cliente!',
            'successAddingClient' => 'Cliente adicionado com sucesso!',
            'successEditingClient' => 'Cliente editado com sucesso!',
        ],

        'Goal' => [
            'name' => 'Objetivo',
            'fields' => [
                'objective' => 'Objetivo',
                'target_weight' => 'Peso Alvo',
                'deadline' => 'Prazo',
            ],
            'objective' => [
                'weight' => 'Perda de Peso',
                'muscle' => 'Ganho de Massa Muscular',
                'health' => 'Saúde',
            ]
        ],

        'Avaliation' => [
            'name' => 'Avaliação',
            'fields' => [
                'date' => 'Data',
                'body_fat_perc' => 'Percentual de Gordura Corporal',
                'skeletal_muscle_mass_kg' => 'Massa Muscular Esquelética',
                'muscle_rate_perc' => 'Percentual de Músculos',
                'subcutaneous_fat_perc' => 'Percentual de Gordura Subcutânea',
                'visceral_fat_perc' => 'Percentual de Gordura Visceral',
                'body_water_perc' => 'Percentual de Água Corporal',
                'skeletal_muscle_perc' => 'Percentual de Músculo Esquelético',
                'muscle_mass_kg' => 'Massa Muscular',
                'bone_mass_kg' => 'Massa Óssea',
                'protein_perc' => 'Percentual de Proteína',
            ],
        ],
    ],

    'pages' => [
        'login' => [
            'emailPlaceholder' => 'Digite seu e-mail',
            'passwordPlaceholder' => 'Digite sua senha',
            'loginButton' => 'Entrar',
            'loginGoogle' => 'Entrar com Google',
            'forgotPassword' => 'Esqueceu a senha?',
        ],

        'client' => [
            'index' => [
                'title' => 'Clientes',
                'addButton' => 'Adicionar Cliente',
                'editButton' => 'Editar Cliente',
                'deleteConfirmation' => 'Tem certeza que deseja excluir o cliente :clientName? Essa operação é permanente e não pode ser desfeita!',
                'deleteSuccess' => 'Cliente excluído com sucesso!',
            ],
            'register' => [
                'title' => 'Cadastrar Cliente',
                'cardInfo' => 'Informações do Cliente',
                'cardMeasures' => 'Medidas',
            ],
            'table' => [
                'colName' => 'Nome',
                'colEmail' => 'E-mail',
                'colPhone' => 'Telefone',
            ]
        ]
    ],
];
