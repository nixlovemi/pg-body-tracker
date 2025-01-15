<?php

return [
    'dontHavePermission' => 'You do not have permission to access this page! Please login again!',
    'userNameDash' => 'User',
    'logout' => 'Logout',

    'helpers' => [
        'modelValidation' => [
            'idFieldNotFound' => ':modelLabel not found!',
            'invalidField' => 'The field ":attribute" contains an invalid value!',
            'verifyBeforeSave' => 'Verify the data before saving!',
            'validateSuccess' => 'Data validated successfully!',
        ],
    ],

    'models' => [
        'User' => [
            'name' => 'User',
            'roles' => [
                'root' => 'Root',
                'manager' => 'Manager',
                'client' => 'Client',
            ],
            'fields' => [
                'name' => 'First Name',
                'lastName' => 'Last Name',
                'pictureUrl' => 'Picture URL',
                'password' => 'Password',
                'passwordToken' => 'Password Token',
                'role' => 'Role',
                'active' => 'Active',
            ],
            'fLogin' => [
                'invalidEmail' => 'Enter a valid e-mail!',
                'emptyPassword' => 'Fill in the password!',
                'invalidCredentials' => 'Invalid user or password!',
                'loginUserError' => 'Error trying to log in the user!',
                'loginSuccess' => 'User logged in successfully!',
            ],
        ],

        'Client' => [
            'name' => 'Cliente',
            'gender' => [
                'male' => 'Masculino',
                'female' => 'Feminino',
            ],
            'fields' => [
                'phone' => 'Phone',
                'gender' => 'Gender',
                'birthdate' => 'Birthdate',
                'height' => 'Height',
                'weight' => 'Weight',
            ]
        ],

        'Goal' => [
            'name' => 'Goal',
            'fields' => [
                'objective' => 'Objective',
                'target_weight' => 'Target Weight',
                'deadline' => 'Deadline',
            ],
            'objective' => [
                'weight' => 'Weight Loss',
                'muscle' => 'Muscle Gain',
                'health' => 'Health',
            ]
        ],

        'Avaliation' => [
            'name' => 'Avaliation',
            'fields' => [
                'date' => 'Date',
                'body_fat_perc' => 'Body Fat Percentage',
                'skeletal_muscle_mass_kg' => 'Skeletal Muscle Mass',
                'muscle_rate_perc' => 'Muscle Rate',
                'subcutaneous_fat_perc' => 'Subcutaneous Fat Percentage',
                'visceral_fat_perc' => 'Visceral Fat Percentage',
                'body_water_perc' => 'Body Water Percentage',
                'skeletal_muscle_perc' => 'Skeletal Muscle Percentage',
                'muscle_mass_kg' => 'Muscle Mass',
                'bone_mass_kg' => 'Bone Mass',
                'protein_perc' => 'Protein Percentage',
            ],
        ],
    ],

    'pages' => [
        'login' => [
            'emailPlaceholder' => 'Type your e-mail',
            'passwordPlaceholder' => 'Type your password',
            'loginButton' => 'Login',
            'loginGoogle' => 'Login with Google',
            'forgotPassword' => 'Forgot your password?',
        ],

        'client' => [
            'index' => [
                'title' => 'Clients',
                'addButton' => 'Add Client',
            ],
            'table' => [
                'colName' => 'Name',
                'colEmail' => 'Email',
                'colPhone' => 'Phone',
            ]
        ]
    ],
];
