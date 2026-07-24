<?php
/**
 * Public brand identity safeguards for Nigel's Kitchen Table.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the approved public brand name.
 *
 * @return string
 */
function nkt_brand_name() {
	return "Nigel's Kitchen Table";
}

/**
 * Return the approved public-facing site description.
 *
 * @return string
 */
function nkt_brand_tagline() {
	return __( 'Seasonal recipes, beautiful bakes and practical kitchen knowledge.', 'larder' );
}

/**
 * Apply the approved WordPress identity when legacy settings are still present.
 *
 * The migration is intentionally conservative: custom values are preserved unless
 * they are blank or clearly belong to the former public brand.
 *
 * @param bool $force Whether to apply the approved values regardless of the current options.
 */
function nkt_apply_brand_identity_options( $force = false ) {
	$brand_version = 1;

	if ( ! $force && (int) get_option( 'nkt_brand_identity_version', 0 ) >= $brand_version ) {
		return;
	}

	$current_name    = trim( (string) get_option( 'blogname', '' ) );
	$current_tagline = trim( (string) get_option( 'blogdescription', '' ) );
	$legacy_names    = array(
		'The Gourmet Larder',
		'Gourmet Larder',
		'thegourmetlarder',
		'thegourmetlarder.com',
	);

	if ( $force || '' === $current_name || in_array( $current_name, $legacy_names, true ) ) {
		update_option( 'blogname', nkt_brand_name() );
	}

	if ( $force || '' === $current_tagline || false !== stripos( $current_tagline, 'gourmet larder' ) ) {
		update_option( 'blogdescription', nkt_brand_tagline() );
	}

	update_option( 'nkt_brand_identity_version', $brand_version, false );
}
add_action( 'init', 'nkt_apply_brand_identity_options', 1 );
add_action( 'after_switch_theme', 'nkt_apply_brand_identity_options' );

/**
 * Keep the browser-title site name aligned even before cached options refresh.
 *
 * @param array $parts WordPress document title parts.
 * @return array
 */
function nkt_brand_document_title_parts( $parts ) {
	if ( ! is_admin() ) {
		$parts['site'] = nkt_brand_name();
	}
	return $parts;
}
add_filter( 'document_title_parts', 'nkt_brand_document_title_parts', 20 );

/**
 * Keep Yoast's Open Graph site name aligned with the approved public brand.
 *
 * @return string
 */
function nkt_brand_yoast_site_name() {
	return nkt_brand_name();
}
add_filter( 'wpseo_opengraph_site_name', 'nkt_brand_yoast_site_name' );

/**
 * Keep Yoast WebSite schema aligned while retaining the legacy name only as an
 * alternate name for search continuity.
 *
 * @param array $data WebSite schema data.
 * @return array
 */
function nkt_brand_yoast_website_schema( $data ) {
	$data['name']          = nkt_brand_name();
	$data['alternateName'] = 'The Gourmet Larder';
	return $data;
}
add_filter( 'wpseo_schema_website', 'nkt_brand_yoast_website_schema' );
