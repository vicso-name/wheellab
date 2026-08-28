<?php
/**
 * Block: Stats Showcase Section
 * Registered as: acf/stats-showcase-section
 * Source: WheelLab Website (Figma) — node 527:24777 ("Card") for the
 * static layout (bg/logos/circle/text), reference motion from a separate
 * Animation.mp4 export not itself in Figma — see stats_showcase_section.js's
 * header comment for the full mechanics writeup.
 *
 * Four fixed 3D icons (arrow/gear/coins/chart — theme assets, not
 * per-slide uploads) sit on one shared circular orbit behind the text.
 * Each slide advance rotates that orbit ~90°, swapping which two icons
 * are visible (left/right) — see stats_showcase_section.js for the orbit
 * math and stats_showcase_section.scss for the orbit/mask layout.
 *
 * Assets: build/css/sections/stats_showcase_section.min.css
 *         build/js/sections/stats_showcase_section.min.js
 */

$logo   = get_field('logo')   ?: null;
$slides = get_field('slides') ?: [];

$slides = array_values(array_filter($slides, static function ($slide) {
    return !empty($slide['stat']) && !empty($slide['title']);
}));

if (empty($logo['url']) || !$slides) {
    return;
}

$class  = 'stats-showcase-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$bg_url     = esc_url(wheellab_asset_url('assets/img/stats-showcase/bg.webp'));
$circle_url = esc_url(wheellab_asset_url('assets/img/stats-showcase/circle.png'));

// Fixed icon set + orbit order. The key is also the base-angle lookup
// stats_showcase_section.js uses (ICON_BASE_ANGLES) — keep both in sync.
$orbit_icons = [
    'coins' => 'Coins',
    'arrow' => 'Arrow',
    'gear'  => 'Gear',
    'chart' => 'Chart',
];

// Enough repeats to fill any reasonable viewport height twice over
// (doubled below for a seamless -50% loop) — not tied to Figma's own
// 7-per-column count, which was just however many fit its one canvas.
$logo_repeats = 10;

// Title is always exactly two words (see the ACF field's own
// instructions) — split into its two independently-masked lines. A
// title with only one word just leaves the second line empty rather
// than erroring; more than two words folds the remainder onto line two.
$title_lines = array_map(static function ($slide) {
    $parts = explode(' ', trim((string) $slide['title']), 2);
    return [$parts[0] ?? '', $parts[1] ?? ''];
}, $slides);
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="stats-showcase-section__bg" aria-hidden="true">
        <img src="<?php echo $bg_url; ?>" alt="" loading="lazy">
        <div class="stats-showcase-section__bg-gradient"></div>
    </div>

    <div class="stats-showcase-section__circle" aria-hidden="true">
        <img src="<?php echo $circle_url; ?>" alt="" loading="lazy">
    </div>

    <div class="stats-showcase-section__logos" aria-hidden="true">
        <?php for ($col = 0; $col < 4; $col++) :
            $direction = $col % 2 === 0 ? 'up' : 'down';
        ?>
            <div class="stats-showcase-section__logo-col stats-showcase-section__logo-col--<?php echo $direction; ?>">
                <?php for ($set = 0; $set < 2; $set++) : ?>
                    <div class="stats-showcase-section__logo-set">
                        <?php for ($i = 0; $i < $logo_repeats; $i++) : ?>
                            <img class="stats-showcase-section__logo" src="<?php echo esc_url($logo['url']); ?>" alt="" loading="lazy">
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endfor; ?>
    </div>

    <div class="stats-showcase-section__orbit" aria-hidden="true">
        <?php foreach ($orbit_icons as $key => $label) : ?>
            <img
                class="stats-showcase-section__orbit-icon"
                data-icon="<?php echo esc_attr($key); ?>"
                src="<?php echo esc_url(wheellab_asset_url("assets/img/stats-showcase/icon-{$key}.png")); ?>"
                alt=""
                loading="lazy"
            >
        <?php endforeach; ?>
    </div>

    <div class="container">
        <div class="stats-showcase-section__inner">
            <div class="stats-showcase-section__content">
                <div class="stats-showcase-section__mask stats-showcase-section__mask--stat">
                    <?php foreach ($slides as $index => $slide) : ?>
                        <span class="stats-showcase-section__line" data-role="stat" data-index="<?php echo (int) $index; ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                            <?php echo esc_html($slide['stat']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <h2 class="stats-showcase-section__title">
                    <div class="stats-showcase-section__mask stats-showcase-section__mask--title-1">
                        <?php foreach ($title_lines as $index => $lines) : ?>
                            <span class="stats-showcase-section__line" data-role="title-1" data-index="<?php echo (int) $index; ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                                <?php echo esc_html($lines[0]); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="stats-showcase-section__mask stats-showcase-section__mask--title-2">
                        <?php foreach ($title_lines as $index => $lines) : ?>
                            <span class="stats-showcase-section__line" data-role="title-2" data-index="<?php echo (int) $index; ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                                <?php echo esc_html($lines[1]); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </h2>

                <div class="stats-showcase-section__mask stats-showcase-section__mask--description">
                    <?php foreach ($slides as $index => $slide) : ?>
                        <?php if (!empty($slide['description'])) : ?>
                            <p class="stats-showcase-section__line" data-role="description" data-index="<?php echo (int) $index; ?>" aria-hidden="<?php echo $index === 0 ? 'false' : 'true'; ?>">
                                <?php echo esc_html($slide['description']); ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
