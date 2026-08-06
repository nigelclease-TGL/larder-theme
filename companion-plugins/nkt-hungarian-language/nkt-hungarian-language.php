<?php
/**
 * Plugin Name: Nigel's Kitchen Table – Hungarian Language
 * Description: Hungarian language support for Nigel's Kitchen Table using Polylang. English remains the unchanged default language.
 * Version: 0.4.0
 * Author: Nigel Clease
 * Text Domain: nkt-hungarian-language
 * Requires at least: 6.6
 * Requires PHP: 8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NKT_HU_VERSION', '0.4.0' );

function nkt_hu_current_language_code() {
	if ( function_exists( 'pll_current_language' ) ) {
		$language = pll_current_language( 'slug' );
		if ( is_string( $language ) && '' !== $language ) {
			return strtolower( $language );
		}
	}

	$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
	return strtolower( substr( (string) $locale, 0, 2 ) );
}

function nkt_hu_is_hungarian() {
	return 'hu' === nkt_hu_current_language_code();
}

function nkt_hu_dictionary() {
	static $dictionary = null;

	if ( null === $dictionary ) {
		$dictionary = require __DIR__ . '/languages/hu_HU.php';
	}

	return is_array( $dictionary ) ? $dictionary : array();
}

function nkt_hu_gettext( $translation, $text, $domain ) {
	if ( 'larder' !== $domain || ! nkt_hu_is_hungarian() ) {
		return $translation;
	}

	$dictionary = nkt_hu_dictionary();
	return isset( $dictionary[ $text ] ) ? $dictionary[ $text ] : $translation;
}
add_filter( 'gettext', 'nkt_hu_gettext', 20, 3 );

function nkt_hu_gettext_with_context( $translation, $text, $context, $domain ) {
	return nkt_hu_gettext( $translation, $text, $domain );
}
add_filter( 'gettext_with_context', 'nkt_hu_gettext_with_context', 20, 4 );

function nkt_hu_polylang_post_types( $post_types, $is_settings ) {
	$post_types['wprm_recipe'] = 'wprm_recipe';
	return $post_types;
}
add_filter( 'pll_get_post_types', 'nkt_hu_polylang_post_types', 10, 2 );

function nkt_hu_polylang_taxonomies( $taxonomies, $is_settings ) {
	$taxonomies['recipe_collection'] = 'recipe_collection';
	return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'nkt_hu_polylang_taxonomies', 10, 2 );

function nkt_hu_translatable_theme_mods() {
	return array(
		'larder_hero_title'         => 'Homepage hero title',
		'larder_hero_copy'          => 'Homepage hero introduction',
		'larder_about_title'        => 'Homepage About title',
		'larder_about_copy'         => 'Homepage About introduction',
		'larder_newsletter_title'   => 'Newsletter title',
		'larder_newsletter_copy'    => 'Newsletter introduction',
		'larder_newsletter_promise' => 'Newsletter promise',
		'larder_lead_magnet_title'  => 'Welcome gift title',
		'larder_lead_magnet_copy'   => 'Welcome gift description',
		'larder_lead_magnet_button' => 'Welcome gift button label',
		'larder_promotion_eyebrow'  => 'Promotion eyebrow',
		'larder_promotion_title'    => 'Promotion title',
		'larder_promotion_copy'     => 'Promotion description',
		'larder_promotion_button'   => 'Promotion button label',
	);
}

function nkt_hu_register_polylang_strings() {
	if ( ! is_admin() || ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	foreach ( nkt_hu_translatable_theme_mods() as $setting_id => $label ) {
		$value = get_theme_mod( $setting_id, '' );
		if ( is_string( $value ) && '' !== trim( $value ) ) {
			pll_register_string( $label, $value, "Nigel's Kitchen Table", true );
		}
	}
}
add_action( 'admin_init', 'nkt_hu_register_polylang_strings' );

function nkt_hu_translate_theme_mod_value( $value ) {
	if ( is_string( $value ) && '' !== $value && function_exists( 'pll__' ) ) {
		return pll__( $value );
	}
	return $value;
}

foreach ( array_keys( nkt_hu_translatable_theme_mods() ) as $nkt_hu_theme_mod ) {
	add_filter( 'theme_mod_' . $nkt_hu_theme_mod, 'nkt_hu_translate_theme_mod_value' );
}

function nkt_hu_language_switcher_markup() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return '';
	}

	$languages = pll_the_languages(
		array(
			'raw'                    => 1,
			'hide_if_empty'          => 0,
			'hide_if_no_translation' => 1,
		)
	);

	if ( ! is_array( $languages ) ) {
		return '';
	}

	$languages = array_filter(
		$languages,
		static function ( $language ) {
			return isset( $language['slug'] ) && in_array( $language['slug'], array( 'en', 'hu' ), true );
		}
	);

	if ( count( $languages ) < 2 ) {
		return '';
	}

	$links = '';
	foreach ( $languages as $language ) {
		$slug       = (string) $language['slug'];
		$is_current = ! empty( $language['current_lang'] );
		$short      = 'hu' === $slug ? 'HU' : 'EN';
		$name       = 'hu' === $slug ? 'Magyar' : 'English';
		$links     .= sprintf(
			'<a href="%1$s" lang="%2$s" hreflang="%2$s"%3$s aria-label="%4$s">%5$s</a>',
			esc_url( $language['url'] ),
			esc_attr( $slug ),
			$is_current ? ' aria-current="page"' : '',
			esc_attr( $name ),
			esc_html( $short )
		);
	}

	$aria = nkt_hu_is_hungarian() ? 'Nyelvválasztó' : 'Language selector';
	return '<nav class="nkt-hu-language-switcher" aria-label="' . esc_attr( $aria ) . '">' . $links . '</nav>';
}

function nkt_hu_render_header_switcher() {
	echo wp_kses_post( nkt_hu_language_switcher_markup() );
}
add_action( 'nkt_header_language_switcher', 'nkt_hu_render_header_switcher' );

function nkt_hu_body_class( $classes ) {
	$classes[] = 'site-language-' . sanitize_html_class( nkt_hu_current_language_code() );
	return $classes;
}
add_filter( 'body_class', 'nkt_hu_body_class' );

function nkt_hu_polylang_notice() {
	if ( function_exists( 'pll_current_language' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning"><p>
		<strong><?php esc_html_e( "Nigel's Kitchen Table – Hungarian Language", 'nkt-hungarian-language' ); ?>:</strong>
		<?php esc_html_e( 'Polylang must be installed and activated before Hungarian pages can be enabled.', 'nkt-hungarian-language' ); ?>
	</p></div>
	<?php
}
add_action( 'admin_notices', 'nkt_hu_polylang_notice' );

function nkt_hu_translation_summary( $post_type ) {
	$summary = array(
		'total'      => 0,
		'translated' => 0,
		'published'  => 0,
	);

	if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_get_post_language' ) ) {
		return $summary;
	}

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
		if ( 'en' !== pll_get_post_language( $post_id, 'slug' ) ) {
			continue;
		}

		$summary['total']++;
		$translation_id = (int) pll_get_post( $post_id, 'hu' );
		if ( $translation_id > 0 ) {
			$summary['translated']++;
			if ( 'publish' === get_post_status( $translation_id ) ) {
				$summary['published']++;
			}
		}
	}

	return $summary;
}

function nkt_hu_export_post_ids( $post_type ) {
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

	if ( ! function_exists( 'pll_get_post_language' ) ) {
		return array_map( 'intval', $ids );
	}

	return array_values(
		array_filter(
			array_map( 'intval', $ids ),
			static function ( $post_id ) {
				$language = pll_get_post_language( $post_id, 'slug' );
				return ! $language || 'en' === $language;
			}
		)
	);
}

function nkt_hu_export_media_reference( $attachment_id ) {
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

function nkt_hu_export_post_terms( $post_id, $post_type ) {
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

function nkt_hu_export_recipe_meta( $post_id ) {
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

function nkt_hu_export_content_record( $post_id, $post_type ) {
	$post = get_post( $post_id );
	if ( ! $post ) {
		return array();
	}

	$featured_id = (int) get_post_thumbnail_id( $post_id );
	$record      = array(
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
		'featured_image' => nkt_hu_export_media_reference( $featured_id ),
		'taxonomies'     => nkt_hu_export_post_terms( $post_id, $post_type ),
	);

	if ( 'wprm_recipe' === $post_type ) {
		$record['recipe_meta'] = nkt_hu_export_recipe_meta( $post_id );
	}

	return $record;
}

function nkt_hu_build_translation_manifest() {
	$content = array();
	foreach ( array( 'page', 'post', 'wprm_recipe' ) as $post_type ) {
		$content[ $post_type ] = array();
		foreach ( nkt_hu_export_post_ids( $post_type ) as $post_id ) {
			$record = nkt_hu_export_content_record( $post_id, $post_type );
			if ( $record ) {
				$content[ $post_type ][] = $record;
			}
		}
	}

	return array(
		'manifest_version' => 1,
		'generated_at_gmt' => gmdate( 'c' ),
		'site'             => array(
			'name'           => get_bloginfo( 'name' ),
			'url'            => home_url( '/' ),
			'language'       => 'en-GB',
			'target'         => 'hu-HU',
			'page_on_front'  => (int) get_option( 'page_on_front' ),
			'page_for_posts' => (int) get_option( 'page_for_posts' ),
		),
		'theme_strings'    => nkt_hu_translatable_theme_mods(),
		'theme_mod_values' => array_intersect_key( get_theme_mods(), nkt_hu_translatable_theme_mods() ),
		'content'          => $content,
	);
}

function nkt_hu_download_translation_manifest() {
	if ( ! current_user_can( 'export' ) ) {
		wp_die( esc_html__( 'You do not have permission to export content.', 'nkt-hungarian-language' ) );
	}

	check_admin_referer( 'nkt_hu_export_manifest' );
	$manifest = nkt_hu_build_translation_manifest();
	$filename = 'nkt-english-source-' . gmdate( 'Y-m-d-His' ) . '.json';

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
	echo wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	exit;
}
add_action( 'admin_post_nkt_hu_export_manifest', 'nkt_hu_download_translation_manifest' );

function nkt_hu_register_audit_page() {
	add_management_page(
		'Hungarian Translation Audit',
		'Hungarian Translation Audit',
		'edit_posts',
		'nkt-hungarian-translation-audit',
		'nkt_hu_render_audit_page'
	);
}
add_action( 'admin_menu', 'nkt_hu_register_audit_page' );

function nkt_hu_render_audit_page() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return;
	}

	$post_types = array(
		'post'        => 'Recipes and Kitchen Notes',
		'page'        => 'Pages',
		'wprm_recipe' => 'WP Recipe Maker recipes',
	);
	?>
	<div class="wrap">
		<h1>Hungarian Translation Audit</h1>
		<p>This screen is read-only. English content is never changed by this tool.</p>
		<p><a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=nkt_hu_export_manifest' ), 'nkt_hu_export_manifest' ) ); ?>">Download English translation manifest</a></p>
		<?php if ( ! function_exists( 'pll_get_post' ) ) : ?>
			<div class="notice notice-warning inline"><p>Activate Polylang to calculate translation coverage.</p></div>
		<?php else : ?>
			<table class="widefat striped" style="max-width:900px">
				<thead><tr><th>Content</th><th>Published English</th><th>Hungarian created</th><th>Hungarian published</th><th>Still missing</th></tr></thead>
				<tbody>
				<?php foreach ( $post_types as $post_type => $label ) :
					$summary = nkt_hu_translation_summary( $post_type );
					$missing = max( 0, $summary['total'] - $summary['translated'] );
					?>
					<tr>
						<th scope="row"><?php echo esc_html( $label ); ?></th>
						<td><?php echo esc_html( number_format_i18n( $summary['total'] ) ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $summary['translated'] ) ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $summary['published'] ) ); ?></td>
						<td><strong><?php echo esc_html( number_format_i18n( $missing ) ); ?></strong></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<p><strong>Publishing rule:</strong> keep Hungarian items as drafts until the article text and its separate WP Recipe Maker recipe have both been reviewed.</p>
		<?php endif; ?>
	</div>
	<?php
}

function nkt_hu_enqueue_assets() {
	wp_enqueue_style(
		'nkt-hungarian-language',
		plugins_url( 'assets/language-switcher.css', __FILE__ ),
		array(),
		NKT_HU_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'nkt_hu_enqueue_assets', 30 );
