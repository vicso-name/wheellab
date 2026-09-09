<?php

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
