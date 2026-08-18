<?php
/**
 * Template Name: Privacy Policy
 *
 * Custom page template — hero (title/subtitle/last-updated date, ACF
 * fields on this template via the "Page Template" location rule, see
 * acf-json/group_template_privacy_policy.json), then the regular block
 * editor content below it for the actual policy text. That content is
 * styled with the same article typography as blog posts
 * (.single-post-content — see single_post_body.scss's :where()-scoped
 * H2/H3/paragraph/list/link/image rules), with the block-rhythm/list-gap
 * and 1031px reading-width overrides this page's own content actually
 * measures at scoped in privacy_hero.scss's .privacy-content block
 * (kept separate from the shared file so blog posts are unaffected).
 *
 * Source: WheelLab Website (Figma) — node 527:32387 (hero, desktop),
 * node 783:12146 (hero, mobile), node 527:32397 (body content).
 */

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
