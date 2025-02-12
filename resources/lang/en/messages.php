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
    'buttonLoadMore' => 'Load more',
    'modelErrorNoAccess' => 'You don\'t have permissions to access this content!',
    'saveModelNotFound' => ':modelName not found for editing!',
    'saveModelErrorSavingOther' => 'You don\'t have permission to save this :modelName!',
    'saveModelErrorSaving' => 'There was a problem saving the :modelName, try again.',
    'saveModelSuccessAdding' => ':modelName added successfully!',
    'saveModelSuccessEditing' => ':modelName edited successfully!',
    'saveModelSuccessRemoving' => ':modelName removed successfully!',
    'confirmModalTitle' => 'Confirmation',
    'infoModalTitle' => 'Information',
    'tableActionView' => 'View',

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
                'male' => 'Male',
                'female' => 'Female',
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
        ],

        'Goal' => [
            'name' => 'Goal',
            'fields' => [
                'objective' => 'Objective',
                'initial_weight' => 'Initial Weight',
                'target_weight' => 'Target Weight',
                'deadline' => 'Deadline',
            ],
            'objective' => [
                'weight' => 'Weight Loss',
                'muscle' => 'Muscle Gain',
                'health' => 'Health',
            ],
            'fSave' => [
                'objectiveDateMustBeGreaterThanToday' => 'The deadline must be greater than today!',
            ],
            'confirmDeleteModalText' => 'Are you sure you want to remove this goal? This operation is permanent and cannot be undone!',
        ],

        'Avaliation' => [
            'name' => 'Avaliation',
            'fields' => [
                'date' => 'Date',
                'body_fat_perc' => 'Body Fat Percentage',
                'skeletal_muscle_perc' => 'Skeletal Muscle Percentage',
                'visceral_fat_kg' => 'Visceral Fat',
                'waist_circumference_cm' => 'Waist Circumference',
            ],
            'labelFatMass' => 'Fat Mass',
            'labelLeanMass' => 'Lean Mass',
            'labelTmb' => 'Basal Metabolic Rate',
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
                'cardMeasures' => 'Initial Measures',
                'cardGoals' => 'Goals',
                'cardAvaliations' => 'Avaliations',
                'noGoals' => 'No active goals! Take the opportunity to add a new one.',
                'btnNewGoal' => 'New Goal',
                'btnOldGoals' => 'Old Goals',
                'btnNewAvaliation' => 'New Avaliation',
                'labelActualWeight' => 'Peso Atual',
            ],
            'table' => [
                'colName' => 'Name',
                'colEmail' => 'Email',
                'colPhone' => 'Phone',
            ]
        ],

        'goal' => [
            'modalAddGoal' => [
                'title' => 'Add Goal',
                'labelDaysToDeadline' => 'Days to Deadline',
                'labelProgress' => 'Progress',
                'labelWeightChange' => 'Weight Change',
            ],
        ],

        'avaliation' => [
            'modalAddAvaliation' => [
                'title' => 'Add Avaliation',
                'skeletal_muscle_perc_info' => 'If :skeletal_muscle_perc is not specified, the formula by "Lee et al" will be used for calculation.',
                'waist_circumference_info' => 'If :visceral_fat is not specified, :waist_circumference will be used for calculation.',
            ],
            'deleteConfirmation' => 'Are you sure you want to delete this avaliation? This operation is permanent and cannot be undone!',
            'deleteSuccess' => 'Avaliation deleted successfully!',
        ],
    ],
];
