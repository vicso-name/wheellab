<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <title><?php wp_title(' | ', true, 'right'); ?><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> >
    <a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'wheellab'); ?></a>
    <div id="wrapper">
        <?php
        // Header content: logo + Book a Call live on the ACF Options page
        // (Theme Options > Header). Nav items come from the "primary" WP
        // menu (Appearance > Menus); the "Services" mega-menu is ACF fields
        // attached to that menu item — see docs/acf-block-patterns.md and
        // inc/theme_function.php.
        $header_logo        = get_field('logo', 'option') ?: null;
        $header_logo_mobile = get_field('logo_mobile', 'option') ?: null;
        $header_cta         = get_field('book_a_call', 'option') ?: null;
        $primary_items      = wheellab_get_primary_menu_items();
        ?>
        <header class="header" id="header">
            <div class="container">
                <div class="header__bar">
                <div class="header__bar-inner">

                    <a class="header__logo" href="<?php echo esc_url(home_url('/')); ?>">
                        <?php if (!empty($header_logo['url'])) : ?>
                            <img
                                class="svg header__logo-full"
                                src="<?php echo esc_url($header_logo['url']); ?>"
                                alt="<?php echo esc_attr($header_logo['alt'] ?: get_bloginfo('name')); ?>"
                            >
                        <?php else : ?>
                            <img class="svg header__logo-full" src="<?php echo esc_url(wheellab_asset_url('assets/img/header/logo.svg')); ?>" alt="<?php bloginfo('name'); ?>">
                        <?php endif; ?>

                        <?php if (!empty($header_logo_mobile['url'])) : ?>
                            <img
                                class="svg header__logo-mobile"
                                src="<?php echo esc_url($header_logo_mobile['url']); ?>"
                                alt="<?php echo esc_attr($header_logo_mobile['alt'] ?: get_bloginfo('name')); ?>"
                            >
                        <?php else : ?>
                            <img class="svg header__logo-mobile" src="<?php echo esc_url(wheellab_asset_url('assets/img/header/logo-mobile.svg')); ?>" alt="<?php bloginfo('name'); ?>">
                        <?php endif; ?>
                    </a>

                    <?php if ($primary_items) : ?>
                        <nav class="header__nav" aria-label="<?php esc_attr_e('Primary', 'wheellab'); ?>">
                            <ul class="header__nav-list">
                                <?php foreach ($primary_items as $item) :
                                    $categories  = wheellab_get_menu_item_mega_menu($item->ID);
                                    $has_mega    = (bool) $categories;
                                    $item_target = $item->target ? ' target="' . esc_attr($item->target) . '" rel="noopener"' : '';
                                ?>
                                    <li class="header__nav-item<?php echo $has_mega ? ' header__nav-item--mega' : ''; ?>">
                                        <?php if ($has_mega) : ?>
                                            <button
                                                type="button"
                                                class="header__nav-link header-item"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                aria-controls="<?php echo esc_attr('mega-' . $item->ID); ?>"
                                            ><?php echo esc_html($item->title); ?></button>
                                        <?php else : ?>
                                            <a class="header__nav-link header-item" href="<?php echo esc_url($item->url); ?>"<?php echo $item_target; ?>>
                                                <?php echo esc_html($item->title); ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                    <div class="header__actions">
                        <button type="button" class="header__icon-btn header__search-toggle" aria-expanded="false" aria-controls="header-search">
                            <img class="svg" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/search.svg')); ?>" alt="">
                            <span class="visually-hidden"><?php esc_html_e('Search', 'wheellab'); ?></span>
                        </button>

                        <?php if (!empty($header_cta['url'])) : ?>
                            <a
                                class="header__cta"
                                href="<?php echo esc_url($header_cta['url']); ?>"
                                <?php echo !empty($header_cta['target']) ? 'target="' . esc_attr($header_cta['target']) . '" rel="noopener"' : ''; ?>
                            >
                                <span class="header__cta-inner button-text-m"><?php echo esc_html($header_cta['title'] ?: __('Book a Call', 'wheellab')); ?></span>
                            </a>
                        <?php endif; ?>

                        <button type="button" class="header__icon-btn header__menu-toggle" aria-expanded="false" aria-controls="header-mobile-panel">
                            <img class="svg header__menu-toggle-icon header__menu-toggle-icon--open" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/menu.svg')); ?>" alt="">
                            <img class="svg header__menu-toggle-icon header__menu-toggle-icon--close" src="<?php echo esc_url(wheellab_asset_url('assets/img/icons/close.svg')); ?>" alt="">
                            <span class="visually-hidden"><?php esc_html_e('Menu', 'wheellab'); ?></span>
                        </button>
                    </div>

                </div>

                <?php foreach ($primary_items as $item) :
                    $categories = wheellab_get_menu_item_mega_menu($item->ID);
                    if (!$categories) continue;
                    $mega_id = 'mega-' . $item->ID;
                ?>
                    <div class="header__mega" id="<?php echo esc_attr($mega_id); ?>" hidden>
                        <div class="header__mega-tabs" role="tablist" aria-orientation="vertical">
                            <?php foreach ($categories as $ci => $category) :
                                $tab_id   = 'mega-tab-' . $item->ID . '-' . $ci;
                                $panel_id = 'mega-panel-' . $item->ID . '-' . $ci;
                            ?>
                                <button
                                    type="button"
                                    class="header__mega-tab<?php echo $ci === 0 ? ' is-active' : ''; ?>"
                                    role="tab"
                                    id="<?php echo esc_attr($tab_id); ?>"
                                    aria-controls="<?php echo esc_attr($panel_id); ?>"
                                    aria-selected="<?php echo $ci === 0 ? 'true' : 'false'; ?>"
                                    tabindex="<?php echo $ci === 0 ? '0' : '-1'; ?>"
                                ><?php echo esc_html($category['name']); ?></button>
                            <?php endforeach; ?>
                        </div>

                        <div class="header__mega-panels">
                            <?php foreach ($categories as $ci => $category) :
                                $tab_id   = 'mega-tab-' . $item->ID . '-' . $ci;
                                $panel_id = 'mega-panel-' . $item->ID . '-' . $ci;
                                $cards    = $category['cards'] ?: [];
                            ?>
                                <div
                                    class="header__mega-panel<?php echo $ci === 0 ? ' is-active' : ''; ?>"
                                    role="tabpanel"
                                    id="<?php echo esc_attr($panel_id); ?>"
                                    aria-labelledby="<?php echo esc_attr($tab_id); ?>"
                                    <?php echo $ci === 0 ? '' : 'hidden'; ?>
                                >
                                    <?php foreach ($cards as $card) :
                                        $card_link = $card['link'] ?: [];
                                        $card_url  = $card_link['url'] ?? '';
                                        $card_tag  = $card_url ? 'a' : 'div';
                                    ?>
                                        <<?php echo $card_tag; ?>
                                            class="header__mega-card"
                                            <?php echo $card_url ? 'href="' . esc_url($card_url) . '"' : ''; ?>
                                            <?php echo !empty($card_link['target']) ? 'target="' . esc_attr($card_link['target']) . '" rel="noopener"' : ''; ?>
                                        >
                                            <?php if (!empty($card['icon']['url'])) :
                                                $icon_svg = wheellab_inline_svg((int) ($card['icon']['ID'] ?? 0));
                                            ?>
                                                <span class="header__mega-card-icon">
                                                    <?php if ($icon_svg) : ?>
                                                        <?php echo $icon_svg; ?>
                                                    <?php else : ?>
                                                        <img
                                                            src="<?php echo esc_url($card['icon']['url']); ?>"
                                                            alt=""
                                                            width="28"
                                                            height="28"
                                                            loading="lazy"
                                                        >
                                                    <?php endif; ?>
                                                </span>
                                            <?php endif; ?>
                                            <span class="header__mega-card-text">
                                                <span class="header__mega-card-title"><?php echo esc_html($card['title']); ?></span>
                                                <?php if (!empty($card['description'])) : ?>
                                                    <span class="header__mega-card-desc"><?php echo esc_html($card['description']); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </<?php echo $card_tag; ?>>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="header__search" id="header-search" hidden>
                    <?php get_search_form(); ?>
                </div>
            </div>

            <?php if ($primary_items) : ?>
                <div class="header__mobile-panel" id="header-mobile-panel" hidden>
                    <div class="header__mobile-panel-inner">
                        <nav aria-label="<?php esc_attr_e('Primary', 'wheellab'); ?>">
                            <ul class="header__accordion">
                                <?php foreach ($primary_items as $item) :
                                    $categories  = wheellab_get_menu_item_mega_menu($item->ID);
                                    $has_mega    = (bool) $categories;
                                    $item_target = $item->target ? ' target="' . esc_attr($item->target) . '" rel="noopener"' : '';
                                    $sub_id      = 'mobile-sub-' . $item->ID;
                                ?>
                                    <li class="header__accordion-item">
                                        <?php if ($has_mega) : ?>
                                            <button
                                                type="button"
                                                class="header__accordion-trigger"
                                                aria-expanded="false"
                                                aria-controls="<?php echo esc_attr($sub_id); ?>"
                                            >
                                                <?php echo esc_html($item->title); ?>
                                            </button>

                                            <div class="header__accordion-panel" id="<?php echo esc_attr($sub_id); ?>" hidden>
                                                <ul class="header__accordion-sub">
                                                    <?php foreach ($categories as $ci => $category) :
                                                        $cat_id = 'mobile-cat-' . $item->ID . '-' . $ci;
                                                        $cards  = $category['cards'] ?: [];
                                                    ?>
                                                        <li class="header__accordion-subitem">
                                                            <button
                                                                type="button"
                                                                class="header__accordion-subtrigger"
                                                                aria-expanded="false"
                                                                aria-controls="<?php echo esc_attr($cat_id); ?>"
                                                            ><?php echo esc_html($category['name']); ?></button>

                                                            <div class="header__accordion-cards" id="<?php echo esc_attr($cat_id); ?>" hidden>
                                                                <?php foreach ($cards as $card) :
                                                                    $card_link = $card['link'] ?: [];
                                                                    $card_url  = $card_link['url'] ?? '';
                                                                    $card_tag  = $card_url ? 'a' : 'div';
                                                                ?>
                                                                    <<?php echo $card_tag; ?>
                                                                        class="header__mega-card"
                                                                        <?php echo $card_url ? 'href="' . esc_url($card_url) . '"' : ''; ?>
                                                                        <?php echo !empty($card_link['target']) ? 'target="' . esc_attr($card_link['target']) . '" rel="noopener"' : ''; ?>
                                                                    >
                                                                        <?php if (!empty($card['icon']['url'])) :
                                                                            $icon_svg = wheellab_inline_svg((int) ($card['icon']['ID'] ?? 0));
                                                                        ?>
                                                                            <span class="header__mega-card-icon">
                                                                                <?php if ($icon_svg) : ?>
                                                                                    <?php echo $icon_svg; ?>
                                                                                <?php else : ?>
                                                                                    <img
                                                                                        src="<?php echo esc_url($card['icon']['url']); ?>"
                                                                                        alt=""
                                                                                        width="28"
                                                                                        height="28"
                                                                                        loading="lazy"
                                                                                    >
                                                                                <?php endif; ?>
                                                                            </span>
                                                                        <?php endif; ?>
                                                                        <span class="header__mega-card-text">
                                                                            <span class="header__mega-card-title"><?php echo esc_html($card['title']); ?></span>
                                                                            <?php if (!empty($card['description'])) : ?>
                                                                                <span class="header__mega-card-desc"><?php echo esc_html($card['description']); ?></span>
                                                                            <?php endif; ?>
                                                                        </span>
                                                                    </<?php echo $card_tag; ?>>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php else : ?>
                                            <a class="header__accordion-link" href="<?php echo esc_url($item->url); ?>"<?php echo $item_target; ?>>
                                                <?php echo esc_html($item->title); ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        </header><!-- header -->
        <main id="main">