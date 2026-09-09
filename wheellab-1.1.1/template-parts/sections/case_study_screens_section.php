<?php

$title       = get_field('title')       ?: '';
$description = get_field('description') ?: '';
$images      = get_field('images')      ?: [];

$images = array_values(array_filter($images, static function ($item) {
    return !empty($item['label']) && !empty($item['image']['url']);
}));

if (!$title && !$images) {
    return;
}

$class  = 'case-study-screens-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title || $description) : ?>
            <div class="case-study-screens-section__header">
                <?php if ($title) : ?>
                    <h2 class="case-study-screens-section__title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="case-study-screens-section__description body-m"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($images) : ?>
            <div class="case-study-screens-section__grid">
                <?php foreach ($images as $item) :
                    $width = $item['width'] ?? 'full';
                ?>
                    <div class="case-study-screens-section__item case-study-screens-section__item--<?php echo esc_attr($width); ?>">
                        <span class="case-study-screens-section__label body-m"><?php echo esc_html($item['label']); ?></span>

                        <div class="case-study-screens-section__frame">
                            <img
                                src="<?php echo esc_url($item['image']['url']); ?>"
                                alt="<?php echo esc_attr($item['image']['alt'] ?: $item['label']); ?>"
                                loading="lazy"
                            >
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
