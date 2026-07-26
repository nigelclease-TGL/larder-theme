<?php
/**
 * Plugin Name: NKT Translation Manifest Exporter
 * Description: Creates a read-only JSON export of English Nigel's Kitchen Table content for offline Hungarian translation. It does not change the public website or database content.
 * Version: 1.0.0
 * Author: Nigel Clease
 * Requires at least: 6.6
 * Requires PHP: 8.1
 * Text Domain: nkt-translation-manifest-exporter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_TME_VERSION = '1.0.0';

/**
 * Post types included in the translation source export.
 *
 * @return string[]
 */
function nkt_tme_post_types() {
	$post_types = array( 'page', 'post' );

	if ( post_type_exists( 'wprm_recipe' ) ) {
		$post_types[] = 'wprm_recipe';
	}

	if ( post_type_exists( 'wpcf7_contact_form' ) ) {
		$post_types[] = 'wpcf7_contact_form';
	}

	return $post_types;
}

/**
 * Return true when a post is English or has not yet been assigned a language.
 */
function nkt_tme_is_english_source( $post_id ) {
	if ( ! function_exists( 'pll_get_post_language' ) ) {
		return true;
	}

	$language = pll_get_post_language( $post_id, 'slug' );
	return ! $language || 'en' === $language;
}

/**
 * Export a media reference without copying the binary file.
 */
function nkt_tme_media_reference( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( $attachment_id <= 0 ) {
		return null;
	}

	$attachment = get_post( $attachment_id );
	if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
		return null;
	}

	return array(
		'id'          => $attachment_id,
		'url'         => wp_get_attachment_url( $attachment_id ),
		'alt'         => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
		'title'       => get_the_title( $attachment_id ),
		'caption'     => wp_get_attachment_caption( $attachment_id ),
		'description' => $attachment->post_content,
	);
}

/**
 * Export public taxonomy assignments for one post.
 */
function nkt_tme_post_terms( $post_id, $post_type ) {
	$result = array();

	foreach ( get_object_taxonomies( $post_type, 'objects' ) as $taxonomy => $object ) {
		if ( ! $object->public && ! in_array( $taxonomy, array( 'category', 'post_tag', 'recipe_collection' ), true ) ) {
			continue;
		}

		$terms = wp_get_object_terms( $post_id, $taxonomy );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}

		$result[ $taxonomy ] = array_map(
			static function ( $term ) {
				return array(
					'id'          => (int) $term->term_id,
					'name'        => $term->name,
					'slug'        => $term->slug,
					'description' => $term->description,
					'parent'      => (int) $term->parent,
				);
			},
			$terms
		);
	}

	return $result;
}

/**
 * Export metadata for recipe and form objects while excluding editor locks.
 */
function nkt_tme_object_meta( $post_id ) {
	$meta   = get_post_meta( $post_id );
	$result = array();

	foreach ( $meta as $key => $values ) {
		if ( in_array( $key, array( '_edit_lock', '_edit_last' ), true ) ) {
			continue;
		}

		$result[ $key ] = array_map( 'maybe_unserialize', $values );
		if ( 1 === count( $result[ $key ] ) ) {
			$result[ $key ] = $result[ $key ][0];
		}
	}

	return $result;
}

/**
 * Build a stable fingerprint used later to detect changed English sources.
 */
function nkt_tme_source_fingerprint( $record ) {
	$fingerprint_source = array(
		'id'             => $record['id'],
		'post_type'      => $record['post_type'],
		'title'          => $record['title'],
		'slug'           => $record['slug'],
		'excerpt'        => $record['excerpt'],
		'content'        => $record['content'],
		'yoast_title'    => $record['yoast_title'],
		'yoast_metadesc' => $record['yoast_metadesc'],
		'taxonomies'     => $record['taxonomies'],
	);

	if ( isset( $record['object_meta'] ) ) {
		$fingerprint_source['object_meta'] = $record['object_meta'];
	}

	return hash( 'sha256', wp_json_encode( $fingerprint_source ) );
}

