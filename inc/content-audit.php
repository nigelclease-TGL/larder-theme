<?php
/**
 * Editorial recipe and Kitchen Note audit helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function nkt_content_audit_is_note( $post_id ) {
	return has_category( array( 'kitchen-notes', 'baking-guides' ), $post_id );
}

/**
 * Extract all WPRM recipe IDs referenced by a post.
 *
 * @param string $content Post content.
 * @return int[]
 */
function nkt_content_audit_recipe_ids( $content ) {
	$ids     = array();
	$content = (string) $content;

	if ( preg_match_all( '/\[(?:wprm-recipe|wprm-recipe-snippet)[^\]]*\bid=["\']?(\d+)/i', $content, $shortcodes ) ) {
		$ids = array_merge( $ids, array_map( 'absint', $shortcodes[1] ) );
	}

	if ( preg_match_all( '/<!--\s+wp:[^>]*(?:wp-recipe-maker|wprm)[^>]*?\{.*?["\'](?:id|recipeId|recipe_id)["\']\s*:\s*["\']?(\d+)/is', $content, $blocks ) ) {
		$ids = array_merge( $ids, array_map( 'absint', $blocks[1] ) );
	}

	return array_values( array_filter( array_unique( $ids ) ) );
}

function nkt_content_audit_has_recipe_card( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	return 1 === count( nkt_content_audit_recipe_ids( $content ) );
}

/**
 * Return normalised heading text from a post.
 *
 * @param string $content Post content.
 * @return string[]
 */
function nkt_content_audit_headings( $content ) {
	$headings = array();
	if ( preg_match_all( '/<h([2-6])\b[^>]*>(.*?)<\/h\1>/is', (string) $content, $matches ) ) {
		foreach ( $matches[2] as $heading ) {
			$headings[] = strtolower( trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( html_entity_decode( $heading, ENT_QUOTES, 'UTF-8' ) ) ) ) );
		}
	}
	return $headings;
}

function nkt_content_audit_has_heading( $headings, $needles ) {
	foreach ( (array) $headings as $heading ) {
		foreach ( (array) $needles as $needle ) {
			if ( false !== strpos( $heading, strtolower( $needle ) ) ) {
				return true;
			}
		}
	}
	return false;
}

function nkt_content_audit_heading_hierarchy_valid( $content ) {
	if ( ! preg_match_all( '/<h([2-6])\b/i', (string) $content, $matches ) ) {
		return false;
	}

	$levels = array_map( 'intval', $matches[1] );
	if ( 2 !== $levels[0] ) {
		return false;
	}

	$previous = $levels[0];
	foreach ( array_slice( $levels, 1 ) as $level ) {
		if ( $level > $previous + 1 ) {
			return false;
		}
		$previous = $level;
	}
	return true;
}

function nkt_content_audit_has_empty_sections( $content ) {
	$content = (string) $content;
	if ( ! preg_match_all( '/<h([2-6])\b[^>]*>.*?<\/h\1>/is', $content, $matches, PREG_OFFSET_CAPTURE ) ) {
		return true;
	}

	$count = count( $matches[0] );
	for ( $index = 0; $index < $count; $index++ ) {
		$heading_html = $matches[0][ $index ][0];
		$start        = $matches[0][ $index ][1] + strlen( $heading_html );
		$end          = $index + 1 < $count ? $matches[0][ $index + 1 ][1] : strlen( $content );
		$section      = substr( $content, $start, $end - $start );
		$plain        = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( preg_replace( '/<!--.*?-->/s', '', $section ) ) ) );
		$has_media    = false !== stripos( $section, '<img' ) || false !== stripos( $section, 'wp-recipe-maker' ) || false !== stripos( $section, 'wprm-recipe' );

		if ( '' === $plain && ! $has_media ) {
			return true;
		}
	}
	return false;
}

function nkt_content_audit_internal_link_count( $content ) {
	$urls      = array();
	$site_host = strtolower( (string) wp_parse_url( home_url( '/' ), PHP_URL_HOST ) );

	if ( preg_match_all( '/\bhref=["\']([^"\']+)["\']/i', (string) $content, $matches ) ) {
		foreach ( $matches[1] as $url ) {
			$url = html_entity_decode( trim( $url ), ENT_QUOTES, 'UTF-8' );
			if ( '' === $url || '#' === $url[0] || preg_match( '/^(?:mailto|tel|javascript):/i', $url ) ) {
				continue;
			}

			$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
			if ( '' === $host || $site_host === $host ) {
				$normalised          = preg_replace( '/#.*$/', '', $url );
				$urls[ $normalised ] = true;
			}
		}
	}

	return count( $urls );
}

