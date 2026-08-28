<?php
/**
 * Theme tweaks & helpers
 */

defined('ABSPATH') || exit;

add_filter('widget_text', 'do_shortcode', 11);
remove_action('wp_head', 'wp_generator');


remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_content_feed', 'wp_staticize_emoji');
remove_filter('comment_text_rss', 'wp_staticize_emoji');
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
add_filter('emoji_svg_url', '__return_false');

function wheellab_nav_active_classes( $classes, $item = null, $args = null ) {
    $markers = ['current-menu-item', 'current-menu-parent', 'current_page_item', 'current_page_parent', 'current-menu-ancestor'];
    $has_current = false;

    foreach ($markers as $m) {
        $key = array_search($m, $classes, true);
        if ($key !== false) {
            unset($classes[$key]);
            $has_current = true;
        }
    }
    if ($has_current && !in_array('active', $classes, true)) {
        $classes[] = 'active';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'wheellab_nav_active_classes', 10, 3);
add_filter('page_css_class', 'wheellab_nav_active_classes', 10, 3);

function wheellab_attachment_fill_alt_from_title( $response ) {
    if (is_array($response) && empty($response['alt']) && !empty($response['title'])) {
        $response['alt'] = sanitize_text_field( $response['title'] );
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'wheellab_attachment_fill_alt_from_title');
add_filter('wpcf7_autop_or_not', '__return_false');

function wheellab_opengraph_namespace( $output ) {
    if (strpos($output, 'og: http://ogp.me/ns#') === false) {
        $output .= ' prefix="og: http://ogp.me/ns# fb: http://www.facebook.com/2008/fbml"';
    }
    return $output;
}
add_filter('language_attributes', 'wheellab_opengraph_namespace');

function wheellab_opengraph_meta_tags() {
    if (!is_singular()) {
        return;
    }

    global $post;
    if (!$post) {
        return;
    }

    $img_src = $img_width = $img_height = '';

    if (function_exists('get_field')) {
        $image_share = get_field('image_share', $post->ID);
    } else {
        $image_share = null;
    }

    if (!empty($image_share['url'])) {
        $img_src    = $image_share['url'];
        $img_width  = isset($image_share['width'])  ? (int)$image_share['width']  : '';
        $img_height = isset($image_share['height']) ? (int)$image_share['height'] : '';
    } elseif (has_post_thumbnail($post->ID)) {
        $img_data = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
        if ($img_data) {
            $img_src    = $img_data[0];
            $img_width  = (int)$img_data[1];
            $img_height = (int)$img_data[2];
        }
    }

    $excerpt = $post->post_content
        ? wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 55 )
        : get_bloginfo('description');

    $published = get_gmt_from_date( $post->post_date_gmt ? $post->post_date_gmt : $post->post_date, 'c' );
    $modified  = get_gmt_from_date( $post->post_modified_gmt ? $post->post_modified_gmt : $post->post_modified, 'c' );

    echo "\n<!-- Open Graph -->\n";
    echo '<meta property="og:type" content="article" />' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( get_the_title($post) ) . "\" />\n";
    echo '<meta property="og:description" content="' . esc_attr( $excerpt ) . "\" />\n";
    echo '<meta property="og:url" content="' . esc_url( get_permalink($post) ) . "\" />\n";
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo('name') ) . "\" />\n";
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . "\" />\n";
    if ($img_src) {
        echo '<meta property="og:image" content="' . esc_url( $img_src ) . "\" />\n";
        if ($img_width)  echo '<meta property="og:image:width" content="' . esc_attr( $img_width ) . "\" />\n";
        if ($img_height) echo '<meta property="og:image:height" content="' . esc_attr( $img_height ) . "\" />\n";
    }
    echo '<meta property="article:published_time" content="' . esc_attr( $published ) . "\" />\n";
    echo '<meta property="article:modified_time" content="' . esc_attr( $modified ) . "\" />\n";

    echo "\n<!-- Twitter -->\n";
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( get_the_title($post) ) . "\" />\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $excerpt ) . "\" />\n";
    echo '<meta name="twitter:url" content="' . esc_url( get_permalink($post) ) . "\" />\n";
    if ($img_src) {
        echo '<meta name="twitter:image" content="' . esc_url( $img_src ) . "\" />\n";
    }
}
add_action('wp_head', 'wheellab_opengraph_meta_tags', 5);

