<?php
/**
 * Clean, predictable enqueues with early critical CSS/JS,
 * conditional Swiper, and safe versioning.
 */

defined('ABSPATH') || exit;

/* -----------------------------------------------------------
 * Helpers
 * ----------------------------------------------------------- */

/**
 * Return theme URI (child theme aware).
 */
function wheellab_theme_uri(): string {
    return get_stylesheet_directory_uri();
}

/**
 * Return theme dir (child theme aware).
 */
function wheellab_theme_dir(): string {
    return get_stylesheet_directory();
}

/**
 * Smart asset version: filemtime if exists, else S_VERSION or theme version.
 */
function wheellab_asset_ver(string $rel): string {
    $abs = wheellab_theme_dir() . '/' . ltrim($rel, '/');
    if (file_exists($abs)) {
        return (string) filemtime($abs);
    }
    if (defined('S_VERSION')) return (string) S_VERSION;
    return (string) wp_get_theme()->get('Version');
}

/**
 * Build asset URL from relative path.
 */
function wheellab_asset_url(string $rel): string {
    return wheellab_theme_uri() . '/' . ltrim($rel, '/');
}

/**
 * Should we load Swiper on this request?
 * You can override with: add_filter('wheellab_load_swiper', '__return_false');
 */
function wheellab_should_load_swiper(): bool {
    return (bool) apply_filters('wheellab_load_swiper', true);
}

/* -----------------------------------------------------------
 * Admin assets
 * ----------------------------------------------------------- */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'btf-admin-styles',
        wheellab_asset_url('build/css/admin-styles.min.css'),
        [],
        wheellab_asset_ver('build/css/admin-styles.min.css')
    );
});

/* -----------------------------------------------------------
 * Frontend assets
 * ----------------------------------------------------------- */
/**
 * Load styles as early as possible to reduce FOUC.
 * Priority 5 -> earlier than default 10.
 */
add_action('wp_enqueue_scripts', function () {

    // 0) Optional: load style.css only if you actually use it.
    // If your theme's design is entirely in build/css/style.min.css, you can safely skip it.
    // wp_enqueue_style('btf-style', get_stylesheet_uri(), [], wheellab_asset_ver('style.css'));
    // wp_style_add_data('btf-style', 'rtl', 'replace');

    // 1) Swiper (conditionally)
    if (wheellab_should_load_swiper()) {
        wp_enqueue_style(
            'btf-swiper-style',
            wheellab_asset_url('assets/swiper/swiper-bundle.min.css'),
            [],
            wheellab_asset_ver('assets/swiper/swiper-bundle.min.css')
        );
    }

    // 2) Main theme CSS (make it depend on swiper-style if present)
    $style_deps = [];
    if (wheellab_should_load_swiper()) $style_deps[] = 'btf-swiper-style';

    wp_enqueue_style(
        'btf-main-styles',
        wheellab_asset_url('build/css/style.min.css'),
        $style_deps,
        wheellab_asset_ver('build/css/style.min.css')
    );

    // 2b) Page-template-specific CSS. These sections aren't ACF blocks
    // (parse_blocks() won't find them), so they're not covered by
    // wheellab_enqueue_detected_block_assets() — enqueue per template here.
    if (is_page_template('template-blog.php')) {
        wp_enqueue_style(
            'btf-blog-hero-styles',
            wheellab_asset_url('build/css/sections/blog_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/blog_hero.min.css')
        );
        // blog_card.min.css is shared with the Featured Posts Section block
        // (enqueued there via block-detection instead — see
        // wheellab_enqueue_detected_block_assets() in inc/acf_blocks.php).
        wp_enqueue_style(
            'btf-blog-card-styles',
            wheellab_asset_url('build/css/sections/blog_card.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/blog_card.min.css')
        );
        wp_enqueue_style(
            'btf-blog-filter-styles',
            wheellab_asset_url('build/css/sections/blog_filter.min.css'),
            ['btf-main-styles', 'btf-blog-card-styles'],
            wheellab_asset_ver('build/css/sections/blog_filter.min.css')
        );

        // Bare get_field() is safe here — this runs during the real page
        // render (no nested WP_Query active yet), same as blog_hero.php's
        // own field lookups above.
        if (get_field('subscribe_enabled')) {
            wp_enqueue_style(
                'btf-blog-subscribe-styles',
                wheellab_asset_url('build/css/sections/blog_subscribe.min.css'),
                ['btf-main-styles'],
                wheellab_asset_ver('build/css/sections/blog_subscribe.min.css')
            );
        }
    }

    // Case Study archive + single (case_study CPT) — same reasoning:
    // archive-case_study.php / single-case_study.php aren't ACF blocks
    // either. case_study_section.min.css supplies the reused
    // .case-study-section__card-* component styles; case_study_archive
    // is the archive/single-only layout around them.
    if (is_post_type_archive('case_study') || is_singular('case_study')) {
        wp_enqueue_style(
            'btf-case-study-card-styles',
            wheellab_asset_url('build/css/sections/case_study_section.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/case_study_section.min.css')
        );
        wp_enqueue_style(
            'btf-case-study-archive-styles',
            wheellab_asset_url('build/css/sections/case_study_archive.min.css'),
            ['btf-main-styles', 'btf-case-study-card-styles'],
            wheellab_asset_ver('build/css/sections/case_study_archive.min.css')
        );
    }

    // 3) Scripts
    // Swiper JS (conditionally)
    $script_deps = [];
    if (wheellab_should_load_swiper()) {
        wp_enqueue_script(
            'btf-swiper-script',
            wheellab_asset_url('assets/swiper/swiper-bundle.min.js'),
            [],
            wheellab_asset_ver('assets/swiper/swiper-bundle.min.js'),
            true // footer
        );
        $script_deps[] = 'btf-swiper-script';
    }

    wp_enqueue_script(
        'btf-main-scripts',
        wheellab_asset_url('build/js/general.min.js'),
        $script_deps,
        wheellab_asset_ver('build/js/general.min.js'),
        true
    );

    // Blog page: category filter + "load more", both AJAX-driven.
    if (is_page_template('template-blog.php')) {
        wp_enqueue_script(
            'btf-blog-filter-script',
            wheellab_asset_url('build/js/sections/blog_filter.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/blog_filter.min.js'),
            true
        );
        wp_localize_script('btf-blog-filter-script', 'wheellabBlog', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wheellab_blog_query'),
        ]);

        if (get_field('subscribe_enabled')) {
            wp_enqueue_script(
                'btf-blog-subscribe-script',
                wheellab_asset_url('build/js/sections/blog_subscribe.min.js'),
                [],
                wheellab_asset_ver('build/js/sections/blog_subscribe.min.js'),
                true
            );
            wp_localize_script('btf-blog-subscribe-script', 'wheellabMailchimp', [
                'ajaxUrl'      => admin_url('admin-ajax.php'),
                'nonce'        => wp_create_nonce('wheellab_mailchimp_subscribe'),
                'genericError' => __('Something went wrong. Please try again later.', 'wheellab'),
            ]);
        }
    }

}, 5);

