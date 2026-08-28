<?php

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$client_logo = get_field('client_logo') ?: null;
$challenges  = get_field('challenges')  ?: [];

if (!$title && !$description && empty($client_logo['url']) && !$challenges) {
    return;
}

$class  = 'case-study-about-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <div class="case-study-about-section__content">
            <div class="case-study-about-section__text">
                <?php if ($title) : ?>
                    <h2 class="case-study-about-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <div class="case-study-about-section__description body-m">
                        <?php echo wpautop(esc_html($description)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($client_logo['url'])) : ?>
                <div class="case-study-about-section__logo">
                    <img src="<?php echo esc_url($client_logo['url']); ?>" alt="<?php echo esc_attr($client_logo['alt'] ?? ''); ?>" loading="lazy">
                </div>
            <?php endif; ?>
        </div>

        <?php
        $challenge_texts = array_values(array_filter(array_map(static function ($challenge) {
            return $challenge['text'] ?? '';
        }, $challenges)));
        $challenge_rows = array_chunk($challenge_texts, 2);
        ?>

        <?php if ($challenge_rows) : ?>
            <div class="case-study-about-section__challenges">
                <?php $number = 1; foreach ($challenge_rows as $row) : ?>
                    <div class="case-study-about-section__challenge-row">
                        <?php foreach ($row as $challenge_text) : ?>
                            <div class="case-study-about-section__challenge">
                                <div class="case-study-about-section__challenge-inner">
                                    <span class="case-study-about-section__challenge-number"><?php echo (int) $number++; ?></span>
                                    <p class="case-study-about-section__challenge-text h4"><?php echo esc_html($challenge_text); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
