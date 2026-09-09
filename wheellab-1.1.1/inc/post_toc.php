<?php

defined('ABSPATH') || exit;

const WHEELLAB_TOC_LABELS_META = '_wheellab_toc_labels';

/**
 * Normalize a heading exactly the same way on the editor/front end before it is
 * used as a lookup key for a custom TOC label.
 */
function wheellab_toc_normalize_heading_text(string $text): string {
    $text = trim($text);
    $text = (string) preg_replace('/\s+/u', ' ', $text);

    return $text;
}

/**
 * Build a deterministic key for a heading. The occurrence counter makes
 * duplicate H2 titles safe without coupling overrides to their global order.
 */
function wheellab_toc_heading_key(string $text, array &$occurrences): string {
    $text = wheellab_toc_normalize_heading_text($text);

    if (!isset($occurrences[$text])) {
        $occurrences[$text] = 0;
    }

    $occurrences[$text]++;

    return $occurrences[$text] . '|' . $text;
}

function wheellab_sanitize_toc_labels_meta($value): string {
    if (!is_string($value) || trim($value) === '') {
        return '';
    }

    $decoded = json_decode($value, true);
    if (!is_array($decoded)) {
        return '';
    }

    $clean = [];

    foreach ($decoded as $key => $label) {
        if (!is_string($key) || !is_scalar($label)) {
            continue;
        }

        // Keep the heading text in the key intact; only remove markup/control
        // characters. Changing punctuation here would make editor/front-end
        // lookup inconsistent.
        $key = wp_strip_all_tags($key);
        $key = (string) preg_replace('/[\x00-\x1F\x7F]/u', '', $key);
        $key = trim($key);
        $label = trim(sanitize_text_field((string) $label));

        if ($key !== '' && $label !== '') {
            $clean[$key] = $label;
        }
    }

    return $clean ? wp_json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
}

function wheellab_get_post_toc_labels(int $post_id = 0): array {
    $post_id = $post_id ?: get_the_ID();
    if (!$post_id) {
        return [];
    }

    $raw = get_post_meta($post_id, WHEELLAB_TOC_LABELS_META, true);
    if (!is_string($raw) || $raw === '') {
        return [];
    }

    $labels = json_decode($raw, true);
    return is_array($labels) ? $labels : [];
}

add_action('init', function () {
    register_post_meta('post', WHEELLAB_TOC_LABELS_META, [
        'type'              => 'string',
        'single'            => true,
        'default'           => '',
        'show_in_rest'      => true,
        'sanitize_callback' => 'wheellab_sanitize_toc_labels_meta',
        'auth_callback'     => static function (): bool {
            return current_user_can('edit_posts');
        },
    ]);
});

add_action('enqueue_block_editor_assets', function () {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'post') {
        return;
    }

    wp_enqueue_script(
        'wheellab-post-toc-editor',
        wheellab_asset_url('build/js/post_toc_editor.min.js'),
        ['wp-components', 'wp-data', 'wp-editor', 'wp-element', 'wp-i18n', 'wp-plugins'],
        wheellab_asset_ver('build/js/post_toc_editor.min.js'),
        true
    );
});
