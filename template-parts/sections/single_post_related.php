<?php
/**
 * Section: Single Post — Read More (related posts)
 * Used by: single.php only. Not an ACF block — a per-post settings
 * group on the post edit screen instead (acf-json/group_single_post_related.json),
 * since this always renders automatically rather than being manually
 * inserted into the content, matching single_post_rating.php /
 * single_post_author_card.php's reasoning.
 *
 * Structurally and visually the same component as
 * template-parts/sections/featured_posts_section.php (node 527:24951) —
 * same header/nav/swiper/blog_card markup and CSS — just without a
 * description field, and sourced from per-post fields instead of block
 * fields. Sits BELOW .single-post-body as its own full-width section
 * (.container, 1568px), not inside the article/sidebar layout.
 *
 * "show_related_posts" defaults to true even for posts saved before this
 * field existed — ACF's own default_value only pre-fills the admin form,
 * get_field() returns '' (not the default) for a value that was never
 * actually saved, so that has to be handled explicitly below.
 *
 * Assets: build/css/sections/single_post_related.min.css
 *         build/js/sections/single_post_related.min.js
 */

$post_id = get_the_ID();

$enabled_raw = get_field('show_related_posts', $post_id);
$enabled     = $enabled_raw === '' || $enabled_raw === null ? true : (bool) $enabled_raw;

if (!$enabled) {
    return;
}

$source = get_field('related_posts_source', $post_id) ?: 'latest';

if ($source === 'manual') {
    $post_ids   = get_field('related_posts', $post_id) ?: [];
    $query_args = $post_ids ? [
        'post_type'      => 'post',
        'post__in'       => $post_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($post_ids),
    ] : null;
} else {
    $count      = (int) get_field('related_posts_count', $post_id) ?: 10;
    $query_args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'post__not_in'   => [$post_id],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
}

$posts_query = $query_args ? new WP_Query($query_args) : null;

if (!$posts_query || !$posts_query->have_posts()) {
    return;
}

$arrow_left_url    = esc_url(wheellab_asset_url('assets/img/icons/arrow-left.svg'));
$arrow_right_url   = esc_url(wheellab_asset_url('assets/img/icons/arrow-right.svg'));
$chevron_left_url  = esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg'));
$chevron_right_url = esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg'));
?>
<section class="single-post-related">
    <div class="container">
        <div class="single-post-related__header">
            <h2 class="single-post-related__title"><?php esc_html_e('Read more on this topic', 'wheellab'); ?></h2>

            <?php if ($posts_query->post_count > 1) : ?>
                <div class="single-post-related__nav-group">
                    <button type="button" class="single-post-related__nav single-post-related__nav--prev">
                        <img class="svg single-post-related__nav-icon single-post-related__nav-icon--desktop" src="<?php echo $arrow_left_url; ?>" alt="">
                        <img class="svg single-post-related__nav-icon single-post-related__nav-icon--mobile" src="<?php echo $chevron_left_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Previous post', 'wheellab'); ?></span>
                    </button>
                    <button type="button" class="single-post-related__nav single-post-related__nav--next">
                        <img class="svg single-post-related__nav-icon single-post-related__nav-icon--desktop" src="<?php echo $arrow_right_url; ?>" alt="">
                        <img class="svg single-post-related__nav-icon single-post-related__nav-icon--mobile" src="<?php echo $chevron_right_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Next post', 'wheellab'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Full-bleed on purpose, same reasoning as .featured-posts-section__swiper. ?>
    <div class="single-post-related__swiper swiper">
        <div class="swiper-wrapper">
            <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                <div class="swiper-slide single-post-related__slide">
                    <?php get_template_part('template-parts/sections/blog_card'); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>
<?php
wp_reset_postdata();
