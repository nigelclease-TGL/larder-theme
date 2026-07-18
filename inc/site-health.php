<?php
/**
 * Launch-readiness checks for WordPress Site Health.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determine whether the current URL or environment is intended for staging.
 *
 * @return bool
 */
function nkt_is_staging_site() {
	$environment = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';
	$host        = (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST );

	if ( in_array( $environment, array( 'local', 'development', 'staging' ), true ) ) {
		return true;
	}

	return str_starts_with( $host, 'staging.' )
		|| str_contains( $host, '.staging.' )
		|| str_ends_with( $host, '.test' )
		|| str_ends_with( $host, '.local' )
		|| in_array( $host, array( 'localhost', '127.0.0.1' ), true );
}

/**
 * Build a Site Health response in the expected WordPress format.
 *
 * @param string $test Test identifier.
 * @param string $label Result label.
 * @param string $status good, recommended or critical.
 * @param string $description Result explanation.
 * @param string $actions Optional actions markup.
 * @return array
 */
function nkt_site_health_result( $test, $label, $status, $description, $actions = '' ) {
	return array(
		'label'       => $label,
		'status'      => $status,
		'badge'       => array(
			'label' => __( "Nigel's Kitchen Table", 'larder' ),
			'color' => 'blue',
		),
		'description' => '<p>' . esc_html( $description ) . '</p>',
		'actions'     => $actions,
		'test'        => $test,
	);
}

/**
 * Check the theme's essential page structure.
 *
 * @return array
 */
function nkt_site_health_structure() {
	$static_home = 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0;
	$pages       = array(
		nkt_setup_find_page( array( 'recipes' ) ),
		nkt_setup_find_page( array( 'recipe-collections', 'collections', 'seasons' ) ),
		nkt_setup_find_page( array( 'kitchen-notes', 'baking-guides' ) ),
		nkt_setup_find_page( array( 'about-nigel', 'my-story', 'about' ) ),
		nkt_setup_find_page( array( 'contact', 'contact-me' ) ),
	);
	$pages_ready = ! in_array( null, $pages, true );

	if ( $static_home && $pages_ready ) {
		return nkt_site_health_result(
			'nkt_theme_structure',
			__( 'The Kitchen Table page structure is ready', 'larder' ),
			'good',
			__( 'The static homepage and essential discovery pages are published.', 'larder' )
		);
	}

	return nkt_site_health_result(
		'nkt_theme_structure',
		__( 'Complete the Kitchen Table page structure', 'larder' ),
		'critical',
		__( 'The editorial homepage requires a static front page plus the Recipes, Collections, Kitchen Notes, About and Contact pages.', 'larder' ),
		'<p><a href="' . esc_url( admin_url( 'themes.php?page=nkt-setup' ) ) . '">' . esc_html__( 'Open Kitchen Table Setup', 'larder' ) . '</a></p>'
	);
}

/**
 * Check the recipe plugin needed by the recipe template.
 *
 * @return array
 */
function nkt_site_health_recipe_plugin() {
	$ready = class_exists( 'WPRM_Recipe_Manager' ) || defined( 'WPRM_VERSION' );

	if ( $ready ) {
		return nkt_site_health_result(
			'nkt_recipe_plugin',
			__( 'WP Recipe Maker is available', 'larder' ),
			'good',
			__( 'Recipe cards, print views and recipe schema can be rendered by the active plugin.', 'larder' )
		);
	}

	return nkt_site_health_result(
		'nkt_recipe_plugin',
		__( 'WP Recipe Maker is required', 'larder' ),
		'critical',
		__( 'Install and activate WP Recipe Maker before launch so existing recipe cards and structured data remain available.', 'larder' ),
		'<p><a href="' . esc_url( admin_url( 'plugin-install.php?s=WP%20Recipe%20Maker&tab=search&type=term' ) ) . '">' . esc_html__( 'Find WP Recipe Maker', 'larder' ) . '</a></p>'
	);
}

/**
 * Check whether search visibility matches the site environment.
 *
 * @return array
 */
function nkt_site_health_visibility() {
	$is_staging = nkt_is_staging_site();
	$is_public  = (bool) get_option( 'blog_public' );
	$correct    = $is_staging ? ! $is_public : $is_public;

	if ( $correct ) {
		return nkt_site_health_result(
			'nkt_search_visibility',
			$is_staging ? __( 'Staging is hidden from search engines', 'larder' ) : __( 'Production is visible to search engines', 'larder' ),
			'good',
			$is_staging ? __( 'Search engine indexing is discouraged while this site is being tested.', 'larder' ) : __( 'Search engines are allowed to index the production website.', 'larder' )
		);
	}

	return nkt_site_health_result(
		'nkt_search_visibility',
		$is_staging ? __( 'Hide staging from search engines', 'larder' ) : __( 'Allow production indexing before launch', 'larder' ),
		'critical',
		$is_staging ? __( 'This appears to be a staging site, but search engine indexing is currently allowed.', 'larder' ) : __( 'This appears to be the production site, but search engine indexing is currently discouraged.', 'larder' ),
		'<p><a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'Open Reading Settings', 'larder' ) . '</a></p>'
	);
}

