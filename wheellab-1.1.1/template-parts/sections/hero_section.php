<?php

$title_white      = get_field('title_white')      ?: '';
$title_accent     = get_field('title_accent')     ?: '';
$description      = get_field('description')      ?: '';
$bg_video         = get_field('background_video') ?: null;
$bg_poster        = get_field('background_poster') ?: null;
$primary_button   = get_field('primary_button')    ?: null;
$secondary_button = get_field('secondary_button')  ?: null;

$class  = 'hero';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

if (!$title_white && !$title_accent) {
    return;
}

$poster_url = !empty($bg_poster['url']) ? esc_url($bg_poster['url']) : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="hero__background">
        <?php if (!empty($bg_video['url'])) : ?>
            <video class="hero__background-media" autoplay muted loop playsinline<?php echo $poster_url ? ' poster="' . $poster_url . '"' : ''; ?>>
                <source src="<?php echo esc_url($bg_video['url']); ?>" type="<?php echo esc_attr($bg_video['mime_type'] ?? 'video/mp4'); ?>">
            </video>
        <?php elseif ($poster_url) : ?>
            <img class="hero__background-media" src="<?php echo $poster_url; ?>" alt="" loading="eager">
        <?php endif; ?>

        <div class="hero__background-overlay"></div>
    </div>

    <div class="container">
        <div class="hero__content">
            <div class="hero__text">
                <h1 class="hero__title display-2">
                    <?php if ($title_white) : ?><span><?php echo esc_html($title_white); ?></span><?php endif; ?>
                    <?php if ($title_accent) : ?><span class="hero__title-accent"><?php echo esc_html($title_accent); ?></span><?php endif; ?>
                </h1>

                <?php if ($description) : ?>
                    <p class="hero__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($primary_button['url']) || !empty($secondary_button['url'])) : ?>
                <div class="hero__buttons">
                    <?php if (!empty($secondary_button['url'])) : ?>
                        <a class="hero__btn-secondary" href="<?php echo esc_url($secondary_button['url']); ?>"
                            <?php echo !empty($secondary_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <span class="hero__btn-secondary-inner">
                                <span class="button-text-m"><?php echo esc_html($secondary_button['title'] ?: 'See our Services'); ?></span>
                                <svg class="hero__btn-secondary-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($primary_button['url'])) : ?>
                        <a class="btn-gradient" href="<?php echo esc_url($primary_button['url']); ?>"
                            <?php echo !empty($primary_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                            <span class="btn-gradient__inner button-text-m"><?php echo esc_html($primary_button['title'] ?: 'Book a Call'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
