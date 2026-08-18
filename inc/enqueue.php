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

    // Privacy Policy page template — hero styles, plus single_post_body
    // .min.css for its shared .single-post-content article-typography
    // rules (see that file's header comment for why it's reused here).
    if (is_page_template('template-privacy-policy.php')) {
        wp_enqueue_style(
            'btf-privacy-hero-styles',
            wheellab_asset_url('build/css/sections/privacy_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/privacy_hero.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-body-styles',
            wheellab_asset_url('build/css/sections/single_post_body.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_body.min.css')
        );
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

    // Case Study Hero (acf/case-study-hero) is a real ACF block now, so
    // wheellab_enqueue_detected_block_assets() (inc/acf_blocks.php) picks
    // its CSS/JS up automatically wherever the editor inserts it — no
    // manual enqueue needed here, same as every other block.

    // Single post (single.php) — none of these are ACF blocks, same
    // reasoning as everything else in this block.
    if (is_singular('post')) {
        wp_enqueue_style(
            'btf-single-post-hero-styles',
            wheellab_asset_url('build/css/sections/single_post_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_hero.min.css')
        );
        // blog_card.php is reused by single_post_related.php below, but
        // (unlike featured_posts_section, an actual ACF block)
        // single_post_related isn't one, so wheellab_enqueue_detected_block_assets()
        // never finds it and its blog-card-fallback path doesn't fire here
        // either (this page's other real ACF blocks make $found_any true) —
        // same reasoning as is_author()'s explicit blog-card enqueue below.
        wp_enqueue_style(
            'btf-blog-card-styles',
            wheellab_asset_url('build/css/sections/blog_card.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/blog_card.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-body-styles',
            wheellab_asset_url('build/css/sections/single_post_body.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_body.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-toc-styles',
            wheellab_asset_url('build/css/sections/single_post_toc.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_toc.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-cta-styles',
            wheellab_asset_url('build/css/sections/single_post_cta.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_cta.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-rating-styles',
            wheellab_asset_url('build/css/sections/single_post_rating.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_rating.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-author-card-styles',
            wheellab_asset_url('build/css/sections/single_post_author_card.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_author_card.min.css')
        );
        wp_enqueue_style(
            'btf-single-post-related-styles',
            wheellab_asset_url('build/css/sections/single_post_related.min.css'),
            ['btf-main-styles', 'btf-blog-card-styles'],
            wheellab_asset_ver('build/css/sections/single_post_related.min.css')
        );
        // contact_section.min.css IS an ACF block, but it's rendered here
        // via get_template_part() (falling back to Theme Options > Contact,
        // same as author.php) rather than being inserted through the block
        // editor, so wheellab_enqueue_detected_block_assets()'s post_content
        // scan never finds it either — same reasoning as blog-card above.
        wp_enqueue_style(
            'btf-contact-section-styles',
            wheellab_asset_url('build/css/sections/contact_section.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/contact_section.min.css')
        );
    }

    // Author archive (author.php) — same reasoning as the blocks above:
    // author_hero.min.css isn't an ACF block so it's never auto-detected.
    // reviews_section.min.css/contact_section.min.css ARE ACF blocks, but
    // wheellab_enqueue_detected_block_assets() (inc/acf_blocks.php) only
    // runs on is_singular() pages — author.php is an archive, so it's
    // never picked up there either and has to be enqueued explicitly too.
    if (is_author()) {
        wp_enqueue_style(
            'btf-author-hero-styles',
            wheellab_asset_url('build/css/sections/author_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/author_hero.min.css')
        );
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
        wp_enqueue_style(
            'btf-reviews-section-styles',
            wheellab_asset_url('build/css/sections/reviews_section.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/reviews_section.min.css')
        );
        wp_enqueue_style(
            'btf-contact-section-styles',
            wheellab_asset_url('build/css/sections/contact_section.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/contact_section.min.css')
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

    // Single post: sidebar Table of Contents scroll-spy, the AJAX-driven
    // "Rate this article" widget, and the "Read more" swiper carousel.
    if (is_singular('post')) {
        wp_enqueue_script(
            'btf-single-post-toc-script',
            wheellab_asset_url('build/js/sections/single_post_toc.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/single_post_toc.min.js'),
            true
        );
        wp_enqueue_script(
            'btf-single-post-rating-script',
            wheellab_asset_url('build/js/sections/single_post_rating.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/single_post_rating.min.js'),
            true
        );
        wp_localize_script('btf-single-post-rating-script', 'wheellabRating', [
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('wheellab_rate_post'),
            'personSingular' => __('%s person rated', 'wheellab'),
            'personPlural'   => __('%s people rated', 'wheellab'),
        ]);
        wp_enqueue_script(
            'btf-single-post-related-script',
            wheellab_asset_url('build/js/sections/single_post_related.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/single_post_related.min.js'),
            true
        );
        wp_enqueue_script(
            'btf-contact-section-script',
            wheellab_asset_url('build/js/sections/contact_section.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/contact_section.min.js'),
            true
        );
    }

    // Author archive: "load more" pagination (no chips — see
    // author_posts.js), plus the Reviews/Contact block scripts that
    // wheellab_enqueue_detected_block_assets() can't reach here (see the
    // matching CSS block above for why).
    if (is_author()) {
        wp_enqueue_script(
            'btf-author-posts-script',
            wheellab_asset_url('build/js/sections/author_posts.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/author_posts.min.js'),
            true
        );
        wp_localize_script('btf-author-posts-script', 'wheellabBlog', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wheellab_blog_query'),
        ]);

        wp_enqueue_script(
            'btf-reviews-section-script',
            wheellab_asset_url('build/js/sections/reviews_section.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/reviews_section.min.js'),
            true
        );
        wp_enqueue_script(
            'btf-contact-section-script',
            wheellab_asset_url('build/js/sections/contact_section.min.js'),
            [],
            wheellab_asset_ver('build/js/sections/contact_section.min.js'),
            true
        );
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