/**
 * Check legal and URL basics that are easy to miss before launch.
 *
 * @return array
 */
function nkt_site_health_legal_urls() {
	$https_ready     = is_ssl();
	$permalink_ready = '' !== (string) get_option( 'permalink_structure' );
	$privacy_ready   = (bool) get_privacy_policy_url();

	if ( $https_ready && $permalink_ready && $privacy_ready ) {
		return nkt_site_health_result(
			'nkt_legal_urls',
			__( 'HTTPS, permalinks and privacy are ready', 'larder' ),
			'good',
			__( 'The site uses HTTPS, pretty permalinks and an assigned WordPress Privacy Policy page.', 'larder' )
		);
	}

	$missing = array();
	if ( ! $https_ready ) {
		$missing[] = __( 'HTTPS', 'larder' );
	}
	if ( ! $permalink_ready ) {
		$missing[] = __( 'pretty permalinks', 'larder' );
	}
	if ( ! $privacy_ready ) {
		$missing[] = __( 'a Privacy Policy page', 'larder' );
	}

	return nkt_site_health_result(
		'nkt_legal_urls',
		__( 'Complete the legal and URL basics', 'larder' ),
		'critical',
		sprintf(
			/* translators: %s: comma-separated missing launch items. */
			__( 'Before launch, configure: %s.', 'larder' ),
			implode( ', ', $missing )
		),
		'<p><a href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '">' . esc_html__( 'Permalink Settings', 'larder' ) . '</a> · <a href="' . esc_url( admin_url( 'options-privacy.php' ) ) . '">' . esc_html__( 'Privacy Settings', 'larder' ) . '</a></p>'
	);
}

/**
 * Check the optional marketing and contact integrations.
 *
 * @return array
 */
function nkt_site_health_integrations() {
	$seo_ready       = nkt_has_seo_plugin();
	$contact_ready   = defined( 'WPCF7_VERSION' );
	$mailchimp_ready = ( defined( 'MC4WP_VERSION' ) || function_exists( 'mc4wp_show_form' ) ) && absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) ) > 0;

	if ( $seo_ready && $contact_ready && $mailchimp_ready ) {
		return nkt_site_health_result(
			'nkt_integrations',
			__( 'SEO, contact and newsletter integrations are connected', 'larder' ),
			'good',
			__( 'The recommended launch integrations are active and the newsletter form ID is configured.', 'larder' )
		);
	}

	return nkt_site_health_result(
		'nkt_integrations',
		__( 'Finish the launch integrations', 'larder' ),
		'recommended',
		__( 'Confirm an SEO plugin, Contact Form 7 and Mailchimp for WordPress before going live. The site remains usable without all three, but key launch functions may be incomplete.', 'larder' ),
		'<p><a href="' . esc_url( admin_url( 'themes.php?page=nkt-setup' ) ) . '">' . esc_html__( 'Review integrations', 'larder' ) . '</a></p>'
	);
}

/**
 * Check business-growth and transparency readiness.
 *
 * @return array
 */
function nkt_site_health_growth_readiness() {
	$mailchimp_ready = ( defined( 'MC4WP_VERSION' ) || function_exists( 'mc4wp_show_form' ) ) && absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) ) > 0;
	$newsletter_page = (bool) nkt_setup_find_page( array( 'newsletter' ) );
	$standards_page  = (bool) nkt_setup_find_page( array( 'editorial-standards', 'recipe-standards' ) );
	$disclosure_page = (bool) nkt_setup_find_page( array( 'affiliate-disclosure', 'disclosure' ) );
	$privacy_ready   = (bool) get_privacy_policy_url();
	$measurement     = nkt_has_measurement_integration();
	$core_ready      = $mailchimp_ready && $newsletter_page && $standards_page && $disclosure_page && $privacy_ready;

	if ( $core_ready ) {
		return nkt_site_health_result(
			'nkt_growth_readiness',
			__( 'Newsletter and commercial transparency are ready', 'larder' ),
			'good',
			$measurement
				? __( 'The newsletter, privacy and trust pages are configured, and an existing measurement integration can receive the theme’s conversion events.', 'larder' )
				: __( 'The newsletter, privacy and trust pages are configured. Conversion events are available for a future analytics integration, but the theme does not install tracking itself.', 'larder' )
		);
	}

	return nkt_site_health_result(
		'nkt_growth_readiness',
		__( 'Complete newsletter and commercial transparency setup', 'larder' ),
		'recommended',
		__( 'Before promoting products, partnerships or a lead magnet, connect the newsletter and publish the Privacy, Editorial Standards and Affiliate Disclosure pages.', 'larder' ),
		'<p><a href="' . esc_url( admin_url( 'themes.php?page=nkt-setup' ) ) . '">' . esc_html__( 'Open Kitchen Table Setup', 'larder' ) . '</a></p>'
	);
}

