<?php

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

    <?php ?>
    <div class="service-feature-cards__swiper swiper">
        <div class="swiper-wrapper">
            <?php foreach ($cards as $card) :
                $card_title       = $card['title']       ?? '';
                $card_description = $card['description'] ?? '';
                $card_features    = $card['features']    ?? '';
                $card_image       = $card['image']       ?? null;
                $has_card_image   = !empty($card_image['url']);
                if (!$card_title) continue;
            ?>
                <div class="swiper-slide service-feature-cards__slide">
                    <div class="service-feature-cards__card">
                        <div class="service-feature-cards__card-inner">
                            <div
                                class="service-feature-cards__card-image<?php echo !$has_card_image ? ' service-feature-cards__card-image--placeholder' : ''; ?>"
                                <?php echo !$has_card_image ? 'aria-hidden="true"' : ''; ?>
                            >
                                <?php if ($has_card_image) : ?>
                                    <img src="<?php echo esc_url($card_image['url']); ?>" alt="<?php echo esc_attr($card_image['alt'] ?? ''); ?>" loading="lazy">
                                <?php endif; ?>
                            </div>

                            <div class="service-feature-cards__card-text">
                                <h3 class="service-feature-cards__card-title"><?php echo esc_html($card_title); ?></h3>

                                <?php if ($card_description) : ?>
                                    <p class="service-feature-cards__card-description body-m"><?php echo nl2br(esc_html($card_description)); ?></p>
                                <?php endif; ?>

                                <?php if ($card_features) : ?>
                                    <div class="service-feature-cards__card-divider" aria-hidden="true"></div>

                                    <div class="service-feature-cards__card-features body-m">
                                        <?php echo wp_kses_post($card_features); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
