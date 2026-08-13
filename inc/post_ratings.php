<?php
/**
 * Post Ratings
 *
 * Backs template-parts/sections/single_post_rating.php (the "Rate this
 * article" widget on single.php). Votes live in a dedicated DB table
 * rather than postmeta/comments, since we need one row per (post,
 * rater) with a UNIQUE constraint the database itself enforces — that's
 * the actual guard against a rater voting twice, not the disabled
 * button state in the template (that's just UX; a duplicate POST straight
 * to admin-ajax.php would otherwise be able to skip past a JS-only check).
 *
 * Rater identity: logged-in user ID, or an opaque per-browser cookie for
 * guests, set lazily on their first vote. Deliberately NOT IP-based —
 * shared/NAT'd IPs (offices, cafes) would false-reject unrelated visitors.
 *
 * Average/count are cached into postmeta (_wheellab_rating_average,
 * _wheellab_rating_count) so pages that just display them (if any, later)
 * don't need a table read + AVG() on every load — only recomputed when a
 * new vote is actually written.
 */

const WHEELLAB_RATINGS_DB_VERSION = '1.0';

function wheellab_ratings_table_name(): string {
    global $wpdb;
    return $wpdb->prefix . 'wheellab_post_ratings';
}

function wheellab_create_ratings_table(): void {
    global $wpdb;

    $table           = wheellab_ratings_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id BIGINT UNSIGNED NOT NULL,
        rater_key VARCHAR(64) NOT NULL,
        rating TINYINT UNSIGNED NOT NULL,
        created_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY post_rater (post_id, rater_key),
        KEY post_id (post_id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    update_option('wheellab_ratings_db_version', WHEELLAB_RATINGS_DB_VERSION);
}
add_action('after_switch_theme', 'wheellab_create_ratings_table');

// Safety net for installs where this file shipped after the theme was
// already active (after_switch_theme won't fire again on its own).
add_action('init', static function () {
    if (get_option('wheellab_ratings_db_version') !== WHEELLAB_RATINGS_DB_VERSION) {
        wheellab_create_ratings_table();
    }
});

/**
 * Identifies the current visitor for duplicate-vote checks.
 *
 * @param bool $allow_cookie_set Whether a guest cookie may be created
 *                                right now (only during the actual vote
 *                                request — read-only page renders must
 *                                not start setting cookies from a GET).
 */
function wheellab_get_rater_key(bool $allow_cookie_set = false): string {
    if (is_user_logged_in()) {
        return 'user_' . get_current_user_id();
    }

    if (!empty($_COOKIE['wheellab_rater']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['wheellab_rater'])) {
        return 'guest_' . $_COOKIE['wheellab_rater'];
    }

    $token = bin2hex(random_bytes(16));

    if ($allow_cookie_set && !headers_sent()) {
        setcookie('wheellab_rater', $token, [
            'expires'  => time() + YEAR_IN_SECONDS,
            'path'     => defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/',
            'domain'   => defined('COOKIE_DOMAIN') && COOKIE_DOMAIN ? COOKIE_DOMAIN : '',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['wheellab_rater'] = $token;
    }

    return 'guest_' . $token;
}

/**
 * The rating this visitor already gave $post_id, or null if they haven't
 * (or if they're an anonymous guest with no rater cookie yet, in which
 * case a DB lookup isn't even needed — they can't have a row).
 */
function wheellab_get_user_post_rating(int $post_id): ?int {
    if (!is_user_logged_in() && empty($_COOKIE['wheellab_rater'])) {
        return null;
    }

    global $wpdb;
    $table  = wheellab_ratings_table_name();
    $rating = $wpdb->get_var($wpdb->prepare(
        "SELECT rating FROM {$table} WHERE post_id = %d AND rater_key = %s",
        $post_id,
        wheellab_get_rater_key(false)
    ));

    return $rating !== null ? (int) $rating : null;
}

/**
 * Cached read — recomputes from the table only when the cache is empty
 * (i.e. never written yet, including "zero votes").
 */
function wheellab_get_post_rating_stats(int $post_id): array {
    $average = get_post_meta($post_id, '_wheellab_rating_average', true);
    $count   = get_post_meta($post_id, '_wheellab_rating_count', true);

    if ($average === '' || $count === '') {
        return wheellab_refresh_post_rating_stats($post_id);
    }

    return ['average' => (float) $average, 'count' => (int) $count];
}

function wheellab_refresh_post_rating_stats(int $post_id): array {
    global $wpdb;
    $table = wheellab_ratings_table_name();

    $row = $wpdb->get_row($wpdb->prepare(
        "SELECT COUNT(*) AS total, AVG(rating) AS average FROM {$table} WHERE post_id = %d",
        $post_id
    ));

    $count   = $row ? (int) $row->total : 0;
    $average = $row && $row->average !== null ? round((float) $row->average, 1) : 0.0;

    update_post_meta($post_id, '_wheellab_rating_average', $average);
    update_post_meta($post_id, '_wheellab_rating_count', $count);

    return ['average' => $average, 'count' => $count];
}

function wheellab_ajax_rate_post(): void {
    check_ajax_referer('wheellab_rate_post', 'nonce');

    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $rating  = isset($_POST['rating']) ? absint($_POST['rating']) : 0;

    if (!$post_id || get_post_type($post_id) !== 'post' || $rating < 1 || $rating > 5) {
        wp_send_json_error(['message' => __('Invalid rating request.', 'wheellab')], 400);
    }

    global $wpdb;
    $table     = wheellab_ratings_table_name();
    $rater_key = wheellab_get_rater_key(true);

    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT rating FROM {$table} WHERE post_id = %d AND rater_key = %s",
        $post_id,
        $rater_key
    ));

    if ($existing !== null) {
        $stats = wheellab_get_post_rating_stats($post_id);
        wp_send_json_error([
            'message'       => __('You have already rated this article.', 'wheellab'),
            'already_rated' => true,
            'rating'        => (int) $existing,
            'average'       => $stats['average'],
            'count'         => $stats['count'],
        ], 409);
    }

    $inserted = $wpdb->insert($table, [
        'post_id'    => $post_id,
        'rater_key'  => $rater_key,
        'rating'     => $rating,
        'created_at' => current_time('mysql'),
    ], ['%d', '%s', '%d', '%s']);

    // The UNIQUE KEY is the real guard — a near-simultaneous duplicate
    // (double-click, two tabs) lands here instead of the $existing check
    // above winning the race.
    if (!$inserted) {
        $stats = wheellab_get_post_rating_stats($post_id);
        wp_send_json_error([
            'message'       => __('You have already rated this article.', 'wheellab'),
            'already_rated' => true,
            'average'       => $stats['average'],
            'count'         => $stats['count'],
        ], 409);
    }

    $stats = wheellab_refresh_post_rating_stats($post_id);

    wp_send_json_success([
        'rating'  => $rating,
        'average' => $stats['average'],
        'count'   => $stats['count'],
    ]);
}
add_action('wp_ajax_wheellab_rate_post', 'wheellab_ajax_rate_post');
add_action('wp_ajax_nopriv_wheellab_rate_post', 'wheellab_ajax_rate_post');
