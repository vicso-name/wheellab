<?php
/**
 * Section: Blog Subscribe (newsletter banner)
 * Used by: template-parts/sections/blog_filter.php only — rendered inline
 * inside .blog-filter__grid, after the 6th card, so it reads as a full-width
 * row breaking up the two 3-card rows above it from the two below (node
 * 527:28142, desktop; node 620:11682, mobile 393px). Not a standalone
 * get_template_part() call from template-blog.php, and not an ACF block —
 * see blog_filter.php for why it only appears once, on the initial
 * (non-AJAX, uncategorized) grid render.
 *
 * Fields live on the "Blog" page template (acf-json/group_template_blog.json,
 * "Subscribe Block" + "Mailchimp" tabs), same as Blog Hero. Explicit post ID
 * here (not a bare get_field()) because this template part can run while a
 * DIFFERENT WP_Query's the_post() loop is active (blog_filter.php's own
 * $initial_query, mid-iteration over post cards) — a bare get_field() would
 * resolve against whatever post that loop is currently on instead of the
 * Blog page itself. Same class of bug already hit once on Case Study
 * Section — see that template's own header comment.
 */

$page_id = get_queried_object_id();

$title           = get_field('subscribe_title', $page_id)           ?: '';
$description     = get_field('subscribe_description', $page_id)     ?: '';
$button_text     = get_field('subscribe_button_text', $page_id)     ?: __('Subscribe', 'wheellab');
$glow_url        = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));

if (!$title && !$description) {
    return;
}
?>

<div class="blog-subscribe">
    <div class="blog-subscribe__bezel">
        <div class="blog-subscribe__inner">
            <img class="blog-subscribe__glow blog-subscribe__glow--1" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
            <img class="blog-subscribe__glow blog-subscribe__glow--2" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

            <div class="blog-subscribe__content">
                <?php if ($title || $description) : ?>
                    <div class="blog-subscribe__text">
                        <?php if ($title) : ?>
                            <h3 class="blog-subscribe__title h4"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>

                        <?php if ($description) : ?>
                            <p class="blog-subscribe__description body-m"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form class="blog-subscribe__form" novalidate>
                    <div class="blog-subscribe__field">
                        <input
                            type="email"
                            name="email"
                            class="blog-subscribe__input body-s"
                            placeholder="<?php esc_attr_e('Enter your email', 'wheellab'); ?>"
                            aria-label="<?php esc_attr_e('Email address', 'wheellab'); ?>"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <button type="submit" class="blog-subscribe__submit btn-gradient">
                        <span class="btn-gradient__inner button-text-m"><?php echo esc_html($button_text); ?></span>
                    </button>
                </form>

                <p class="blog-subscribe__status" role="status" aria-live="polite" hidden></p>
            </div>
        </div>
    </div>
</div>
