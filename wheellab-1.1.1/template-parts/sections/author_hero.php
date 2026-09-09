<?php

$author    = get_queried_object();
$author_id = $author instanceof WP_User ? $author->ID : 0;

$name       = $author_id ? get_the_author_meta('display_name', $author_id) : '';
$job_title  = $author_id ? (get_field('job_title', 'user_' . $author_id) ?: '') : '';
$bio        = $author_id ? get_the_author_meta('description', $author_id) : '';
$instagram  = $author_id ? get_field('social_instagram', 'user_' . $author_id) : null;
$linkedin   = $author_id ? get_field('social_linkedin', 'user_' . $author_id) : null;

$photo_field = $author_id ? get_field('photo', 'user_' . $author_id) : null;
$photo_url   = $photo_field['url'] ?? get_avatar_url($author_id, ['size' => 200]);
$photo_alt   = $photo_field['alt'] ?? $name;

$instagram_icon_url = esc_url(wheellab_asset_url('assets/img/icons/instagram.svg'));
$linkedin_icon_url  = esc_url(wheellab_asset_url('assets/img/icons/linkedin.svg'));
?>

<section class="author-hero section-first">
    <div class="container">
        <div class="author-hero__card">
            <div class="author-hero__card-inner">
                <div class="author-hero__photo">
                    <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>" loading="eager">
                </div>

                <div class="author-hero__content">
                    <div class="author-hero__info">
                        <h1 class="author-hero__name"><?php echo esc_html($name); ?></h1>
                        <?php if ($job_title) : ?>
                            <span class="author-hero__diamond" aria-hidden="true"></span>
                            <span class="author-hero__job-title body-m"><?php echo esc_html($job_title); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($bio) : ?>
                        <p class="author-hero__bio body-m"><?php echo nl2br(esc_html($bio)); ?></p>
                    <?php endif; ?>

                    <?php if ($instagram || $linkedin) : ?>
                        <div class="author-hero__socials">
                            <?php if (!empty($instagram['url'])) : ?>
                                <a
                                    class="author-hero__social"
                                    href="<?php echo esc_url($instagram['url']); ?>"
                                    <?php echo !empty($instagram['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                                >
                                    <img class="svg author-hero__social-icon" src="<?php echo $instagram_icon_url; ?>" alt="">
                                    <span class="body-m"><?php echo esc_html($instagram['title'] ?: __('Instagram', 'wheellab')); ?></span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($linkedin['url'])) : ?>
                                <a
                                    class="author-hero__social"
                                    href="<?php echo esc_url($linkedin['url']); ?>"
                                    <?php echo !empty($linkedin['target']) ? 'target="_blank" rel="noopener"' : ''; ?>
                                >
                                    <img class="svg author-hero__social-icon" src="<?php echo $linkedin_icon_url; ?>" alt="">
                                    <span class="body-m"><?php echo esc_html($linkedin['title'] ?: __('LinkedIn', 'wheellab')); ?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
