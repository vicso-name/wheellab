<?php

$post_categories = get_the_category();
$tags = array_filter($post_categories, static fn($cat) => $cat->slug !== 'uncategorized');

$author_id    = (int) get_the_author_meta('ID');
$author_name  = get_the_author();
$author_title = get_field('job_title', 'user_' . $author_id) ?: '';
$author_url   = $author_id ? get_author_posts_url($author_id) : '';

$photo_field = $author_id ? get_field('photo', 'user_' . $author_id) : null;
$photo_url   = $photo_field['url'] ?? get_avatar_url($author_id, ['size' => 120]);
$photo_alt   = $photo_field['alt'] ?? $author_name;

$reading_time = wheellab_reading_time(get_the_ID());
$views        = wheellab_get_post_views(get_the_ID());

$calendar_icon_url = esc_url(wheellab_asset_url('assets/img/icons/calendar.svg'));
$clock_icon_url     = esc_url(wheellab_asset_url('assets/img/icons/clock.svg'));
$eye_icon_url       = esc_url(wheellab_asset_url('assets/img/icons/eye.svg'));
?>

<section class="single-post-hero">
    <div class="single-post-hero__bg" aria-hidden="true">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('full', ['class' => 'single-post-hero__bg-image', 'loading' => 'eager', 'alt' => '']); ?>
        <?php endif; ?>
        <div class="single-post-hero__bg-gradient"></div>
    </div>

    <div class="container">
        <div class="single-post-hero__content">
            <?php if ($tags) : ?>
                <div class="single-post-hero__tags">
                    <?php foreach ($tags as $tag) : ?>
                        <span class="single-post-hero__tag"><?php echo esc_html($tag->name); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h1 class="single-post-hero__title"><?php the_title(); ?></h1>

            <div class="single-post-hero__author">
                <?php if ($author_url) : ?>
                    <a class="single-post-hero__avatar" href="<?php echo esc_url($author_url); ?>">
                        <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>">
                    </a>
                <?php else : ?>
                    <span class="single-post-hero__avatar">
                        <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>">
                    </span>
                <?php endif; ?>

                <div class="single-post-hero__author-content">
                    <div class="single-post-hero__author-info">
                        <?php if ($author_url) : ?>
                            <a class="single-post-hero__author-name" href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                        <?php else : ?>
                            <span class="single-post-hero__author-name"><?php echo esc_html($author_name); ?></span>
                        <?php endif; ?>

                        <?php if ($author_title) : ?>
                            <span class="single-post-hero__diamond" aria-hidden="true"></span>
                            <span class="single-post-hero__author-title"><?php echo esc_html($author_title); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="single-post-hero__meta">
                        <span class="single-post-hero__meta-item">
                            <img class="svg single-post-hero__meta-icon" src="<?php echo $calendar_icon_url; ?>" alt="">
                            <?php echo esc_html(get_the_date()); ?>
                        </span>
                        <span class="single-post-hero__diamond" aria-hidden="true"></span>
                        <span class="single-post-hero__meta-item">
                            <img class="svg single-post-hero__meta-icon" src="<?php echo $clock_icon_url; ?>" alt="">
                            <?php
                            printf(

                                esc_html(_n('%d min', '%d min', $reading_time, 'wheellab')),
                                (int) $reading_time
                            );
                            ?>
                        </span>
                        <span class="single-post-hero__diamond" aria-hidden="true"></span>
                        <span class="single-post-hero__meta-item">
                            <img class="svg single-post-hero__meta-icon" src="<?php echo $eye_icon_url; ?>" alt="">
                            <?php
                            printf(

                                esc_html(_n('%s view', '%s views', $views, 'wheellab')),
                                esc_html(number_format_i18n($views))
                            );
                            ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
