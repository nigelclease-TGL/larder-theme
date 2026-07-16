<?php
/**
 * Lightweight social metadata for Nigel's Kitchen Table.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Output basic metadata only when no dedicated SEO plugin is active.
 */
function nkt_output_social_meta() {
	if ( is_admin() ) {
		return;
	}

	// Yoast, Rank Math and All in One SEO already output canonical social metadata.
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = get_bloginfo( 'description' );
	$url         = home_url( '/' );
	$image       = '';
	$type        = 'website';

	if ( is_singular() ) {
		$url  = get_permalink();
		$type = is_singular( 'post' ) ? 'article' : 'website';

		if ( has_excerpt() ) {
			$description = get_the_excerpt();
		} else {
			$description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 );
		}

		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail_url( get_the_ID(), 'large' );
		}
	}

	if ( ! $image ) {
		$hero_id = absint( get_theme_mod( 'larder_hero_image' ) );
		$image   = $hero_id ? wp_get_attachment_image_url( $hero_id, 'large' ) : '';
	}

	$description = trim( wp_strip_all_tags( $description ) );
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( "Nigel's Kitchen Table" ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<?php if ( $image ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image ); ?>">
		<meta name="twitter:card" content="summary_large_image">
	<?php else : ?>
		<meta name="twitter:card" content="summary">
	<?php endif; ?>
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php
}
add_action( 'wp_head', 'nkt_output_social_meta', 5 );
