<?php
/**
 * Block: Case Study Quote Section
 * Registered as: acf/case-study-quote-section
 * Source: WheelLab Website (Figma) — node 527:31672 ("Container").
 *
 * The blur-in-on-scroll reveal (src/js/sections/case_study_quote_section.js)
 * follows the same IntersectionObserver pattern as Domains Section's own
 * card reveal (domains_section.js/.scss) — .is-visible added once ~30%
 * of the quote has scrolled into view, skipped entirely for
 * prefers-reduced-motion users.
 *
 * Assets: build/css/sections/case_study_quote_section.min.css
 *         build/js/sections/case_study_quote_section.min.js
 */

$quote  = get_field('quote')  ?: '';
$author = get_field('author') ?: '';

if (!$quote) {
    return;
}

$class  = 'case-study-quote-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$quote_mark_url = esc_url(wheellab_asset_url('assets/img/icons/quote-mark.svg'));
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="case-study-quote-section__inner">
            <img class="svg case-study-quote-section__mark" src="<?php echo $quote_mark_url; ?>" alt="" aria-hidden="true">

            <blockquote class="case-study-quote-section__quote h1">
                <?php echo esc_html($quote); ?>
            </blockquote>

            <?php if ($author) : ?>
                <cite class="case-study-quote-section__author">&mdash; <?php echo esc_html($author); ?></cite>
            <?php endif; ?>
        </div>
    </div>
</section>
