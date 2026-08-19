<?php
/**
 * Block: Case Study Showcase Section
 * Registered as: acf/case-study-showcase-section
 * Source: WheelLab Website (Figma) — node 697:6970 ("Highlights Section",
 * desktop), node 783:5247 ("Highlights Section", mobile).
 *
 * Every "card" in Figma's own mock is a bespoke, one-off illustrated
 * composite (device mockups, coins, glow layers) unique to the BoxBet
 * example — not a structural component worth reproducing. Per the
 * request, this is just an image showcase: each repeater row is one
 * uploaded image inside the same simple card frame (Figma's own
 * "Case study frame" component), sized by a Full/Half Width choice
 * instead of the fixed pixel heights Figma's mock happens to use for
 * that one example's images.
 *
 * "Half Width" rows pair up side by side purely via flex-wrap (see
 * case_study_showcase_section.scss) — no PHP pairing/grouping logic
 * needed, and an odd one out simply wraps alone at full width. Mobile
 * (node 783:5247) confirms this is the right model: Figma's own "phone +
 * MacBook" pair — two Half Width images on desktop — stacks as two
 * separate full-width cards on mobile, exactly what flex-wrap already
 * does at a narrow viewport once Half Width is forced to 100% there.
 *
 * Assets: build/css/sections/case_study_showcase_section.min.css
 */

$title  = get_field('title')  ?: '';
$images = get_field('images') ?: [];

if (!$title && !$images) {
    return;
}

$class  = 'case-study-showcase-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title) : ?>
            <h2 class="case-study-showcase-section__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($images) : ?>
            <div class="case-study-showcase-section__images">
                <?php foreach ($images as $item) :
                    $image = $item['image'] ?? null;
                    $width = $item['width'] ?? 'full';
                    if (empty($image['url'])) continue;
                ?>
                    <div class="case-study-showcase-section__item case-study-showcase-section__item--<?php echo esc_attr($width); ?>">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
