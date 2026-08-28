<?php
/**
 * Block: Service Capability Cards
 * Registered as: acf/service-capability-cards
 * Source: WheelLab Website (Figma) — node 806:11709 ("Frame").
 *
 * DOM/CSS is a direct copy of Solutions Section's own card recipe
 * (.solutions-section__card > __card-bezel > __card-inner > glow pair
 * + __card-info + __card-image), same class structure and nesting,
 * same hover targets/values, byte-identical corner arrow SVG — not
 * re-derived. Only real differences from Solutions Section: this is a
 * PLAIN wrapping row on this page, not a Swiper slider (no
 * slide/nav-group/swiper wrapper — the Figma "Container" itself is
 * flex-wrap), and card height is this design's own 640px, not
 * Solutions Section's 720px. No mobile Figma frame was given for this
 * node (unlike Solutions Section's sourced 758:61077), so mobile below
 * just collapses to a single auto-height column instead of replicating
 * that other block's specific mobile crop/tint treatment.
 *
 * Each card's illustration is a bespoke per-card upload (no shared
 * fallback, unlike service_industry_tiles' icon — these are real
 * commissioned art, not a generic repeated placeholder). Link is
 * optional: the corner arrow only renders when a link is set, and the
 * card itself is a plain <div> (not clickable) when it isn't.
 *
 * Ambient glow pair reuses this theme's existing shared glow texture
 * (assets/img/contact/glow.jpg — same one Solutions Section's own card
 * glow already uses) rather than the two new bg-up/bg-down assets
 * Figma exports for this node, matching the simplification precedent
 * used throughout this page's other decorative glows.
 *
 * Assets: build/css/sections/service_capability_cards.min.css
 */

$title = get_field('title') ?: '';
$cards = get_field('cards') ?: [];

$cards = array_values(array_filter($cards, static function ($card) {
    return !empty($card['title']) && !empty($card['description']) && !empty($card['image']['url']);
}));

if (!$title || !$cards) {
    return;
}

$class  = 'service-capability-cards';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$glow_url = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <h2 class="service-capability-cards__title"><?php echo esc_html($title); ?></h2>

        <div class="service-capability-cards__grid">
            <?php foreach ($cards as $card) :
                $link    = $card['link'] ?? null;
                $has_url = !empty($link['url']);
                $tag     = $has_url ? 'a' : 'div';
            ?>
                <<?php echo $tag; ?>
                    class="service-capability-cards__card"
                    <?php if ($has_url) : ?>
                        href="<?php echo esc_url($link['url']); ?>"
                        <?php echo !empty($link['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                    <?php endif; ?>
                >
                    <div class="service-capability-cards__card-bezel">
                        <div class="service-capability-cards__card-inner">
                            <img class="service-capability-cards__card-glow service-capability-cards__card-glow--a" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
                            <img class="service-capability-cards__card-glow service-capability-cards__card-glow--b" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

                            <div class="service-capability-cards__card-info">
                                <div class="service-capability-cards__card-title-row">
                                    <h3 class="service-capability-cards__card-title h1"><?php echo esc_html($card['title']); ?></h3>

                                    <?php if ($has_url) : ?>
                                        <svg class="service-capability-cards__card-arrow" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M26.674 15.6903L12.3297 30.0347L9.97266 27.6777L24.317 13.3333H11.674V10H30.0073V28.3333H26.674V15.6903Z" fill="currentColor"/>
                                        </svg>
                                    <?php endif; ?>
                                </div>

                                <p class="service-capability-cards__card-description body-m"><?php echo nl2br(esc_html($card['description'])); ?></p>
                            </div>

                            <div class="service-capability-cards__card-image">
                                <img src="<?php echo esc_url($card['image']['url']); ?>" alt="<?php echo esc_attr($card['image']['alt'] ?? ''); ?>" loading="lazy">
                            </div>
                        </div>
                    </div>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
