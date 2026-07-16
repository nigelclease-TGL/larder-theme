<?php
/**
 * Recipe collections taxonomy and helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register editorial recipe collections for posts.
 */
function nkt_register_recipe_collections() {
	$labels = array(
		'name'                       => __( 'Recipe Collections', 'larder' ),
		'singular_name'              => __( 'Recipe Collection', 'larder' ),
		'search_items'               => __( 'Search recipe collections', 'larder' ),
		'popular_items'              => __( 'Popular recipe collections', 'larder' ),
		'all_items'                  => __( 'All recipe collections', 'larder' ),
		'edit_item'                  => __( 'Edit recipe collection', 'larder' ),
		'update_item'                => __( 'Update recipe collection', 'larder' ),
		'add_new_item'               => __( 'Add new recipe collection', 'larder' ),
		'new_item_name'              => __( 'New recipe collection name', 'larder' ),
		'separate_items_with_commas' => __( 'Separate collections with commas', 'larder' ),
		'add_or_remove_items'        => __( 'Add or remove recipe collections', 'larder' ),
		'choose_from_most_used'      => __( 'Choose from the most used collections', 'larder' ),
		'menu_name'                  => __( 'Recipe Collections', 'larder' ),
	);

	register_taxonomy(
		'recipe_collection',
		array( 'post' ),
		array(
			'labels'            => $labels,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => 'recipe-collections',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'nkt_register_recipe_collections' );

/**
 * Flush rewrite rules once after this feature is introduced.
 */
function nkt_maybe_flush_collection_rewrites() {
	if ( get_option( 'nkt_collections_rewrite_version' ) !== '1' ) {
		nkt_register_recipe_collections();
		flush_rewrite_rules( false );
		update_option( 'nkt_collections_rewrite_version', '1' );
	}
}
add_action( 'after_switch_theme', 'nkt_maybe_flush_collection_rewrites' );

/**
 * Return the collections index URL.
 */
function nkt_get_collections_url() {
	$page = get_page_by_path( 'recipe-collections' );
	return $page ? get_permalink( $page ) : home_url( '/recipe-collections/' );
}
