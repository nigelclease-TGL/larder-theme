<?php
/**
 * Homepage recipe selection helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Confirm a post can be used as a homepage recipe.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function nkt_homepage_is_recipe_post( $post_id ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) || 'publish' !== get_post_status( $post_id ) ) {
		return false;
	}

	return ! has_category( array( 'kitchen-notes', 'baking-guides' ), $post_id );
}

/**
 * Return the fixed homepage collection cards in their approved order.
 *
 * @return array<int,array<string,mixed>>
 */
function nkt_homepage_collection_definitions() {
	return array(
		array(
			'key'   => 'spring_summer',
			'label' => __( 'Spring & Summer', 'larder' ),
			'slugs' => array( 'spring-summer', 'summer' ),
		),
		array(
			'key'   => 'autumn_winter',
			'label' => __( 'Autumn & Winter', 'larder' ),
			'slugs' => array( 'autumn-winter', 'autumn' ),
		),
		array(
			'key'   => 'cakes',
			'label' => __( 'Cakes & Muffins', 'larder' ),
			'slugs' => array( 'cakes-muffins', 'cakes-and-muffins', 'cakes', 'cake' ),
		),
		array(
			'key'   => 'biscuits',
			'label' => __( 'Biscuits', 'larder' ),
			'slugs' => array( 'biscuits', 'cookies' ),
		),
		array(
			'key'   => 'bread',
			'label' => __( 'Bread', 'larder' ),
			'slugs' => array( 'bread', 'breads' ),
		),
	);
}

/**
 * Locate the WordPress category for a homepage collection definition.
 *
 * @param array<string,mixed> $definition Collection definition.
 * @return WP_Term|null
 */
function nkt_homepage_collection_category( $definition ) {
	foreach ( (array) $definition['slugs'] as $slug ) {
		$category = get_category_by_slug( $slug );

		if ( $category ) {
			return $category;
		}
	}

	$category = get_term_by( 'name', (string) $definition['label'], 'category' );

	return $category instanceof WP_Term ? $category : null;
}

/**
 * Return published recipe choices for Customizer controls.
 *
 * @param int $category_id Optional category ID used to restrict the list.
 * @return array<int,string>
 */
function nkt_homepage_recipe_options( $category_id = 0 ) {
	$options = array(
		0 => __( '— Select a recipe —', 'larder' ),
	);

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => true,
		'suppress_filters'       => false,
	);

	if ( $category_id ) {
		$args['cat'] = absint( $category_id );
	}

	$post_ids = get_posts( $args );

	foreach ( $post_ids as $post_id ) {
		if ( nkt_homepage_is_recipe_post( $post_id ) ) {
			$options[ $post_id ] = get_the_title( $post_id );
		}
	}

	return $options;
}

/**
 * Get one selected homepage recipe, including compatibility with earlier setting names.
 *
 * @param string   $setting Current setting name.
 * @param string[] $legacy_settings Earlier setting names.
 * @return int
 */
function nkt_homepage_recipe_id( $setting, $legacy_settings = array() ) {
	$setting_names = array_merge( array( $setting ), $legacy_settings );

	foreach ( $setting_names as $setting_name ) {
		$post_id = absint( get_theme_mod( $setting_name, 0 ) );

		if ( nkt_homepage_is_recipe_post( $post_id ) ) {
			return $post_id;
		}
	}

	return 0;
}

/**
 * Get an ordered list of selected homepage recipes.
 *
 * @param string   $prefix Current setting prefix.
 * @param int      $count Number of recipe slots.
 * @param string[] $legacy_prefixes Earlier setting prefixes.
 * @param string[] $legacy_list_settings Earlier comma-separated list settings.
 * @return int[]
 */
function nkt_homepage_recipe_ids( $prefix, $count, $legacy_prefixes = array(), $legacy_list_settings = array() ) {
	$recipe_ids = array();

	for ( $index = 1; $index <= $count; $index++ ) {
		$legacy_settings = array();

		foreach ( $legacy_prefixes as $legacy_prefix ) {
			$legacy_settings[] = $legacy_prefix . $index;
		}

		$post_id = nkt_homepage_recipe_id( $prefix . $index, $legacy_settings );

		if ( $post_id && ! in_array( $post_id, $recipe_ids, true ) ) {
			$recipe_ids[] = $post_id;
		}
	}

	if ( empty( $recipe_ids ) ) {
		foreach ( $legacy_list_settings as $list_setting ) {
			$raw_value = get_theme_mod( $list_setting, '' );

			if ( is_array( $raw_value ) ) {
				$candidates = $raw_value;
			} else {
				$candidates = preg_split( '/[\s,]+/', (string) $raw_value, -1, PREG_SPLIT_NO_EMPTY );
			}

			foreach ( (array) $candidates as $candidate ) {
				$post_id = absint( $candidate );

				if ( nkt_homepage_is_recipe_post( $post_id ) && ! in_array( $post_id, $recipe_ids, true ) ) {
					$recipe_ids[] = $post_id;
				}

				if ( count( $recipe_ids ) >= $count ) {
					break 2;
				}
			}
		}
	}

	return array_slice( $recipe_ids, 0, $count );
}

