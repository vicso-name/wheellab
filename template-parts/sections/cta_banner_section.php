<?php
/**
 * Block: CTA Banner Section
 * Registered as: acf/cta-banner-section
 * Source: WheelLab Website (Figma) — node 527:28993 ("Banner", original
 * blog usage) and node 700:19490 ("Frame", same reusable Banner
 * component reused as a Case Study page's closing CTA).
 *
 * Simple 3-field block (title, short description, button link) meant to
 * be dropped into content to prompt the reader toward another page
 * (portfolio, subscribe form, start-a-project, etc). Button reuses
 * .btn-gradient/.btn-gradient__inner (src/scss/partials/_general.scss) —
 * same component every other CTA button on the site already uses.
 *
 * Wrapped in .container here rather than left full-bleed — a blog
 * post's the_content() already sits inside a narrower prose column, so
 * this was never needed there, but single-case_study.php's the_content()
 * has no such wrapper of its own, so the banner rendered edge-to-edge
 * instead of inset like every other Case Study block.
 *
 * Assets: build/css/sections/cta_banner_section.min.css
 */

$title       = get_field('title') ?: '';
$description = get_field('description') ?: '';
$link        = get_field('link') ?: null;

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
            <div class="cta-banner-section__content">
                <div class="cta-banner-section__text">
                    <h3 class="cta-banner-section__title"><?php echo esc_html($title); ?></h3>
                    <?php if ($description) : ?>
                        <p class="cta-banner-section__description"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </div>

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
