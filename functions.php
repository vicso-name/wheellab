<?php

/**
 * wheellab functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wheellab
*/

const WHEELLAB_VERSION = '1.0.0';

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function wheellab_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on wheellab, use a find and replace
		* to change 'wheellab' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'wheellab', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'wheellab' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Required for the 'full' alignment option (and its data-align="full"
	// attribute) to be available at all — without it, WP's block editor
	// never applies the align-full/align-wide handling to any block,
	// custom or core, regardless of what a block's own 'supports'
	// declare. All of this theme's page-section blocks (inc/acf_blocks.php)
	// are always full-bleed by design and need this on the FRONTEND, not
	// just in the editor — removing it (2026-08-25's ACF Blocks v3
	// migration did, on the mistaken assumption it was only an editor-
	// canvas-preview-sizing concern) silently shrank every section block
	// down to the theme's normal (non-full-bleed) content width.
	add_theme_support('align-wide');

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'wheellab_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'wheellab_setup' );


require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/cpt_cases.php';
require_once get_template_directory() . '/inc/cpt_services.php';
require_once get_template_directory() . '/inc/acf_blocks.php';
require_once get_template_directory() . '/inc/acf_options.php';
require_once get_template_directory() . '/inc/ajax_blog.php';
require_once get_template_directory() . '/inc/ajax_mailchimp.php';
require_once get_template_directory() . '/inc/post_ratings.php';
require_once get_template_directory() . '/inc/theme_function.php';
require_once get_template_directory() . '/inc/theme_settings.php';