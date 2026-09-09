<?php

get_header();

$search_query = get_search_query();
?>
<main id="main" class="site-search-page">
    <section class="site-search-page__section">
        <div class="container">
            <div class="site-search-page__header">
                <p class="site-search-page__eyebrow button-text-m"><?php esc_html_e('Search', 'wheellab'); ?></p>
                <h1 class="site-search-page__title">
                    <?php
                    printf(
                        esc_html__('Results for “%s”', 'wheellab'),
                        esc_html($search_query)
                    );
                    ?>
                </h1>
                <?php if (have_posts()) : ?>
                    <p class="site-search-page__count body-m">
                        <?php
                        global $wp_query;
                        printf(
                            esc_html(_n('%s result found', '%s results found', (int) $wp_query->found_posts, 'wheellab')),
                            number_format_i18n((int) $wp_query->found_posts)
                        );
                        ?>
                    </p>
                <?php endif; ?>
            </div>

            <?php if (have_posts()) : ?>
                <div class="site-search-page__grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <?php get_template_part('template-parts/search/result-card', null, ['context' => 'page']); ?>
                    <?php endwhile; ?>
                </div>

                <nav class="site-search-page__pagination" aria-label="<?php esc_attr_e('Search results pages', 'wheellab'); ?>">
                    <?php
                    echo wp_kses_post(paginate_links([
                        'prev_text' => __('Previous', 'wheellab'),
                        'next_text' => __('Next', 'wheellab'),
                    ]));
                    ?>
                </nav>
            <?php else : ?>
                <div class="site-search-page__empty">
                    <h2 class="h3"><?php esc_html_e('Nothing matched your search.', 'wheellab'); ?></h2>
                    <p class="body-m"><?php esc_html_e('Try a different phrase or browse the main navigation.', 'wheellab'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
