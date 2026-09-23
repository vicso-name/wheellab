<?php

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

    <?php get_template_part('template-parts/sections/single_post_related'); ?>
    <?php get_template_part('template-parts/sections/contact_section'); ?>

<?php endwhile; ?>

<?php get_footer(); ?>
