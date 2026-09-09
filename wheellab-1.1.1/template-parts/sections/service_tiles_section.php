<?php
/**
 * Block: Service Tiles Section
 * Registered as: acf/service-tiles-section
 * Source: WheelLab Website (Figma) — node 806:11624 ("Tile section"),
 * reusing the same "Case study frame" component as Case Study Section
 * in two variants: an Image variant (top-left large tile, bottom-right
 * small tile) and a Text variant (the other 4 tiles — title +
 * description, one of them — bottom-left — in a highlighted/glow
 * treatment). No per-item link/button in this instance, so the Text
 * variant's own arrow icon (node …;444:7803 "Icon") is intentionally
 * not reproduced here — per explicit instruction, these tiles are
 * static, not clickable.
 *
 * Assets: build/css/sections/service_tiles_section.min.css
 */

$title             = get_field('title')       ?: '';
$description       = get_field('description') ?: '';
$tile_large        = get_field('tile_large');
$tile_small        = get_field('tile_small');
$card_1_title      = get_field('card_1_title')       ?: '';
$card_1_description = get_field('card_1_description') ?: '';
$card_2_title      = get_field('card_2_title')       ?: '';
$card_2_description = get_field('card_2_description') ?: '';
$card_3_title      = get_field('card_3_title')       ?: '';
$card_3_description = get_field('card_3_description') ?: '';
$accent_title      = get_field('accent_title')       ?: '';
$accent_description = get_field('accent_description') ?: '';

$cards = array_filter([
    ['title' => $card_1_title, 'description' => $card_1_description],
    ['title' => $card_2_title, 'description' => $card_2_description],
    ['title' => $card_3_title, 'description' => $card_3_description],
], fn($card) => $card['title']);

$has_content = $title || $cards || $accent_title || !empty($tile_large['url']) || !empty($tile_small['url']);
if (!$has_content) {
    return;
}

$class  = 'service-tiles-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$glow_url = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title) : ?>
            <div class="service-tiles-section__header">
                <h2 class="service-tiles-section__title"><?php echo esc_html($title); ?></h2>

                <?php if ($description) : ?>
                    <p class="service-tiles-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="service-tiles-section__grid">
            <div class="service-tiles-section__row service-tiles-section__row--top">
                <?php if (!empty($tile_large['url'])) : ?>
                    <figure class="service-tiles-section__tile service-tiles-section__tile--photo service-tiles-section__tile--large">
                        <img src="<?php echo esc_url($tile_large['url']); ?>" alt="<?php echo esc_attr($tile_large['alt']); ?>" loading="lazy">
                    </figure>
                <?php endif; ?>

                <?php if ($cards) : ?>
                    <div class="service-tiles-section__stack">
                        <?php foreach ($cards as $card) : ?>
                            <div class="service-tiles-section__tile service-tiles-section__tile--text">
                                <div class="service-tiles-section__tile-inner">
                                    <h3 class="service-tiles-section__tile-title h4"><?php echo esc_html($card['title']); ?></h3>
                                    <?php if ($card['description']) : ?>
                                        <p class="service-tiles-section__tile-description body-m"><?php echo esc_html($card['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="service-tiles-section__row service-tiles-section__row--bottom">
                <?php if ($accent_title) : ?>
                    <div class="service-tiles-section__tile service-tiles-section__tile--text service-tiles-section__tile--accent">
                        <div class="service-tiles-section__tile-inner">
                            <img class="service-tiles-section__tile-glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
                            <h3 class="service-tiles-section__tile-title h4"><?php echo esc_html($accent_title); ?></h3>
                            <?php if ($accent_description) : ?>
                                <p class="service-tiles-section__tile-description body-m"><?php echo esc_html($accent_description); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($tile_small['url'])) : ?>
                    <figure class="service-tiles-section__tile service-tiles-section__tile--photo service-tiles-section__tile--small">
                        <img src="<?php echo esc_url($tile_small['url']); ?>" alt="<?php echo esc_attr($tile_small['alt']); ?>" loading="lazy">
                    </figure>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
