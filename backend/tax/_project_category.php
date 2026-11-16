<?php
register_taxonomy(
    'project_category',
    ['project'],
    [
        'label' => 'Kategorie',
        'labels' => [
            'name' => 'Kategorien',
            'singular_name' => 'Kategorie',
            'menu_name' => 'Kategorien',
            'all_items' => 'Alle Kategorien',
            'edit_item' => 'Kategorie bearbeiten',
            'view_item' => 'Kategorie ansehen',
            'update_item' => 'Kategorie updaten',
            'add_new_item' => 'Neue Kategorie hinzufügen',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => false,
        'show_in_nav_menus' => false,
        'show_admin_column' => true,
        'query_var' => false,
        'hierarchical' => true,
        'rewrite' => false,
    ],
);
