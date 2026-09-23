<?php

$width       = get_field('width') ?: 'post';
$show_header = get_field('show_header');
$title       = get_field('title') ?: __('Frequently Asked Questions', 'wheellab');
$items       = get_field('items') ?: [];

$items = array_values(array_filter($items, static fn($item) => !empty($item['question'])));

if (!$items) {
    return;
}

$batch_size    = 5;
$has_load_more = count($items) > $batch_size;

$is_full = $width === 'full';

$class  = 'faq-section';
$class .= ' faq-section--' . ($is_full ? 'full' : 'post');
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$question_icon_url  = esc_url(wheellab_asset_url('assets/img/icons/question.svg'));
$plus_icon_url      = esc_url(wheellab_asset_url('assets/img/icons/plus.svg'));

$close_icon_url     = esc_url(wheellab_asset_url('assets/img/icons/close-line.svg'));

$wrapper_class = $is_full ? ' class="container"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div<?php echo $wrapper_class; ?>>
        <?php if ($is_full && $show_header) : ?>
            <h2 class="faq-section__title--full"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <div class="faq-section__card">
            <div class="faq-section__inner">

                <?php if (!$is_full && $show_header) : ?>
                    <div class="faq-section__header">
                        <img class="svg faq-section__header-icon" src="<?php echo $question_icon_url; ?>" alt="">
                        <h2 class="faq-section__title"><?php echo esc_html($title); ?></h2>
                    </div>
                <?php endif; ?>

                <div class="faq-section__list">
                    <?php foreach ($items as $index => $item) :
                        $question   = $item['question'];
                        $answer     = $item['answer'] ?? '';
                        $button     = $item['button'] ?? null;
                        $is_open    = $index === 0;
                        $is_batched = $index >= $batch_size;
                    ?>
                        <div class="faq-section__item<?php echo $is_open ? ' is-open' : ''; ?><?php echo $is_batched ? ' faq-section__item--more' : ''; ?>">
                            <button type="button" class="faq-section__question" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
                                <span class="faq-section__question-text"><?php echo esc_html($question); ?></span>
                                <span class="faq-section__toggle" aria-hidden="true">
                                    <img class="svg faq-section__toggle-icon faq-section__toggle-icon--plus" src="<?php echo $plus_icon_url; ?>" alt="">
                                    <img class="svg faq-section__toggle-icon faq-section__toggle-icon--close" src="<?php echo $close_icon_url; ?>" alt="">
                                </span>
                            </button>

                            <div class="faq-section__answer-wrap">
                                <div class="faq-section__answer-inner">
                                    <?php if ($answer) : ?>
                                        <p class="faq-section__answer"><?php echo nl2br(esc_html($answer)); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($button['url'])) : ?>
                                        <a
                                            class="faq-section__button"
                                            href="<?php echo esc_url($button['url']); ?>"
                                            <?php echo !empty($button['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                                        >
                                            <span class="faq-section__button-text"><?php echo esc_html($button['title'] ?: __('Learn more', 'wheellab')); ?></span>
                                            <svg class="faq-section__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>

        <?php if ($has_load_more) : ?>
            <div class="faq-section__load-more">
                <button type="button" class="faq-section__button faq-section__load-more-btn" data-batch-size="<?php echo (int) $batch_size; ?>">
                    <span class="faq-section__button-text"><?php esc_html_e('Load more', 'wheellab'); ?></span>
                    <svg class="faq-section__button-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M13.0001 16.1716L18.3641 10.8076L19.7783 12.2218L12.0001 20L4.22192 12.2218L5.63614 10.8076L11.0001 16.1716V4H13.0001V16.1716Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        <?php endif; ?>
    </div>
</section>
