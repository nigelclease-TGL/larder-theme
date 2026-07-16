<?php
/**
 * Theme bootstrap.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function larder_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 420, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'larder' ),
			'footer'  => __( 'Footer Menu', 'larder' ),
		)
	);
}
add_action( 'after_setup_theme', 'larder_setup' );

function larder_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'larder-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'larder-style',
		get_stylesheet_uri(),
		array( 'larder-fonts' ),
		$version
	);

	wp_enqueue_style(
		'larder-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'larder-style' ),
		$version
	);

	wp_enqueue_style(
		'larder-templates',
		get_template_directory_uri() . '/assets/css/templates.css',
		array( 'larder-main' ),
		$version
	);

	wp_enqueue_style(
		'larder-pages',
		get_template_directory_uri() . '/assets/css/pages.css',
		array( 'larder-templates' ),
		$version
	);

	wp_enqueue_style(
		'larder-wprm',
		get_template_directory_uri() . '/assets/css/wp-recipe-maker.css',
		array( 'larder-pages' ),
		$version
	);

	wp_enqueue_script(
		'larder-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$version,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'larder_enqueue_assets' );
