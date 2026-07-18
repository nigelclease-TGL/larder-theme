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
require_once get_template_directory() . '/inc/navigation.php';
require_once get_template_directory() . '/inc/template-tags.php';
require_once get_template_directory() . '/inc/search.php';
require_once get_template_directory() . '/inc/discovery.php';
require_once get_template_directory() . '/inc/setup-wizard.php';
require_once get_template_directory() . '/inc/site-health.php';

function larder_setup() {
	load_theme_textdomain( 'larder', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'custom-logo', array( 'height' => 132, 'width' => 520, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	add_editor_style(
		array(
			'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Instrument+Serif:ital@0;1&family=Source+Sans+3:wght@400;500;600;700&display=swap',
			'assets/css/editor.css',
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'larder' ),
			'footer'  => __( 'Footer Menu', 'larder' ),
		)
	);

	add_image_size( 'larder-card', 640, 800, true );
	add_image_size( 'larder-hero', 1000, 1250, true );
}
add_action( 'after_setup_theme', 'larder_setup' );

function larder_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'larder-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Instrument+Serif:ital@0;1&family=Source+Sans+3:wght@400;500;600;700&display=swap',
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
	wp_enqueue_style( 'nkt-editorial-content', get_template_directory_uri() . '/assets/css/editorial-content.css', array( 'nkt-collections' ), $version );
	wp_enqueue_style( 'nkt-about-contact', get_template_directory_uri() . '/assets/css/about-contact.css', array( 'nkt-editorial-content' ), $version );
	wp_enqueue_style( 'nkt-recipes-hub', get_template_directory_uri() . '/assets/css/recipes-hub.css', array( 'nkt-about-contact' ), $version );
	wp_enqueue_style( 'nkt-home-finishing', get_template_directory_uri() . '/assets/css/home-finishing.css', array( 'nkt-recipes-hub' ), $version );
	wp_enqueue_style( 'nkt-recipe-experience', get_template_directory_uri() . '/assets/css/recipe-experience.css', array( 'nkt-home-finishing' ), $version );

	$brand_dependency = 'nkt-recipe-experience';

	if ( is_singular( 'post' ) ) {
		wp_enqueue_style( 'nkt-recipe-brand-template', get_template_directory_uri() . '/assets/css/recipe-brand-template.css', array( 'nkt-recipe-experience' ), $version );
		wp_enqueue_style( 'nkt-recipe-phase-3', get_template_directory_uri() . '/assets/css/recipe-phase-3.css', array( 'nkt-recipe-brand-template' ), $version );
		wp_enqueue_style( 'nkt-recipe-phase-3-guide', get_template_directory_uri() . '/assets/css/recipe-phase-3-guide.css', array( 'nkt-recipe-phase-3' ), $version );
		$brand_dependency = 'nkt-recipe-phase-3-guide';
	}

	wp_enqueue_style( 'nkt-brand-system', get_template_directory_uri() . '/assets/css/brand-system.css', array( $brand_dependency ), $version );
	wp_enqueue_style( 'nkt-brand-v2', get_template_directory_uri() . '/assets/css/brand-v2.css', array( 'nkt-brand-system' ), $version );
	wp_enqueue_style( 'nkt-home-phase-2', get_template_directory_uri() . '/assets/css/home-phase-2.css', array( 'nkt-brand-v2' ), $version );
	wp_enqueue_style( 'nkt-home-phase-2-finish', get_template_directory_uri() . '/assets/css/home-phase-2-finish.css', array( 'nkt-home-phase-2' ), $version );
	wp_enqueue_style( 'nkt-home-phase-2-final', get_template_directory_uri() . '/assets/css/home-phase-2-final.css', array( 'nkt-home-phase-2-finish' ), $version );
	wp_enqueue_style( 'nkt-discovery-phase-4', get_template_directory_uri() . '/assets/css/discovery-phase-4.css', array( 'nkt-home-phase-2-final' ), $version );
	wp_enqueue_style( 'nkt-site-phase-4', get_template_directory_uri() . '/assets/css/site-phase-4.css', array( 'nkt-discovery-phase-4' ), $version );
	wp_enqueue_style( 'nkt-launch-phase-5', get_template_directory_uri() . '/assets/css/launch-phase-5.css', array( 'nkt-site-phase-4' ), $version );

	wp_enqueue_script(
		'larder-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	if ( is_singular( 'post' ) ) {
		wp_enqueue_script(
			'larder-recipe-tools',
			get_template_directory_uri() . '/assets/js/recipe-tools.js',
			array(),
			$version,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'larder_enqueue_assets' );

/**
 * Use the bundled monogram until a WordPress Site Icon is selected.
 */
function nkt_fallback_site_icon() {
	if ( has_site_icon() ) {
		return;
	}

	$icon_url = get_template_directory_uri() . '/assets/images/nkt-monogram.svg';
	printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( $icon_url ) );
}
add_action( 'wp_head', 'nkt_fallback_site_icon', 2 );
add_action( 'admin_head', 'nkt_fallback_site_icon', 2 );

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
