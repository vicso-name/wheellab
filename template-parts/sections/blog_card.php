<?php
/**
 * Partial: Blog Card
 * Renders one article card. Must run inside a loop (the_post() already
 * called) — used both by the initial PHP render in blog_filter.php and by
 * the AJAX handler in inc/ajax_blog.php, so the markup never drifts
 * between the two.
 *
 * Source: WheelLab Website (Figma) — node 235:4331 ("CardsBlog").
 */

$categories = get_the_category();
$tag        = $categories[0]->name ?? '';
$glow_url   = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));

// "Jason Stathem, CTO" (node 235:4322/235:4383) — job_title is an ACF
// field on the WP user profile (acf-json/group_author_fields.json), same
// field the author page hero reads via author_hero.php.
$author_id    = (int) get_the_author_meta('ID');
$author_title = get_field('job_title', 'user_' . $author_id) ?: '';
$author_name  = get_the_author() . ($author_title ? ', ' . $author_title : '');
$author_url   = $author_id ? get_author_posts_url($author_id) : '';
?>
<div class="blog-card">
    <?php // Stretched-link overlay: the card itself is no longer a single
    // <a> (an <a> can't contain another <a> — invalid HTML, breaks click
    // behavior) now that .blog-card__author below is its own real link
    // to the author page. This covers the whole card and links to the
    // post; .blog-card__author sits above it (z-index) so it stays
    // independently clickable. ?>
    <a class="blog-card__link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>"></a>

    <div class="blog-card__bezel">
        <div class="blog-card__inner">
            <img class="blog-card__glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

            <div class="blog-card__image">
                <?php if ($tag) : ?>
                    <span class="blog-card__tag button-text-s"><?php echo esc_html($tag); ?></span>
                <?php endif; ?>
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium_large', ['class' => 'blog-card__img', 'loading' => 'lazy', 'alt' => get_the_title()]); ?>
                <?php endif; ?>
            </div>

            <div class="blog-card__text">
                <h3 class="blog-card__title h4"><?php the_title(); ?></h3>
                <div class="blog-card__meta header-item">
                    <?php if ($author_url) : ?>
                        <a class="blog-card__author" href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                    <?php else : ?>
                        <span class="blog-card__author"><?php echo esc_html($author_name); ?></span>
                    <?php endif; ?>
                    <span class="blog-card__date"><?php echo esc_html(get_the_date()); ?></span>
                </div>
            </div>

        </div>
    </div>
</div>