function nkt_content_audit_content_images_have_alt( $content ) {
	if ( ! preg_match_all( '/<img\b[^>]*>/i', (string) $content, $images ) ) {
		return true;
	}

	foreach ( $images[0] as $image ) {
		if ( ! preg_match( '/\balt=["\']([^"\']*)["\']/i', $image, $alt ) || '' === trim( html_entity_decode( $alt[1], ENT_QUOTES, 'UTF-8' ) ) ) {
			return false;
		}
	}
	return true;
}

function nkt_content_audit_recipe_object( $recipe_id ) {
	if ( class_exists( 'WPRM_Recipe_Manager' ) && is_callable( array( 'WPRM_Recipe_Manager', 'get_recipe' ) ) ) {
		try {
			return WPRM_Recipe_Manager::get_recipe( $recipe_id );
		} catch ( Throwable $exception ) {
			return null;
		}
	}
	return null;
}

function nkt_content_audit_recipe_value( $recipe, $methods ) {
	if ( ! is_object( $recipe ) ) {
		return null;
	}
	foreach ( (array) $methods as $method ) {
		if ( is_callable( array( $recipe, $method ) ) ) {
			try {
				return $recipe->{$method}();
			} catch ( Throwable $exception ) {
				continue;
			}
		}
	}
	return null;
}

function nkt_content_audit_recipe_meta_value( $recipe_id, $keys ) {
	foreach ( (array) $keys as $key ) {
		$value = get_post_meta( $recipe_id, $key, true );
		if ( '' !== $value && null !== $value && array() !== $value ) {
			return $value;
		}
	}
	return null;
}

function nkt_content_audit_recipe_data( $recipe_id ) {
	$recipe       = nkt_content_audit_recipe_object( $recipe_id );
	$image        = nkt_content_audit_recipe_value( $recipe, array( 'image_id', 'image_url', 'image' ) );
	$summary      = nkt_content_audit_recipe_value( $recipe, array( 'summary' ) );
	$servings     = nkt_content_audit_recipe_value( $recipe, array( 'servings' ) );
	$prep_time    = nkt_content_audit_recipe_value( $recipe, array( 'prep_time' ) );
	$cook_time    = nkt_content_audit_recipe_value( $recipe, array( 'cook_time' ) );
	$custom_time  = nkt_content_audit_recipe_value( $recipe, array( 'custom_time' ) );
	$total_time   = nkt_content_audit_recipe_value( $recipe, array( 'total_time' ) );
	$ingredients  = nkt_content_audit_recipe_value( $recipe, array( 'ingredients' ) );
	$instructions = nkt_content_audit_recipe_value( $recipe, array( 'instructions' ) );

	if ( null === $image ) {
		$image = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_image_id', 'wprm_recipe_image_id' ) );
	}
	if ( null === $summary ) {
		$summary = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_summary', 'wprm_recipe_summary' ) );
	}
	if ( null === $servings ) {
		$servings = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_servings', 'wprm_recipe_servings' ) );
	}
	if ( null === $prep_time ) {
		$prep_time = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_prep_time', 'wprm_recipe_prep_time' ) );
	}
	if ( null === $cook_time ) {
		$cook_time = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_cook_time', 'wprm_recipe_cook_time' ) );
	}
	if ( null === $custom_time ) {
		$custom_time = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_custom_time', 'wprm_recipe_custom_time' ) );
	}
	if ( null === $total_time ) {
		$total_time = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_total_time', 'wprm_recipe_total_time' ) );
	}
	if ( null === $ingredients ) {
		$ingredients = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_ingredients', 'wprm_recipe_ingredients' ) );
	}
	if ( null === $instructions ) {
		$instructions = nkt_content_audit_recipe_meta_value( $recipe_id, array( 'wprm_instructions', 'wprm_recipe_instructions' ) );
	}

	$image_id = absint( is_scalar( $image ) ? $image : 0 );
	return array(
		'image'        => $image_id > 0 || has_post_thumbnail( $recipe_id ) || ( is_string( $image ) && 0 === strpos( $image, 'http' ) ),
		'summary'      => '' !== trim( wp_strip_all_tags( is_scalar( $summary ) ? (string) $summary : '' ) ),
		'yield'        => '' !== trim( is_scalar( $servings ) ? (string) $servings : '' ),
		'timing'       => array_sum( array_map( 'floatval', array( $prep_time, $cook_time, $custom_time, $total_time ) ) ) > 0,
		'ingredients'  => ! empty( $ingredients ),
		'instructions' => ! empty( $instructions ),
	);
}

