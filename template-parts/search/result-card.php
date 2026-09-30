<?php

defined('ABSPATH') || exit;

$post_id = get_the_ID();
$context = isset($args['context']) ? sanitize_html_class((string) $args['context']) : 'page';
$type    = wheellab_search_result_type_label($post_id);
?>
<article class="site-search-card site-search-card--<?php echo esc_attr($context); ?>">
    <a class="site-search-card__link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>"></a>

    <div class="site-search-card__media" aria-hidden="true">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium', [
                'class'   => 'site-search-card__image',
                'loading' => 'lazy',
                'alt'     => '',
            ]); ?>
        <?php else : ?>
            <span class="site-search-card__placeholder"></span>
        <?php endif; ?>
    </div>

    <div class="site-search-card__content">
        <span class="site-search-card__type button-text-m"><?php echo esc_html($type); ?></span>
        <h3 class="site-search-card__title <?php echo $context === 'page' ? 'h3' : 'h4'; ?>"><?php the_title(); ?></h3>
    </div>

    <span class="site-search-card__arrow" aria-hidden="true">
        <img src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/search-result-arrow.svg')); ?>" alt="" width="24" height="24">
    </span>
</article>
