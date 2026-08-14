<?php
/**
 * Section: Single Post — Rating
 * Used by: single.php only. Not an ACF block — the widget reads live
 * vote data from a dedicated DB table (inc/post_ratings.php) rather than
 * any admin-editable field.
 *
 * One vote per rater is enforced server-side (DB UNIQUE KEY on
 * post_id+rater_key, re-checked in the AJAX handler) — the disabled
 * button state rendered below is only a UX convenience for a rater we
 * already recognize, not the actual guard.
 *
 * Source: WheelLab Website (Figma) — node 618:9919 ("Rate card", mobile),
 * node 581:15393 ("Rate card", desktop). Desktop puts the title and star
 * buttons on one row (title left, buttons right); mobile stacks them —
 * see single_post_rating.scss's __header rule for the breakpoint switch.
 * Assets: build/css/sections/single_post_rating.min.css
 *         build/js/sections/single_post_rating.min.js
 */

$post_id       = get_the_ID();
$stats         = wheellab_get_post_rating_stats($post_id);
$user_rating   = wheellab_get_user_post_rating($post_id);
$already_rated = $user_rating !== null;

$star_icon_url = esc_url(wheellab_asset_url('assets/img/icons/star.svg'));
?>
<div
    class="single-post-rating<?php echo $already_rated ? ' is-rated' : ''; ?>"
    data-post-id="<?php echo esc_attr($post_id); ?>"
    data-rated="<?php echo $already_rated ? '1' : '0'; ?>"
>
    <div class="single-post-rating__inner">
        <div class="single-post-rating__header">
            <p class="single-post-rating__title"><?php esc_html_e('Rate this article', 'wheellab'); ?></p>

            <div class="single-post-rating__stars" role="group" aria-label="<?php esc_attr_e('Rate this article from 1 to 5 stars', 'wheellab'); ?>">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <button
                        type="button"
                        class="single-post-rating__star<?php echo $already_rated && $i <= $user_rating ? ' is-active' : ''; ?>"
                        data-rating="<?php echo (int) $i; ?>"
                        <?php echo $already_rated ? 'disabled' : ''; ?>
                        aria-label="<?php echo esc_attr(sprintf(
                            /* translators: %d: star position, 1-5 */
                            _n('%d star', '%d stars', $i, 'wheellab'),
                            $i
                        )); ?>"
                    >
                        <img class="svg single-post-rating__star-icon" src="<?php echo $star_icon_url; ?>" alt="">
                    </button>
                <?php endfor; ?>
            </div>
        </div>

        <div class="single-post-rating__summary">
            <span class="single-post-rating__summary-stars">
                <img class="svg single-post-rating__summary-icon" src="<?php echo $star_icon_url; ?>" alt="">
                <span class="single-post-rating__summary-value" data-role="average"><?php echo esc_html(number_format_i18n($stats['average'], 1)); ?></span>
            </span>
            <span class="single-post-rating__diamond" aria-hidden="true"></span>
            <span class="single-post-rating__summary-count" data-role="count-text">
                <?php
                printf(
                    /* translators: %s: number of people who rated */
                    esc_html(_n('%s person rated', '%s people rated', $stats['count'], 'wheellab')),
                    esc_html(number_format_i18n($stats['count']))
                );
                ?>
            </span>
        </div>
    </div>
</div>
