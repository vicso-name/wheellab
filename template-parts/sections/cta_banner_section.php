<?php
/**
 * Block: CTA Banner Section
 * Registered as: acf/cta-banner-section
 * Source: WheelLab Website (Figma) — node 527:28993 ("Banner", original
 * blog usage) and node 700:19490 ("Frame", same reusable Banner
 * component reused as a Case Study page's closing CTA).
 *
 * Title, description, primary button link, and now an optional
 * secondary button (node 806:11704 "Background section" / 806:11708
 * "Banner" reuses this exact same bezel/glow/title/description recipe —
 * same accent-1 purple-tinted card, same bg-main-surface-1 inner, same
 * h4 28/36 title and 20/30 description — just with a second glass-style
 * button, so this extends the existing block with an optional field
 * rather than duplicating it).
 *
 * The two Figma sources use genuinely DIFFERENT layouts, confirmed by
 * fetching both rather than assumed: 527:28993 (original, single
 * button) stacks the button directly below the text in one column;
 * 806:11708 (new, two buttons) puts text and buttons side by side as
 * separate flex children, text capped at 711px. Both are real, sourced
 * layouts, not one implementation being "wrong" — so
 * .cta-banner-section__content only switches to the side-by-side row
 * (via the --split modifier, added in PHP below whenever a secondary
 * button exists) for the two-button case; every existing single-button
 * usage (blog, case-study pages) keeps the original stacked column
 * completely unchanged.
 *
 * Primary button reuses .btn-gradient/.btn-gradient__inner
 * (src/scss/partials/_general.scss) — same component every other CTA
 * button on the site already uses. Secondary button is this block's own
 * copy of the glass-frame recipe (matches .service-hero__btn-secondary
 * etc. — duplicated per block by this codebase's own convention, see
 * .btn-gradient's own comment in _general.scss for why) with the same
 * inline arrow-down-line SVG already used by service_hero.php.
 *
 * Wrapped in .container here rather than left full-bleed — a blog
 * post's the_content() already sits inside a narrower prose column, so
 * this was never needed there, but single-case_study.php's the_content()
 * has no such wrapper of its own, so the banner rendered edge-to-edge
 * instead of inset like every other Case Study block.
 *
 * Assets: build/css/sections/cta_banner_section.min.css
 */

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
