<?php

return [
    'thousandSeparator' => ',',
    'decimalSeparator' => '.',
    'dateFormat' => 'm/d/Y',
    'selectEmptyOption' => 'Select...',
    'dontHavePermission' => 'You do not have permission to access this page! Please login again!',
    'userNameDash' => 'User',
    'logout' => 'Logout',
    'buttonSave' => 'Save',
    'buttonBackToList' => 'Back to list',

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
                'first_name' => 'Name',
                'last_name' => 'Last Name',
                'phone' => 'Phone',
                'gender' => 'Biological Sex',
                'birthdate' => 'Birthdate',
                'height' => 'Height',
                'weight' => 'Weight',
            ],
            'clientNotFound' => 'Client not found for editing!',
            'errorSavingClient' => 'There was a problem saving the Client, try again.',
            'errorSavingOtherClient' => 'You don\'t have permission to save this client!',
            'successAddingClient' => 'Client added successfully!',
            'successEditingClient' => 'Client edited successfully!',
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
                'editButton' => 'Edit Client',
                'deleteConfirmation' => 'Are you sure you want to delete the client :clientName? This operation is permanent and cannot be undone!',
                'deleteSuccess' => 'Client deleted successfully!',
            ],
            'register' => [
                'title' => 'Register Client',
                'cardInfo' => 'Client Information',
                'cardMeasures' => 'Measures',
            ],
            'table' => [
                'colName' => 'Name',
                'colEmail' => 'Email',
                'colPhone' => 'Phone',
            ]
        ]
    ],
];
