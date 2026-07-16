<?php
/**
 * Recipe-focused search behaviour.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Limit public site searches to published recipe posts.
 *
 * @param WP_Query $query Main query instance.
 */
function nkt_focus_public_search_on_recipes( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', 'post' );
	$query->set( 'post_status', 'publish' );
	$query->set( 'posts_per_page', 12 );
	$query->set( 'ignore_sticky_posts', true );
}
add_action( 'pre_get_posts', 'nkt_focus_public_search_on_recipes' );

/**
 * Keep empty searches from returning every post on the site.
 *
 * @param string $search SQL search fragment.
 * @param WP_Query $query Query object.
 * @return string
 */
function nkt_prevent_empty_public_search( $search, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return $search;
	}

	if ( '' === trim( (string) $query->get( 's' ) ) ) {
		return ' AND 1=0 ';
	}

	return $search;
}
add_filter( 'posts_search', 'nkt_prevent_empty_public_search', 10, 2 );
