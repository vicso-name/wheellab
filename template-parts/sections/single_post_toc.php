<?php

$items = $args['items'] ?? [];
if ( ! $items ) {
    return;
}

$panel_id = 'single-post-toc-panel';
?>
<?php // The shell keeps the card's space in the flow while the nav is pinned on
      // mobile, so nothing below it jumps when the collapsed bar takes over. ?>
<div class="single-post-toc-shell" data-toc-shell>
    <nav class="single-post-toc" aria-label="<?php esc_attr_e( 'Article content', 'wheellab' ); ?>">
        <?php // Mobile-only: revealed by JS once the card has scrolled away.
              // Hidden from desktop and from no-JS mobile via CSS, so the plain
              // card below stays the only control in those cases. ?>
        <button
            type="button"
            class="single-post-toc__trigger"
            aria-expanded="false"
            aria-controls="<?php echo esc_attr( $panel_id ); ?>"
        >
            <span class="single-post-toc__trigger-text">
                <span class="single-post-toc__trigger-label"><?php esc_html_e( 'Article content', 'wheellab' ); ?></span>
                <span class="single-post-toc__trigger-current" data-role="toc-current"><?php echo esc_html( $items[0]['text'] ); ?></span>
            </span>
            <img
                class="svg single-post-toc__trigger-icon"
                src="<?php echo esc_url( wheellab_asset_url( 'assets/img/icons/chevron-right.svg' ) ); ?>"
                alt=""
            >
        </button>

        <div class="single-post-toc__panel" id="<?php echo esc_attr( $panel_id ); ?>">
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
        </div>
    </nav>
</div>
