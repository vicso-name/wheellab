<?php

defined('ABSPATH') || exit;

if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
        'page_title' => __('Theme Options', 'wheellab'),
        'menu_title' => __('Theme Options', 'wheellab'),
        'menu_slug'  => 'theme-options',
        'capability' => 'edit_theme_options',
        'icon_url'   => 'dashicons-admin-customizer',
        'redirect'   => true,
    ]);

    acf_add_options_sub_page([
        'page_title'  => __('Header', 'wheellab'),
        'menu_title'  => __('Header', 'wheellab'),
        'parent_slug' => 'theme-options',
    ]);

    acf_add_options_sub_page([
        'page_title'  => __('Footer', 'wheellab'),
        'menu_title'  => __('Footer', 'wheellab'),
        'parent_slug' => 'theme-options',
    ]);

    acf_add_options_sub_page([
        'page_title'  => __('Reviews', 'wheellab'),
        'menu_title'  => __('Reviews', 'wheellab'),
        'parent_slug' => 'theme-options',
    ]);

    acf_add_options_sub_page([
        'page_title'  => __('Contact', 'wheellab'),
        'menu_title'  => __('Contact', 'wheellab'),
        'parent_slug' => 'theme-options',
    ]);
}
