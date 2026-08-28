<?php

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
