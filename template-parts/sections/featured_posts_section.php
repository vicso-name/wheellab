<?php
/**
 * Block: Featured Posts Section
 * Registered as: acf/featured-posts-section
 * Source: WheelLab Website (Figma) — node 527:24951. Cards reuse the same
 * markup as the blog page (template-parts/sections/blog_card.php, node
 * 235:4331 — identical bezel/inner/tag/title/meta styling and tokens).
 * Assets: build/css/sections/featured_posts_section.min.css
 *         build/js/sections/featured_posts_section.min.js
 *
 * "Posts Source" lets an editor choose between the 10-latest-posts default
 * and a hand-picked, hand-ordered list (ACF relationship field — chosen
 * over a multi Post Object field because it gives editors search/filter
 * and drag-to-reorder for free, which is exactly what "pick a few posts to
 * feature, in a specific order" needs).
 */

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$source      = get_field('posts_source') ?: 'latest';

if ($source === 'manual') {
    $post_ids   = get_field('manual_posts') ?: [];
    $query_args = $post_ids ? [
        'post_type'      => 'post',
        'post__in'       => $post_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($post_ids),
    ] : null;
} else {
    $count      = (int) get_field('posts_count') ?: 10;
    $query_args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
}

$posts_query = $query_args ? new WP_Query($query_args) : null;

$class  = 'featured-posts-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$arrow_left_url  = esc_url(wheellab_asset_url('assets/img/icons/arrow-left.svg'));
$arrow_right_url = esc_url(wheellab_asset_url('assets/img/icons/arrow-right.svg'));

// Mobile (node 758:62165) uses a different, smaller nav button — same
// 44px/mask-icon recipe as .solutions-section__nav, not the desktop
// bare-icon style — toggled via CSS like the header's logo-full/-mobile
// swap (see src/scss/sections/featured_posts_section.scss).
$chevron_left_url  = esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg'));
$chevron_right_url = esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg'));
?>

<?php if ($posts_query && $posts_query->have_posts()) : ?>
<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="featured-posts-section__header">
            <?php if ($title || $description) : ?>
                <div class="featured-posts-section__text">
                    <?php if ($title) : ?>
                        <h2 class="featured-posts-section__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if ($description) : ?>
                        <p class="featured-posts-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($posts_query->post_count > 1) : ?>
                <div class="featured-posts-section__nav-group">
                    <button type="button" class="featured-posts-section__nav featured-posts-section__nav--prev">
                        <img class="svg featured-posts-section__nav-icon featured-posts-section__nav-icon--desktop" src="<?php echo $arrow_left_url; ?>" alt="">
                        <img class="svg featured-posts-section__nav-icon featured-posts-section__nav-icon--mobile" src="<?php echo $chevron_left_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Previous post', 'wheellab'); ?></span>
                    </button>
                    <button type="button" class="featured-posts-section__nav featured-posts-section__nav--next">
                        <img class="svg featured-posts-section__nav-icon featured-posts-section__nav-icon--desktop" src="<?php echo $arrow_right_url; ?>" alt="">
                        <img class="svg featured-posts-section__nav-icon featured-posts-section__nav-icon--mobile" src="<?php echo $chevron_right_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Next post', 'wheellab'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Full-bleed on purpose, same reasoning as .reviews-section__swiper
    // — the peeking next card is clipped at the section's own edge, not
    // .container's 1568px measure. Padding keeps slide 1 aligned with the
    // title above. ?>
    <div class="featured-posts-section__swiper swiper">
        <div class="swiper-wrapper">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <div class="swiper-slide featured-posts-section__slide">
                    <?php get_template_part('template-parts/sections/blog_card'); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
wp_reset_postdata();
endif;
?>
