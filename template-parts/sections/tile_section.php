<?php

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$cards       = get_field('cards')       ?: [];

$class  = 'tile-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$glow_url = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));

if (!$cards) {
    return;
}
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <img class="tile-section__glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

    <div class="container">
        <?php if ($title || $description) : ?>
            <div class="tile-section__header">
                <?php if ($title) : ?>
                    <h2 class="tile-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="tile-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="tile-section__grid">
            <?php foreach ($cards as $card) :
                $card_icon        = $card['icon'] ?? null;
                $card_title       = $card['title'] ?? '';
                $card_description = $card['description'] ?? '';
                if (!$card_title) continue;
            ?>
                <div class="tile-section__card">
                    <div class="tile-section__card-inner">
                        <?php if (!empty($card_icon['url'])) :
                            $icon_url = esc_url($card_icon['url']);
                        ?>
                            <div class="tile-section__card-icon">
                                <span class="tile-section__card-icon-glyph" style="mask-image:url('<?php echo $icon_url; ?>');-webkit-mask-image:url('<?php echo $icon_url; ?>');" aria-hidden="true"></span>
                            </div>
                        <?php endif; ?>

                        <div class="tile-section__card-text">
                            <h3 class="tile-section__card-title h4"><?php echo esc_html($card_title); ?></h3>
                            <?php if ($card_description) : ?>
                                <p class="tile-section__card-description body-m"><?php echo nl2br(esc_html($card_description)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
