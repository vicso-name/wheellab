<?php
/**
 * Block: Service Stats Section
 * Registered as: acf/service-stats-section
 * Source: WheelLab Website (Figma) — "Statistics" component (desktop
 * node 806:11701, mobile node 806:11974). Standalone version of the
 * same stats banner also available as an optional add-on under
 * Service Comparison Section — both share wheellab_render_stats_banner()
 * (inc/theme_function.php) and src/scss/partials/_stats_banner.scss so
 * the two never drift out of sync.
 *
 * Assets: build/css/sections/service_stats_section.min.css
 */

$stats = get_field('stats') ?: [];

if (!$stats) {
    return;
}

$class  = 'service-stats-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php wheellab_render_stats_banner($stats); ?>
    </div>
</section>
