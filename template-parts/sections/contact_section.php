<?php

$title           = get_field('title')       ?: get_field('title', 'option')       ?: '';
$description     = get_field('description') ?: get_field('description', 'option') ?: '';
$shortcode       = get_field('form_shortcode') ?: get_field('form_shortcode', 'option') ?: '';

// Global, not per-block — one shared process/CTA across every page (see
// Theme Options > Contact). "steps" and "cta_button" are deliberately
// namespaced (contact_*): both names already exist on unrelated field
// groups elsewhere (service_process_deck, case_study_section), and
// get_field(..., 'option') resolves by name only, with no block context
// to disambiguate — the bare names silently pulled the wrong field.
$steps           = get_field('contact_steps', 'option')     ?: [];
$cta_title       = get_field('cta_title', 'option')          ?: '';
$cta_description = get_field('cta_description', 'option')    ?: '';
$cta_button      = get_field('contact_cta_button', 'option') ?: null;

$class  = 'contact-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';

$id     = ' id="' . esc_attr(!empty($block['anchor']) ? $block['anchor'] : 'contact') . '"';

$glow_url = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="contact-section__bg" aria-hidden="true">
        <img class="contact-section__bg-image contact-section__bg-image--tl" src="<?php echo $glow_url; ?>" alt="">
        <img class="contact-section__bg-image contact-section__bg-image--br" src="<?php echo $glow_url; ?>" alt="">
        <div class="contact-section__bg-fade contact-section__bg-fade--top"></div>
        <div class="contact-section__bg-fade contact-section__bg-fade--bottom"></div>
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

                <?php if ($steps) : ?>
                    <div class="contact-section__steps">
                        <?php foreach ($steps as $step) :
                            if (empty($step['text'])) continue;
                        ?>
                            <div class="contact-section__step">
                                <span class="contact-section__step-marker-col" aria-hidden="true">
                                    <span class="contact-section__step-marker"></span>
                                    <span class="contact-section__step-connector"></span>
                                </span>
                                <p class="contact-section__step-text body-m"><?php echo esc_html($step['text']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($cta_title || $cta_description || !empty($cta_button['url'])) : ?>
                    <div class="contact-section__divider"></div>

                    <div class="contact-section__cta">
                        <div class="contact-section__cta-text">
                            <?php if ($cta_title) : ?>
                                <h3 class="contact-section__cta-title body-m body-m--bold"><?php echo esc_html($cta_title); ?></h3>
                            <?php endif; ?>

                            <?php if ($cta_description) : ?>
                                <p class="contact-section__cta-description body-m"><?php echo esc_html($cta_description); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($cta_button['url'])) : ?>
                            <a
                                class="contact-section__cta-button"
                                href="<?php echo esc_url($cta_button['url']); ?>"
                                <?php echo !empty($cta_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                            >
                                <span class="contact-section__cta-button-text button-text-m"><?php echo esc_html($cta_button['title'] ?: __('Book a meeting', 'wheellab')); ?></span>
                                <img class="svg contact-section__cta-button-icon" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg')); ?>" alt="">
                            </a>
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

        </div>
    </div>
</section>