function wheellab_custom_body_classes( $classes ) {
    if ((is_archive() || is_author() || is_category() || is_home() || is_tag()) && get_post_type() === 'post') {
        // $classes[] = 'is-blog';
    }
    if (is_post_type_archive('project') || is_tax('project_category') || is_singular('project')) {
        $classes[] = 'color-theme-black';
    }
    return $classes;
}
add_filter('body_class', 'wheellab_custom_body_classes');

function wheellab_get_template_page_id( $template_name = '' ) {
    if (!$template_name) return 0;

    $pages = get_posts([
        'post_type'   => 'page',
        'meta_key'    => '_wp_page_template',
        'meta_value'  => $template_name,
        'numberposts' => 1,
        'fields'      => 'ids',
        'no_found_rows' => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'suppress_filters' => true,
    ]);
    return !empty($pages[0]) ? (int)$pages[0] : 0;
}

function wheellab_allow_svg_upload_mimes( $mimes ) {
    if ( current_user_can('manage_options') ) {
        $mimes['svg'] = 'image/svg+xml';
    }
    return $mimes;
}
add_filter('upload_mimes', 'wheellab_allow_svg_upload_mimes');


function wheellab_login_logo_css() {
    $url = get_stylesheet_directory_uri() . '/assets/img/login-logo.jpg';
    echo '<style>
        #login h1 a {
            background-image: url("' . esc_url($url) . '");
            background-size: contain;
            width: 100%;
        }
    </style>';
}
add_action('login_head', 'wheellab_login_logo_css');
add_filter('jpeg_quality', function($arg){ return 100; });
add_filter('big_image_size_threshold', '__return_false');


add_filter( 'rank_math/frontend/breadcrumb/args', function( $args ) {
  $args['delimiter']   = '&nbsp;/&nbsp;';
  $args['wrap_before'] = '<nav class="rank-math-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';
  $args['wrap_after']  = '</nav>';
  $args['before']      = '<span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
  $args['after']       = '</span>';
  return $args;
});

add_filter( 'rank_math/frontend/breadcrumb/html', function( $html, $crumbs, $class ) {
    ob_start(); ?>
    <nav class="mkdf-container-inner breadcrumbs rank-math-breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <?php
        $pos = 0;
        $last = count($crumbs);
        foreach ($crumbs as $crumb) {
            $pos++;
            if ($pos === $last) { ?>
                <span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span class="last" itemprop="name"><?php echo esc_html($crumb[0]); ?></span>
                    <meta itemprop="position" content="<?php echo esc_attr($pos); ?>" />
                </span>
            <?php } else { ?>
                <span itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo esc_url($crumb[1]); ?>">
                        <span itemprop="name"><?php echo esc_html($crumb[0]); ?></span>
                    </a>
                    <meta itemprop="position" content="<?php echo esc_attr($pos); ?>" />
                </span>
                <span class="separator"> / </span>
            <?php }
        } ?>
    </nav>
    <?php
    return ob_get_clean();
}, 10, 3);

function wheellab_is_blog() {
    return ( is_archive() || is_author() || is_category() || is_home() || is_tag() ) && get_post_type() === 'post';
}

/**
 * Top-level items of the "primary" nav menu, in menu order.
 * Sub-menus (Appearance > Menus parent/child) are ignored on purpose —
 * the "Services" mega-menu is driven entirely by ACF fields on the menu
 * item itself (see acf-json/group_menu_item_mega_menu.json), not by
 * nested WP menu items.
 *
 * @return WP_Post[]
 */
function wheellab_get_primary_menu_items(): array {
    $locations = get_nav_menu_locations();
    if ( empty( $locations['primary'] ) ) {
        return [];
    }

    $items = wp_get_nav_menu_items( $locations['primary'] );
    if ( ! $items ) {
        return [];
    }

    $items = array_filter( $items, static fn( $item ) => (int) $item->menu_item_parent === 0 );
    usort( $items, static fn( $a, $b ) => (int) $a->menu_order <=> (int) $b->menu_order );

    return array_values( $items );
}

/**
 * Mega-menu categories/cards attached to a primary nav menu item via ACF
 * (location: Menu Item). Returns [] if the toggle is off or no categories
 * are configured.
 *
 * @return array<int, array{name: string, cards: array}>
 */
