<?php

$background_image = get_field('background_image') ?: null;
$hero_tags         = get_field('hero_tags')        ?: [];
$hero_highlights   = get_field('hero_highlights')  ?: [];

$bg_url = '';
$bg_alt = '';
if (!empty($background_image['url'])) {
    $bg_url = $background_image['url'];
    $bg_alt = $background_image['alt'] ?? '';
} elseif (has_post_thumbnail()) {
    $bg_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
}

$class  = 'case-study-hero';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="case-study-hero__bg" aria-hidden="true">
        <?php if ($bg_url) : ?>
            <img class="case-study-hero__bg-image" src="<?php echo esc_url($bg_url); ?>" alt="<?php echo esc_attr($bg_alt); ?>" loading="eager">
        <?php endif; ?>
        <div class="case-study-hero__bg-gradient"></div>
    </div>

    <div class="container">
        <div class="case-study-hero__content">
            <div class="case-study-hero__center">
                <h1 class="case-study-hero__title"><?php the_title(); ?></h1>

                <?php if ($hero_tags) : ?>
                    <div class="case-study-hero__tags">
                        <?php foreach ($hero_tags as $tag) :
                            $tag_label = $tag['label'] ?? '';
                            if (!$tag_label) continue;
                        ?>
                            <span class="case-study-hero__tag"><?php echo esc_html($tag_label); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($hero_highlights) : ?>
                <div class="case-study-hero__footer">
                    <div class="case-study-hero__divider" aria-hidden="true"></div>

                    <div class="case-study-hero__highlights">
                        <?php foreach ($hero_highlights as $highlight) :
                            $highlight_label = $highlight['label'] ?? '';
                            $highlight_value = $highlight['value'] ?? '';
                            $highlight_note  = $highlight['note']  ?? '';
                            if (!$highlight_label || !$highlight_value) continue;
                        ?>
                            <div class="case-study-hero__highlight">
                                <span class="case-study-hero__highlight-label"><?php echo esc_html($highlight_label); ?></span>
                                <span class="case-study-hero__highlight-value"><?php echo esc_html($highlight_value); ?></span>
                                <?php if ($highlight_note) : ?>
                                    <span class="case-study-hero__highlight-note"><?php echo esc_html($highlight_note); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
