<?php

$term     = get_queried_object();
$term_id  = $term instanceof WP_Term ? $term->term_id : 0;
$taxonomy = $term instanceof WP_Term ? $term->taxonomy : '';

$initial_query = new WP_Query(wheellab_blog_query_args([], 1, 0, $taxonomy, $term_id));
?>

<section class="author-posts">
    <div class="container">

        <p class="blog-filter__count body-s">
            <span class="blog-filter__count-number"><?php echo (int) $initial_query->found_posts; ?></span> <?php esc_html_e('articles shown', 'wheellab'); ?>
        </p>

        <div
            class="blog-filter__grid"
            data-taxonomy="<?php echo esc_attr($taxonomy); ?>"
            data-term="<?php echo esc_attr($term_id); ?>"
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
