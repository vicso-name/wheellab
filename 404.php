<?php
/**
 * 404 — Not Found
 *
 * Source: WheelLab Website (Figma) — node 527:32652 ("Main content").
 * Styles live in src/scss/partials/_errors.scss (part of the global
 * style.min.css bundle, not a conditionally-enqueued section) since
 * this is a foundational site template, not a block or page-specific
 * layout.
 */

get_header();
?>

<section class="error-404">
    <div class="error-404__bg" aria-hidden="true"></div>

    <div class="container">
        <div class="error-404__content">
            <div class="error-404__image">
                <img
                    src="<?php echo esc_url(wheellab_asset_url('assets/img/404/slot-machine.png')); ?>"
                    alt="<?php esc_attr_e('A 3D slot machine landing on a sad face', 'wheellab'); ?>"
                    loading="eager"
                >
            </div>

            <div class="error-404__text">
                <h1 class="error-404__title"><?php esc_html_e('Not lucky page', 'wheellab'); ?></h1>

                <p class="error-404__description">
                    <?php esc_html_e("You went to a page that doesn\u{2019}t exist,", 'wheellab'); ?>
                    <br>
                    <?php esc_html_e("let\u{2019}s get you back on track", 'wheellab'); ?>
                </p>

                <a class="btn-gradient error-404__button" href="<?php echo esc_url(home_url('/')); ?>">
                    <span class="btn-gradient__inner button-text-m"><?php esc_html_e('Back to Home', 'wheellab'); ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
