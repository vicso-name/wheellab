<?php
/**
 * Block: Case Study Section
 * Registered as: acf/case-study-section
 * Source: WheelLab Website (Figma) — node 758:46934 ("Case Study Section").
 * Assets: build/css/sections/case_study_section.min.css
 *         build/js/sections/case_study_section.min.js
 *
 * Backed by the `case_study` CPT (inc/cpt_cases.php) rather than a
 * repeater field — same "Posts Source" latest/manual pattern as Featured
 * Posts Section, so per-case data (title, featured image, description,
 * stats) lives on its own post instead of being duplicated per block
 * instance. Cards link to their case's single page.
 *
 * Each case's two stat tags are their own nested repeater (icon/value/
 * label) rather than fixed per-index fields, so the layout still holds
 * up if an admin adds a third one later even though Figma's own mock
 * always shows exactly two.
 */

$title       = get_field('title')        ?: '';
$description = get_field('description')  ?: '';
$source      = get_field('cases_source') ?: 'latest';
$cta_button  = get_field('cta_button')   ?: null;

if ($source === 'manual') {
    $case_ids   = get_field('manual_cases') ?: [];
    $query_args = $case_ids ? [
        'post_type'      => 'case_study',
        'post__in'       => $case_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => count($case_ids),
    ] : null;
} else {
    $count      = (int) get_field('cases_count') ?: 10;
    $query_args = [
        'post_type'      => 'case_study',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
}

$cases_query = $query_args ? new WP_Query($query_args) : null;

$class  = 'case-study-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

// node 758:44648;437:14500 — a rotated, blurred nebula texture behind
// each card (mix-blend-mode: lighten, blur(35px)). Reusing the same
// shared glow texture already used by Domains/Tile Section rather than
// the much larger (14MB raw, 4096×2286) sprite sheet Figma uses here —
// same blur radius (35px) as .blog-card__glow, so it's simplified the
// same way: a plain corner anchor instead of the exact rotated crop.
$glow_url          = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));
$chevron_left_url  = esc_url(wheellab_asset_url('assets/img/icons/chevron-left.svg'));
$chevron_right_url = esc_url(wheellab_asset_url('assets/img/icons/chevron-right.svg'));

$cta_url = !empty($cta_button['url']) ? $cta_button['url'] : get_post_type_archive_link('case_study');

if (!$cases_query || !$cases_query->have_posts()) {
    return;
}
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="case-study-section__header">
            <?php if ($title || $description) : ?>
                <div class="case-study-section__text">
                    <?php if ($title) : ?>
                        <h2 class="case-study-section__title"><?php echo esc_html($title); ?></h2>
                    <?php endif; ?>

                    <?php if ($description) : ?>
                        <p class="case-study-section__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($cases_query->post_count > 1) : ?>
                <div class="case-study-section__nav-group">
                    <button type="button" class="case-study-section__nav case-study-section__nav--prev">
                        <img class="svg" src="<?php echo $chevron_left_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Previous case', 'wheellab'); ?></span>
                    </button>
                    <button type="button" class="case-study-section__nav case-study-section__nav--next">
                        <img class="svg" src="<?php echo $chevron_right_url; ?>" alt="">
                        <span class="visually-hidden"><?php esc_html_e('Next case', 'wheellab'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php // Full-bleed on purpose, same reasoning as .solutions-section__swiper. ?>
    <div class="case-study-section__swiper swiper">
        <div class="swiper-wrapper">
            <?php while ($cases_query->have_posts()) : $cases_query->the_post();
                // Explicit post ID here — inside an ACF block's render,
                // get_field() with no ID resolves against the BLOCK's own
                // fields by default, not the looped post. This block also
                // has its own "description" field, so the bare call was
                // silently returning the section's description on every
                // card instead of each case's own, and "stats" (which the
                // block doesn't have at all) was just always empty.
                $case_id          = get_the_ID();
                $case_description = get_field('description', $case_id) ?: '';
                $case_stats       = get_field('stats', $case_id)       ?: [];
            ?>
                <div class="swiper-slide case-study-section__slide">
                    <a class="case-study-section__card" href="<?php the_permalink(); ?>">
                        <div class="case-study-section__card-bezel">
                            <div class="case-study-section__card-inner">
                                <img class="case-study-section__card-glow" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="case-study-section__card-image">
                                        <?php the_post_thumbnail('large', ['loading' => 'lazy']); ?>
                                    </div>
                                <?php endif; ?>

                                <div class="case-study-section__card-text">
                                    <h3 class="case-study-section__card-title h1"><?php the_title(); ?></h3>

                                    <?php if ($case_description) : ?>
                                        <p class="case-study-section__card-description body-m"><?php echo nl2br(esc_html($case_description)); ?></p>
                                    <?php endif; ?>

                                    <?php if ($case_stats) : ?>
                                        <div class="case-study-section__card-stats">
                                            <?php foreach ($case_stats as $stat) :
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
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php if ($cta_url) : ?>
        <div class="container">
            <a class="case-study-section__cta btn-gradient" href="<?php echo esc_url($cta_url); ?>"
                <?php echo !empty($cta_button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>>
                <span class="btn-gradient__inner button-text-m"><?php echo esc_html($cta_button['title'] ?? '' ?: 'View all cases'); ?></span>
            </a>
        </div>
    <?php endif; ?>
</section>
<?php wp_reset_postdata(); ?>
