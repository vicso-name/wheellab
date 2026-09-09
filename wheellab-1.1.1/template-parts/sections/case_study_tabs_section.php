<?php

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$tabs        = get_field('tabs')        ?: [];

$tabs = array_values(array_filter($tabs, static function ($tab) {
    return !empty($tab['label']) && !empty($tab['image']['url']);
}));

if (!$title && !$tabs) {
    return;
}

$class  = 'case-study-tabs-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$block_id = 'case-study-tabs-' . ($block['id'] ?? uniqid());
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title || $description) : ?>
            <div class="case-study-tabs-section__header">
                <?php if ($title) : ?>
                    <h2 class="case-study-tabs-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="case-study-tabs-section__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($tabs) : ?>
            <div class="case-study-tabs-section__row">
                <div class="case-study-tabs-section__tabs" role="tablist" aria-label="<?php echo esc_attr($title ?: __('Highlights', 'wheellab')); ?>">
                    <?php foreach ($tabs as $index => $tab) : ?>
                        <button
                            type="button"
                            class="case-study-tabs-section__tab<?php echo $index === 0 ? ' is-active' : ''; ?>"
                            role="tab"
                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                            aria-controls="<?php echo esc_attr($block_id . '-image-' . $index); ?>"
                            data-index="<?php echo (int) $index; ?>"
                        >
                            <span class="case-study-tabs-section__tab-inner"><?php echo esc_html($tab['label']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="case-study-tabs-section__frame">
                    <div class="case-study-tabs-section__images">
                        <?php foreach ($tabs as $index => $tab) : ?>
                            <img
                                id="<?php echo esc_attr($block_id . '-image-' . $index); ?>"
                                class="case-study-tabs-section__image<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                src="<?php echo esc_url($tab['image']['url']); ?>"
                                alt="<?php echo esc_attr($tab['image']['alt'] ?: $tab['label']); ?>"
                                data-index="<?php echo (int) $index; ?>"
                                <?php echo $index === 0 ? 'loading="eager"' : 'loading="lazy"'; ?>
                            >
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
