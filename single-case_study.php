<?php
/**
 * Single: Case Study (case_study CPT)
 *
 * Case Study Hero (title, tags, background image, highlight strip) is
 * a real ACF block (acf/case-study-hero) — it renders wherever the
 * editor placed it via the_content() below, same as any other block on
 * a Page. The rest of the page is entirely block-driven too; this
 * template no longer force-inserts a fixed section of its own.
 *
 * `description`/`stats` (the case's own ACF fields) are still very
 * much in use — Case Study Section's card excerpts on the listing/
 * slider (case_study_section.php) read them via get_field(…, $case_id)
 * — this template just no longer duplicates that same data again on
 * the single page itself.
 */

get_header();

while (have_posts()) : the_post();
    the_content();
endwhile;

get_footer();
