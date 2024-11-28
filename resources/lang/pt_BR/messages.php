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
                'weight' => 'Peso',
            ]
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
    ]
];
