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
 * Return the dedicated Pinterest image attachment ID for a post.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function nkt_get_pinterest_image_id( $post_id ) {
	return absint( get_post_meta( $post_id, '_nkt_pinterest_image_id', true ) );
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
	$image_id  = nkt_get_pinterest_image_id( $post->ID );
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

	wp_nonce_field( 'nkt_save_pinterest_image', 'nkt_pinterest_image_nonce' );
	?>
	<div class="nkt-pinterest-image-control">
		<p><?php esc_html_e( 'Choose a vertical 2:3 image, ideally 1000 × 1500 px. It will be used by the Pinterest sharing link and the save panel near the end of the recipe.', 'larder' ); ?></p>
		<div class="nkt-pinterest-image-preview" style="margin-bottom:10px;">
			<?php if ( $image_url ) : ?>
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" style="display:block;width:100%;height:auto;max-height:260px;object-fit:contain;">
			<?php endif; ?>
		</div>
		<input type="hidden" name="nkt_pinterest_image_id" value="<?php echo esc_attr( $image_id ); ?>">
		<p>
			<button type="button" class="button nkt-select-pinterest-image"><?php esc_html_e( 'Choose image', 'larder' ); ?></button>
			<button type="button" class="button-link-delete nkt-remove-pinterest-image"<?php echo $image_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'larder' ); ?></button>
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
