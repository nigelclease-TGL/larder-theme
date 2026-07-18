<?php
/**
 * Small performance and content-quality enhancements.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add early connections for the hosted font files.
 *
 * @param array  $urls Resource URLs.
 * @param string $relation_type Hint type.
 * @return array
 */
function larder_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'larder_resource_hints', 10, 2 );

/**
 * Supply sensible loading attributes while keeping visible brand images eager.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function larder_image_attributes( $attr ) {
	$attr['decoding'] = 'async';
	$class            = isset( $attr['class'] ) ? (string) $attr['class'] : '';

	if ( false !== strpos( $class, 'custom-logo' ) ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
		return $attr;
	}

	if ( empty( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'larder_image_attributes' );

function larder_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'larder_excerpt_length', 99 );

function larder_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'larder_excerpt_more' );

function larder_body_classes( $classes ) {
	if ( is_singular( 'post' ) ) {
		$classes[] = 'is-recipe-post';
	}
	if ( function_exists( 'nkt_is_staging_site' ) && nkt_is_staging_site() ) {
		$classes[] = 'is-staging-site';
	}
	return $classes;
}
add_filter( 'body_class', 'larder_body_classes' );

/**
 * Let WordPress load block styles only when their blocks are present.
 *
 * @return bool
 */
function nkt_use_separate_core_block_assets() {
	return true;
}
add_filter( 'should_load_separate_core_block_assets', 'nkt_use_separate_core_block_assets' );

/**
 * Remove legacy payloads that are not used by this public theme.
 */
function nkt_remove_legacy_head_assets() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'nkt_remove_legacy_head_assets' );

/**
 * Prevent self-pingbacks when internal links are added to a post.
 *
 * @param string[] $links Pingback URLs.
 */
function nkt_disable_self_pingbacks( &$links ) {
	$home = home_url( '/' );
	foreach ( $links as $key => $link ) {
		if ( str_starts_with( $link, $home ) ) {
			unset( $links[ $key ] );
		}
}
add_action( 'pre_ping', 'nkt_disable_self_pingbacks' );
