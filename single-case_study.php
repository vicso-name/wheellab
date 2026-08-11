<?php
/**
 * Single: Case Study (case_study CPT)
 *
 * No Figma spec exists for this page — title, featured image, stats
 * (reusing Case Study Section's own .case-study-section__card-stat
 * component), then the_content(). See
 * src/scss/sections/case_study_archive.scss.
 */

get_header();

while (have_posts()) : the_post();
    $description = get_field('description') ?: '';
    $stats       = get_field('stats')       ?: [];
    $archive_url = get_post_type_archive_link('case_study');
?>

<section class="case-study-single">
    <div class="container">
        <div class="case-study-single__header">
            <h1 class="case-study-single__title"><?php the_title(); ?></h1>

            <?php if ($description) : ?>
                <p class="case-study-single__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
            <?php endif; ?>
        </div>

        <?php if (has_post_thumbnail()) : ?>
            <div class="case-study-single__image">
                <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
            </div>
        <?php endif; ?>

        <?php if ($stats) : ?>
            <div class="case-study-single__stats">
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

        <?php if (get_the_content()) : ?>
            <div class="case-study-single__content body-m">
                <?php the_content(); ?>
            </div>
        <?php endif; ?>

        <?php if ($archive_url) : ?>
            <a class="case-study-single__back btn-gradient" href="<?php echo esc_url($archive_url); ?>">
                <span class="btn-gradient__inner button-text-m"><?php esc_html_e('All case studies', 'wheellab'); ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
