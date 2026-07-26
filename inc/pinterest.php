<?php
/**
 * Pinterest image support for recipe posts.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolve an attachment ID from an image tag.
 *
 * @param string $image_tag Complete HTML image tag.
 * @return int
 */
function nkt_pinterest_attachment_id_from_tag( $image_tag ) {
	if ( preg_match( '/\bwp-image-(\d+)\b/i', $image_tag, $class_match ) ) {
		return absint( $class_match[1] );
	}

	if ( preg_match( '/\b(?:data-id|data-attachment-id)=(?:"|\')(\d+)(?:"|\')/i', $image_tag, $id_match ) ) {
		return absint( $id_match[1] );
	}

	if ( ! preg_match( '/\bsrc=(?:"|\')([^"\']+)(?:"|\')/i', $image_tag, $src_match ) ) {
		return 0;
	}

	$src = html_entity_decode( $src_match[1], ENT_QUOTES, get_bloginfo( 'charset' ) );
	$id  = attachment_url_to_postid( $src );

	if ( $id ) {
		return absint( $id );
	}

	// WordPress often stores a resized image URL in post content. Retry with
	// the generated -WIDTHxHEIGHT suffix removed from the filename.
	$original_src = preg_replace( '/-\d+x\d+(?=\.[a-z0-9]+(?:\?.*)?$)/i', '', $src );

	return $original_src ? absint( attachment_url_to_postid( $original_src ) ) : 0;
}

/**
 * Detect a legacy Pinterest image already embedded in a recipe post.
 *
 * Older Gourmet Larder posts generally contain a heading such as
 * "PIN THIS RECIPE ON PINTEREST" or "PIN FOR LATER", followed by the
 * dedicated vertical image. This detector uses that established structure
 * and avoids choosing unrelated article photography.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function nkt_detect_existing_pinterest_image_id( $post_id ) {
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
		$position = stripos( wp_strip_all_tags( $content ), $signal );

		if ( false === $position ) {
			continue;
		}

		// Work with the original HTML. Locate the signal case-insensitively and
		// inspect the following section for the first embedded image.
		$html_position = stripos( $content, $signal );
		if ( false === $html_position ) {
			continue;
		}

		$section = substr( $content, $html_position, 6000 );
		if ( preg_match( '/<img\b[^>]*>/i', $section, $image_match ) ) {
			$image_id = nkt_pinterest_attachment_id_from_tag( $image_match[0] );
			if ( $image_id ) {
				return $image_id;
			}
		}
	}

	// Secondary support for images explicitly named or classed as Pinterest
	// assets, even when the old heading wording is absent.
	if ( preg_match_all( '/<img\b[^>]*>/i', $content, $image_matches ) ) {
		foreach ( $image_matches[0] as $image_tag ) {
			if ( ! preg_match( '/pinterest|pin[-_ ]?(?:image|graphic|later)|save[-_ ]?pin/i', $image_tag ) ) {
				continue;
			}

			$image_id = nkt_pinterest_attachment_id_from_tag( $image_tag );
			if ( $image_id ) {
				return $image_id;
			}
		}
	}

	return 0;
}

/**
 * Return the Pinterest image attachment ID for a post.
 *
 * A manually selected image always takes priority. Existing Pinterest images
 * embedded in legacy recipe content are detected automatically as a fallback.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function nkt_get_pinterest_image_id( $post_id ) {
	$manual_image_id = absint( get_post_meta( $post_id, '_nkt_pinterest_image_id', true ) );

	return $manual_image_id ? $manual_image_id : nkt_detect_existing_pinterest_image_id( $post_id );
}

/**
 * Register the Pinterest image meta box.
 */
function nkt_register_pinterest_image_meta_box() {
	add_meta_box(
		'nkt-pinterest-image',
		__( 'Pinterest image', 'larder' ),
		'nkt_render_pinterest_image_meta_box',
		'post',
		'side',
		'low'
	);
}
add_action( 'add_meta_boxes', 'nkt_register_pinterest_image_meta_box' );

/**
 * Render the Pinterest image selector.
 *
 * @param WP_Post $post Current post.
 */
