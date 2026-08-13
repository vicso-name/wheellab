<?php
/**
 * Section: Single Post — Author + Taxonomy Card
 * Used by: single.php only (not an ACF block — reads post/taxonomy/user
 * data directly, same reasoning as single_post_hero.php).
 *
 * The taxonomy tags row loops every taxonomy registered for the 'post'
 * post type EXCEPT 'category' (post_tag, and any custom ones registered
 * later) — single_post_hero.php already shows categories up top, so
 * repeating them here would just duplicate those same pills; this
 * instead surfaces whatever else the post is tagged with. The whole row
 * (divider included) is omitted if the post has no terms in any of
 * those other taxonomies, per spec.
 *
 * Source: WheelLab Website (Figma) — node 581:15436 (desktop),
 * node 617:14281 (mobile).
 * Assets: build/css/sections/single_post_author_card.min.css
 */

$post_id = get_the_ID();

$terms = [];
foreach (get_object_taxonomies('post', 'names') as $taxonomy) {
    if ($taxonomy === 'category') {
        continue;
    }
    $post_terms = get_the_terms($post_id, $taxonomy);
    if (!$post_terms || is_wp_error($post_terms)) {
        continue;
    }
    $terms = array_merge($terms, $post_terms);
}

$author_id    = (int) get_the_author_meta('ID');
$author_name  = get_the_author();
$author_title = get_field('job_title', 'user_' . $author_id) ?: '';
$author_url   = $author_id ? get_author_posts_url($author_id) : '';
$instagram    = $author_id ? get_field('social_instagram', 'user_' . $author_id) : null;
$linkedin     = $author_id ? get_field('social_linkedin', 'user_' . $author_id) : null;

$photo_field = $author_id ? get_field('photo', 'user_' . $author_id) : null;
$photo_url   = $photo_field['url'] ?? get_avatar_url($author_id, ['size' => 120]);
$photo_alt   = $photo_field['alt'] ?? $author_name;

$reading_time = wheellab_reading_time($post_id);
$views        = wheellab_get_post_views($post_id);

$calendar_icon_url = esc_url(wheellab_asset_url('assets/img/icons/calendar.svg'));
$clock_icon_url     = esc_url(wheellab_asset_url('assets/img/icons/clock.svg'));
$eye_icon_url       = esc_url(wheellab_asset_url('assets/img/icons/eye.svg'));
$instagram_icon_url = esc_url(wheellab_asset_url('assets/img/icons/instagram.svg'));
$linkedin_icon_url  = esc_url(wheellab_asset_url('assets/img/icons/linkedin.svg'));
?>
<div class="single-post-author">
    <div class="single-post-author__inner">
        <?php if ($terms) : ?>
            <div class="single-post-author__tags">
                <?php foreach ($terms as $term) : ?>
                    <a class="single-post-author__tag" href="<?php echo esc_url(get_term_link($term)); ?>"><?php echo esc_html($term->name); ?></a>
                <?php endforeach; ?>
            </div>
            <div class="single-post-author__divider"></div>
        <?php endif; ?>

        <div class="single-post-author__row">
            <?php if ($author_url) : ?>
                <a class="single-post-author__avatar" href="<?php echo esc_url($author_url); ?>">
                    <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>" loading="lazy">
                </a>
            <?php else : ?>
                <span class="single-post-author__avatar">
                    <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>" loading="lazy">
                </span>
            <?php endif; ?>

            <div class="single-post-author__content">
                <div class="single-post-author__details">
                    <?php if ($author_url) : ?>
                        <a class="single-post-author__name" href="<?php echo esc_url($author_url); ?>"><?php echo esc_html($author_name); ?></a>
                    <?php else : ?>
                        <span class="single-post-author__name"><?php echo esc_html($author_name); ?></span>
                    <?php endif; ?>

                    <?php if ($author_title) : ?>
                        <span class="single-post-author__diamond" aria-hidden="true"></span>
                        <span class="single-post-author__title"><?php echo esc_html($author_title); ?></span>
                    <?php endif; ?>
                </div>

                <div class="single-post-author__stats">
                    <span class="single-post-author__stat">
                        <img class="svg single-post-author__stat-icon" src="<?php echo $calendar_icon_url; ?>" alt="">
                        <?php echo esc_html(get_the_date()); ?>
                    </span>
                    <span class="single-post-author__diamond" aria-hidden="true"></span>
                    <span class="single-post-author__stat">
                        <img class="svg single-post-author__stat-icon" src="<?php echo $clock_icon_url; ?>" alt="">
                        <?php
                        printf(
                            /* translators: %d: reading time in minutes */
                            esc_html(_n('%d min', '%d min', $reading_time, 'wheellab')),
                            (int) $reading_time
                        );
                        ?>
                    </span>
                    <span class="single-post-author__diamond" aria-hidden="true"></span>
                    <span class="single-post-author__stat">
                        <img class="svg single-post-author__stat-icon" src="<?php echo $eye_icon_url; ?>" alt="">
                        <?php
                        printf(
                            /* translators: %s: formatted view count */
                            esc_html(_n('%s view', '%s views', $views, 'wheellab')),
                            esc_html(number_format_i18n($views))
                        );
                        ?>
                    </span>
                </div>
            </div>

            <?php if (!empty($instagram['url']) || !empty($linkedin['url'])) : ?>
                <div class="single-post-author__socials">
                    <?php if (!empty($instagram['url'])) : ?>
                        <a
                            class="single-post-author__social"
                            href="<?php echo esc_url($instagram['url']); ?>"
                            <?php echo !empty($instagram['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                            aria-label="<?php echo esc_attr($instagram['title'] ?: __('Instagram', 'wheellab')); ?>"
                        >
                            <img class="svg single-post-author__social-icon" src="<?php echo $instagram_icon_url; ?>" alt="">
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($linkedin['url'])) : ?>
                        <a
                            class="single-post-author__social"
                            href="<?php echo esc_url($linkedin['url']); ?>"
                            <?php echo !empty($linkedin['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                            aria-label="<?php echo esc_attr($linkedin['title'] ?: __('LinkedIn', 'wheellab')); ?>"
                        >
                            <img class="svg single-post-author__social-icon" src="<?php echo $linkedin_icon_url; ?>" alt="">
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
