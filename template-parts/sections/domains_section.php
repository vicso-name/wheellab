<?php
/**
 * Block: Domains Section
 * Registered as: acf/domains-section
 * Source: WheelLab Website (Figma) — node 527:24726 ("Features List",
 * desktop only so far). Same card language as Solutions Section (node
 * 527:24748) but laid out as a grid instead of a slider, and in the
 * accent-1/purple bezel rather than Solutions' accent-2/cyan one — that's
 * a real difference in the Figma tokens, not a copy-paste slip.
 *
 * The first card is a single dedicated ACF group field (full-width,
 * larger illustration) — everything after it is a plain repeater.
 * Assets: build/css/sections/domains_section.min.css
 */

$title        = get_field('title')        ?: '';
$description  = get_field('description')  ?: '';
$featured     = get_field('featured_card') ?: [];
$cards        = get_field('cards')        ?: [];

$class  = 'domains-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

// node 514:10399 — the actual glow texture used by this section's cards
// (both the wide featured card and the grid cards share the same source
// asset, just cropped/positioned differently), not the generic glow.jpg
// reused from Contact Section.
$glow_url = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));

$has_featured = !empty($featured['title']) && !empty($featured['link']['url']);

if (!$has_featured && !$cards) {
    return;
}

// Shared markup for both the featured card and the grid cards — only the
// --featured modifier and image size differ between them.
$render_card = function ($card, $is_featured) use ($glow_url) {
    $card_title       = $card['title'] ?? '';
    $card_description = $card['description'] ?? '';
    $card_image       = $card['image'] ?? null;
    $card_link        = $card['link'] ?? null;
    if (!$card_title || empty($card_link['url'])) return;

    $modifier = $is_featured ? ' domains-section__card--featured' : '';
    ?>
    <a class="domains-section__card<?php echo esc_attr($modifier); ?>" href="<?php echo esc_url($card_link['url']); ?>"
        <?php echo !empty($card_link['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
        <div class="domains-section__card-bezel">
            <div class="domains-section__card-inner">
                <img class="domains-section__card-glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

                <div class="domains-section__card-text">
                    <div class="domains-section__card-info">
                        <h3 class="domains-section__card-title h1"><?php echo esc_html($card_title); ?></h3>
                        <?php if ($card_description) : ?>
                            <p class="domains-section__card-description body-m"><?php echo nl2br(esc_html($card_description)); ?></p>
                        <?php endif; ?>
                    </div>

                    <svg class="domains-section__card-arrow" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M26.674 15.6903L12.3297 30.0347L9.97266 27.6777L24.317 13.3333H11.674V10H30.0073V28.3333H26.674V15.6903Z" fill="currentColor"/>
                    </svg>
                </div>

                <?php if (!empty($card_image['url'])) : ?>
                    <div class="domains-section__card-image">
                        <img src="<?php echo esc_url($card_image['url']); ?>" alt="<?php echo esc_attr($card_image['alt'] ?? ''); ?>" loading="lazy">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php
};
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title || $description) : ?>
            <div class="domains-section__header">
                <?php if ($title) : ?>
                    <h2 class="domains-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="domains-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($has_featured) : ?>
            <div class="domains-section__featured">
                <?php $render_card($featured, true); ?>
            </div>
        <?php endif; ?>

        <?php if ($cards) : ?>
            <div class="domains-section__grid">
                <?php foreach ($cards as $card) : $render_card($card, false); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
