<?php

$categories = get_the_category();
$tag        = $categories[0]->name ?? '';
$glow_url   = esc_url(wheellab_asset_url('assets/img/contact/glow.jpg'));

$author_name = get_the_author();
?>
<div class="blog-card">
    <?php

    ?>
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
                    <span class="blog-card__author"><?php echo esc_html($author_name); ?></span>
                    <span class="blog-card__date"><?php echo esc_html(get_the_date('m.d.Y')); ?></span>
                </div>
            </div>

        </div>
    </div>
</div>