/**
 * Export one content record.
 */
function nkt_tme_content_record( $post_id, $post_type ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	$record = array(
		'id'             => (int) $post_id,
		'post_type'      => $post_type,
		'status'         => $post->post_status,
		'title'          => $post->post_title,
		'slug'           => $post->post_name,
		'excerpt'        => $post->post_excerpt,
		'content'        => $post->post_content,
		'parent'         => (int) $post->post_parent,
		'menu_order'     => (int) $post->menu_order,
		'published_gmt'  => $post->post_date_gmt,
		'modified_gmt'   => $post->post_modified_gmt,
		'permalink'      => get_permalink( $post_id ),
		'page_template'  => get_post_meta( $post_id, '_wp_page_template', true ),
		'yoast_title'    => get_post_meta( $post_id, '_yoast_wpseo_title', true ),
		'yoast_metadesc' => get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ),
		'featured_image' => nkt_tme_media_reference( get_post_thumbnail_id( $post_id ) ),
		'taxonomies'     => nkt_tme_post_terms( $post_id, $post_type ),
	);

	if ( in_array( $post_type, array( 'wprm_recipe', 'wpcf7_contact_form' ), true ) ) {
		$record['object_meta'] = nkt_tme_object_meta( $post_id );
	}

	$record['source_fingerprint'] = nkt_tme_source_fingerprint( $record );
	return $record;
}

/**
 * Export all terms that may need a Hungarian counterpart, including unused terms.
 */
function nkt_tme_taxonomy_catalogue() {
	$catalogue = array();

	foreach ( array( 'category', 'post_tag', 'recipe_collection' ) as $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) ) {
			continue;
		}

		$catalogue[ $taxonomy ] = array_map(
			static function ( $term ) {
				return array(
					'id'          => (int) $term->term_id,
					'name'        => $term->name,
					'slug'        => $term->slug,
					'description' => $term->description,
					'parent'      => (int) $term->parent,
					'count'       => (int) $term->count,
				);
			},
			$terms
		);
	}

	return $catalogue;
}

/**
 * Export menu locations and their current English items.
 */
function nkt_tme_navigation_manifest() {
	$result    = array();
	$locations = get_nav_menu_locations();

	foreach ( $locations as $location => $menu_id ) {
		$menu = wp_get_nav_menu_object( $menu_id );
		if ( ! $menu ) {
			continue;
		}

		$items = wp_get_nav_menu_items( $menu_id );
		$result[ $location ] = array(
			'menu_id'   => (int) $menu_id,
			'menu_name' => $menu->name,
			'items'     => array(),
		);

		foreach ( (array) $items as $item ) {
			$result[ $location ]['items'][] = array(
				'id'          => (int) $item->ID,
				'title'       => $item->title,
				'url'         => $item->url,
				'object'      => $item->object,
				'object_id'   => (int) $item->object_id,
				'parent'      => (int) $item->menu_item_parent,
				'menu_order'  => (int) $item->menu_order,
				'target'      => $item->target,
				'description' => $item->description,
			);
		}
	}

	return $result;
}

/**
 * Theme strings stored outside normal post content.
 */
function nkt_tme_theme_mod_manifest() {
	$setting_ids = array(
		'larder_hero_title',
		'larder_hero_copy',
		'larder_about_title',
		'larder_about_copy',
		'larder_newsletter_title',
		'larder_newsletter_copy',
		'larder_newsletter_promise',
		'larder_lead_magnet_title',
		'larder_lead_magnet_copy',
		'larder_lead_magnet_button',
		'larder_promotion_eyebrow',
		'larder_promotion_title',
		'larder_promotion_copy',
		'larder_promotion_button',
	);

	$result = array();
	foreach ( $setting_ids as $setting_id ) {
		$value = get_theme_mod( $setting_id, '' );
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			$result[ $setting_id ] = $value;
		}
	}

	return $result;
}

