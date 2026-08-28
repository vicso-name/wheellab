<?php

defined('ABSPATH') || exit;

add_action('init', 'wheellab_register_service_cpt');
function wheellab_register_service_cpt() {
    register_post_type('service', [
        'labels' => [
            'name'               => __('Services', 'wheellab'),
            'singular_name'      => __('Service', 'wheellab'),
            'add_new_item'       => __('Add New Service', 'wheellab'),
            'edit_item'          => __('Edit Service', 'wheellab'),
            'new_item'           => __('New Service', 'wheellab'),
            'view_item'          => __('View Service', 'wheellab'),
            'all_items'          => __('Services', 'wheellab'),
            'search_items'       => __('Search Services', 'wheellab'),
            'not_found'          => __('No services found', 'wheellab'),
            'not_found_in_trash' => __('No services found in Trash', 'wheellab'),
        ],
        'public'       => true,
        'has_archive'  => false,
        'rewrite'      => ['slug' => 'services'],
        'menu_icon'    => 'dashicons-admin-tools',
        'menu_position' => 21,
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}
