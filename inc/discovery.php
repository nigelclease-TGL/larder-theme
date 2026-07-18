<?php
/**
 * Recipe discovery, filtering and sorting helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return category IDs that should not appear in recipe-only discovery areas.
 *
 * @return int[]
 */
function nkt_get_non_recipe_category_ids() {
	static $excluded_ids = null;

	if ( null !== $excluded_ids ) {
		return $excluded_ids;
	}

	$excluded_ids = array_filter( array( (int) get_option( 'default_category' ) ) );

	foreach ( array( 'kitchen-notes', 'baking-guides' ) as $slug ) {
		$category = get_category_by_slug( $slug );

		if ( $category ) {
			$excluded_ids[] = (int) $category->term_id;
		}
	}

	$excluded_ids = array_values( array_unique( array_filter( array_map( 'absint', $excluded_ids ) ) ) );

	return $excluded_ids;
}

/**
 * Return the most useful recipe categories for navigation.
 *
 * @param int $limit Maximum number of categories.
 * @return WP_Term[]
 */
function nkt_get_recipe_discovery_categories( $limit = 10 ) {
	return get_categories(
		array(
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => absint( $limit ),
			'exclude'    => nkt_get_non_recipe_category_ids(),
		)
	);
}

/**
 * Return available discovery sort options.
 *
 * @param bool $include_relevance Include search relevance.
 * @return array<string,string>
 */
function nkt_get_discovery_sort_options( $include_relevance = false ) {
	$options = array();

	if ( $include_relevance ) {
		$options['relevance'] = __( 'Best match', 'larder' );
	}

	$options['newest']  = __( 'Newest first', 'larder' );
	$options['popular'] = __( 'Most discussed', 'larder' );
	$options['title']   = __( 'A–Z', 'larder' );
	$options['oldest']  = __( 'Oldest first', 'larder' );

	return $options;
}

/**
 * Return the validated discovery sort key from the URL.
 *
 * @param string $default Default sort key.
 * @param bool   $include_relevance Whether relevance is allowed.
 * @return string
 */
function nkt_get_requested_discovery_sort( $default = 'newest', $include_relevance = false ) {
	$options = nkt_get_discovery_sort_options( $include_relevance );
	$sort    = isset( $_GET['sort'] ) ? sanitize_key( wp_unslash( $_GET['sort'] ) ) : $default; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	return array_key_exists( $sort, $options ) ? $sort : $default;
}

/**
 * Convert a discovery sort key into WP_Query arguments.
 *
 * @param string $sort Sort key.
 * @return array<string,mixed>
 */
function nkt_get_discovery_order_args( $sort ) {
	switch ( $sort ) {
		case 'popular':
			return array(
				'orderby' => array(
					'comment_count' => 'DESC',
					'date'          => 'DESC',
				),
			);
		case 'title':
			return array(
				'orderby' => 'title',
				'order'   => 'ASC',
			);
		case 'oldest':
			return array(
				'orderby' => 'date',
				'order'   => 'ASC',
			);
		case 'relevance':
			return array(
				'orderby' => 'relevance',
				'order'   => 'DESC',
			);
		case 'newest':
		default:
			return array(
				'orderby' => 'date',
				'order'   => 'DESC',
			);
	}
}

/**
 * Apply visitor-selected sorting to standard archive and search queries.
 *
 * @param WP_Query $query Query instance.
 * @return void
 */
function nkt_apply_discovery_sort_to_main_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( ! $query->is_search() && ! $query->is_archive() && ! $query->is_home() ) {
		return;
	}

	$include_relevance = $query->is_search();
	$default           = $include_relevance ? 'relevance' : 'newest';
	$sort              = nkt_get_requested_discovery_sort( $default, $include_relevance );

	foreach ( nkt_get_discovery_order_args( $sort ) as $key => $value ) {
		$query->set( $key, $value );
	}
}
add_action( 'pre_get_posts', 'nkt_apply_discovery_sort_to_main_query', 20 );

/**
 * Return a translated recipe count label.
 *
 * @param int $count Number of recipes.
 * @return string
 */
function nkt_get_recipe_count_label( $count ) {
	$count = absint( $count );

	return sprintf(
		/* translators: %s: number of recipes. */
		_n( '%s recipe', '%s recipes', $count, 'larder' ),
		number_format_i18n( $count )
	);
}
