<?php

defined('ABSPATH') || exit;

function wheellab_remove_emoji(): void {
    remove_action('wp_head',             'print_emoji_detection_script', 7);
    remove_action('wp_print_styles',     'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles',  'print_emoji_styles');
    remove_filter('the_content_feed',    'wp_staticize_emoji');
    remove_filter('comment_text_rss',    'wp_staticize_emoji');
    remove_filter('wp_mail',             'wp_staticize_emoji_for_email');
}
add_action('init', 'wheellab_remove_emoji');

function wheellab_remove_head_noise(): void {

    remove_action('wp_head', 'feed_links',       2);
    remove_action('wp_head', 'feed_links_extra', 3);

    remove_action('wp_head', 'wp_generator');

    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
}
add_action('init', 'wheellab_remove_head_noise');

add_filter('xmlrpc_enabled', '__return_false');

function wheellab_remove_jquery_migrate(WP_Scripts $scripts): void {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff(
            $scripts->registered['jquery']->deps,
            ['jquery-migrate']
        );
    }
}
add_action('wp_default_scripts', 'wheellab_remove_jquery_migrate');

function wheellab_remove_unneeded_image_sizes(array $sizes): array {
    unset($sizes['medium_large']);
    return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'wheellab_remove_unneeded_image_sizes');

function wheellab_register_admin_image_sizes(): void {
    add_image_size('acf-icon-preview', 80, 80, true);
}
add_action('after_setup_theme', 'wheellab_register_admin_image_sizes');

function wheellab_register_theme_support(): void {

    add_theme_support('title-tag');

    add_theme_support('post-thumbnails');

    add_theme_support('html5', [
        'search-form', 'comment-form', 'comment-list',
        'gallery', 'caption', 'style', 'script',
    ]);

    add_theme_support('editor-styles');

    register_nav_menus([
        'primary' => __('Primary Navigation', 'wheellab'),
        'footer'  => __('Footer Navigation', 'wheellab'),
    ]);
}
add_action('after_setup_theme', 'wheellab_register_theme_support');

function wheellab_disable_comment_support(): void {
    remove_post_type_support('post', 'comments');
    remove_post_type_support('page', 'comments');
}
add_action('init', 'wheellab_disable_comment_support');

add_filter('comments_open',  '__return_false', 20, 2);
add_filter('pings_open',     '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);

function wheellab_remove_comments_menu(): void {
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'wheellab_remove_comments_menu');

function wheellab_remove_comments_admin_bar(WP_Admin_Bar $bar): void {
    $bar->remove_node('comments');
}
add_action('admin_bar_menu', 'wheellab_remove_comments_admin_bar', 999);

function wheellab_redirect_comments_admin(): void {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url(), 301);
        exit;
    }
}
add_action('admin_init', 'wheellab_redirect_comments_admin');

add_filter('acf/settings/save_json', fn(): string =>
    get_template_directory() . '/acf-json'
);

add_filter('acf/settings/load_json', function(array $paths): array {
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});

$wheellab_acf_dir = get_template_directory() . '/acf-json';
if (!is_dir($wheellab_acf_dir)) {
    wp_mkdir_p($wheellab_acf_dir);
}

add_filter('login_errors', fn(): string =>
    __('Incorrect credentials. Please try again.', 'wheellab')
);

add_filter('login_site_html_link', '__return_empty_string');

function wheellab_require_email_login(
    WP_User|WP_Error|null $user,
    string $username,
    string $password
): WP_User|WP_Error|null {
    if ($user instanceof WP_User) return $user;
    if (!is_email($username)) {
        return new WP_Error(
            'email_required',
            __('Please log in with your email address.', 'wheellab')
        );
    }
    return $user;
}
add_filter('authenticate', 'wheellab_require_email_login', 10, 3);
