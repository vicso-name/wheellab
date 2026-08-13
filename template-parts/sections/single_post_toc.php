<?php
/**
 * Section: Single Post — Table of Contents (sidebar)
 * Used by: single.php only. Not an ACF block — $args['items'] is built
 * by wheellab_add_heading_anchors_and_toc() from the post's own <h2>
 * headings (see single.php and inc/theme_function.php). Returns nothing
 * for posts with no h2 headings at all.
 *
 * Active-item highlighting (node 527:29046, "Benefit from our
 * cross-industry experience") is scroll-spy, handled client-side in
 * single_post_toc.js — server-rendered with no item active, since
 * "currently in view" only means something once the page can scroll.
 *
 * Source: WheelLab Website (Figma) — node 527:29045.
 * Assets: build/css/sections/single_post_toc.min.css
 *         build/js/sections/single_post_toc.min.js
 */

$items = $args['items'] ?? [];
if ( ! $items ) {
    return;
}
?>
<nav class="single-post-toc" aria-label="<?php esc_attr_e( 'Article content', 'wheellab' ); ?>">
    <div class="single-post-toc__label"><?php esc_html_e( 'Article content', 'wheellab' ); ?></div>
    <ul class="single-post-toc__list">
        <?php foreach ( $items as $item ) : ?>
            <li>
                <a class="single-post-toc__link" href="#<?php echo esc_attr( $item['anchor'] ); ?>" data-toc-anchor="<?php echo esc_attr( $item['anchor'] ); ?>">
                    <?php echo esc_html( $item['text'] ); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
