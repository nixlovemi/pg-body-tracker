<?php

return [

    /**
     * The UI framework that should be used to generate the components.
     * Can be set to:
     * - bootstrap-5
     * - bootstrap-4
     * - tailwind-3 (upcoming feature)
     */
    'ui' => 'bootstrap-5',

    /** Set all the displayed action icons. */
    'icon' => [
        'filter' => '<i class="fa-solid fas fa-filter fa-2x"></i>',
        'rows_number' => '<i class="fa-solid fas fa-list-ol"></i>',
        'sort' => '<i class="fa-solid fas fa-sort fa-fw"></i>',
        'sort_asc' => '<i class="fa-solid fas fa-sort-up fa-fw"></i>',
        'sort_desc' => '<i class="fa-solid fas fa-sort-down fa-fw"></i>',
        'search' => '<i class="fa-solid fas fa-search"></i>',
        'validate' => '<i class="fa-solid fas fa-check"></i>',
        'info' => '<i class="fa-solid fas fa-info-circle"></i>',
        'reset' => '<i class="fa-solid fas fa-undo-alt"></i>',
        'drag_drop' => '<i class="fa-solid fas fas fa-th"></i>',
        'add' => '<i class="fa-solid fas fa-plus-circle fa-fw"></i>',
        'create' => '<i class="fa-solid fas fa-plus-circle fa-fw"></i>',
        'show' => '<i class="fa-solid fas fa-eye fa-fw"></i>',
        'edit' => '<i class="fa-solid fas fa-pencil-alt fa-fw"></i>',
        'destroy' => '<i class="fa-solid fas fa-trash fa-fw"></i>',
        'active' => '<i class="fa-solid fas fa-check-circle text-success fa-fw"></i>',
        'inactive' => '<i class="fa-solid fas fa-times-circle text-danger fa-fw"></i>',
        'email_verified' => '<i class="fa-solid far fa-envelope-open text-success fa-fw"></i>',
        'email_unverified' => '<i class="fa-solid fa-envelope-open text-danger fa-fw"></i>',
        'toggle_on' => '<i class="fa-solid fas fa-toggle-on fa-fw"></i>',
        'toggle_off' => '<i class="fa-solid fas fa-toggle-off fa-fw"></i>',
    ],

    /** The default table select HTML components attributes. */
    'html_select_components_attributes' => [],

    /** Whether the select allowing to choose the number of rows per page should be displayed by default. */
    'enable_number_of_rows_per_page_choice' => false,

    /** The default number-of-rows-per-page-select options. */
    'number_of_rows_per_page_default_options' => [10, 25, 50, 75, 100],

];
