<?php

$title       = single_term_title('', false);
$description = get_the_archive_description();
?>

<section class="blog-hero section-first">
    <div class="container">
        <div class="blog-hero__content">
            <h1 class="blog-hero__title h2"><?php echo esc_html($title); ?></h1>

            <?php if ($description) : ?>
                <div class="blog-hero__description body-m"><?php echo wp_kses_post($description); ?></div>
            <?php endif; ?>
        </div>
    </div>
</section>
