<?php

$page_id = get_queried_object_id();

$title           = get_field('subscribe_title', $page_id)           ?: '';
$description     = get_field('subscribe_description', $page_id)     ?: '';
$button_text     = get_field('subscribe_button_text', $page_id)     ?: __('Subscribe', 'wheellab');
$glow_url        = esc_url(wheellab_asset_url('assets/img/domains/card-glow.png'));

if (!$title && !$description) {
    return;
}
?>

<div class="blog-subscribe">
    <div class="blog-subscribe__bezel">
        <div class="blog-subscribe__inner">
            <img class="blog-subscribe__glow blog-subscribe__glow--1" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">
            <img class="blog-subscribe__glow blog-subscribe__glow--2" src="<?php echo $glow_url; ?>" alt="" aria-hidden="true">

            <div class="blog-subscribe__content">
                <?php if ($title || $description) : ?>
                    <div class="blog-subscribe__text">
                        <?php if ($title) : ?>
                            <h3 class="blog-subscribe__title h4"><?php echo esc_html($title); ?></h3>
                        <?php endif; ?>

                        <?php if ($description) : ?>
                            <p class="blog-subscribe__description body-m"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form class="blog-subscribe__form" novalidate>
                    <div class="blog-subscribe__field">
                        <input
                            type="email"
                            name="email"
                            class="blog-subscribe__input body-s"
                            placeholder="<?php esc_attr_e('Enter your email', 'wheellab'); ?>"
                            aria-label="<?php esc_attr_e('Email address', 'wheellab'); ?>"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <button type="submit" class="blog-subscribe__submit btn-gradient">
                        <span class="btn-gradient__inner button-text-m"><?php echo esc_html($button_text); ?></span>
                    </button>
                </form>

                <p class="blog-subscribe__status" role="status" aria-live="polite" hidden></p>
            </div>
        </div>
    </div>
</div>