function wheellab_get_menu_item_mega_menu( int $menu_item_id ): array {
    if ( ! function_exists( 'get_field' ) || ! get_field( 'has_mega_menu', $menu_item_id ) ) {
        return [];
    }

    return get_field( 'mega_menu_categories', $menu_item_id ) ?: [];
}

/**
 * Inline an uploaded SVG attachment as markup, with every fill/stroke
 * normalized to `currentColor` so it always renders in a single,
 * CSS-controllable color — regardless of what color the uploaded file
 * itself was exported with (e.g. mega-menu card icons, which admins can
 * upload in any color, but must always read as a plain white glyph on
 * their category-tinted chip).
 *
 * SVG uploads are already gated to `manage_options` users in
 * wheellab_allow_svg_upload_mimes(), but a light strip of <script> tags
 * and inline event handlers is applied anyway as defense in depth before
 * this unescaped markup is echoed.
 *
 * Returns '' (and the caller should fall back to a plain <img>) when the
 * attachment isn't an SVG, doesn't exist, or fails to parse — e.g. an
 * admin uploaded a PNG/JPG icon instead.
 */
function wheellab_inline_svg( int $attachment_id, string $class = '' ): string {
    if ( ! $attachment_id || get_post_mime_type( $attachment_id ) !== 'image/svg+xml' ) {
        return '';
    }

    $path = get_attached_file( $attachment_id );
    if ( ! $path || ! file_exists( $path ) ) {
        return '';
    }

    $svg = file_get_contents( $path );
    if ( ! $svg || stripos( $svg, '<svg' ) === false ) {
        return '';
    }

    // Defense in depth: strip <script> blocks and on*="" event handlers.
    $svg = (string) preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $svg );
    $svg = (string) preg_replace( '/\son\w+="[^"]*"/i', '', $svg );

    // Force a single color so it always reads clearly on the icon chip,
    // whatever color the source file happened to use.
    $svg = (string) preg_replace( '/\bfill="(?!none")[^"]*"/i', 'fill="currentColor"', $svg );
    $svg = (string) preg_replace( '/\bstroke="(?!none")[^"]*"/i', 'stroke="currentColor"', $svg );

    if ( $class ) {
        $svg = (string) preg_replace( '/<svg\s/', '<svg class="' . esc_attr( $class ) . '" ', $svg, 1 );
    }

    return $svg;
}

/**
 * Injects id="" anchors into every <h2> in already-rendered post HTML
 * (skipping any that already have one) and returns both the modified
 * markup and a flat list describing them — single source of truth for
 * both, so the sidebar Table of Contents (template-parts/sections/
 * single_post_toc.php) can never link to a heading that doesn't
 * actually carry that id. Only h2 is considered, per the TOC's Figma
 * spec (node 527:29045) — h3+ aren't included.
 *
 * @return array{0: string, 1: array<int, array{text: string, anchor: string}>}
 */
function wheellab_add_heading_anchors_and_toc( string $content ): array {
    if ( trim( $content ) === '' || stripos( $content, '<h2' ) === false ) {
        return [ $content, [] ];
    }

    $dom = new DOMDocument();
    $prev_errors = libxml_use_internal_errors( true );
    // NOIMPLIED/NODEFDTD: load the fragment as-is, without DOMDocument
    // wrapping it in an implied <html><body> (which saveHTML() would
    // then also emit back out).
    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $prev_errors );

    $toc  = [];
    $used = [];

    // Snapshot into a plain array first — getElementsByTagName() returns
    // a live list, and mutating attributes on nodes while iterating a
    // live NodeList is a common source of skipped items.
    $headings = iterator_to_array( $dom->getElementsByTagName( 'h2' ) );

    foreach ( $headings as $node ) {
        $text = trim( $node->textContent );
        if ( $text === '' ) {
            continue;
        }

        // Skip h2s that belong to an embedded ACF block (Reviews, FAQ,
        // etc.) rather than the article's own outline — every block in
        // this theme renders as <section class="...">, unlike a plain
        // core/heading block's bare <h2>, so an ancestor <section> is a
        // reliable signal either way.
        $ancestor    = $node->parentNode;
        $inside_block = false;
        while ( $ancestor ) {
            if ( $ancestor instanceof DOMElement && strtolower( $ancestor->tagName ) === 'section' ) {
                $inside_block = true;
                break;
            }
            $ancestor = $ancestor->parentNode;
        }
        if ( $inside_block ) {
            continue;
        }

        $anchor = $node->getAttribute( 'id' );
        if ( $anchor === '' ) {
            $anchor = sanitize_title( $text ) ?: 'section';
            $base   = $anchor;
            $i      = 2;
            while ( isset( $used[ $anchor ] ) ) {
                $anchor = $base . '-' . $i++;
            }
            $node->setAttribute( 'id', $anchor );
        }
        $used[ $anchor ] = true;

        $toc[] = [ 'text' => $text, 'anchor' => $anchor ];
    }

    $html = '';
    foreach ( $dom->childNodes as $child ) {
        $html .= $dom->saveHTML( $child );
    }

    return [ $html, $toc ];
}

