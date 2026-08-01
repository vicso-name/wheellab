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
?>
<a class="blog-card" href="<?php the_permalink(); ?>">
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
                    <span class="blog-card__author"><?php the_author(); ?></span>
                    <span class="blog-card__date"><?php echo esc_html(get_the_date()); ?></span>
                </div>
            </div>

        </div>
    </div>
</a>
