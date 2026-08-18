<?php
/**
 * Section: Privacy Policy Hero
 * Used by: template-privacy-policy.php only. Fields live on the page
 * template itself (ACF "Page Template" location rule), not a Gutenberg
 * block — see acf-json/group_template_privacy_policy.json.
 *
 * Source: WheelLab Website (Figma) — node 527:32387 (desktop),
 * node 783:12146 (mobile). Mobile keeps the same title/description/date
 * sizes as desktop (no mobile-specific type scale was given for this
 * hero, unlike single_post_hero's own sourced mobile step-down) — only
 * text alignment (centered → left) and the vertical gaps change.
 * Assets: build/css/sections/privacy_hero.min.css
 */

$title        = get_field('hero_title')       ?: get_the_title();
$description  = get_field('hero_description') ?: '';
$last_updated = get_field('last_updated')      ?: current_time('m.d.Y');
?>
<section class="privacy-hero">
    <div class="privacy-hero__bg" aria-hidden="true"></div>

    <div class="container">
        <div class="privacy-hero__content">
            <h1 class="privacy-hero__title"><?php echo esc_html($title); ?></h1>

            <?php if ($description) : ?>
                <p class="privacy-hero__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <p class="privacy-hero__updated">
                <?php
                printf(
                    /* translators: %s: last updated date, e.g. 06.26.2026 */
                    esc_html__('Last Updated: %s', 'wheellab'),
                    esc_html($last_updated)
                );
                ?>
            </p>
        </div>
    </div>

    <div class="privacy-hero__divider"></div>
</section>
