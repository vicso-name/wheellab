<?php
/**
 * Template Name: Blog
 *
 * Custom page template for the blog landing page — built section by
 * section directly here (not as ACF Gutenberg blocks) since it's a
 * one-off, non-reusable layout. Fields for each section live on this
 * template via ACF's "Page Template" location rule, e.g.
 * acf-json/group_template_blog.json for the hero.
 *
 * Below the grid, the regular block editor content is rendered so an
 * admin can add ordinary reusable ACF blocks (Reviews Section, Contact
 * Section, ...) from the block inserter, same as on any other page —
 * no dedicated fields/template-parts needed for those, and
 * wheellab_enqueue_detected_block_assets() already enqueues their
 * CSS/JS automatically once they're detected in the page content.
 */

get_header();

get_template_part('template-parts/sections/blog_hero');
get_template_part('template-parts/sections/blog_filter');

if (have_posts()) :
    while (have_posts()) : the_post();
        the_content();
    endwhile;
endif;

get_footer();
