<?php
/**
 * Template Name: Blog
 *
 * Custom page template for the blog landing page — built section by
 * section directly here (not as ACF Gutenberg blocks) since it's a
 * one-off, non-reusable layout. Fields for each section live on this
 * template via ACF's "Page Template" location rule, e.g.
 * acf-json/group_template_blog.json for the hero.
 */

get_header();

get_template_part('template-parts/sections/blog_hero');
get_template_part('template-parts/sections/blog_filter');

get_footer();
