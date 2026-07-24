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
 * Normalise public identity text before comparison.
 *
 * This prevents harmless differences in apostrophe style, entities or whitespace
 * from creating a false critical Site Health result.
 *
 * @param string $value Text to normalise.
 * @return string
 */
function nkt_normalize_brand_text( $value ) {
	$value = html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$value = str_replace( array( '’', '‘', '`', '´' ), "'", $value );
	$value = preg_replace( '/\s+/u', ' ', $value );
	return strtolower( trim( (string) $value ) );
}

/**
 * Apply the approved WordPress identity when legacy settings are still present.
 *
 * The migration is intentionally conservative: custom values are preserved unless
 * they are blank, still use the former public brand or use the WordPress default.
 *
 * @param bool $force Whether to apply the approved values regardless of the current options.
 */
function nkt_apply_brand_identity_options( $force = false ) {
	$brand_version = 2;

	if ( ! $force && (int) get_option( 'nkt_brand_identity_version', 0 ) >= $brand_version ) {
		return;
	}

	$current_name       = trim( (string) get_option( 'blogname', '' ) );
	$current_tagline    = trim( (string) get_option( 'blogdescription', '' ) );
	$normalised_tagline = nkt_normalize_brand_text( $current_tagline );
	$legacy_names       = array(
		'The Gourmet Larder',
		'Gourmet Larder',
		'thegourmetlarder',
		'thegourmetlarder.com',
	);

	if ( $force || '' === $current_name || in_array( $current_name, $legacy_names, true ) ) {
		update_option( 'blogname', nkt_brand_name() );
	}

	if ( $force || '' === $current_tagline || false !== stripos( $current_tagline, 'gourmet larder' ) || 'just another wordpress site' === $normalised_tagline ) {
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

/**
 * Confirm the core WordPress identity uses the approved public brand.
 *
 * The site title must match Nigel's Kitchen Table after normalisation. The tagline
 * may be edited by the site owner, provided it is present and does not use legacy
 * or default WordPress wording.
 *
 * @return bool
 */
function nkt_brand_identity_is_ready() {
	$current_name    = nkt_normalize_brand_text( get_option( 'blogname', '' ) );
	$approved_name   = nkt_normalize_brand_text( nkt_brand_name() );
	$current_tagline = nkt_normalize_brand_text( get_option( 'blogdescription', '' ) );

	return $approved_name === $current_name
		&& '' !== $current_tagline
		&& 'just another wordpress site' !== $current_tagline
		&& false === strpos( $current_tagline, 'gourmet larder' );
}

/**
 * Report the public brand identity in WordPress Site Health.
 *
 * @return array
 */
function nkt_brand_site_health_test() {
	$ready = nkt_brand_identity_is_ready();

	return array(
		'label'       => $ready ? __( 'The public website identity is fully branded', 'larder' ) : __( 'Align the public website identity', 'larder' ),
		'status'      => $ready ? 'good' : 'critical',
		'badge'       => array(
			'label' => __( "Nigel's Kitchen Table", 'larder' ),
			'color' => 'blue',
		),
		'description' => '<p>' . esc_html( $ready ? __( 'The WordPress site title, tagline, browser titles and Yoast site name use the approved public identity.', 'larder' ) : __( 'The WordPress site title is not Nigel’s Kitchen Table, or the tagline is blank, uses the former brand or still uses the WordPress default.', 'larder' ) ) . '</p>',
		'actions'     => $ready ? '' : '<p><a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Open General Settings', 'larder' ) . '</a></p>',
		'test'        => 'nkt_public_brand_identity',
	);
}

/**
 * Register the public brand test with Site Health.
 *
 * @param array $tests Existing Site Health tests.
 * @return array
 */
function nkt_register_brand_site_health_test( $tests ) {
	$tests['direct']['nkt_public_brand_identity'] = array(
		'label' => __( 'Public brand identity', 'larder' ),
		'test'  => 'nkt_brand_site_health_test',
	);
	return $tests;
}
add_filter( 'site_status_tests', 'nkt_register_brand_site_health_test' );