function nkt_get_content_audit( $post_id ) {
	$post_id        = absint( $post_id );
	$is_note        = nkt_content_audit_is_note( $post_id );
	$thumbnail_id   = get_post_thumbnail_id( $post_id );
	$image_alt      = $thumbnail_id ? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) : '';
	$categories     = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );
	$useful_cats    = array_filter( $categories, static fn( $term ) => $term instanceof WP_Term && 'uncategorized' !== $term->slug );
	$content        = (string) get_post_field( 'post_content', $post_id );
	$plain_content  = wp_strip_all_tags( strip_shortcodes( $content ) );
	$word_count     = str_word_count( $plain_content );
	$minimum_words  = $is_note ? 300 : 500;
	$headings       = nkt_content_audit_headings( $content );
	$internal_links = nkt_content_audit_internal_link_count( $content );
	$legacy_branding = false !== stripos( $plain_content, 'The Gourmet Larder' ) || false !== stripos( $plain_content, '@thegourmetlarder' ) || false !== stripos( $plain_content, '#thegourmetlarder' );

	$checks = array(
		'featured_image'    => array( 'label' => __( 'Featured image', 'larder' ), 'complete' => (bool) $thumbnail_id, 'critical' => true ),
		'image_alt'         => array( 'label' => __( 'Featured image alt text', 'larder' ), 'complete' => ! $thumbnail_id || '' !== $image_alt, 'critical' => false ),
		'content_image_alt' => array( 'label' => __( 'Alt text on every article image', 'larder' ), 'complete' => nkt_content_audit_content_images_have_alt( $content ), 'critical' => false ),
		'excerpt'           => array( 'label' => __( 'Editorial excerpt', 'larder' ), 'complete' => '' !== trim( (string) get_post_field( 'post_excerpt', $post_id ) ), 'critical' => true ),
		'category'          => array( 'label' => __( 'Useful category', 'larder' ), 'complete' => ! empty( $useful_cats ), 'critical' => true ),
		'content_depth'     => array( 'label' => sprintf( __( 'At least %d useful words', 'larder' ), $minimum_words ), 'complete' => $word_count >= $minimum_words, 'critical' => false ),
		'internal_links'    => array( 'label' => sprintf( __( 'At least %d contextual internal links', 'larder' ), $is_note ? 1 : 2 ), 'complete' => $internal_links >= ( $is_note ? 1 : 2 ), 'critical' => false ),
		'heading_hierarchy' => array( 'label' => __( 'Clean H2–H4 heading hierarchy', 'larder' ), 'complete' => nkt_content_audit_heading_hierarchy_valid( $content ), 'critical' => false ),
		'nonempty_sections' => array( 'label' => __( 'No empty or duplicated-looking sections', 'larder' ), 'complete' => ! nkt_content_audit_has_empty_sections( $content ), 'critical' => false ),
		'current_branding'  => array( 'label' => __( 'No legacy Gourmet Larder wording', 'larder' ), 'complete' => ! $legacy_branding, 'critical' => false ),
	);

	if ( defined( 'WPSEO_VERSION' ) ) {
		$checks['yoast_focus_keyphrase'] = array(
			'label'    => __( 'Yoast focus keyphrase', 'larder' ),
			'complete' => '' !== trim( (string) get_post_meta( $post_id, '_yoast_wpseo_focuskw', true ) ),
			'critical' => false,
		);
		$checks['yoast_seo_title'] = array(
			'label'    => __( 'Yoast SEO title reviewed', 'larder' ),
			'complete' => '' !== trim( (string) get_post_meta( $post_id, '_yoast_wpseo_title', true ) ),
			'critical' => false,
		);
		$checks['yoast_meta_description'] = array(
			'label'    => __( 'Yoast meta description', 'larder' ),
			'complete' => '' !== trim( (string) get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ),
			'critical' => false,
		);
	}

	if ( ! $is_note ) {
		$recipe_ids  = nkt_content_audit_recipe_ids( $content );
		$recipe_data = 1 === count( $recipe_ids ) ? nkt_content_audit_recipe_data( $recipe_ids[0] ) : array(
			'image' => false,
			'summary' => false,
			'yield' => false,
			'timing' => false,
			'ingredients' => false,
			'instructions' => false,
		);

		$checks['recipe_card'] = array( 'label' => __( 'Exactly one WP Recipe Maker card', 'larder' ), 'complete' => 1 === count( $recipe_ids ), 'critical' => true );
		$checks['recipe_image'] = array( 'label' => __( 'Recipe-card image', 'larder' ), 'complete' => $recipe_data['image'], 'critical' => true );
		$checks['recipe_summary'] = array( 'label' => __( 'Recipe-card summary', 'larder' ), 'complete' => $recipe_data['summary'], 'critical' => true );
		$checks['recipe_yield'] = array( 'label' => __( 'Recipe yield or servings', 'larder' ), 'complete' => $recipe_data['yield'], 'critical' => true );
		$checks['recipe_timing'] = array( 'label' => __( 'Recipe preparation or cooking times', 'larder' ), 'complete' => $recipe_data['timing'], 'critical' => true );
		$checks['recipe_method'] = array( 'label' => __( 'Complete recipe ingredients and instructions', 'larder' ), 'complete' => $recipe_data['ingredients'] && $recipe_data['instructions'], 'critical' => true );
		$checks['section_highlights'] = array( 'label' => __( 'Why You’ll Love This Recipe or Recipe Highlights section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'why you’ll love', "why you'll love", 'recipe highlights' ) ), 'critical' => false );
		$checks['section_ingredients'] = array( 'label' => __( 'Ingredients and substitutions section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'ingredients' ) ), 'critical' => false );
		$checks['section_method'] = array( 'label' => __( 'How to make or method section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'how to make', 'method', 'instructions' ) ), 'critical' => false );
		$checks['section_tips'] = array( 'label' => __( 'Nigel’s Recipe Tips section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'recipe tips', 'nigel’s tips', "nigel's tips" ) ), 'critical' => false );
		$checks['section_storage'] = array( 'label' => __( 'Storage and freezing section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'storage', 'freezing' ) ), 'critical' => false );
		$checks['section_nutrition'] = array( 'label' => __( 'Nutrition section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'nutrition' ) ), 'critical' => false );
		$checks['section_tools'] = array( 'label' => __( 'Tools or equipment section', 'larder' ), 'complete' => nkt_content_audit_has_heading( $headings, array( 'tools', 'equipment' ) ), 'critical' => false );
	}

	$issues   = array();
	$critical = array();
	foreach ( $checks as $key => $check ) {
		if ( ! $check['complete'] ) {
			$issues[ $key ] = $check['label'];
			if ( $check['critical'] ) {
				$critical[ $key ] = $check['label'];
			}
		}
	}

	return array(
		'post_id'         => $post_id,
		'type'            => $is_note ? 'note' : 'recipe',
		'type_label'      => $is_note ? __( 'Kitchen Note', 'larder' ) : __( 'Recipe', 'larder' ),
		'checks'          => $checks,
		'issues'          => $issues,
		'critical_issues' => $critical,
		'ready'           => empty( $issues ),
		'publish_ready'   => empty( $critical ),
		'word_count'      => $word_count,
		'score'           => count( $checks ) ? (int) round( ( ( count( $checks ) - count( $issues ) ) / count( $checks ) ) * 100 ) : 100,
	);
}

function nkt_get_content_audit_post_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = get_posts(
			array(
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'fields'                 => 'ids',
				'orderby'                => 'title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);
		$ids = array_map( 'absint', $ids );
	}
	return $ids;
}

function nkt_get_content_audit_summary() {
	$summary = array( 'total' => 0, 'recipes' => 0, 'notes' => 0, 'ready' => 0, 'needs_attention' => 0, 'critical' => 0 );
	foreach ( nkt_get_content_audit_post_ids() as $post_id ) {
		$audit = nkt_get_content_audit( $post_id );
		++$summary['total'];
		++$summary[ 'note' === $audit['type'] ? 'notes' : 'recipes' ];
		++$summary[ $audit['ready'] ? 'ready' : 'needs_attention' ];
		if ( ! $audit['publish_ready'] ) {
			++$summary['critical'];
		}
	}
	return $summary;
}

function nkt_register_content_audit_page() {
	add_management_page( __( 'Recipe Content Audit', 'larder' ), __( 'Recipe Content Audit', 'larder' ), 'edit_posts', 'nkt-content-audit', 'nkt_render_content_audit_page' );
}
add_action( 'admin_menu', 'nkt_register_content_audit_page' );

function nkt_content_audit_admin_assets( $hook ) {
	if ( in_array( $hook, array( 'tools_page_nkt-content-audit', 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_style( 'nkt-content-audit-admin', get_template_directory_uri() . '/assets/css/admin-content-audit.css', array(), wp_get_theme()->get( 'Version' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'nkt_content_audit_admin_assets' );

function nkt_content_audit_badge( $complete, $label ) {
	$class = $complete ? 'nkt-audit-badge--good' : 'nkt-audit-badge--todo';
	printf( '<span class="nkt-audit-badge %1$s"><span aria-hidden="true">%2$s</span>%3$s</span>', esc_attr( $class ), $complete ? '✓' : '•', esc_html( $label ) );
}
