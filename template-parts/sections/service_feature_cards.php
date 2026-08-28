<?php
/**
 * Block: Service Feature Cards
 * Registered as: acf/service-feature-cards
 * Source: WheelLab Website (Figma) — node 806:11625 ("Container", full
 * block), node 806:11626 ("Content container", header text + slider
 * nav), node 806:11635 ("Feature cards" / "Card row"), node 806:11654
 * ("Feature card", single card).
 *
 * Same header + full-bleed Swiper + chevron-nav recipe as Solutions
 * Section / Case Study Section (see those files) — reused verbatim
 * rather than re-derived. Plain ACF repeater for cards, each holding its
 * own title/description/features list (no CPT backing — unlike Case
 * Study Section, these aren't independent content, just a fixed-purpose
 * list for this one block instance).
 *
 * Card illustration is a fixed, theme-bundled graphic (mask-tinted
 * accent-2 cyan via CSS, see service_feature_cards.scss), NOT a per-card
 * upload — the Figma mock shows the exact same "wireframe → mockup"
 * glyph on every card, matching this codebase's existing "shared fixed
 * icon" precedent (stats_showcase_section's orbit icons) rather than
 * the "per-item image field" one.
 *
 * Mobile (≤576px, explicit instruction): no slider at all — Swiper is
 * never initialized below that width (see service_feature_cards.js) and
 * .service-feature-cards__swiper/__wrapper fall back to a plain stacked
 * column via CSS, one card per row.
 *
 * Assets: build/css/sections/service_feature_cards.min.css
 *         build/js/sections/service_feature_cards.min.js
 */

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$cards       = get_field('cards')       ?: [];

if (!$title || !$cards) {
    return;
}

$class  = 'service-feature-cards';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="service-feature-cards__header">
            <div class="service-feature-cards__text">
                <h2 class="service-feature-cards__title"><?php echo esc_html($title); ?></h2>

                <?php if ($description) : ?>
                    <p class="service-feature-cards__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>

            <?php if (count($cards) > 1) : ?>
                <div class="service-feature-cards__nav-group">
                    <button type="button" class="service-feature-cards__nav service-feature-cards__nav--prev">
                        <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg')); ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Previous card', 'wheellab'); ?></span>
                    </button>
                    <button type="button" class="service-feature-cards__nav service-feature-cards__nav--next">
                        <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg')); ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Next card', 'wheellab'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Full-bleed on purpose, same reasoning as .solutions-section__swiper. ?>
    <div class="service-feature-cards__swiper swiper">
        <div class="swiper-wrapper">
            <?php foreach ($cards as $card) :
                $card_title       = $card['title']       ?? '';
                $card_description = $card['description'] ?? '';
                $card_features    = $card['features']    ?? [];
                if (!$card_title) continue;
            ?>
                <div class="swiper-slide service-feature-cards__slide">
                    <div class="service-feature-cards__card">
                        <div class="service-feature-cards__card-inner">
                            <div class="service-feature-cards__card-image" aria-hidden="true"></div>

                            <div class="service-feature-cards__card-text">
                                <h3 class="service-feature-cards__card-title"><?php echo esc_html($card_title); ?></h3>

                                <?php if ($card_description) : ?>
                                    <p class="service-feature-cards__card-description body-m"><?php echo nl2br(esc_html($card_description)); ?></p>
                                <?php endif; ?>

                                <?php if ($card_features) : ?>
                                    <div class="service-feature-cards__card-divider" aria-hidden="true"></div>

                                    <ul class="service-feature-cards__card-list">
                                        <?php foreach ($card_features as $feature) :
                                            $feature_label = $feature['label'] ?? '';
                                            if (!$feature_label) continue;
                                        ?>
                                            <li class="service-feature-cards__card-list-item">
                                                <svg class="service-feature-cards__card-bullet" width="6" height="28" viewBox="0 0 6 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                    <path d="M0 14L3 11L6 14L3 17L0 14Z" fill="currentColor"/>
                                                </svg>
                                                <span class="body-m"><?php echo esc_html($feature_label); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
