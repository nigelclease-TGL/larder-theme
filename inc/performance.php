<?php
/**
 * Small performance and content-quality enhancements.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add early connections for the hosted font files.
 *
 * @param array  $urls Resource URLs.
 * @param string $relation_type Hint type.
 * @return array
 */
function larder_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'larder_resource_hints', 10, 2 );

/**
 * Supply sensible loading attributes while keeping visible brand images eager.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function larder_image_attributes( $attr ) {
	$attr['decoding'] = 'async';
	$class            = isset( $attr['class'] ) ? (string) $attr['class'] : '';

	if ( false !== strpos( $class, 'custom-logo' ) ) {
		$attr['loading']       = 'eager';
		$attr['fetchpriority'] = 'high';
		return $attr;
	}

	if ( empty( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'larder_image_attributes' );

function larder_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'larder_excerpt_length', 99 );

function larder_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'larder_excerpt_more' );

function larder_body_classes( $classes ) {
	if ( is_singular( 'post' ) ) {
		$classes[] = 'is-recipe-post';
	}
	if ( function_exists( 'nkt_is_staging_site' ) && nkt_is_staging_site() ) {
		$classes[] = 'is-staging-site';
	}
	return $classes;
}
add_filter( 'body_class', 'larder_body_classes' );

/**
 * Let WordPress load block styles only when their blocks are present.
 *
 * @return bool
 */
function nkt_use_separate_core_block_assets() {
	return true;
}
add_filter( 'should_load_separate_core_block_assets', 'nkt_use_separate_core_block_assets' );

/**
 * Remove legacy payloads that are not used by this public theme.
 */
function nkt_remove_legacy_head_assets() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'nkt_remove_legacy_head_assets' );

/**
 * Prevent self-pingbacks when internal links are added to a post.
 *
 * @param string[] $links Pingback URLs.
 */
function nkt_disable_self_pingbacks( &$links ) {
	$home = home_url( '/' );
	foreach ( $links as $key => $link ) {
		if ( str_starts_with( $link, $home ) ) {
			unset( $links[ $key ] );
		}
	}
}
add_action( 'pre_ping', 'nkt_disable_self_pingbacks' );

/**
 * Remove the former Gourmet Larder promotional ending saved after recipe cards.
 *
 * Older posts contain Pinterest graphics, social prompts, repeated related links
 * and a signature after the WP Recipe Maker card. The new theme supplies its own
 * newsletter, sharing, related-recipes and comments sections, so that saved tail
 * must not be rendered twice. Post content in the database is left unchanged.
 *
 * @param string $content Filtered post content.
 * @return string
 */
function nkt_remove_legacy_recipe_tail( $content ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( false === stripos( $content, 'wprm-recipe-container' ) ) {
		return $content;
	}

	$token_pattern = '/<!--.*?-->|<![^>]*>|<\/?[a-zA-Z][^>]*>/s';
	if ( ! preg_match_all( $token_pattern, $content, $matches, PREG_OFFSET_CAPTURE ) ) {
		return $content;
	}

	$void_elements = array( 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr' );
	$stack         = array();
	$target_id     = null;
	$element_id    = 0;

	foreach ( $matches[0] as $match ) {
		$token  = $match[0];
		$offset = (int) $match[1];
		$length = strlen( $token );

		if ( str_starts_with( $token, '<!--' ) || str_starts_with( $token, '<!' ) ) {
			continue;
		}

		if ( ! preg_match( '/^<\s*(\/?)\s*([a-zA-Z0-9:-]+)/', $token, $tag_match ) ) {
			continue;
		}

		$is_closing = '/' === $tag_match[1];
		$tag_name   = strtolower( $tag_match[2] );

		if ( $is_closing ) {
			$matched_index = null;
			for ( $index = count( $stack ) - 1; $index >= 0; $index-- ) {
				if ( $stack[ $index ]['tag'] === $tag_name ) {
					$matched_index = $index;
					break;
				}
			}

			if ( null === $matched_index ) {
				continue;
			}

			$closed_element = $stack[ $matched_index ];
			$stack          = array_slice( $stack, 0, $matched_index );

			if ( null === $target_id || $closed_element['id'] !== $target_id ) {
				continue;
			}

			$recipe_end = $offset + $length;
			$tail       = substr( $content, $recipe_end );
			$tail_text  = strtolower( wp_strip_all_tags( $tail ) );
			$markers    = array(
				'please let me know how it turned out',
				'hungry for more',
				'pin this recipe',
				'happy baking',
				'take a look at my inspirational',
				'don’t forget to subscribe',
				"don't forget to subscribe",
			);
			$legacy_tail_found = false;

			foreach ( $markers as $marker ) {
				if ( false !== strpos( $tail_text, $marker ) ) {
					$legacy_tail_found = true;
					break;
				}
			}

			if ( ! $legacy_tail_found ) {
				return $content;
			}

			$clean_content = substr( $content, 0, $recipe_end );
			for ( $index = count( $stack ) - 1; $index >= 0; $index-- ) {
				$clean_content .= '</' . $stack[ $index ]['tag'] . '>';
			}

			return $clean_content;
		}

		$is_self_closing = str_ends_with( rtrim( $token ), '/>' ) || in_array( $tag_name, $void_elements, true );
		if ( $is_self_closing ) {
			continue;
		}

		$element_id++;
		$is_recipe_container = preg_match( '/\bid\s*=\s*(["\'])wprm-recipe-container-[^"\']*\1/i', $token )
			|| preg_match( '/\bclass\s*=\s*(["\'])[^"\']*\bwprm-recipe-container\b[^"\']*\1/i', $token );

		$stack[] = array(
			'tag' => $tag_name,
			'id'  => $element_id,
		);

		if ( null === $target_id && $is_recipe_container ) {
			$target_id = $element_id;
		}
	}

	return $content;
}
add_filter( 'the_content', 'nkt_remove_legacy_recipe_tail', 99 );
