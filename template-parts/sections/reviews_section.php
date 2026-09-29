<?php

$use_custom     = (bool) get_field('use_custom_reviews');
$custom_reviews = $use_custom ? (get_field('custom_reviews') ?: []) : [];
$reviews        = $custom_reviews ?: (get_field('reviews', 'option') ?: []);

$reviews = array_values(array_filter($reviews, static function ($review) {
    return !empty($review['quote']);
}));

// Swiper 11's loop reorders the real slides instead of cloning them, so a
// short list cannot fill a wide track: with three reviews the right-hand
//336px of the section stayed empty at 1440 and new quotes slid in out of
// nowhere. Repeating the set until there are enough slides gives the loop
// something to work with — the viewer sees the same cycle either way.
// Nine covers an ultra-wide track, where a 754px slide plus its 60px gap
// leaves roughly four visible and the loop wants about twice that.
// A local, not a const: the block can legitimately appear twice on one page,
// and a file-scope const would warn on the second render.
$min_slides = 9;

$review_count  = count($reviews);
$review_passes = ($review_count > 1 && $review_count < $min_slides)
    ? (int) ceil($min_slides / $review_count)
    : 1;

$class  = 'reviews-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$quote_icon_url = esc_url(wheellab_asset_url('assets/img/icons/quote.svg'));
// Chevrons, not arrows: arrow-*.svg carries a hardcoded fill="white", so it
// cannot take the nav's colour or its disabled state. See case_study_section,
// which this matches.
$chevron_left_url  = esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg'));
$chevron_right_url = esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg'));
?>

<?php if ($reviews) : ?>
<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <?php

    ?>
    <div class="reviews-section__swiper swiper">
        <div class="swiper-wrapper">
            <?php for ($pass = 0; $pass < $review_passes; $pass++) : ?>
            <?php foreach ($reviews as $review) :
                $quote       = $review['quote'] ?? '';
                $author_name = $review['author_name'] ?? '';
                $author_role = $review['author_role'] ?? '';
            ?>
                <div class="swiper-slide reviews-section__slide">
                    <img class="svg reviews-section__quote-icon" src="<?php echo $quote_icon_url; ?>" alt="">

                    <p class="reviews-section__quote subhead"><?php echo nl2br(esc_html($quote)); ?></p>

                    <?php if ($author_name || $author_role) : ?>
                        <div class="reviews-section__meta">
                            <?php if ($author_name) : ?>
                                <span class="reviews-section__author-name"><?php echo esc_html($author_name); ?></span>
                            <?php endif; ?>

                            <?php if ($author_name && $author_role) : ?>
                                <span class="reviews-section__diamond" aria-hidden="true"></span>
                            <?php endif; ?>

                            <?php if ($author_role) : ?>
                                <span class="reviews-section__author-role"><?php echo esc_html($author_role); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>

    <?php if (count($reviews) > 1) : ?>
        <div class="container">
            <div class="reviews-section__pagination">
                <button type="button" class="reviews-section__nav reviews-section__nav--prev">
                    <img class="svg" src="<?php echo $chevron_left_url; ?>" alt="">
                    <span class="visually-hidden"><?php esc_html_e('Previous review', 'wheellab'); ?></span>
                </button>
                <button type="button" class="reviews-section__nav reviews-section__nav--next">
                    <img class="svg" src="<?php echo $chevron_right_url; ?>" alt="">
                    <span class="visually-hidden"><?php esc_html_e('Next review', 'wheellab'); ?></span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>
