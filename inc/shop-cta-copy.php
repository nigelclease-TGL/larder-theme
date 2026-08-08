<?php
/**
 * Shop My Kitchen call-to-action wording.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_SHOP_CTA_COPY_VERSION = '2.1.0-alpha.5';

/**
 * Use clearer Shop My Kitchen and retailer call-to-action wording.
 *
 * @param string $translation Translated text.
 * @param string $text Original text.
 * @param string $domain Text domain.
 * @return string
 */
function nkt_filter_shop_cta_copy( $translation, $text, $domain ) {
	if ( 'larder' !== $domain ) {
		return $translation;
	}

	$replacements = array(
		'Why I recommend it' => 'View in Shop My Kitchen',
		'View at %s'         => 'Shop at %s',
		'View at Amazon UK'  => 'Shop at Amazon UK',
		'View at retailer'   => 'Shop at retailer',
	);

	return isset( $replacements[ $text ] ) ? $replacements[ $text ] : $translation;
}
add_filter( 'gettext', 'nkt_filter_shop_cta_copy', 20, 3 );

/**
 * Upgrade retailer rows that still contain the old generated "View at …" copy.
 * Custom retailer button wording is preserved unchanged.
 */
function nkt_migrate_shop_cta_copy() {
	if ( get_option( 'nkt_shop_cta_copy_version' ) === NKT_SHOP_CTA_COPY_VERSION ) {
		return;
	}

	$product_ids = get_posts(
		array(
			'post_type'      => NKT_PRODUCT_POST_TYPE,
			'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	foreach ( $product_ids as $product_id ) {
		$retailers = get_post_meta( $product_id, '_nkt_product_retailers', true );
		if ( ! is_array( $retailers ) ) {
			continue;
		}

		$changed = false;
		foreach ( $retailers as &$retailer ) {
			if ( ! is_array( $retailer ) || empty( $retailer['retailer'] ) ) {
				continue;
			}

			$retailer_name = sanitize_text_field( $retailer['retailer'] );
			$legacy_text   = sprintf( 'View at %s', $retailer_name );
			$current_text  = isset( $retailer['button_text'] ) ? trim( (string) $retailer['button_text'] ) : '';

			if ( $legacy_text === $current_text ) {
				$retailer['button_text'] = sprintf( 'Shop at %s', $retailer_name );
				$changed                 = true;
			}
		}
		unset( $retailer );

		if ( $changed ) {
			update_post_meta( $product_id, '_nkt_product_retailers', $retailers );
		}
	}

	update_option( 'nkt_shop_cta_copy_version', NKT_SHOP_CTA_COPY_VERSION, false );
}
add_action( 'init', 'nkt_migrate_shop_cta_copy', 30 );
