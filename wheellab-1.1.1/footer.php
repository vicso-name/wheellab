</main><!-- main -->
<?php

$footer_link_groups  = get_field('link_groups', 'option')        ?: [];
$footer_instagram    = get_field('social_instagram', 'option')   ?: '';
$footer_linkedin     = get_field('social_linkedin', 'option')    ?: '';
$footer_clutch_image = get_field('clutch_badge_image', 'option') ?: null;
$footer_clutch_link  = get_field('clutch_badge_link', 'option')  ?: '';
$footer_legal_links  = get_field('legal_links', 'option')        ?: [];
$footer_copyright    = get_field('copyright_text', 'option')     ?: '';
?>
    <footer class="footer" id="footer">
        <div class="footer__bg" aria-hidden="true">
            <img class="footer__bg-image" src="<?php echo esc_url(wheellab_asset_url('assets/img/footer/footer-glow.jpg')); ?>" alt="" loading="lazy">
        </div>
        <div class="footer__logomark" aria-hidden="true">
            <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/footer/logomark.svg')); ?>" alt="">
        </div>
        <div class="container">
            <div class="footer-content">

                <a class="footer__wordmark" href="<?php echo esc_url(home_url('/')); ?>">
                    <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/footer/wordmark.svg')); ?>" alt="<?php bloginfo('name'); ?>" width="406" height="59">
                </a>

                <div class="footer__card">
                    <div class="footer__card-inner">

                        <?php if ($footer_link_groups) : ?>
                            <div class="footer__link-groups">
                                <?php foreach ($footer_link_groups as $group) :
                                    $links = $group['links'] ?: [];
                                    if (empty($group['title']) && !$links) continue;
                                ?>
                                    <div class="footer__link-group">
                                        <?php if ($group['title']) : ?>
                                            <p class="footer__link-group-title header-item"><?php echo esc_html($group['title']); ?></p>
                                        <?php endif; ?>
                                        <?php foreach ($links as $row) :
                                            $link = $row['link'] ?: null;
                                            if (!$link || empty($link['url'])) continue;
                                        ?>
                                            <a
                                                class="footer__link body-m"
                                                href="<?php echo esc_url($link['url']); ?>"
                                                <?php echo $link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>
                                            ><?php echo esc_html($link['title']); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="footer__divider"></div>

                        <div class="footer__bottom">
                            <div class="footer__social">
                                <?php if ($footer_instagram || $footer_linkedin) : ?>
                                    <div class="footer__social-icons">
                                        <?php if ($footer_instagram) : ?>
                                            <a class="footer__social-icon" href="<?php echo esc_url($footer_instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                                <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/instagram.svg')); ?>" alt="">
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($footer_linkedin) : ?>
                                            <a class="footer__social-icon" href="<?php echo esc_url($footer_linkedin); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                                                <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/linkedin.svg')); ?>" alt="">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($footer_clutch_image['url'])) : ?>
                                    <a
                                        class="footer__clutch"
                                        <?php if ($footer_clutch_link) : ?>
                                            href="<?php echo esc_url($footer_clutch_link); ?>" target="_blank" rel="noopener"
                                        <?php else : ?>
                                            href="#" tabindex="-1" aria-hidden="true"
                                        <?php endif; ?>
                                    >
                                        <img
                                            src="<?php echo esc_url($footer_clutch_image['url']); ?>"
                                            alt="<?php echo esc_attr($footer_clutch_image['alt']); ?>"
                                            width="<?php echo esc_attr($footer_clutch_image['width']); ?>"
                                            height="<?php echo esc_attr($footer_clutch_image['height']); ?>"
                                            loading="lazy"
                                        >
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($footer_legal_links || $footer_copyright) : ?>
                                <div class="footer__legal">
                                    <?php foreach ($footer_legal_links as $row) :
                                        $link = $row['link'] ?: null;
                                        if (!$link || empty($link['url'])) continue;
                                    ?>
                                        <a
                                            class="footer__legal-link body-s"
                                            href="<?php echo esc_url($link['url']); ?>"
                                            <?php echo $link['target'] ? 'target="_blank" rel="noopener"' : ''; ?>
                                        ><?php echo esc_html($link['title']); ?></a>
                                    <?php endforeach; ?>
                                    <?php if ($footer_copyright) : ?>
                                        <p class="footer__copyright body-s"><?php echo esc_html(date('Y') . ' ' . $footer_copyright); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </footer><!-- footer -->
</div><!-- wrapper -->
<?php wp_footer(); ?>
    </body>
</html>