/**
 * Build the complete read-only English source manifest.
 */
function nkt_tme_build_manifest() {
	$content = array();

	foreach ( nkt_tme_post_types() as $post_type ) {
		$content[ $post_type ] = array();
		$ids = get_posts(
			array(
				'post_type'              => $post_type,
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'suppress_filters'       => false,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		foreach ( $ids as $post_id ) {
			if ( ! nkt_tme_is_english_source( $post_id ) ) {
				continue;
			}

			$record = nkt_tme_content_record( $post_id, $post_type );
			if ( $record ) {
				$content[ $post_type ][] = $record;
			}
		}
	}

	return array(
		'manifest_version' => 1,
		'exporter_version' => NKT_TME_VERSION,
		'generated_at_gmt' => gmdate( 'c' ),
		'site'             => array(
			'name'              => get_bloginfo( 'name' ),
			'url'               => home_url( '/' ),
			'language'          => 'en-GB',
			'target'            => 'hu-HU',
			'page_on_front'     => (int) get_option( 'page_on_front' ),
			'page_for_posts'    => (int) get_option( 'page_for_posts' ),
			'privacy_page_id'   => (int) get_option( 'wp_page_for_privacy_policy' ),
			'permalink_structure' => get_option( 'permalink_structure' ),
		),
		'theme_mod_values' => nkt_tme_theme_mod_manifest(),
		'navigation'       => nkt_tme_navigation_manifest(),
		'taxonomies'       => nkt_tme_taxonomy_catalogue(),
		'content'          => $content,
	);
}

/**
 * Download handler. The only operation is producing a JSON response.
 */
function nkt_tme_download_manifest() {
	if ( ! current_user_can( 'export' ) ) {
		wp_die( esc_html__( 'You do not have permission to export content.', 'nkt-translation-manifest-exporter' ) );
	}

	check_admin_referer( 'nkt_tme_export_manifest' );
	$manifest = nkt_tme_build_manifest();
	$filename = 'nkt-english-source-' . gmdate( 'Y-m-d-His' ) . '.json';

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_nkt_tme_export_manifest', 'nkt_tme_download_manifest' );

/**
 * Add the read-only exporter under Tools.
 */
function nkt_tme_register_page() {
	add_management_page(
		'Translation Manifest Export',
		'Translation Manifest Export',
		'export',
		'nkt-translation-manifest-export',
		'nkt_tme_render_page'
	);
}
add_action( 'admin_menu', 'nkt_tme_register_page' );

/**
 * Render the exporter screen.
 */
function nkt_tme_render_page() {
	if ( ! current_user_can( 'export' ) ) {
		return;
	}

	$post_counts = array();
	foreach ( nkt_tme_post_types() as $post_type ) {
		$count = wp_count_posts( $post_type );
		$post_counts[ $post_type ] = isset( $count->publish ) ? (int) $count->publish : 0;
	}
	?>
	<div class="wrap">
		<h1>Translation Manifest Export</h1>
		<p><strong>This tool is read-only.</strong> It does not edit posts, recipes, settings, URLs, menus or the public website.</p>
		<p>The JSON file contains the English text and structural references needed to prepare the Hungarian website offline.</p>
		<table class="widefat striped" style="max-width:700px;margin:1rem 0">
			<thead><tr><th>Content type</th><th>Published records found</th></tr></thead>
			<tbody>
			<?php foreach ( $post_counts as $post_type => $count ) : ?>
				<tr><th scope="row"><?php echo esc_html( $post_type ); ?></th><td><?php echo esc_html( number_format_i18n( $count ) ); ?></td></tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p><a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nkt_tme_export_manifest' ), 'nkt_tme_export_manifest' ) ); ?>">Download English translation manifest</a></p>
		<p>After downloading, deactivate and delete this exporter. Send the JSON file for offline translation work.</p>
	</div>
	<?php
}
