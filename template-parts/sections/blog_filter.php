<?php
/**
 * Section: Blog Filter + Grid
 * Used by: template-blog.php only.
 *
 * The first page renders server-side (no flash of empty grid / no JS
 * dependency for the initial view); category switches and "load more"
 * clicks are handled client-side via admin-ajax (see inc/ajax_blog.php,
 * src/js/sections/blog_filter.js).
 *
 * The category chips come straight from Figma (node 527:28120) — no "All"
 * chip is shown there, so clicking the active chip again deselects it
 * (falls back to showing everything) instead of adding an 8th chip not in
 * the design. The chips' active/selected style isn't specified in Figma
 * either; it reuses the project's existing "solid accent pill" language.
 *
 * Source: WheelLab Website (Figma) — node 527:28120 (chips), 527:28132
 * ("shown" count), 527:28151 ("View more" button), 527:28142 (Subscribe
 * banner, spliced in after the 6th card below).
 */

$categories = get_categories(['hide_empty' => false]);
$categories = array_filter($categories, static fn($cat) => $cat->slug !== 'uncategorized');

$initial_query = new WP_Query(wheellab_blog_query_args('', 1));

// Subscribe banner only on the plain, unfiltered first page (never on
// category switches or "load more" — inc/ajax_blog.php's grid response
// never includes it, and it only ever makes sense once, between the
// first two 3-card rows and the next two, not re-inserted mid-append).
$subscribe_enabled = (bool) get_field('subscribe_enabled', get_queried_object_id());
?>

<section class="blog-filter">
    <div class="container">

        <?php if ($categories) : ?>
            <div class="blog-filter__chips" role="tablist" aria-label="<?php esc_attr_e('Filter articles by category', 'wheellab'); ?>">
                <?php foreach ($categories as $category) : ?>
                    <button
                        type="button"
                        class="blog-filter__chip body-s"
                        role="tab"
                        aria-selected="false"
                        data-category="<?php echo esc_attr($category->slug); ?>"
                    ><?php echo esc_html($category->name); ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p class="blog-filter__count body-s">
            <span class="blog-filter__count-number"><?php echo (int) $initial_query->found_posts; ?></span> <?php esc_html_e('articles shown', 'wheellab'); ?>
        </p>

        <div
            class="blog-filter__grid"
            data-category=""
            data-page="1"
            data-max-pages="<?php echo (int) $initial_query->max_num_pages; ?>"
        >
            <?php if ($initial_query->have_posts()) :
                $card_index = 0;
                while ($initial_query->have_posts()) : $initial_query->the_post();
                    $card_index++;
                    get_template_part('template-parts/sections/blog_card');

                    if ($card_index === 6 && $subscribe_enabled && $initial_query->post_count > 6) {
                        get_template_part('template-parts/sections/blog_subscribe');
                    }
                endwhile;
            endif; ?>
        </div>
        <?php wp_reset_postdata(); ?>

        <div class="blog-filter__load-more"<?php echo $initial_query->max_num_pages > 1 ? '' : ' hidden'; ?>>
            <button type="button" class="btn-gradient">
                <span class="btn-gradient__inner button-text-m"><?php esc_html_e('View more', 'wheellab'); ?></span>
            </button>
        </div>

    </div>
</section>