/**
 * Estimated reading time in whole minutes (minimum 1) — WordPress has no
 * built-in equivalent. ~200 words/minute, a commonly used average
 * reading speed. Used by the single post hero (node 527:28732, "6 min").
 */
function wheellab_reading_time( int $post_id = 0 ): int {
    $post_id = $post_id ?: get_the_ID();
    $text    = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
    $words   = str_word_count( $text );
    return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Simple post view counter stored as post meta. Not unique-visitor
 * aware (a refresh or a bot both count) — deliberately basic, matching
 * the "963 views" stat in the single post hero rather than building
 * out real analytics. Not hooked automatically: call
 * wheellab_track_post_view() once per page load from single.php only,
 * so admin screens, AJAX and REST requests never increment it.
 */
function wheellab_get_post_views( int $post_id = 0 ): int {
    $post_id = $post_id ?: get_the_ID();
    return (int) get_post_meta( $post_id, 'wheellab_views', true );
}

function wheellab_track_post_view( int $post_id = 0 ): void {
    if ( is_admin() || wp_doing_ajax() || wp_is_json_request() ) {
        return;
    }
    $post_id = $post_id ?: get_the_ID();
    if ( ! $post_id ) {
        return;
    }
    update_post_meta( $post_id, 'wheellab_views', wheellab_get_post_views( $post_id ) + 1 );
}

add_action('init', function(){

    // Disable Comments
    if ( function_exists('get_field') && get_field('disable_comments','option') ) {

        add_action('admin_init', function () {
            global $pagenow;
            if ($pagenow === 'edit-comments.php') {
                wp_safe_redirect( admin_url() );
                exit;
            }
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

            foreach ( get_post_types() as $post_type ) {
                if ( post_type_supports($post_type, 'comments') ) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        });

        add_filter('comments_open', '__return_false', 20, 2);
        add_filter('pings_open', '__return_false', 20, 2);
        add_filter('comments_array', '__return_empty_array', 10, 2);

        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });

        add_action('init', function () {
            if ( is_admin_bar_showing() ) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        });

        add_action('admin_menu', function () {
            remove_submenu_page( 'options-general.php', 'options-discussion.php' );
        }, 999);
    }

    // Hide default widgets
    if ( function_exists('get_field') && get_field('disable_widgets','option') ) {

        add_action('wp_dashboard_setup', function () {
            global $wp_meta_boxes;
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
            unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_php_nag']);
        });

        remove_action('try_gutenberg_panel', 'wp_try_gutenberg_panel');

        add_action('wp_dashboard_setup', function () {
            remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'side' );
        });

        add_action('wp_dashboard_setup', function () {
            remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal' );
        });
    } else {
        add_action( 'widgets_init', function () {
            register_sidebar([
                'name'          => esc_html__( 'Sidebar', 'wheellab' ),
                'id'            => 'sidebar-1',
                'description'   => esc_html__( 'Add widgets here.', 'wheellab' ),
                'before_widget' => '<section id="%1$s" class="widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<h2 class="widget-title">',
                'after_title'   => '</h2>',
            ]);
        });
    }

    // Disable jQuery
    if ( function_exists('get_field') && get_field('disable_jquery','option') ) {
        add_action('wp_enqueue_scripts', function () {
            if ( !is_admin() ) {
                wp_deregister_script('jquery');
            }
        }, 100);
    }
}, 20);

// A post can't be manually related to itself in the "Read More" section's
// relationship field (acf-json/group_single_post_related.json) — $post_id
// here is the post the field group is attached to (the one being edited),
// not the field's stored value.
add_filter('acf/fields/relationship/query/key=field_spr_manual', function ($args, $field, $post_id) {
    $args['post__not_in'] = [(int) $post_id];
    return $args;
}, 10, 3);
