<?php

defined('ABSPATH') || exit;

const WHEELLAB_BLOG_POSTS_PER_PAGE = 12;

function wheellab_blog_query_args(array $category_slugs, int $paged, int $author_id = 0): array {
    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => WHEELLAB_BLOG_POSTS_PER_PAGE,
        'paged'          => max(1, $paged),
        'ignore_sticky_posts' => true,
    ];

    $category_slugs = array_filter(array_unique($category_slugs));

    if ($category_slugs) {
        // category__in is a union (OR) match — a post needs only one of the
        // selected categories, not all of them.
        $term_ids = get_terms([
            'taxonomy'   => 'category',
            'slug'       => $category_slugs,
            'fields'     => 'ids',
            'hide_empty' => false,
        ]);

        if (!empty($term_ids) && !is_wp_error($term_ids)) {
            $args['category__in'] = $term_ids;
        }
    }

    if ($author_id > 0) {
        $args['author'] = $author_id;
    }

    return $args;
}

add_action('wp_ajax_wheellab_blog_query', 'wheellab_ajax_blog_query');
add_action('wp_ajax_nopriv_wheellab_blog_query', 'wheellab_ajax_blog_query');

function wheellab_ajax_blog_query(): void {
    check_ajax_referer('wheellab_blog_query', 'nonce');

    $category_raw   = isset($_POST['category']) ? wp_unslash($_POST['category']) : '';
    $category_slugs = $category_raw !== ''
        ? array_map('sanitize_title', explode(',', $category_raw))
        : [];
    $paged     = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $author_id = isset($_POST['author']) ? absint($_POST['author']) : 0;

    $query = new WP_Query(wheellab_blog_query_args($category_slugs, $paged, $author_id));

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            get_template_part('template-parts/sections/blog_card');
        }
    }
    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html'       => $html,
        'foundPosts' => (int) $query->found_posts,
        'maxPages'   => (int) $query->max_num_pages,
        'page'       => max(1, $paged),
    ]);
}
