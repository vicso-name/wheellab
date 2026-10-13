<?php

defined('ABSPATH') || exit;

/**
 * IconSans icon set (https://github.com/mortezasabihi/iconsans, MIT).
 * SVGs live in assets/iconsans/{Linear,Bold}/{Name}.svg.
 * ACF stores a value like "Linear/Activity".
 */

/** ACF field keys that get the IconSans choices. */
function wheellab_iconsans_field_keys(): array {
    return [
        'field_ts_card_iconsans',
        'field_sit_card_iconsans',
        'field_mm_card_iconsans',
    ];
}

/** @return array<string,string> value => label, e.g. "Linear/Activity" => "Activity (Linear)". */
function wheellab_iconsans_choices(): array {
    static $choices = null;
    if ($choices !== null) return $choices;

    $choices = [];
    foreach (['Linear', 'Bold'] as $style) {
        $files = glob(wheellab_theme_dir() . '/assets/iconsans/' . $style . '/*.svg') ?: [];
        sort($files);
        foreach ($files as $file) {
            $name = basename($file, '.svg');
            $choices[$style . '/' . $name] = str_replace('-', ' ', $name) . ' (' . $style . ')';
        }
    }
    return $choices;
}

/** Validates a stored value and returns its relative file path, or ''. */
function wheellab_iconsans_path(string $value): string {
    if (!preg_match('#^(Linear|Bold)/([A-Za-z0-9-]+)$#', $value, $m)) return '';
    $rel = 'assets/iconsans/' . $m[1] . '/' . $m[2] . '.svg';
    return file_exists(wheellab_theme_dir() . '/' . $rel) ? $rel : '';
}

function wheellab_iconsans_url(string $value): string {
    $rel = wheellab_iconsans_path($value);
    return $rel ? wheellab_asset_url($rel) : '';
}

function wheellab_iconsans_svg(string $value, string $class = ''): string {
    $rel = wheellab_iconsans_path($value);
    if (!$rel) return '';

    $svg = (string) file_get_contents(wheellab_theme_dir() . '/' . $rel);
    if ($class) {
        $svg = (string) preg_replace('/<svg\s/', '<svg class="' . esc_attr($class) . '" ', $svg, 1);
    }
    return preg_replace('/<svg\s/', '<svg aria-hidden="true" focusable="false" ', $svg, 1) ?: $svg;
}

/**
 * Icon URL for a card sub-field set: IconSans pick wins, then the uploaded image, then $fallback.
 */
function wheellab_card_icon_url(array $card, string $fallback = ''): string {
    $picked = !empty($card['iconsans']) ? wheellab_iconsans_url((string) $card['iconsans']) : '';
    if ($picked) return $picked;
    return !empty($card['icon']['url']) ? (string) $card['icon']['url'] : $fallback;
}

foreach (wheellab_iconsans_field_keys() as $key) {
    add_filter('acf/load_field/key=' . $key, function (array $field): array {
        $field['choices'] = wheellab_iconsans_choices();
        return $field;
    });
}

add_action('acf/input/admin_enqueue_scripts', function () {
    wp_enqueue_script(
        'wheellab-iconsans-admin',
        wheellab_asset_url('assets/js/iconsans-admin.js'),
        ['acf-input'],
        wheellab_asset_ver('assets/js/iconsans-admin.js'),
        true
    );
    wp_localize_script('wheellab-iconsans-admin', 'wheellabIconsans', [
        'base' => wheellab_asset_url('assets/iconsans/'),
    ]);
    wp_add_inline_style('acf-input', '.wl-iconsans-opt{display:flex;align-items:center;gap:8px}.wl-iconsans-opt img{width:20px;height:20px;flex-shrink:0}');
});
