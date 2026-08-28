<?php

defined('ABSPATH') || exit;

function wheellab_theme_uri(): string {
    return get_stylesheet_directory_uri();
}

function wheellab_theme_dir(): string {
    return get_stylesheet_directory();
}

function wheellab_asset_ver(string $rel): string {
    $abs = wheellab_theme_dir() . '/' . ltrim($rel, '/');
    if (file_exists($abs)) {
        return (string) filemtime($abs);
    }
    if (defined('WHEELLAB_VERSION')) return (string) WHEELLAB_VERSION;
    return (string) wp_get_theme()->get('Version');
}

function wheellab_asset_url(string $rel): string {
    return wheellab_theme_uri() . '/' . ltrim($rel, '/');
}

function wheellab_should_load_swiper(): bool {
    return (bool) apply_filters('wheellab_load_swiper', true);
}

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'btf-admin-styles',
        wheellab_asset_url('build/css/admin-styles.min.css'),
        [],
        wheellab_asset_ver('build/css/admin-styles.min.css')
    );
});

add_action('wp_enqueue_scripts', function () {

    if (wheellab_should_load_swiper()) {
        wp_enqueue_style(
            'btf-swiper-style',
            wheellab_asset_url('assets/swiper/swiper-bundle.min.css'),
            [],
            wheellab_asset_ver('assets/swiper/swiper-bundle.min.css')
        );
    }

    $style_deps = [];
    if (wheellab_should_load_swiper()) $style_deps[] = 'btf-swiper-style';

    wp_enqueue_style(
        'btf-main-styles',
        wheellab_asset_url('build/css/style.min.css'),
        $style_deps,
        wheellab_asset_ver('build/css/style.min.css')
    );

    if (is_page_template('template-blog.php')) {
        wp_enqueue_style(
            'btf-blog-hero-styles',
            wheellab_asset_url('build/css/sections/blog_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/blog_hero.min.css')
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

        if (get_field('subscribe_enabled')) {
            wp_enqueue_style(
                'btf-blog-subscribe-styles',
                wheellab_asset_url('build/css/sections/blog_subscribe.min.css'),
                ['btf-main-styles'],
                wheellab_asset_ver('build/css/sections/blog_subscribe.min.css')
            );
        }
    }

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

    if (is_singular('post')) {
        wp_enqueue_style(
            'btf-single-post-hero-styles',
            wheellab_asset_url('build/css/sections/single_post_hero.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/single_post_hero.min.css')
        );

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

        wp_enqueue_style(
            'btf-contact-section-styles',
            wheellab_asset_url('build/css/sections/contact_section.min.css'),
            ['btf-main-styles'],
            wheellab_asset_ver('build/css/sections/contact_section.min.css')
        );
    }

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

    $script_deps = [];
    if (wheellab_should_load_swiper()) {
        wp_enqueue_script(
            'btf-swiper-script',
            wheellab_asset_url('assets/swiper/swiper-bundle.min.js'),
            [],
            wheellab_asset_ver('assets/swiper/swiper-bundle.min.js'),
            true
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

add_action('wp_default_scripts', function ($scripts) {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $deps = $scripts->registered['jquery']->deps;
        $scripts->registered['jquery']->deps = array_diff($deps, ['jquery-migrate']);
    }
});

