<?php
/**
 * Block: AI Highlight Section
 * Registered as: acf/ai-highlight-section
 * Source: WheelLab Website (Figma) — node 618:10351 ("AI highlight").
 *
 * Meant to be added as the first block inside a blog post's content,
 * summarizing what the article covers. Always rendered at the post
 * content column width (1031px) — unlike faq_section there's no
 * full-width variant in the source design, so no width toggle field.
 * Assets: build/css/sections/ai_highlight_section.min.css
 */

$title   = get_field('title') ?: __('What is this article about?', 'wheellab');
$content = get_field('content');

if (!$content) {
    return;
}

$class  = 'ai-highlight-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$sparkle_icon_url = esc_url(wheellab_asset_url('assets/img/icons/sparkling.svg'));
?>
<div class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="ai-highlight-section__inner">
        <div class="ai-highlight-section__title-row">
            <img class="ai-highlight-section__icon svg" src="<?php echo $sparkle_icon_url; ?>" alt="" aria-hidden="true">
            <p class="ai-highlight-section__title"><?php echo esc_html($title); ?></p>
        </div>
        <div class="ai-highlight-section__content">
            <?php echo wp_kses_post($content); ?>
        </div>
    </div>
</div>
