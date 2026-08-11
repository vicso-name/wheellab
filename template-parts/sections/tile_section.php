<?php
/**
 * Block: Tile Section
 * Registered as: acf/tile-section
 * Source: WheelLab Website (Figma) — node 783:27431 ("Tile section").
 *
 * Plain repeater for cards (icon, title, description) — same convention
 * as Solutions/Domains Section, no options-page default/override split.
 * Icons are admin-uploaded SVGs tinted via CSS mask (mask-image +
 * background-color) rather than requiring pre-colored files, so any
 * single-color glyph uploaded here always renders in the accent-2 cyan.
 * Assets: build/css/sections/tile_section.min.css
 */

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$cards       = get_field('cards')       ?: [];

$class  = 'tile-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

// node 783:27546 — the same shared glow texture already used by Domains
// Section (confirmed byte-identical), just re-cropped/centered on the
// section itself here instead of anchored to a card.
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
