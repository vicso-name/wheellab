<?php

$title        = get_field('hero_title')       ?: get_the_title();
$description  = get_field('hero_description') ?: '';
$last_updated = get_field('last_updated')      ?: current_time('m.d.Y');
?>
<section class="privacy-hero">
    <div class="privacy-hero__bg" aria-hidden="true"></div>

    <div class="container">
        <div class="privacy-hero__content">
            <h1 class="privacy-hero__title"><?php echo esc_html($title); ?></h1>

            <?php if ($description) : ?>
                <p class="privacy-hero__description"><?php echo esc_html($description); ?></p>
            <?php endif; ?>

            <p class="privacy-hero__updated">
                <?php
                printf(

                    esc_html__('Last Updated: %s', 'wheellab'),
                    esc_html($last_updated)
                );
                ?>
            </p>
        </div>
    </div>

    <div class="privacy-hero__divider"></div>
</section>
