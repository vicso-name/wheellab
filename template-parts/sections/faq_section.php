<?php
/**
 * Block: FAQ Section
 * Registered as: acf/faq-section
 * Source: WheelLab Website (Figma) — node 581:15346 ("Faq"), also
 * reused verbatim (as a Figma component instance) at node 806:11724
 * for the Service page — same card/accordion/per-item-button, so no
 * new block was built for that; this file was extended instead.
 *
 * Two width modes (field: width) so the same block works both inside a
 * blog post's article column and as a standalone full-width section on
 * any other page — see the "Width" field's instructions in the admin.
 * The sitewide .container wrapper (max-width 1568px + its own 24px
 * horizontal padding) only applies in "full" mode, matching every other
 * page section. In "post" mode the block already sits inside the
 * article's own reading-width column (single_post_body.scss) — adding
 * .container's padding there too would shrink the card an extra 48px
 * narrower than single_post_rating/single_post_author, which don't go
 * through .container at all.
 *
 * "Show Header" now renders two different treatments depending on
 * width mode, both sourced from real Figma nodes rather than one being
 * invented: "post" mode keeps the original icon + title row INSIDE the
 * card (581:15346's own header, meant for a compact blog-embedded
 * widget); "full" mode instead renders a plain centered H2 ABOVE the
 * card with no icon (node 806:11725 "FAQ header") — matching how every
 * other standalone page section titles itself. The "Load More" button
 * (node 806:11729, full mode only in practice but not hard-restricted
 * to it) is a new optional field — same visual recipe as the existing
 * per-item CTA button (same arrow-down-line icon), just centered below
 * the card instead of inside an open item.
 *
 * The first item is open by default (matches the Figma example state);
 * the rest start collapsed. Accordion behavior (open/close, one or many
 * at once) is handled client-side in faq_section.js; without JS the
 * first item still reads correctly since its expanded state is rendered
 * server-side.
 * Assets: build/css/sections/faq_section.min.css
 *         build/js/sections/faq_section.min.js
 */

$width       = get_field('width') ?: 'post';
$show_header = get_field('show_header');
$title       = get_field('title') ?: __('Frequently Asked Questions', 'wheellab');
$items       = get_field('items') ?: [];
$load_more   = get_field('load_more');

if (!$items) {
    return;
}

$is_full = $width === 'full';

$class  = 'faq-section';
$class .= ' faq-section--' . ($is_full ? 'full' : 'post');
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$question_icon_url  = esc_url(wheellab_asset_url('assets/img/icons/question.svg'));
$plus_icon_url      = esc_url(wheellab_asset_url('assets/img/icons/plus.svg'));
// Not assets/img/icons/close.svg (used by the mobile menu toggle) — that
// one's stroke reads noticeably bolder once scaled to this icon's size;
// this is the actual close-line asset from the FAQ node itself.
$close_icon_url     = esc_url(wheellab_asset_url('assets/img/icons/close-line.svg'));

$wrapper_class = $is_full ? ' class="container"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div<?php echo $wrapper_class; ?>>
        <?php if ($is_full && $show_header) : ?>
            <h2 class="faq-section__title--full"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <div class="faq-section__card">
            <div class="faq-section__inner">

                <?php if (!$is_full && $show_header) : ?>
                    <div class="faq-section__header">
                        <img class="svg faq-section__header-icon" src="<?php echo $question_icon_url; ?>" alt="">
                        <h2 class="faq-section__title"><?php echo esc_html($title); ?></h2>
                    </div>
                <?php endif; ?>

                <div class="faq-section__list">
                    <?php foreach ($items as $index => $item) :
                        $question = $item['question'] ?? '';
                        $answer   = $item['answer']    ?? '';
                        $button   = $item['button']    ?? null;
                        if (!$question) continue;
                        $is_open = $index === 0;
                    ?>
                        <div class="faq-section__item<?php echo $is_open ? ' is-open' : ''; ?>">
                            <button type="button" class="faq-section__question" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
                                <span class="faq-section__question-text"><?php echo esc_html($question); ?></span>
                                <span class="faq-section__toggle" aria-hidden="true">
                                    <img class="svg faq-section__toggle-icon faq-section__toggle-icon--plus" src="<?php echo $plus_icon_url; ?>" alt="">
                                    <img class="svg faq-section__toggle-icon faq-section__toggle-icon--close" src="<?php echo $close_icon_url; ?>" alt="">
                                </span>
                            </button>

                            <div class="faq-section__answer-wrap">
                                <div class="faq-section__answer-inner">
                                    <?php if ($answer) : ?>
                                        <p class="faq-section__answer"><?php echo nl2br(esc_html($answer)); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($button['url'])) : ?>
                                        <a
                                            class="faq-section__button"
                                            href="<?php echo esc_url($button['url']); ?>"
                                            <?php echo !empty($button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                                        >
                                            <span class="faq-section__button-text"><?php echo esc_html($button['title'] ?: __('Learn more', 'wheellab')); ?></span>
                                            <svg class="faq-section__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <?php if (!empty($load_more['url'])) : ?>
            <div class="faq-section__load-more">
                <a
                    class="faq-section__button"
                    href="<?php echo esc_url($load_more['url']); ?>"
                    <?php echo !empty($load_more['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                >
                    <span class="faq-section__button-text"><?php echo esc_html($load_more['title'] ?: __('Load more', 'wheellab')); ?></span>
                    <svg class="faq-section__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
