<?php

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

function wheellab_get_menu_item_mega_menu( int $menu_item_id ): array {
    if ( ! function_exists( 'get_field' ) || ! get_field( 'has_mega_menu', $menu_item_id ) ) {
        return [];
    }

    return get_field( 'mega_menu_categories', $menu_item_id ) ?: [];
}

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

    $svg = (string) preg_replace( '#<script\b[^>]*>.*?</script>#is', '', $svg );
    $svg = (string) preg_replace( '/\son\w+="[^"]*"/i', '', $svg );

    $svg = (string) preg_replace( '/\bfill="(?!none")[^"]*"/i', 'fill="currentColor"', $svg );
    $svg = (string) preg_replace( '/\bstroke="(?!none")[^"]*"/i', 'stroke="currentColor"', $svg );

    if ( $class ) {
        $svg = (string) preg_replace( '/<svg\s/', '<svg class="' . esc_attr( $class ) . '" ', $svg, 1 );
    }

    return $svg;
}

function wheellab_add_heading_anchors_and_toc( string $content ): array {
    if ( trim( $content ) === '' || stripos( $content, '<h2' ) === false ) {
        return [ $content, [] ];
    }

    $dom = new DOMDocument();
    $prev_errors = libxml_use_internal_errors( true );

    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();
    libxml_use_internal_errors( $prev_errors );

    $toc  = [];
    $used = [];

    $headings = iterator_to_array( $dom->getElementsByTagName( 'h2' ) );

    foreach ( $headings as $node ) {
        $text = trim( $node->textContent );
        if ( $text === '' ) {
            continue;
        }

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

function wheellab_reading_time( int $post_id = 0 ): int {
    $post_id = $post_id ?: get_the_ID();
    $text    = wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) );
    $words   = str_word_count( $text );
    return max( 1, (int) ceil( $words / 200 ) );
}

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

    if ( function_exists('get_field') && get_field('disable_jquery','option') ) {
        add_action('wp_enqueue_scripts', function () {
            if ( !is_admin() ) {
                wp_deregister_script('jquery');
            }
        }, 100);
    }
}, 20);

add_filter('acf/fields/relationship/query/key=field_spr_manual', function ($args, $field, $post_id) {
    $args['post__not_in'] = [(int) $post_id];
    return $args;
}, 10, 3);

function wheellab_parse_video_timestamp($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return 0;
    }
    if (strpos($value, ':') === false) {
        return max(0, (int) $value);
    }
    $parts = array_map('intval', explode(':', $value));
    $seconds = 0;
    foreach ($parts as $part) {
        $seconds = $seconds * 60 + $part;
    }
    return max(0, $seconds);
}

function wheellab_extract_youtube_id($url) {
    if (preg_match('#(?:youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m)) {
        return $m[1];
    }
    return '';
}

function wheellab_extract_vimeo_id($url) {
    if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
        return $m[1];
    }
    return '';
}

// Shared markup for the "Statistics" component (Figma nodes 806:11701 /
// 806:11974) — used by both service_stats_section.php (standalone
// block) and, when its own stats field is filled in, by
// service_comparison_section.php (rendered under the comparison
// slider). Styles live in src/scss/partials/_stats_banner.scss,
// @use'd by both blocks' own section stylesheets.
function wheellab_render_stats_banner($stats) {
    if (!$stats) {
        return;
    }
    ?>
    <div class="stats-banner">
        <div class="stats-banner__inner">
            <div class="stats-banner__glow stats-banner__glow--a" aria-hidden="true"></div>
            <div class="stats-banner__glow stats-banner__glow--b" aria-hidden="true"></div>

            <?php foreach ($stats as $stat) :
                $value       = $stat['value']       ?? '';
                $label       = $stat['label']       ?? '';
                $description = $stat['description'] ?? '';
                if (!$value) continue;
            ?>
                <div class="stats-banner__item">
                    <div class="stats-banner__heading">
                        <p class="stats-banner__value"><?php echo esc_html($value); ?></p>
                        <?php if ($label) : ?>
                            <p class="stats-banner__label"><?php echo esc_html($label); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($description) : ?>
                        <p class="stats-banner__description body-m"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
