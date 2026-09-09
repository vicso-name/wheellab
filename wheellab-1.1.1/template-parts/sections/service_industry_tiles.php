<?php

$title = get_field('title') ?: '';
$cards = get_field('cards') ?: [];

$cards = array_values(array_filter($cards, static function ($card) {
    return !empty($card['title']) && !empty($card['content']);
}));

if (!$title || !$cards) {
    return;
}

$class  = 'service-industry-tiles';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$fallback_icon_url = wheellab_asset_url('assets/img/services/industry-icon-fallback.svg');
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <h2 class="service-industry-tiles__title"><?php echo esc_html($title); ?></h2>

        <div class="service-industry-tiles__grid">
            <?php foreach ($cards as $card) :
                $icon_url = !empty($card['icon']['url']) ? $card['icon']['url'] : $fallback_icon_url;
            ?>
                <div class="service-industry-tiles__card">
                    <div class="service-industry-tiles__card-inner">
                        <div class="service-industry-tiles__card-icon">
                            <span class="service-industry-tiles__card-icon-glyph" style="mask-image:url('<?php echo esc_url($icon_url); ?>');-webkit-mask-image:url('<?php echo esc_url($icon_url); ?>');" aria-hidden="true"></span>
                        </div>

                        <div class="service-industry-tiles__card-text">
                            <h3 class="service-industry-tiles__card-title h4"><?php echo esc_html($card['title']); ?></h3>
                            <div class="service-industry-tiles__card-content body-m"><?php echo wp_kses_post($card['content']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
