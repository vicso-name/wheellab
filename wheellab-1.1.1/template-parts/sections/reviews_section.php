<?php

$use_custom     = (bool) get_field('use_custom_reviews');
$custom_reviews = $use_custom ? (get_field('custom_reviews') ?: []) : [];
$reviews        = $custom_reviews ?: (get_field('reviews', 'option') ?: []);

$class  = 'reviews-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$quote_icon_url = esc_url(wheellab_asset_url('assets/img/icons/quote.svg'));
$arrow_left_url  = esc_url(wheellab_asset_url('assets/img/icons/arrow-left.svg'));
$arrow_right_url = esc_url(wheellab_asset_url('assets/img/icons/arrow-right.svg'));
?>

<?php if ($reviews) : ?>
<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <?php

    ?>
    <div class="reviews-section__swiper swiper">
        <div class="swiper-wrapper">
            <?php foreach ($reviews as $review) :
                $quote       = $review['quote'] ?? '';
                $author_name = $review['author_name'] ?? '';
                $author_role = $review['author_role'] ?? '';
                if (!$quote) continue;
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
        </div>
    </div>

    <?php if (count($reviews) > 1) : ?>
        <div class="container">
            <div class="reviews-section__pagination">
                <button type="button" class="reviews-section__nav reviews-section__nav--prev">
                    <img class="svg" src="<?php echo $arrow_left_url; ?>" alt="">
                    <span class="visually-hidden"><?php esc_html_e('Previous review', 'wheellab'); ?></span>
                </button>
                <button type="button" class="reviews-section__nav reviews-section__nav--next">
                    <img class="svg" src="<?php echo $arrow_right_url; ?>" alt="">
                    <span class="visually-hidden"><?php esc_html_e('Next review', 'wheellab'); ?></span>
                </button>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php endif; ?>
