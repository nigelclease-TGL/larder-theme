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
