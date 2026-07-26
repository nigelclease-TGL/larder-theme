<?php
/**
 * Pinterest image dimension helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Confirm that an attachment is suitable for automatic Pinterest use.
 *
 * Automatic detection must never select a landscape banner. A suitable
 * image must be portrait and at least 20 percent taller than it is wide.
 * Manually selected images are not restricted by this helper.
 *
 * @param int $attachment_id Attachment ID.
 * @return bool
 */
function nkt_is_portrait_pinterest_attachment( $attachment_id ) {
	$metadata = wp_get_attachment_metadata( absint( $attachment_id ) );

	if ( ! is_array( $metadata ) ) {
		return false;
	}

	$width  = isset( $metadata['width'] ) ? absint( $metadata['width'] ) : 0;
	$height = isset( $metadata['height'] ) ? absint( $metadata['height'] ) : 0;

	if ( ! $width || ! $height ) {
		return false;
	}

	return $height >= (int) ceil( $width * 1.2 );
}

/**
 * Find the first portrait Pinterest image already embedded in a post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function nkt_find_portrait_pinterest_image_id( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );

	if ( '' === trim( $content ) ) {
		return 0;
	}

	$signals = array(
		'PIN THIS RECIPE ON PINTEREST',
		'PIN THIS RECIPE',
		'PIN FOR LATER',
		'SAVE THIS RECIPE ON PINTEREST',
		'SAVE FOR LATER',
	);

	foreach ( $signals as $signal ) {
		$position = stripos( $content, $signal );
		if ( false === $position ) {
			continue;
		}

		$section = substr( $content, $position, 8000 );
		if ( ! preg_match_all( '/<img\b[^>]*>/i', $section, $matches ) ) {
			continue;
		}

		foreach ( $matches[0] as $image_tag ) {
			$image_id = nkt_pinterest_attachment_id_from_tag( $image_tag );
			if ( $image_id && nkt_is_portrait_pinterest_attachment( $image_id ) ) {
				return $image_id;
			}
		}
	}

	if ( preg_match_all( '/<img\b[^>]*>/i', $content, $matches ) ) {
		foreach ( $matches[0] as $image_tag ) {
			if ( ! preg_match( '/pinterest|pin[-_ ]?(?:image|graphic|later)|save[-_ ]?pin/i', $image_tag ) ) {
				continue;
			}

			$image_id = nkt_pinterest_attachment_id_from_tag( $image_tag );
			if ( $image_id && nkt_is_portrait_pinterest_attachment( $image_id ) ) {
				return $image_id;
			}
		}
	}

	return 0;
}

/**
 * Supply a safe automatically detected Pinterest image as though it were the
 * saved override. This prevents the older broad detector from selecting a
 * landscape banner while preserving genuine manual selections.
 *
 * @param mixed  $value     Filtered metadata value.
 * @param int    $object_id Post ID.
 * @param string $meta_key  Meta key.
 * @param bool   $single    Whether a single value was requested.
 * @return mixed
 */
function nkt_filter_pinterest_image_meta( $value, $object_id, $meta_key, $single ) {
	if ( '_nkt_pinterest_image_id' !== $meta_key || 'post' !== get_post_type( $object_id ) ) {
		return $value;
	}

	global $wpdb;
	$manual_id = absint(
		$wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s ORDER BY meta_id DESC LIMIT 1",
				$object_id,
				'_nkt_pinterest_image_id'
			)
		)
	);

	if ( $manual_id ) {
		return $single ? $manual_id : array( $manual_id );
	}

	$detected_id = nkt_find_portrait_pinterest_image_id( $object_id );
	if ( ! $detected_id ) {
		return $value;
	}

	return $single ? $detected_id : array( $detected_id );
}
add_filter( 'get_post_metadata', 'nkt_filter_pinterest_image_meta', 5, 4 );
