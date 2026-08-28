<?php

$title  = get_field('title')  ?: '';
$images = get_field('images') ?: [];

if (!$title && !$images) {
    return;
}

$class  = 'case-study-showcase-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <?php if ($title) : ?>
            <h2 class="case-study-showcase-section__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($images) : ?>
            <div class="case-study-showcase-section__images">
                <?php foreach ($images as $item) :
                    $image = $item['image'] ?? null;
                    $width = $item['width'] ?? 'full';
                    if (empty($image['url'])) continue;
                ?>
                    <div class="case-study-showcase-section__item case-study-showcase-section__item--<?php echo esc_attr($width); ?>">
                        <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
