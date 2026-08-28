<?php

$title          = get_field('title')          ?: '';
$description    = get_field('description')    ?: '';
$link           = get_field('link')           ?: null;
$secondary_link = get_field('secondary_link') ?: null;

if (!$title || empty($link['url'])) {
    return;
}

$class  = 'cta-banner-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>
<div class="container">
    <div class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
        <div class="cta-banner-section__inner">
            <div class="cta-banner-section__content<?php echo !empty($secondary_link['url']) ? ' cta-banner-section__content--split' : ''; ?>">
                <div class="cta-banner-section__text">
                    <h3 class="cta-banner-section__title"><?php echo esc_html($title); ?></h3>
                    <?php if ($description) : ?>
                        <p class="cta-banner-section__description"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </div>

                <div class="cta-banner-section__buttons">
                    <?php if (!empty($secondary_link['url'])) : ?>
                        <a
                            class="cta-banner-section__button-secondary"
                            href="<?php echo esc_url($secondary_link['url']); ?>"
                            <?php echo !empty($secondary_link['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                        >
                            <span class="cta-banner-section__button-secondary-inner">
                                <span class="button-text-m"><?php echo esc_html($secondary_link['title'] ?: __('Learn more', 'wheellab')); ?></span>
                                <svg class="cta-banner-section__button-secondary-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                    <?php endif; ?>

                    <a
                        class="btn-gradient cta-banner-section__button"
                        href="<?php echo esc_url($link['url']); ?>"
                        <?php echo !empty($link['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                    >
                        <span class="btn-gradient__inner button-text-m"><?php echo esc_html($link['title'] ?: __('Learn more', 'wheellab')); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