/* -----------------------------------------------------------
 * Editor (block editor) assets
 * ----------------------------------------------------------- */
add_action('enqueue_block_editor_assets', function () {
    // Editor JS
    wp_enqueue_script(
        'btf-editor-scripts',
        wheellab_asset_url('build/js/admin-scripts.min.js'),
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'],
        wheellab_asset_ver('build/js/admin-scripts.min.js'),
        true
    );

    // Editor CSS
    wp_enqueue_style(
        'btf-editor-styles',
        wheellab_asset_url('build/css/acf-block-toggle.min.css'),
        ['wp-edit-blocks'],
        wheellab_asset_ver('build/css/acf-block-toggle.min.css')
    );
});

/* -----------------------------------------------------------
 * Optional optimizations
 * ----------------------------------------------------------- */

/**
 * Remove jQuery Migrate on frontend in production (optional).
 */
add_action('wp_default_scripts', function ($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $deps = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff($deps, ['jquery-migrate']);
    }
});

/**
 * If you don't use core block library CSS (classic theme) you can dequeue it.
 * Be careful: if you rely on block styles, don't remove them.
 */
// add_action('wp_enqueue_scripts', function () {
//     wp_dequeue_style('wp-block-library');
//     wp_dequeue_style('global-styles');
// }, 100);

/**
 * Resource hints (if you use external fonts/CDNs).
 * Keep minimal to avoid unnecessary DNS work.
 */
// add_filter('wp_resource_hints', function($urls, $relation_type) {
//     if ('preconnect' === $relation_type) {
//         $urls[] = 'https://fonts.googleapis.com';
//         $urls[] = 'https://fonts.gstatic.com';
//     }
//     if ('dns-prefetch' === $relation_type) {
//         $urls[] = '//fonts.googleapis.com';
//         $urls[] = '//fonts.gstatic.com';
//     }
//     return $urls;
// }, 10, 2);
