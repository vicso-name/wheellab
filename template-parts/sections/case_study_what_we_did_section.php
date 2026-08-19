<?php
/**
 * Block: Case Study What We Did Section
 * Registered as: acf/case-study-what-we-did-section
 * Source: WheelLab Website (Figma) — node 697:15561 ("Tile section").
 *
 * The image is a bespoke, one-off illustrated composite (browser mockup
 * + overlapping phone, coins, glow) unique to the BoxBet example — same
 * reasoning as Case Study Showcase/Screens Section's own images: not a
 * structural component worth reproducing, just a plain uploaded image
 * in the standard card frame.
 *
 * Assets: build/css/sections/case_study_what_we_did_section.min.css
 */

$title    = get_field('title')    ?: '';
$image    = get_field('image')    ?: null;
$sections = get_field('sections') ?: [];

$sections = array_values(array_filter($sections, static function ($section) {
    return !empty($section['title']) && !empty($section['description']);
}));

if (!$title && !$sections && empty($image['url'])) {
    return;
}

$class  = 'case-study-what-we-did-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="case-study-what-we-did-section__row">
            <div class="case-study-what-we-did-section__text">
                <?php if ($title) : ?>
                    <h2 class="case-study-what-we-did-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($sections) : ?>
                    <div class="case-study-what-we-did-section__sections">
                        <?php foreach ($sections as $section) : ?>
                            <div class="case-study-what-we-did-section__section">
                                <h4 class="case-study-what-we-did-section__section-title"><?php echo esc_html($section['title']); ?></h4>
                                <p class="case-study-what-we-did-section__section-description"><?php echo esc_html($section['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($image['url'])) : ?>
                <div class="case-study-what-we-did-section__image">
                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?: $title); ?>" loading="lazy">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
