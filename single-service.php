<?php
/**
 * Single: Service (service CPT)
 *
 * Minimal on purpose — unlike single-case_study.php, there's no fixed
 * (non-block) section spec for Service posts yet. Every visual piece
 * (Service Hero, and whatever follows it) is a real ACF block placed in
 * the post's own content via the block editor, same as a normal Page.
 * Add a fixed section here only once a Figma spec calls for one that
 * isn't itself a block (see single-case_study.php for that pattern).
 */

get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();
