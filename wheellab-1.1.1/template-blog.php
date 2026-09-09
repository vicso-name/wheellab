<?php

get_header();

get_template_part('template-parts/sections/blog_hero');
get_template_part('template-parts/sections/blog_filter');

if (have_posts()) :
    while (have_posts()) : the_post();
        the_content();
    endwhile;
endif;

get_footer();
