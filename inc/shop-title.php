<?php
/**
 * Shop My Kitchen document-title corrections.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Present the public product archive with its editorial name instead of the
 * generated custom-post-type archive label.
 *
 * @param array $title Existing document-title parts.
 * @return array
 */
function nkt_shop_document_title_parts( $title ) {
	if ( is_post_type_archive( NKT_PRODUCT_POST_TYPE ) ) {
		$title['title'] = __( 'Shop My Kitchen', 'larder' );
		return $title;
	}

	if ( is_tax( array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ) ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			$title['title'] = sprintf(
				/* translators: %s: product category or brand name. */
				__( '%s – Shop My Kitchen', 'larder' ),
				$term->name
			);
		}
	}

	return $title;
}
add_filter( 'document_title_parts', 'nkt_shop_document_title_parts', 20 );

/**
 * Keep Yoast SEO titles aligned with the public Shop My Kitchen wording.
 *
 * @param string $title Existing Yoast title.
 * @return string
 */
function nkt_shop_wpseo_title( $title ) {
	if ( is_post_type_archive( NKT_PRODUCT_POST_TYPE ) ) {
		return sprintf(
			/* translators: 1: archive name, 2: site name. */
			__( '%1$s - %2$s', 'larder' ),
			__( 'Shop My Kitchen', 'larder' ),
			get_bloginfo( 'name' )
		);
	}

	if ( is_tax( array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ) ) ) {
		$term = get_queried_object();
		if ( $term instanceof WP_Term ) {
			return sprintf(
				/* translators: 1: term name, 2: archive name, 3: site name. */
				__( '%1$s - %2$s - %3$s', 'larder' ),
				$term->name,
				__( 'Shop My Kitchen', 'larder' ),
				get_bloginfo( 'name' )
			);
		}
	}

	return $title;
}
add_filter( 'wpseo_title', 'nkt_shop_wpseo_title', 20 );
