<?php
/**
 * Block: Service Comparison Section
 * Registered as: acf/service-comparison-section
 * Source: WheelLab Website (Figma) — node 806:11678 ("Container").
 *
 * Draggable before/after image reveal — plain Pointer Events + clip-path,
 * no third-party slider library (matches this codebase's own convention
 * of hand-rolling one-off interactions in vanilla JS — see faq_section.js
 * / case_study_tabs_section.js — rather than pulling in a dependency for
 * a UI this small; confirmed with the user explicitly for this block
 * given the handle itself is fully custom-styled regardless of library).
 *
 * The browser-chrome bar (3 dots) is decorative context ("both
 * screenshots live inside the same browser window"), not literal window
 * controls — same "traffic lights" motif some macOS-mockup sections
 * elsewhere on the site use.
 *
 * Mobile (≤576px) is a genuinely different layout, not a CSS reflow of
 * the same markup — node 806:11856 ("Alternative") drops the drag/
 * overlay/chrome-bar mechanic entirely in favor of two plain stacked
 * image cards, each with its own label above it and its own border/bg
 * treatment (the "after" card gets a purple-tinted bezel + glow shadow
 * the "before" one doesn't). Reusing one DOM tree for both would mean
 * un-clipping/repositioning absolutely-positioned elements and moving
 * labels out of the shared header row via CSS alone — this renders both
 * variants and toggles which is visible per breakpoint in CSS instead
 * (see service_comparison_section.scss's __desktop/__mobile rules). The
 * mobile markup carries none of the JS-targeted classes, so
 * service_comparison_section.js simply never touches it — no matchMedia
 * gating needed on the JS side.
 *
 * Ambient background glow (Figma's own hand-rotated nebula crop) is
 * dropped for now per explicit instruction — everything else in this
 * block matches the source design.
 *
 * Assets: build/css/sections/service_comparison_section.min.css
 *         build/js/sections/service_comparison_section.min.js
 */

$title        = get_field('title')        ?: '';
$before_label = get_field('before_label') ?: '';
$after_label  = get_field('after_label')  ?: '';
$before_image = get_field('before_image') ?: null;
$after_image  = get_field('after_image')  ?: null;

if (!$title || empty($before_image['url']) || empty($after_image['url'])) {
    return;
}

$class  = 'service-comparison-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <h2 class="service-comparison-section__title"><?php echo esc_html($title); ?></h2>

        <?php // Desktop/tablet — draggable overlay reveal (node 806:11678). ?>
        <div class="service-comparison-section__details service-comparison-section__desktop">
            <?php if ($before_label || $after_label) : ?>
                <div class="service-comparison-section__header">
                    <span class="service-comparison-section__label service-comparison-section__label--before"><?php echo esc_html($before_label); ?></span>
                    <span class="service-comparison-section__label service-comparison-section__label--after"><?php echo esc_html($after_label); ?></span>
                </div>
            <?php endif; ?>

            <div class="service-comparison-section__card">
                <div class="service-comparison-section__frame">
                    <div class="service-comparison-section__bar" aria-hidden="true">
                        <span class="service-comparison-section__dot service-comparison-section__dot--red"></span>
                        <span class="service-comparison-section__dot service-comparison-section__dot--yellow"></span>
                        <span class="service-comparison-section__dot service-comparison-section__dot--green"></span>
                    </div>

                    <div class="service-comparison-section__stage">
                        <img class="service-comparison-section__image service-comparison-section__image--before" src="<?php echo esc_url($before_image['url']); ?>" alt="<?php echo esc_attr($before_label ?: $before_image['alt']); ?>">
                        <img class="service-comparison-section__image service-comparison-section__image--after" src="<?php echo esc_url($after_image['url']); ?>" alt="<?php echo esc_attr($after_label ?: $after_image['alt']); ?>">
                    </div>
                </div>

                <div class="service-comparison-section__handle" role="slider" tabindex="0" aria-label="<?php esc_attr_e('Comparison position', 'wheellab'); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="50">
                    <span class="service-comparison-section__handle-line" aria-hidden="true"></span>
                    <span class="service-comparison-section__handle-grip" aria-hidden="true">
                        <span class="service-comparison-section__handle-grip-inner">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.5 7C9.32843 7 10 6.32843 10 5.5C10 4.67157 9.32843 4 8.5 4C7.67157 4 7 4.67157 7 5.5C7 6.32843 7.67157 7 8.5 7ZM8.5 13.5C9.32843 13.5 10 12.8284 10 12C10 11.1716 9.32843 10.5 8.5 10.5C7.67157 10.5 7 11.1716 7 12C7 12.8284 7.67157 13.5 8.5 13.5ZM10 18.5C10 19.3284 9.32843 20 8.5 20C7.67157 20 7 19.3284 7 18.5C7 17.6716 7.67157 17 8.5 17C9.32843 17 10 17.6716 10 18.5ZM15.5 7C16.3284 7 17 6.32843 17 5.5C17 4.67157 16.3284 4 15.5 4C14.6716 4 14 4.67157 14 5.5C14 6.32843 14.6716 7 15.5 7ZM17 12C17 12.8284 16.3284 13.5 15.5 13.5C14.6716 13.5 14 12.8284 14 12C14 11.1716 14.6716 10.5 15.5 10.5C16.3284 10.5 17 11.1716 17 12ZM15.5 20C16.3284 20 17 19.3284 17 18.5C17 17.6716 16.3284 17 15.5 17C14.6716 17 14 17.6716 14 18.5C14 19.3284 14.6716 20 15.5 20Z" fill="currentColor"/>
                            </svg>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <?php // Mobile (≤576px) — two plain stacked cards, no drag/overlay (node 806:11856 "Alternative"). ?>
        <div class="service-comparison-section__stack service-comparison-section__mobile">
            <div class="service-comparison-section__stack-item">
                <?php if ($before_label) : ?>
                    <p class="service-comparison-section__stack-label body-m"><?php echo esc_html($before_label); ?></p>
                <?php endif; ?>
                <div class="service-comparison-section__stack-frame">
                    <img class="service-comparison-section__stack-image" src="<?php echo esc_url($before_image['url']); ?>" alt="<?php echo esc_attr($before_label ?: $before_image['alt']); ?>">
                </div>
            </div>

            <div class="service-comparison-section__stack-item service-comparison-section__stack-item--after">
                <?php if ($after_label) : ?>
                    <p class="service-comparison-section__stack-label service-comparison-section__stack-label--after body-m">
                        <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/sparkling.svg')); ?>" alt="">
                        <?php echo esc_html($after_label); ?>
                    </p>
                <?php endif; ?>
                <div class="service-comparison-section__stack-frame service-comparison-section__stack-frame--after">
                    <img class="service-comparison-section__stack-image" src="<?php echo esc_url($after_image['url']); ?>" alt="<?php echo esc_attr($after_label ?: $after_image['alt']); ?>">
                </div>
            </div>
        </div>
    </div>
</section>