/**
 * Return likely earlier setting names for a collection cover recipe.
 *
 * @param string $key Collection key.
 * @param int    $position Collection position.
 * @return string[]
 */
function nkt_homepage_collection_legacy_settings( $key, $position ) {
	return array(
		'larder_collection_' . $key . '_recipe_id',
		'larder_homepage_collection_' . $key . '_recipe_id',
		'larder_home_collection_' . $key . '_recipe',
		'nkt_collection_' . $key . '_recipe_id',
		'nkt_home_collection_' . $key . '_recipe_id',
		'larder_home_category_' . $position . '_recipe_id',
		'larder_category_' . $position . '_recipe_id',
		'nkt_home_category_' . $position . '_recipe_id',
	);
}

/**
 * Try to recover a collection recipe from an older unknown setting name.
 *
 * @param string $key Collection key.
 * @return int
 */
function nkt_homepage_find_legacy_collection_recipe( $key ) {
	$theme_mods = get_theme_mods();
	$key_tokens = array_filter( explode( '_', $key ) );

	foreach ( $theme_mods as $setting_name => $value ) {
		$normalised_name = strtolower( str_replace( '-', '_', (string) $setting_name ) );

		if ( false === strpos( $normalised_name, 'recipe' ) ) {
			continue;
		}

		if ( false === strpos( $normalised_name, 'collection' ) && false === strpos( $normalised_name, 'category' ) ) {
			continue;
		}

		$matches_key = true;
		foreach ( $key_tokens as $token ) {
			if ( false === strpos( $normalised_name, $token ) ) {
				$matches_key = false;
				break;
			}
		}

		$post_id = absint( $value );
		if ( $matches_key && nkt_homepage_is_recipe_post( $post_id ) ) {
			return $post_id;
		}
	}

	return 0;
}

/**
 * Copy recognised earlier homepage recipe settings into the current controls.
 *
 * This is deliberately conservative and never replaces a current selection.
 *
 * @return void
 */
function nkt_migrate_homepage_recipe_settings() {
	$mappings = array(
		'larder_home_hero_recipe_id' => array(
			'larder_hero_recipe_id',
			'larder_featured_recipe_id',
			'larder_homepage_hero_recipe_id',
			'nkt_hero_recipe_id',
			'nkt_home_hero_recipe_id',
		),
	);

	for ( $index = 1; $index <= 6; $index++ ) {
		$mappings[ 'larder_home_latest_recipe_' . $index ] = array(
			'larder_latest_recipe_' . $index,
			'larder_homepage_latest_recipe_' . $index,
			'nkt_latest_recipe_' . $index,
			'nkt_home_latest_recipe_' . $index,
		);
	}

	for ( $index = 1; $index <= 4; $index++ ) {
		$mappings[ 'larder_home_favourite_recipe_' . $index ] = array(
			'larder_popular_recipe_' . $index,
			'larder_favourite_recipe_' . $index,
			'larder_homepage_popular_recipe_' . $index,
			'nkt_popular_recipe_' . $index,
			'nkt_home_popular_recipe_' . $index,
		);
	}

	foreach ( nkt_homepage_collection_definitions() as $index => $definition ) {
		$mappings[ 'larder_home_collection_' . $definition['key'] . '_recipe_id' ] = nkt_homepage_collection_legacy_settings( $definition['key'], $index + 1 );
	}

	foreach ( $mappings as $current_setting => $legacy_settings ) {
		if ( nkt_homepage_is_recipe_post( absint( get_theme_mod( $current_setting, 0 ) ) ) ) {
			continue;
		}

		foreach ( $legacy_settings as $legacy_setting ) {
			$post_id = absint( get_theme_mod( $legacy_setting, 0 ) );

			if ( nkt_homepage_is_recipe_post( $post_id ) ) {
				set_theme_mod( $current_setting, $post_id );
				continue 2;
			}
		}

		if ( 0 === strpos( $current_setting, 'larder_home_collection_' ) ) {
			$key     = str_replace( array( 'larder_home_collection_', '_recipe_id' ), '', $current_setting );
			$post_id = nkt_homepage_find_legacy_collection_recipe( $key );

			if ( $post_id ) {
				set_theme_mod( $current_setting, $post_id );
			}
		}
	}
}
add_action( 'after_setup_theme', 'nkt_migrate_homepage_recipe_settings', 30 );
