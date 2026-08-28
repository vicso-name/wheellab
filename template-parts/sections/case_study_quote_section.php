<?php

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
