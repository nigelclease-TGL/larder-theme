<?php
/**
 * Privacy-conscious business growth helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the configured lead magnet details.
 * The block remains hidden until a destination URL is entered.
 *
 * @return array{title:string,copy:string,url:string,label:string,enabled:bool}
 */
function nkt_get_lead_magnet() {
	$url = esc_url_raw( get_theme_mod( 'larder_lead_magnet_url', '' ) );

	return array(
		'title'   => (string) get_theme_mod( 'larder_lead_magnet_title', __( 'A useful guide for your kitchen', 'larder' ) ),
		'copy'    => (string) get_theme_mod( 'larder_lead_magnet_copy', __( 'Offer a practical seasonal guide, checklist or recipe collection as a thoughtful welcome gift.', 'larder' ) ),
		'url'     => $url,
		'label'   => (string) get_theme_mod( 'larder_lead_magnet_button', __( 'Get the free guide', 'larder' ) ),
		'enabled' => (bool) $url,
	);
}

/**
 * Return the configured homepage promotion details.
 * The block remains hidden until a destination URL is entered.
 *
 * @return array{eyebrow:string,title:string,copy:string,url:string,label:string,enabled:bool}
 */
function nkt_get_home_promotion() {
	$url = esc_url_raw( get_theme_mod( 'larder_promotion_url', '' ) );

	return array(
		'eyebrow' => (string) get_theme_mod( 'larder_promotion_eyebrow', __( 'From the Kitchen Table', 'larder' ) ),
		'title'   => (string) get_theme_mod( 'larder_promotion_title', __( 'Something useful for your kitchen', 'larder' ) ),
		'copy'    => (string) get_theme_mod( 'larder_promotion_copy', __( 'Use this space for a seasonal collection, digital guide, course, event or trusted partner.', 'larder' ) ),
		'url'     => $url,
		'label'   => (string) get_theme_mod( 'larder_promotion_button', __( 'Discover more', 'larder' ) ),
		'enabled' => (bool) $url,
	);
}

/**
 * Detect an existing measurement integration without loading any tracker itself.
 *
 * @return bool
 */
function nkt_has_measurement_integration() {
	return defined( 'GOOGLESITEKIT_VERSION' )
		|| defined( 'MONSTERINSIGHTS_VERSION' )
		|| defined( 'GTM4WP_VERSION' )
		|| defined( 'CAOS_VERSION' )
		|| function_exists( 'monsterinsights_get_ua' );
}

/**
 * Return a page URL only when that page exists.
 *
 * @param string[] $slugs Candidate page slugs.
 * @return string
 */
function nkt_growth_page_url( $slugs ) {
	$page = nkt_setup_find_page( $slugs );

	return $page ? (string) get_permalink( $page ) : '';
}
