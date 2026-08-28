<?php
/**
 * Service CPT
 *
 * Backs upcoming service-detail blocks (see acf_blocks.php) the same way
 * case_study backs the Case Study Section — blocks will query this post
 * type instead of holding service data in their own repeater fields.
 *
 * No archive template is planned for this CPT (unlike case_study, which
 * has archive-case_study.php) — the "Service" nav item is a mega-menu
 * trigger, not a link to a listing page. has_archive is off so /services/
 * doesn't resolve to the theme's empty archive.php fallback.
 */

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
