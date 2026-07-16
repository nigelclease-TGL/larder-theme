<?php
/**
 * Navigation helpers and safe menu fallbacks.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Find the first published page matching a list of likely slugs.
 *
 * @param array  $slugs Candidate slugs.
 * @param string $fallback Fallback path.
 * @return string
 */
function nkt_page_url( $slugs, $fallback ) {
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
			return get_permalink( $page );
		}
	}

	return home_url( $fallback );
}

/**
 * Main navigation shown until a WordPress menu is assigned.
 */
function nkt_primary_menu_fallback() {
	$items = array(
		__( 'Recipes', 'larder' )      => nkt_page_url( array( 'recipes' ), '/recipes/' ),
		__( 'Collections', 'larder' )  => nkt_page_url( array( 'collections', 'seasons' ), '/collections/' ),
		__( 'Kitchen Notes', 'larder' ) => nkt_page_url( array( 'kitchen-notes', 'baking-guides' ), '/kitchen-notes/' ),
		__( 'About Nigel', 'larder' )  => nkt_page_url( array( 'about', 'my-story' ), '/about/' ),
		__( 'Contact', 'larder' )      => nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' ),
	);

	echo '<ul class="primary-menu primary-menu--fallback">';
	foreach ( $items as $label => $url ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * Footer navigation shown until a footer menu is assigned.
 */
function nkt_footer_menu_fallback() {
	$items = array(
		__( 'Recipes', 'larder' )     => nkt_page_url( array( 'recipes' ), '/recipes/' ),
		__( 'About Nigel', 'larder' ) => nkt_page_url( array( 'about', 'my-story' ), '/about/' ),
		__( 'Contact', 'larder' )     => nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' ),
		__( 'Newsletter', 'larder' )  => nkt_page_url( array( 'newsletter' ), '/newsletter/' ),
	);

	echo '<ul class="footer-menu footer-menu--fallback">';
	foreach ( $items as $label => $url ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}
