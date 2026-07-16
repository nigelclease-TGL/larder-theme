<?php
/**
 * Theme bootstrap.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/performance.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/monetization.php';
require_once get_template_directory() . '/inc/collections.php';

function larder_setup() {
	load_theme_textdomain( 'larder', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 520, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'larder' ),
			'footer'  => __( 'Footer Menu', 'larder' ),
		)
	);

	add_image_size( 'larder-card', 900, 675, true );
	add_image_size( 'larder-hero', 1400, 1100, true );
}
add_action( 'after_setup_theme', 'larder_setup' );

function larder_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'larder-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'larder-style', get_stylesheet_uri(), array( 'larder-fonts' ), $version );
	wp_enqueue_style( 'larder-main', get_template_directory_uri() . '/assets/css/main.css', array( 'larder-style' ), $version );
	wp_enqueue_style( 'larder-templates', get_template_directory_uri() . '/assets/css/templates.css', array( 'larder-main' ), $version );
	wp_enqueue_style( 'larder-pages', get_template_directory_uri() . '/assets/css/pages.css', array( 'larder-templates' ), $version );
	wp_enqueue_style( 'larder-wprm', get_template_directory_uri() . '/assets/css/wp-recipe-maker.css', array( 'larder-pages' ), $version );
	wp_enqueue_style( 'larder-header-tools', get_template_directory_uri() . '/assets/css/header-tools.css', array( 'larder-wprm' ), $version );
	wp_enqueue_style( 'larder-final-polish', get_template_directory_uri() . '/assets/css/final-polish.css', array( 'larder-header-tools' ), $version );
	wp_enqueue_style( 'larder-mailchimp', get_template_directory_uri() . '/assets/css/mailchimp.css', array( 'larder-final-polish' ), $version );
	wp_enqueue_style( 'nkt-brand', get_template_directory_uri() . '/assets/css/nkt-brand.css', array( 'larder-mailchimp' ), $version );
	wp_enqueue_style( 'nkt-social-share', get_template_directory_uri() . '/assets/css/social-share.css', array( 'nkt-brand' ), $version );
	wp_enqueue_style( 'nkt-home-editorial', get_template_directory_uri() . '/assets/css/home-editorial.css', array( 'nkt-social-share' ), $version );
	wp_enqueue_style( 'nkt-monetization', get_template_directory_uri() . '/assets/css/monetization.css', array( 'nkt-home-editorial' ), $version );
	wp_enqueue_style( 'nkt-collections', get_template_directory_uri() . '/assets/css/collections.css', array( 'nkt-monetization' ), $version );

	wp_enqueue_script( 'larder-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), $version, true );

	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'larder-recipe-tools', get_template_directory_uri() . '/assets/js/recipe-tools.js', array(), $version, true );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'larder_enqueue_assets' );

function larder_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Recipe Sidebar', 'larder' ),
			'id'            => 'recipe-sidebar',
			'description'   => __( 'Optional widgets shown beside recipe posts.', 'larder' ),
			'before_widget' => '<section class="recipe-sidebar__card widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Recipe Inline Advertisement', 'larder' ),
			'id'            => 'recipe-inline-ad',
			'description'   => __( 'Optional advertisement or sponsor block shown after recipe content. Leave empty until an ad network is connected.', 'larder' ),
			'before_widget' => '<div class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="screen-reader-text">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( 'Homepage Promotion', 'larder' ),
			'id'            => 'homepage-promotion',
			'description'   => __( 'Optional space for an ebook, course, sponsor or seasonal promotion.', 'larder' ),
			'before_widget' => '<section class="nkt-product widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="nkt-product__title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'larder_widgets_init' );
