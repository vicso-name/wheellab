<?php

const WHEELLAB_VERSION = '1.0.0';

function wheellab_setup() {

	load_theme_textdomain( 'wheellab', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );

	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );

	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'wheellab' ),
		)
	);

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

	add_theme_support('align-wide');

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
require_once get_template_directory() . '/inc/case_template_manager.php';
require_once get_template_directory() . '/inc/cpt_services.php';
require_once get_template_directory() . '/inc/service_template_manager.php';
require_once get_template_directory() . '/inc/acf_blocks.php';
require_once get_template_directory() . '/inc/acf_options.php';
require_once get_template_directory() . '/inc/ajax_blog.php';
require_once get_template_directory() . '/inc/ajax_mailchimp.php';
require_once get_template_directory() . '/inc/post_ratings.php';
require_once get_template_directory() . '/inc/theme_function.php';
require_once get_template_directory() . '/inc/theme_settings.php';