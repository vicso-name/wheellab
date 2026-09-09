<?php

$cta = get_field( 'book_a_call', 'option' ) ?: null;
if ( empty( $cta['url'] ) ) {
    return;
}

$glow_url   = esc_url( wheellab_asset_url( 'assets/img/single-post/cta-glow.jpg' ) );
$sphere_url = esc_url( wheellab_asset_url( 'assets/img/single-post/cta-sphere.png' ) );
?>
<div class="single-post-cta">
    <div class="single-post-cta__inner">
        <img class="single-post-cta__bg-glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true" loading="lazy">
        <img class="single-post-cta__bg-sphere" src="<?php echo $sphere_url; ?>" alt="" aria-hidden="true" loading="lazy">

        <div class="single-post-cta__content">
            <div class="single-post-cta__text">
                <h3 class="single-post-cta__title"><?php esc_html_e( 'Let’s talk', 'wheellab' ); ?></h3>
                <p class="single-post-cta__description"><?php esc_html_e( 'Connect with us to explore innovative ideas, gain valuable insights, and receive expert guidance tailored to your needs.', 'wheellab' ); ?></p>
            </div>

            <a
                class="btn-gradient single-post-cta__button"
                href="<?php echo esc_url( $cta['url'] ); ?>"
                <?php echo ! empty( $cta['target'] ) ? 'target="_blank" rel="noopener"' : ''; ?>
            >
                <span class="btn-gradient__inner button-text-m"><?php esc_html_e( 'Contact us', 'wheellab' ); ?></span>
            </a>
        </div>
    </div>
</div>
