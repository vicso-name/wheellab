<?php

defined('ABSPATH') || exit;

const WHEELLAB_SEARCH_AJAX_LIMIT = 8;
const WHEELLAB_SEARCH_PAGE_LIMIT = 12;
const WHEELLAB_SEARCH_MIN_LENGTH = 2;

/**
 * Return all public post types that WordPress considers searchable.
 * Attachments are deliberately excluded from the site-wide search UI.
 */
function wheellab_search_post_types(): array {
    $post_types = get_post_types([
        'public'              => true,
        'exclude_from_search' => false,
    ], 'names');

    unset($post_types['attachment']);

    return (array) apply_filters('wheellab_search_post_types', array_values($post_types));
}

/**
 * Short, human-readable label used on search cards.
 */
function wheellab_search_result_type_label(int $post_id): string {
    $post_type = get_post_type($post_id);
    $object    = $post_type ? get_post_type_object($post_type) : null;

    return $object && !empty($object->labels->singular_name)
        ? (string) $object->labels->singular_name
        : __('Content', 'wheellab');
}

/**
 * Prefer the editorial short description on Service and Case Study items,
 * then fall back to the native excerpt/content for other searchable content.
 */
function wheellab_search_result_excerpt(int $post_id): string {
    $post_type = get_post_type($post_id);
    $text      = '';

    if (in_array($post_type, ['service', 'case_study'], true) && function_exists('get_field')) {
        $text = (string) (get_field('description', $post_id) ?: '');
    }

    if ($text === '') {
        $post = get_post($post_id);
        if ($post) {
            $text = $post->post_excerpt ?: $post->post_content;
        }
    }

    $text = wp_strip_all_tags(strip_shortcodes($text));
    $text = preg_replace('/\s+/', ' ', $text) ?: '';

    return wp_trim_words(trim($text), 24, '…');
}

/**
 * Keep the non-JS search results page site-wide too, not posts-only.
 */
add_action('pre_get_posts', function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query() || !$query->is_search()) {
        return;
    }

    $query->set('post_type', wheellab_search_post_types());
    $query->set('post_status', 'publish');
    $query->set('posts_per_page', WHEELLAB_SEARCH_PAGE_LIMIT);
    $query->set('ignore_sticky_posts', true);
});

add_action('wp_ajax_wheellab_site_search', 'wheellab_ajax_site_search');
add_action('wp_ajax_nopriv_wheellab_site_search', 'wheellab_ajax_site_search');

function wheellab_ajax_site_search(): void {
    check_ajax_referer('wheellab_site_search', 'nonce');

    $search = isset($_POST['search'])
        ? sanitize_text_field(wp_unslash($_POST['search']))
        : '';
    $search = trim($search);

    if (function_exists('mb_substr')) {
        $search = mb_substr($search, 0, 100);
    } else {
        $search = substr($search, 0, 100);
    }

    $length = function_exists('mb_strlen') ? mb_strlen($search) : strlen($search);
    if ($length < WHEELLAB_SEARCH_MIN_LENGTH) {
        wp_send_json_success([
            'html'       => '',
            'foundPosts' => 0,
            'searchUrl'  => '',
        ]);
    }

    $query = new WP_Query([
        'post_type'           => wheellab_search_post_types(),
        'post_status'         => 'publish',
        'posts_per_page'      => WHEELLAB_SEARCH_AJAX_LIMIT,
        's'                   => $search,
        'orderby'             => 'relevance',
        'ignore_sticky_posts' => true,
    ]);

    ob_start();

    while ($query->have_posts()) {
        $query->the_post();
        get_template_part('template-parts/search/result-card', null, [
            'context' => 'header',
        ]);
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success([
        'html'       => $html,
        'foundPosts' => (int) $query->found_posts,
        'searchUrl'  => add_query_arg('s', $search, home_url('/')),
    ]);
}
