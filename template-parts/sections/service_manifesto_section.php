<?php

$heading    = get_field('heading')    ?: '';
$subheading = get_field('subheading') ?: '';
$bg_poster  = get_field('background_poster') ?: null;

if (!$heading) {
    return;
}

$class  = 'service-manifesto-section';
$class .= !empty($block['className']) ? ' ' . $block['className']  : '';
$class .= !empty($block['align'])     ? ' align' . $block['align'] : '';
$id     = !empty($block['anchor'])    ? ' id="' . esc_attr($block['anchor']) . '"' : '';

$poster_url = !empty($bg_poster['url'])
    ? esc_url($bg_poster['url'])
    : wheellab_asset_url('assets/img/services/container-bg.webp');
?>

<section class="<?php echo esc_attr($class); ?>"<?php echo $id; ?>>
    <div class="service-manifesto-section__bg" aria-hidden="true">
        <img class="service-manifesto-section__bg-media" src="<?php echo $poster_url; ?>" alt="" loading="eager">
        <div class="service-manifesto-section__bg-overlay"></div>
        <div class="service-manifesto-section__bg-fade"></div>
    </div>

    <div class="container">
        <div class="service-manifesto-section__inner">
            <h2 class="service-manifesto-section__heading h1"><?php echo esc_html($heading); ?></h2>

            <?php if ($subheading) : ?>
                <svg class="service-manifesto-section__diamond" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="10" y="0.34314" width="13.6863" height="13.6863" rx="2" transform="rotate(45 10 0.34314)" fill="currentColor"/>
                </svg>

                <p class="service-manifesto-section__subheading"><?php echo nl2br(esc_html($subheading)); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
