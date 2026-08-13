<?php
/**
 * Single: Post
 *
 * Built section by section: hero (template-parts/sections/
 * single_post_hero.php), then a two-column body — article content +
 * sidebar (Table of Contents + Contact CTA) — see template-parts/
 * sections/single_post_toc.php and single_post_cta.php. Below that,
 * full content-column width again: a rating widget (single_post_rating.php)
 * and an author + taxonomy card (single_post_author_card.php).
 *
 * The content is captured as a string (not echoed via the_content())
 * because it has to pass through wheellab_add_heading_anchors_and_toc()
 * first — that's what both injects id="" anchors into the post's own
 * <h2> headings AND builds the sidebar TOC's item list from those same
 * headings, so the two can never drift out of sync with each other.
 */

get_header();

while (have_posts()) : the_post();
    wheellab_track_post_view(get_the_ID());

    get_template_part('template-parts/sections/single_post_hero');

    $content = apply_filters('the_content', get_the_content());
    [$content, $toc_items] = wheellab_add_heading_anchors_and_toc($content);
    ?>

    <section class="single-post-body">
        <div class="single-post-body__container">
            <div class="single-post-body__grid">
                <div class="single-post-body__content">
                    <div class="single-post-content body-m"><?php echo $content; ?></div>
                </div>

                <aside class="single-post-body__sidebar">
                    <?php get_template_part('template-parts/sections/single_post_toc', null, ['items' => $toc_items]); ?>
                    <?php get_template_part('template-parts/sections/single_post_cta'); ?>
                </aside>
            </div>

            <div class="single-post-body__footer">
                <?php get_template_part('template-parts/sections/single_post_rating'); ?>
                <?php get_template_part('template-parts/sections/single_post_author_card'); ?>
            </div>
        </div>
    </section>

<?php endwhile; ?>

<?php get_footer(); ?>
