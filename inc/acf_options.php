<?php
/**
 * ACF Options pages.
 *
 * Global, non-block content (footer, and future header/SEO defaults) lives
 * here instead of in a Gutenberg block. Field groups are attached via their
 * location rules in acf-json/ — see docs/acf-block-patterns.md § Footer.
 */

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
}
