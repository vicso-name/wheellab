<?php

defined('ABSPATH') || exit;

$post_id = get_the_ID();
$context = isset($args['context']) ? sanitize_html_class((string) $args['context']) : 'page';
$excerpt = wheellab_search_result_excerpt($post_id);
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
        <span class="site-search-card__type button-text-s"><?php echo esc_html($type); ?></span>
        <h3 class="site-search-card__title h4"><?php the_title(); ?></h3>
        <?php if ($excerpt) : ?>
            <p class="site-search-card__excerpt body-s"><?php echo esc_html($excerpt); ?></p>
        <?php endif; ?>
    </div>

    <span class="site-search-card__arrow" aria-hidden="true">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 16L16 8M10 8H16V14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </span>
</article>
