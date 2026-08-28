<?php

$title       = get_field('title')          ?: get_field('title', 'option')          ?: '';
$description = get_field('description')    ?: get_field('description', 'option')    ?: '';
$badges_text = get_field('badges_text')    ?: get_field('badges_text', 'option')    ?: '';
$badges      = get_field('badges')         ?: get_field('badges', 'option')         ?: [];
$shortcode   = get_field('form_shortcode') ?: get_field('form_shortcode', 'option') ?: '';

if (!$badges) {
    $badges = [
        ['icon' => ['url' => wheellab_asset_url('assets/img/contact/badge-logo-1.jpg'), 'alt' => '']],
        ['icon' => ['url' => wheellab_asset_url('assets/img/contact/badge-logo-2.jpg'), 'alt' => '']],
        ['icon' => ['url' => wheellab_asset_url('assets/img/contact/badge-logo-3.jpg'), 'alt' => '']],
    ];
}

$class  = 'contact-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';

$id     = ' id="' . esc_attr(!empty($block['anchor']) ? $block['anchor'] : 'contact-section') . '"';

$glow_url = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));

ob_start();
foreach ($badges as $badge) :
    $icon = $badge['icon'] ?? null;
    if (empty($icon['url'])) continue;
    ?>
    <span class="contact-section__badge-logo">
        <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt'] ?? ''); ?>">
    </span>
    <?php
endforeach;
$badge_logos_html = ob_get_clean();
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="contact-section__bg" aria-hidden="true">
        <img class="contact-section__bg-image contact-section__bg-image--tl" src="<?php echo $glow_url; ?>" alt="">
        <img class="contact-section__bg-image contact-section__bg-image--br" src="<?php echo $glow_url; ?>" alt="">
    </div>

    <div class="container">
        <div class="contact-section__row">

            <div class="contact-section__info">
                <?php if ($title || $description) : ?>
                    <div class="contact-section__header">
                        <?php if ($title) : ?>
                            <h2 class="contact-section__title"><?php echo esc_html($title); ?></h2>
                        <?php endif; ?>

                        <?php if ($description) : ?>
                            <p class="contact-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($badges) : ?>
                    <div class="contact-section__divider"></div>

                    <div class="contact-section__badges">
                        <div class="contact-section__badge-logos"><?php echo $badge_logos_html; ?></div>

                        <?php if ($badges_text) : ?>
                            <p class="contact-section__badges-text"><?php echo esc_html($badges_text); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($shortcode) : ?>
                <div class="contact-section__form-card">
                    <div class="contact-section__form-inner">
                        <img class="contact-section__form-glow contact-section__form-glow--a" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
                        <img class="contact-section__form-glow contact-section__form-glow--b" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

                        <?php echo do_shortcode($shortcode); ?>

                        <div class="contact-section__sent-overlay" aria-live="polite">
                            <div class="contact-section__sent-icon">
                                <svg width="17" height="12" viewBox="0 0 16.9706 12.0208" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M6.36396 9.19238L15.5564 0L16.9706 1.41421L6.36396 12.0208L0 5.65688L1.41422 4.24268L6.36396 9.19238Z" fill="currentColor"/>
                                </svg>
                            </div>
                            <div class="contact-section__sent-text">
                                <p class="contact-section__sent-title"><?php esc_html_e('Request sent', 'wheellab'); ?></p>
                                <p class="contact-section__sent-subtitle"><?php esc_html_e('We will reach out to you soon', 'wheellab'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($badges) : ?>
                <div class="contact-section__badges contact-section__badges--mobile">
                    <div class="contact-section__badge-logos"><?php echo $badge_logos_html; ?></div>

                    <?php if ($badges_text) : ?>
                        <p class="contact-section__badges-text"><?php echo esc_html($badges_text); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
