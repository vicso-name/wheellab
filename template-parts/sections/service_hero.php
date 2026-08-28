<?php

$bg_image         = get_field('background_serv_image') ?: null;
$heading           = get_field('heading')               ?: '';
$description       = get_field('description')           ?: '';
$secondary_button  = get_field('secondary_button')       ?: null;
$primary_button    = get_field('primary_button')         ?: null;

if (!$heading) {
    return;
}

$class  = 'service-hero';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$bg_url = !empty($bg_image['url'])
    ? $bg_image['url']
    : wheellab_asset_url('assets/img/services/hero-fallback.jpg');
$bg_alt = $bg_image['alt'] ?? '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="service-hero__bg" aria-hidden="true">
        <img class="service-hero__bg-image" src="<?php echo esc_url($bg_url); ?>" alt="<?php echo esc_attr($bg_alt); ?>" loading="eager">
        <div class="service-hero__bg-gradient"></div>
    </div>

    <div class="container">
        <div class="service-hero__content">
            <div class="service-hero__text">
                <h1 class="service-hero__title"><?php echo esc_html($heading); ?></h1>

                <?php if ($description) : ?>
                    <p class="service-hero__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($secondary_button['url']) || !empty($primary_button['url'])) : ?>
                <div class="service-hero__buttons">
                    <?php if (!empty($secondary_button['url'])) : ?>
                        <a class="service-hero__btn-secondary" href="<?php echo esc_url($secondary_button['url']); ?>"
                            <?php echo !empty($secondary_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <span class="service-hero__btn-secondary-inner">
                                <span class="button-text-m"><?php echo esc_html($secondary_button['title'] ?: __('See the animation reel', 'wheellab')); ?></span>
                                <svg class="service-hero__btn-secondary-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($primary_button['url'])) : ?>
                        <a class="btn-gradient" href="<?php echo esc_url($primary_button['url']); ?>"
                            <?php echo !empty($primary_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <span class="btn-gradient__inner button-text-m"><?php echo esc_html($primary_button['title'] ?: __('Start a design brief', 'wheellab')); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
