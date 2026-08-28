<?php
/**
 * Block: Service Industry Tiles
 * Registered as: acf/service-industry-tiles
 * Source: WheelLab Website (Figma) — node 806:11703 ("Tile section",
 * desktop) and node 806:11976 ("Tile section", mobile 393px).
 *
 * Same card bezel/icon-badge system as the general tile_section block
 * (icon 52px accent-2 ghost badge, surface-2/surface-2-light bezel,
 * h4 accent-2 title) — reused deliberately for consistency rather than
 * re-derived, since this Figma layer is literally also named
 * "Tile section". Differs from tile_section in three ways, all per
 * explicit instruction:
 *  - "Content" is a WYSIWYG field (not a plain textarea) so an editor
 *    can bold/italic/link the copy — but the design's own bulleted list
 *    is the one thing that has to come out looking right, so
 *    service_industry_tiles.scss styles :where(ul/ol/li) inside
 *    .service-industry-tiles__card-content specifically to match (solid
 *    white disc bullets, 30px/24px indent, 8px item gap — not the
 *    article-body list styling single_post_body.scss already owns
 *    elsewhere, which uses different spacing for long-form prose).
 *  - Icon is optional per card with a MANDATORY theme-bundled fallback
 *    (assets/img/services/industry-icon-fallback.svg — the exact glyph
 *    Figma itself repeats identically across every card in the source
 *    mock, strongly suggesting it's a generic placeholder rather than
 *    real per-industry art) rather than the plain "hide the icon if
 *    empty" tile_section itself does — an editor can still upload a
 *    unique icon per card if they have one.
 *  - Grid is a plain fixed 2-column layout (Figma's own "Icons row"
 *    pairs), not tile_section's 12-col asymmetric 7/5 first row.
 *
 * Assets: build/css/sections/service_industry_tiles.min.css
 */

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