function nkt_render_pinterest_image_meta_box( $post ) {
	$manual_id  = absint( get_post_meta( $post->ID, '_nkt_pinterest_image_id', true ) );
	$image_id   = nkt_get_pinterest_image_id( $post->ID );
	$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
	$is_detected = $image_id && ! $manual_id;

	wp_nonce_field( 'nkt_save_pinterest_image', 'nkt_pinterest_image_nonce' );
	?>
	<div class="nkt-pinterest-image-control">
		<p><?php esc_html_e( 'The theme automatically uses an existing image beneath a “Pin this recipe” or “Pin for later” section. You can choose a different vertical 2:3 image here at any time.', 'larder' ); ?></p>
		<?php if ( $is_detected ) : ?>
			<p><strong><?php esc_html_e( 'Existing Pinterest image detected automatically.', 'larder' ); ?></strong></p>
		<?php endif; ?>
		<div class="nkt-pinterest-image-preview" style="margin-bottom:10px;">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="display:block;width:100%;height:auto;max-height:260px;object-fit:contain;">
			<?php endif; ?>
		</div>
		<input type="hidden" name="nkt_pinterest_image_id" value="<?php echo esc_attr( $manual_id ); ?>">
		<p>
			<button type="button" class="button nkt-select-pinterest-image"><?php esc_html_e( 'Choose different image', 'larder' ); ?></button>
			<button type="button" class="button-link-delete nkt-remove-pinterest-image"<?php echo $manual_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove override', 'larder' ); ?></button>
		</p>
	</div>
	<?php
}

/**
 * Save the selected Pinterest image.
 *
 * @param int $post_id Post ID.
 */
function nkt_save_pinterest_image_meta( $post_id ) {
	if ( ! isset( $_POST['nkt_pinterest_image_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nkt_pinterest_image_nonce'] ) ), 'nkt_save_pinterest_image' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$image_id = isset( $_POST['nkt_pinterest_image_id'] ) ? absint( $_POST['nkt_pinterest_image_id'] ) : 0;

	if ( $image_id ) {
		update_post_meta( $post_id, '_nkt_pinterest_image_id', $image_id );
	} else {
		delete_post_meta( $post_id, '_nkt_pinterest_image_id' );
	}
}
add_action( 'save_post_post', 'nkt_save_pinterest_image_meta' );

/**
 * Load the WordPress media frame on post-editing screens.
 *
 * @param string $hook_suffix Current admin page.
 */
function nkt_enqueue_pinterest_admin_media( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'nkt_enqueue_pinterest_admin_media' );

/**
 * Add the small media-selector script to post-editing screens.
 */
function nkt_pinterest_admin_script() {
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}
	?>
	<script>
	(function($){
		'use strict';
		$(document).on('click', '.nkt-select-pinterest-image', function(event){
			event.preventDefault();
			var control = $(this).closest('.nkt-pinterest-image-control');
			var frame = wp.media({
				title: <?php echo wp_json_encode( __( 'Choose Pinterest image', 'larder' ) ); ?>,
				button: { text: <?php echo wp_json_encode( __( 'Use this image', 'larder' ) ); ?> },
				library: { type: 'image' },
				multiple: false
			});
			frame.on('select', function(){
				var image = frame.state().get('selection').first().toJSON();
				var preview = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
				control.find('input[name="nkt_pinterest_image_id"]').val(image.id);
				control.find('.nkt-pinterest-image-preview').html('<img src="' + preview + '" alt="" style="display:block;width:100%;height:auto;max-height:260px;object-fit:contain;">');
				control.find('.nkt-remove-pinterest-image').prop('hidden', false);
			});
			frame.open();
		});
		$(document).on('click', '.nkt-remove-pinterest-image', function(event){
			event.preventDefault();
			var control = $(this).closest('.nkt-pinterest-image-control');
			control.find('input[name="nkt_pinterest_image_id"]').val('');
			control.find('.nkt-pinterest-image-preview').empty();
			$(this).prop('hidden', true);
		});
	})(jQuery);
	</script>
	<?php
}
add_action( 'admin_footer-post.php', 'nkt_pinterest_admin_script' );
add_action( 'admin_footer-post-new.php', 'nkt_pinterest_admin_script' );
