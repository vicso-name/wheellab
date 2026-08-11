<?php
/**
 * Block: Solutions Section
 * Registered as: acf/solutions-section
 * Source: WheelLab Website (Figma) — node 527:24748 (desktop), node
 * 758:61068/758:61069/758:61077 (mobile — title, description, card).
 * Assets: build/css/sections/solutions_section.min.css
 *         build/js/sections/solutions_section.min.js
 *
 * Plain repeater for now (per explicit instruction) — no options-page
 * default/override split like Reviews Section. Each card's illustration is
 * shown via object-fit: contain rather than Figma's per-image hand-tuned
 * cropping, since that positioning can't generalize to arbitrary
 * admin-uploaded images.
 */

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$cards       = get_field('cards')       ?: [];

$class  = 'solutions-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$glow_url          = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));
$chevron_left_url  = esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg'));
$chevron_right_url = esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg'));
?>

<?php if ($cards) : ?>
<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="solutions-section__header">
            <?php if ($title || $description) : ?>
                <div class="solutions-section__text">
                    <?php if ($title) : ?>
                        <h2 class="solutions-section__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if ($description) : ?>
                        <p class="solutions-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (count($cards) > 1) : ?>
                <div class="solutions-section__nav-group">
                    <button type="button" class="solutions-section__nav solutions-section__nav--prev">
                        <img class="svg" src="<?php echo $chevron_left_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Previous solution', 'wheellab'); ?></span>
                    </button>
                    <button type="button" class="solutions-section__nav solutions-section__nav--next">
                        <img class="svg" src="<?php echo $chevron_right_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Next solution', 'wheellab'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Full-bleed on purpose, same reasoning as .featured-posts-section__swiper. ?>
    <div class="solutions-section__swiper swiper">
        <div class="swiper-wrapper">
            <?php foreach ($cards as $card) :
                $card_title       = $card['title'] ?? '';
                $card_description = $card['description'] ?? '';
                $card_image       = $card['image'] ?? null;
                $card_link        = $card['link'] ?? null;
                if (!$card_title || empty($card_link['url'])) continue;
            ?>
                <div class="swiper-slide solutions-section__slide">
                    <a class="solutions-section__card" href="<?php echo esc_url($card_link['url']); ?>"
                        <?php echo !empty($card_link['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                        <div class="solutions-section__card-bezel">
                            <div class="solutions-section__card-inner">
                                <img class="solutions-section__card-glow solutions-section__card-glow--a" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
                                <img class="solutions-section__card-glow solutions-section__card-glow--b" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

                                <?php // Text before image in the DOM on purpose: desktop positions the
                                // image absolutely so source order doesn't matter there, but mobile
                                // renders it in normal flow (node 758:61077), where source order IS
                                // the visual order — text first, image last. ?>
                                <div class="solutions-section__card-info">
                                    <div class="solutions-section__card-title-row">
                                        <h3 class="solutions-section__card-title h1"><?php echo esc_html($card_title); ?></h3>

                                        <svg class="solutions-section__card-arrow" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M26.674 15.6903L12.3297 30.0347L9.97266 27.6777L24.317 13.3333H11.674V10H30.0073V28.3333H26.674V15.6903Z" fill="currentColor"/>
                                        </svg>
                                    </div>

                                    <?php if ($card_description) : ?>
                                        <p class="solutions-section__card-description body-m"><?php echo nl2br(esc_html($card_description)); ?></p>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($card_image['url'])) : ?>
                                    <div class="solutions-section__card-image">
                                        <img src="<?php echo esc_url($card_image['url']); ?>" alt="<?php echo esc_attr($card_image['alt'] ?? ''); ?>" loading="lazy">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
