<?php
/**
 * Section: Blog Hero
 * Used by: template-blog.php only (fields live on the "Blog" page template,
 * not a Gutenberg block — see acf-json/group_template_blog.json)
 * Assets:  build/css/sections/blog_hero.min.css
 */

$title       = get_field('hero_title')       ?: '';
$description = get_field('hero_description') ?: '';
?>

<section class="blog-hero section-first">
    <div class="container">
        <?php if ($title || $description) : ?>
            <div class="blog-hero__content">
                <?php if ($title) : ?>
                    <h1 class="blog-hero__title h2"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if ($description) : ?>
                    <p class="blog-hero__description body-m"><?php echo nl2br(esc_html($description)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