/**
 * Register the theme-specific Site Health tests.
 *
 * @param array $tests Existing tests.
 * @return array
 */
function nkt_register_site_health_tests( $tests ) {
	$tests['direct']['nkt_theme_structure'] = array(
		'label' => __( 'Kitchen Table page structure', 'larder' ),
		'test'  => 'nkt_site_health_structure',
	);
	$tests['direct']['nkt_recipe_plugin'] = array(
		'label' => __( 'Recipe plugin availability', 'larder' ),
		'test'  => 'nkt_site_health_recipe_plugin',
	);
	$tests['direct']['nkt_search_visibility'] = array(
		'label' => __( 'Launch search visibility', 'larder' ),
		'test'  => 'nkt_site_health_visibility',
	);
	$tests['direct']['nkt_legal_urls'] = array(
		'label' => __( 'Launch legal and URL settings', 'larder' ),
		'test'  => 'nkt_site_health_legal_urls',
	);
	$tests['direct']['nkt_integrations'] = array(
		'label' => __( 'Launch integrations', 'larder' ),
		'test'  => 'nkt_site_health_integrations',
	);
	$tests['direct']['nkt_growth_readiness'] = array(
		'label' => __( 'Business growth and transparency', 'larder' ),
		'test'  => 'nkt_site_health_growth_readiness',
	);

	return $tests;
}
add_filter( 'site_status_tests', 'nkt_register_site_health_tests' );

/**
 * Add useful theme configuration details to Site Health Info.
 *
 * @param array $info Existing debug information.
 * @return array
 */
function nkt_site_health_debug_information( $info ) {
	$info['nkt_theme'] = array(
		'label'       => __( "Nigel's Kitchen Table", 'larder' ),
		'description' => __( 'Theme configuration details useful during staging and launch support.', 'larder' ),
		'show_count'  => true,
		'fields'      => array(
			'theme_version' => array(
				'label' => __( 'Theme version', 'larder' ),
				'value' => wp_get_theme()->get( 'Version' ),
			),
			'environment' => array(
				'label' => __( 'Detected environment', 'larder' ),
				'value' => nkt_is_staging_site() ? __( 'Staging or development', 'larder' ) : __( 'Production', 'larder' ),
			),
			'hero_image' => array(
				'label' => __( 'Homepage hero configured', 'larder' ),
				'value' => absint( get_theme_mod( 'larder_hero_image', 0 ) ) ? __( 'Yes', 'larder' ) : __( 'No — automatic recipe fallback is used', 'larder' ),
			),
			'primary_menu' => array(
				'label' => __( 'Primary menu assigned', 'larder' ),
				'value' => has_nav_menu( 'primary' ) ? __( 'Yes', 'larder' ) : __( 'No — theme fallback menu is used', 'larder' ),
			),
			'newsletter_form' => array(
				'label' => __( 'Mailchimp form ID', 'larder' ),
				'value' => absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) ) ?: __( 'Not configured', 'larder' ),
			),
			'lead_magnet' => array(
				'label' => __( 'Welcome gift configured', 'larder' ),
				'value' => nkt_get_lead_magnet()['enabled'] ? __( 'Yes', 'larder' ) : __( 'No', 'larder' ),
			),
			'homepage_promotion' => array(
				'label' => __( 'Homepage promotion configured', 'larder' ),
				'value' => ( nkt_get_home_promotion()['enabled'] || is_active_sidebar( 'homepage-promotion' ) ) ? __( 'Yes', 'larder' ) : __( 'No', 'larder' ),
			),
			'measurement_integration' => array(
				'label' => __( 'Measurement integration detected', 'larder' ),
				'value' => nkt_has_measurement_integration() ? __( 'Yes', 'larder' ) : __( 'No — theme events remain available', 'larder' ),
			),
		),
	);

	return $info;
}
add_filter( 'debug_information', 'nkt_site_health_debug_information' );
