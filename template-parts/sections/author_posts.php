<?php
/**
 * Section: Author Posts
 * Used by: author.php only. "N articles from this author" (node
 * 527:30095) + a 12-per-page grid of the author's posts + AJAX "load
 * more" (node 527:30115) — same query shape, card partial and
 * load-more mechanics as the Blog page grid (template-parts/sections/
 * blog_filter.php), just author-scoped instead of category-scoped, so
 * it reuses that section's CSS classes (blog_filter.scss) and posts
 * through the same inc/ajax_blog.php endpoint with an `author` id
 * instead of a `category` slug.
 *
 * Source: WheelLab Website (Figma) — node 527:30074.
 */

$author    = get_queried_object();
$author_id = $author instanceof WP_User ? $author->ID : 0;

$initial_query = new WP_Query(wheellab_blog_query_args('', 1, $author_id));
?>

<section class="author-posts">
    <div class="container">

        <p class="blog-filter__count body-s">
            <span class="blog-filter__count-number"><?php echo (int) $initial_query->found_posts; ?> <?php esc_html_e('articles', 'wheellab'); ?></span> <?php esc_html_e('from this author', 'wheellab'); ?>
        </p>

        <div
            class="blog-filter__grid"
            data-author="<?php echo esc_attr($author_id); ?>"
            data-page="1"
            data-max-pages="<?php echo (int) $initial_query->max_num_pages; ?>"
        >
            <?php if ($initial_query->have_posts()) : ?>
                <?php while ($initial_query->have_posts()) : $initial_query->the_post(); ?>
                    <?php get_template_part('template-parts/sections/blog_card'); ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <?php wp_reset_postdata(); ?>

        <div class="blog-filter__load-more"<?php echo $initial_query->max_num_pages > 1 ? '' : ' hidden'; ?>>
            <button type="button" class="btn-gradient">
                <span class="btn-gradient__inner button-text-m"><?php esc_html_e('View more', 'wheellab'); ?></span>
            </button>
        </div>

    </div>
</section>
