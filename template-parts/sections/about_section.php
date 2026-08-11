<?php
/**
 * Block: About Section
 * Registered as: acf/about-section
 * Source: WheelLab Website (Figma) — node 527:24707 ("Content Group").
 *
 * The Figma frame gives no outer section wrapper (no title/description
 * header sits above this card, unlike Solutions/Domains/Tile), so this is
 * the section's entire content — wrapped in the standard 100px vertical
 * section padding + .container used everywhere else.
 *
 * The divider (node 527:24724) always renders once there's a title, so
 * the 96px/96px gap above and below it holds even with no logos yet. The
 * logo row itself (node 527:24725, annotated "Running line with trusted
 * companies") is an infinite marquee — the Figma mock uses placeholder
 * logos, so this renders whatever the admin uploads to the Logos gallery,
 * duplicated once in the markup for a seamless CSS loop regardless of
 * count — and stays hidden until at least one is uploaded.
 * Assets: build/css/sections/about_section.min.css
 */

$label       = get_field('label')       ?: '';
$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$logos       = get_field('logos')       ?: [];

$class  = 'about-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

if (!$title) {
    return;
}
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="about-section__card">
            <div class="about-section__card-inner">
                <div class="about-section__text">
                    <?php if ($label) : ?>
                        <span class="about-section__label body-s"><?php echo esc_html($label); ?></span>
                    <?php endif; ?>

                    <div class="about-section__content">
                        <h2 class="about-section__title"><?php echo esc_html($title); ?></h2>

                        <?php if ($description) : ?>
                            <div class="about-section__extra">
                                <svg class="about-section__extra-icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M18.8686 12.8295L12.6106 6.57154L14.2603 4.92188L23.3346 13.9962L14.2603 23.0705L12.6106 21.4209L18.8686 15.1629H4.66797V12.8295H18.8686Z" fill="currentColor"/>
                                </svg>
                                <p class="about-section__extra-text body-m"><?php echo nl2br(esc_html($description)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="about-section__divider">

                <?php if ($logos) : ?>
                    <div class="about-section__marquee">
                        <div class="about-section__marquee-track" style="--marquee-duration:<?php echo esc_attr(count($logos) * 4); ?>s;">
                            <?php foreach ([1, 2] as $pass) : ?>
                                <?php foreach ($logos as $logo) : ?>
                                    <img class="about-section__marquee-logo" src="<?php echo esc_url($logo['url']); ?>" alt="<?php echo esc_attr($logo['alt'] ?? ''); ?>" loading="lazy" aria-hidden="<?php echo $pass === 2 ? 'true' : 'false'; ?>">
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
