<?php
/**
 * Case Study CPT
 *
 * Backs Case Study Section (acf/case-study-section) the same way regular
 * WP posts back Featured Posts Section — the block queries this post type
 * (latest N or a manual relationship pick, see case_study_section.php)
 * instead of holding case data in its own repeater field.
 */

add_action('init', 'wheellab_register_case_study_cpt');
function wheellab_register_case_study_cpt() {
    register_post_type('case_study', [
        'labels' => [
            'name'               => __('Case Studies', 'wheellab'),
            'singular_name'      => __('Case Study', 'wheellab'),
            'add_new_item'       => __('Add New Case Study', 'wheellab'),
            'edit_item'          => __('Edit Case Study', 'wheellab'),
            'new_item'           => __('New Case Study', 'wheellab'),
            'view_item'          => __('View Case Study', 'wheellab'),
            'all_items'          => __('Case Studies', 'wheellab'),
            'search_items'       => __('Search Case Studies', 'wheellab'),
            'not_found'          => __('No case studies found', 'wheellab'),
            'not_found_in_trash' => __('No case studies found in Trash', 'wheellab'),
        ],
        'public'       => true,
        'has_archive'  => 'cases',
        'rewrite'      => ['slug' => 'cases'],
        'menu_icon'    => 'dashicons-portfolio',
        'menu_position' => 20,
        'supports'     => ['title', 'editor', 'thumbnail'],
        'show_in_rest' => true,
    ]);
}
