<?php

get_header();

get_template_part('template-parts/sections/privacy_hero');

if (have_posts()) :
    while (have_posts()) : the_post();
        ?>
        <section class="privacy-content">
            <div class="container">
                <div class="single-post-content body-m"><?php the_content(); ?></div>
            </div>
        </section>
        <?php
    endwhile;
endif;

get_footer();
