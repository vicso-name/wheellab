<?php
/**
 * Archive: Case Study (case_study CPT)
 * URL: /cases/ (has_archive => 'cases', see inc/cpt_cases.php)
 *
 * No Figma spec exists for this page — reuses Case Study Section's own
 * card component styles (see src/scss/sections/case_study_archive.scss)
 * for visual consistency instead of a new design. This is what the
 * "View all cases" button links to by default.
 */

get_header();
?>

<section class="case-study-archive">
    <div class="container">
        <div class="case-study-archive__header">
            <h1 class="case-study-archive__title"><?php esc_html_e('Case Studies', 'wheellab'); ?></h1>
        </div>

        <?php if (have_posts()) : ?>
            <div class="case-study-archive__grid">
                <?php while (have_posts()) : the_post();
                    $stats = get_field('stats') ?: [];
                ?>
                    <a class="case-study-section__card" href="<?php the_permalink(); ?>">
                        <div class="case-study-section__card-bezel">
                            <div class="case-study-section__card-inner">
                                <img class="case-study-section__card-glow" src="<?php echo esc_url(wheellab_asset_url('assets/img/domains/card-glow.png')); ?>" alt="" aria-hidden="true">

                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="case-study-section__card-image">
                                        <?php the_post_thumbnail('large', ['loading' => 'lazy']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="case-study-section__card-text">
                                    <h2 class="case-study-section__card-title h1"><?php the_title(); ?></h2>

                                    <?php if (get_field('description')) : ?>
                                        <p class="case-study-section__card-description body-m"><?php echo nl2br(esc_html(get_field('description'))); ?></p>
                                    <?php endif; ?>

                                    <?php if ($stats) : ?>
                                        <div class="case-study-section__card-stats">
                                            <?php foreach ($stats as $stat) :
                                                $stat_icon  = $stat['icon']  ?? null;
                                                $stat_value = $stat['value'] ?? '';
                                                $stat_label = $stat['label'] ?? '';
                                                if (!$stat_value) continue;
                                            ?>
                                                <div class="case-study-section__card-stat">
                                                    <?php if (!empty($stat_icon['url'])) :
                                                        $stat_icon_url = esc_url($stat_icon['url']);
                                                    ?>
                                                        <div class="case-study-section__card-stat-icon">
                                                            <span class="case-study-section__card-stat-icon-glyph" style="mask-image:url('<?php echo $stat_icon_url; ?>');-webkit-mask-image:url('<?php echo $stat_icon_url; ?>');" aria-hidden="true"></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="case-study-section__card-stat-text">
                                                        <span class="case-study-section__card-stat-value"><?php echo esc_html($stat_value); ?></span>
                                                        <?php if ($stat_label) : ?>
                                                            <span class="case-study-section__card-stat-label body-s"><?php echo esc_html($stat_label); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

            <div class="case-study-archive__pagination">
                <?php echo paginate_links([
                    'prev_text' => __('Prev', 'wheellab'),
                    'next_text' => __('Next', 'wheellab'),
                ]); ?>
            </div>
        <?php else : ?>
            <p class="case-study-archive__empty body-m"><?php esc_html_e('No case studies published yet.', 'wheellab'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
