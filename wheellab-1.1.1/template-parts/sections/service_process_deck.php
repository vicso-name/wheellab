<?php

$title = get_field('title') ?: '';
$steps = get_field('steps') ?: [];

$steps = array_values(array_filter($steps, static function ($step) {
    return !empty($step['title']) && !empty($step['description']);
}));

if (!$title || count($steps) < 2) {
    return;
}

$class  = 'service-process-deck';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="container">
        <h2 class="service-process-deck__title"><?php echo esc_html($title); ?></h2>
    </div>

    <div class="container">
        <div class="service-process-deck__stage">
            <div class="service-process-deck__deck" style="--deck-count: <?php echo (int) count($steps); ?>;">
                <?php foreach ($steps as $i => $step) :
                    $number = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
                ?>
                    <button
                        type="button"
                        class="service-process-deck__card<?php echo $i === 0 ? ' is-active' : ''; ?>"
                        style="--deck-offset: <?php echo (int) $i; ?>;"
                        data-index="<?php echo (int) $i; ?>"
                        aria-label="<?php echo esc_attr(sprintf( __('Step %1$d: %2$s', 'wheellab'), $i + 1, $step['title'])); ?>"
                    >
                        <span class="service-process-deck__card-inner">
                            <span class="service-process-deck__card-texture service-process-deck__card-texture--top" aria-hidden="true"></span>
                            <span class="service-process-deck__card-texture service-process-deck__card-texture--bottom" aria-hidden="true"></span>

                            <span class="service-process-deck__card-head">
                                <span class="service-process-deck__card-top">
                                    <span class="service-process-deck__card-number h3"><?php echo esc_html($number); ?></span>
                                </span>

                                <span class="service-process-deck__card-text">
                                    <span class="service-process-deck__card-heading h3"><?php echo esc_html($step['title']); ?></span>
                                    <span class="service-process-deck__card-description body-m"><?php echo nl2br(esc_html($step['description'])); ?></span>
                                </span>
                            </span>

                            <span class="service-process-deck__card-bottom">
                                <span class="service-process-deck__card-number service-process-deck__card-number--ghost h3"><?php echo esc_html($number); ?></span>
                            </span>
                        </span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <ul class="service-process-deck__list">
            <?php foreach ($steps as $i => $step) :
                $number = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
            ?>
                <li class="service-process-deck__list-item">
                    <span class="service-process-deck__list-number h3"><?php echo esc_html($number); ?></span>
                    <span class="service-process-deck__list-text">
                        <span class="service-process-deck__list-heading h3"><?php echo esc_html($step['title']); ?></span>
                        <span class="service-process-deck__list-description body-m"><?php echo nl2br(esc_html($step['description'])); ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
