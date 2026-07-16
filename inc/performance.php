<?php
/**
 * Small performance and content-quality enhancements.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function larder_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'larder_resource_hints', 10, 2 );

function larder_image_attributes( $attr ) {
	$attr['decoding'] = 'async';
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
	return $classes;
}
add_filter( 'body_class', 'larder_body_classes' );
