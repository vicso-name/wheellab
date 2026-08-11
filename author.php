<?php
/**
 * Author archive — WordPress's native template for /author/{nicename}/.
 * Structured like template-blog.php (hero + post grid + Reviews + Contact),
 * but for one author's posts instead of the whole blog, and built from a
 * real WP_User rather than a Page — there's no post/block content to hold
 * Reviews/Contact block instances here, so they're rendered directly via
 * get_template_part() instead of the_content(). Reviews Section already
 * falls back to Theme Options > Reviews without a block instance; Contact
 * Section falls back to the new Theme Options > Contact for the same
 * reason (see template-parts/sections/contact_section.php).
 *
 * Author bio data (job title, photo, socials) lives on the WP user
 * profile — acf-json/group_author_fields.json — plus WP core's own
 * "Biographical Info" field for the bio text.
 *
 * Source: WheelLab Website (Figma) — node 527:30074.
 */

get_header();

get_template_part('template-parts/sections/author_hero');
get_template_part('template-parts/sections/author_posts');

get_template_part('template-parts/sections/reviews_section');
get_template_part('template-parts/sections/contact_section');

get_footer();
