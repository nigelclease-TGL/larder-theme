<?php
/**
 * Retailer-independent product platform.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_PRODUCT_PLATFORM_VERSION = '2.1.0-alpha.1';
const NKT_PRODUCT_POST_TYPE        = 'nkt_product';
const NKT_PRODUCT_CATEGORY_TAX     = 'nkt_product_category';
const NKT_PRODUCT_BRAND_TAX        = 'nkt_product_brand';

/**
 * Register the Kitchen Products post type, taxonomies and public REST meta.
 */
function nkt_register_product_platform() {
	$product_labels = array(
		'name'                  => __( 'Kitchen Products', 'larder' ),
		'singular_name'         => __( 'Kitchen Product', 'larder' ),
		'menu_name'             => __( 'Kitchen Products', 'larder' ),
		'name_admin_bar'        => __( 'Kitchen Product', 'larder' ),
		'add_new'               => __( 'Add New', 'larder' ),
		'add_new_item'          => __( 'Add New Kitchen Product', 'larder' ),
		'new_item'              => __( 'New Kitchen Product', 'larder' ),
		'edit_item'             => __( 'Edit Kitchen Product', 'larder' ),
		'view_item'             => __( 'View Kitchen Product', 'larder' ),
		'all_items'             => __( 'All Kitchen Products', 'larder' ),
		'search_items'          => __( 'Search Kitchen Products', 'larder' ),
		'not_found'             => __( 'No kitchen products found.', 'larder' ),
		'not_found_in_trash'    => __( 'No kitchen products found in Trash.', 'larder' ),
		'featured_image'        => __( 'Primary product image', 'larder' ),
		'set_featured_image'    => __( 'Set primary product image', 'larder' ),
		'remove_featured_image' => __( 'Remove primary product image', 'larder' ),
		'use_featured_image'    => __( 'Use as primary product image', 'larder' ),
		'archives'              => __( 'Shop My Kitchen', 'larder' ),
	);

	register_post_type(
		NKT_PRODUCT_POST_TYPE,
		array(
			'labels'             => $product_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-cart',
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'has_archive'        => 'shop-my-kitchen',
			'hierarchical'       => false,
			'rewrite'            => array(
				'slug'       => 'shop-my-kitchen',
				'with_front' => false,
			),
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'taxonomies'         => array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ),
			'menu_position'      => 26,
		)
	);

	$category_labels = array(
		'name'              => __( 'Product Categories', 'larder' ),
		'singular_name'     => __( 'Product Category', 'larder' ),
		'search_items'      => __( 'Search Product Categories', 'larder' ),
		'all_items'         => __( 'All Product Categories', 'larder' ),
		'parent_item'       => __( 'Parent Product Category', 'larder' ),
		'parent_item_colon' => __( 'Parent Product Category:', 'larder' ),
		'edit_item'         => __( 'Edit Product Category', 'larder' ),
		'update_item'       => __( 'Update Product Category', 'larder' ),
		'add_new_item'      => __( 'Add New Product Category', 'larder' ),
		'new_item_name'     => __( 'New Product Category Name', 'larder' ),
		'menu_name'         => __( 'Product Categories', 'larder' ),
	);

	register_taxonomy(
		NKT_PRODUCT_CATEGORY_TAX,
		array( NKT_PRODUCT_POST_TYPE ),
		array(
			'labels'            => $category_labels,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'         => 'shop-my-kitchen/category',
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);

	$brand_labels = array(
		'name'                       => __( 'Product Brands', 'larder' ),
		'singular_name'              => __( 'Product Brand', 'larder' ),
		'search_items'               => __( 'Search Product Brands', 'larder' ),
		'popular_items'              => __( 'Popular Product Brands', 'larder' ),
		'all_items'                  => __( 'All Product Brands', 'larder' ),
		'edit_item'                  => __( 'Edit Product Brand', 'larder' ),
		'update_item'                => __( 'Update Product Brand', 'larder' ),
		'add_new_item'               => __( 'Add New Product Brand', 'larder' ),
		'new_item_name'              => __( 'New Product Brand Name', 'larder' ),
		'separate_items_with_commas' => __( 'Separate product brands with commas', 'larder' ),
		'add_or_remove_items'        => __( 'Add or remove product brands', 'larder' ),
		'choose_from_most_used'      => __( 'Choose from the most used product brands', 'larder' ),
		'menu_name'                  => __( 'Product Brands', 'larder' ),
	);

	register_taxonomy(
		NKT_PRODUCT_BRAND_TAX,
		array( NKT_PRODUCT_POST_TYPE ),
		array(
			'labels'            => $brand_labels,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'query_var'         => true,
			'rewrite'           => array(
				'slug'       => 'shop-my-kitchen/brand',
				'with_front' => false,
			),
		)
	);

	register_post_meta(
		NKT_PRODUCT_POST_TYPE,
		'_nkt_product_gallery_ids',
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'sanitize_callback' => 'nkt_sanitize_product_gallery_ids',
			'auth_callback'     => 'nkt_product_meta_auth_callback',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'integer' ),
					'default' => array(),
				),
			),
		)
	);

	register_post_meta(
		NKT_PRODUCT_POST_TYPE,
		'_nkt_product_retailers',
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'sanitize_callback' => 'nkt_sanitize_product_retailers',
			'auth_callback'     => 'nkt_product_meta_auth_callback',
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'array',
					'default' => array(),
					'items'   => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'retailer'    => array( 'type' => 'string' ),
							'url'         => array( 'type' => 'string', 'format' => 'uri' ),
							'button_text' => array( 'type' => 'string' ),
							'is_primary'  => array( 'type' => 'boolean' ),
						),
					),
				),
			),
		)
	);
}
add_action( 'init', 'nkt_register_product_platform' );

/**
 * Allow editors to update protected product meta.
 *
 * @return bool
 */
function nkt_product_meta_auth_callback() {
	return current_user_can( 'edit_posts' );
}

/**
 * Flush product rewrite rules once per platform schema version.
 */
function nkt_maybe_flush_product_rewrites() {
	if ( get_option( 'nkt_product_platform_rewrite_version' ) === NKT_PRODUCT_PLATFORM_VERSION ) {
		return;
	}

	nkt_register_product_platform();
	flush_rewrite_rules( false );
	update_option( 'nkt_product_platform_rewrite_version', NKT_PRODUCT_PLATFORM_VERSION );
}
add_action( 'init', 'nkt_maybe_flush_product_rewrites', 99 );

/**
 * Sanitize an image ID list.
 *
 * @param mixed $value Raw gallery value.
 * @return array
 */
function nkt_sanitize_product_gallery_ids( $value ) {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}

	if ( ! is_array( $value ) ) {
		return array();
	}

	$ids = array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );
	return $ids;
}

/**
 * Sanitize retailer-independent product offers.
 *
 * @param mixed $value Raw retailer rows.
 * @return array
 */
function nkt_sanitize_product_retailers( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$retailers   = array();
	$has_primary = false;

	foreach ( $value as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$retailer   = isset( $row['retailer'] ) ? sanitize_text_field( $row['retailer'] ) : '';
		$url        = isset( $row['url'] ) ? esc_url_raw( $row['url'], array( 'http', 'https' ) ) : '';
		$button     = isset( $row['button_text'] ) ? sanitize_text_field( $row['button_text'] ) : '';
		$is_primary = ! empty( $row['is_primary'] ) && ! $has_primary;

		if ( '' === $retailer || '' === $url ) {
			continue;
		}

		if ( $is_primary ) {
			$has_primary = true;
		}

		$retailers[] = array(
			'retailer'    => $retailer,
			'url'         => $url,
			'button_text' => $button,
			'is_primary'  => $is_primary,
		);
	}

	if ( $retailers && ! $has_primary ) {
		$retailers[0]['is_primary'] = true;
	}

	return $retailers;
}

/**
 * Return product retailer offers.
 *
 * @param int $post_id Product post ID.
 * @return array
 */
function nkt_get_product_retailers( $post_id = 0 ) {
	$post_id   = $post_id ? absint( $post_id ) : get_the_ID();
	$retailers = get_post_meta( $post_id, '_nkt_product_retailers', true );
	$retailers = nkt_sanitize_product_retailers( $retailers );

	usort(
		$retailers,
		static function ( $left, $right ) {
			return (int) $right['is_primary'] <=> (int) $left['is_primary'];
		}
	);

	return $retailers;
}

/**
 * Return product gallery attachment IDs.
 *
 * @param int $post_id Product post ID.
 * @return array
 */
function nkt_get_product_gallery_ids( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return nkt_sanitize_product_gallery_ids( get_post_meta( $post_id, '_nkt_product_gallery_ids', true ) );
}

/**
 * Return the product archive URL.
 *
 * @return string
 */
function nkt_get_product_archive_url() {
	$url = get_post_type_archive_link( NKT_PRODUCT_POST_TYPE );
	return $url ? $url : home_url( '/shop-my-kitchen/' );
}

/**
 * Register product editing panels.
 */
function nkt_add_product_meta_boxes() {
	add_meta_box(
		'nkt-product-retailers',
		__( 'Retailer Links', 'larder' ),
		'nkt_render_product_retailers_meta_box',
		NKT_PRODUCT_POST_TYPE,
		'normal',
		'high'
	);

	add_meta_box(
		'nkt-product-gallery',
		__( 'Product Gallery', 'larder' ),
		'nkt_render_product_gallery_meta_box',
		NKT_PRODUCT_POST_TYPE,
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'nkt_add_product_meta_boxes' );

/**
 * Render a single retailer row.
 *
 * @param int   $index Retailer row index.
 * @param array $retailer Retailer data.
 */
function nkt_render_product_retailer_row( $index, $retailer ) {
	$retailer = wp_parse_args(
		$retailer,
		array(
			'retailer'    => '',
			'url'         => '',
			'button_text' => '',
			'is_primary'  => false,
		)
	);
	?>
	<tr class="nkt-retailer-row" data-retailer-row>
		<td>
			<label class="screen-reader-text" for="nkt-retailer-name-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Retailer name', 'larder' ); ?></label>
			<input id="nkt-retailer-name-<?php echo esc_attr( $index ); ?>" type="text" name="nkt_product_retailers[<?php echo esc_attr( $index ); ?>][retailer]" value="<?php echo esc_attr( $retailer['retailer'] ); ?>" placeholder="<?php esc_attr_e( 'Amazon UK', 'larder' ); ?>">
		</td>
		<td>
			<label class="screen-reader-text" for="nkt-retailer-url-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Retailer URL', 'larder' ); ?></label>
			<input id="nkt-retailer-url-<?php echo esc_attr( $index ); ?>" type="url" name="nkt_product_retailers[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $retailer['url'] ); ?>" placeholder="https://">
		</td>
		<td>
			<label class="screen-reader-text" for="nkt-retailer-button-<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Button text', 'larder' ); ?></label>
			<input id="nkt-retailer-button-<?php echo esc_attr( $index ); ?>" type="text" name="nkt_product_retailers[<?php echo esc_attr( $index ); ?>][button_text]" value="<?php echo esc_attr( $retailer['button_text'] ); ?>" placeholder="<?php esc_attr_e( 'View at retailer', 'larder' ); ?>">
		</td>
		<td class="nkt-retailer-row__primary">
			<label>
				<input type="radio" name="nkt_product_primary_retailer" value="<?php echo esc_attr( $index ); ?>" <?php checked( ! empty( $retailer['is_primary'] ) ); ?>>
				<span class="screen-reader-text"><?php esc_html_e( 'Primary retailer', 'larder' ); ?></span>
			</label>
		</td>
		<td><button type="button" class="button-link-delete" data-remove-retailer><?php esc_html_e( 'Remove', 'larder' ); ?></button></td>
	</tr>
	<?php
}

/**
 * Render the retailer links editor.
 *
 * @param WP_Post $post Product post.
 */
function nkt_render_product_retailers_meta_box( $post ) {
	wp_nonce_field( 'nkt_save_product_meta', 'nkt_product_meta_nonce' );
	$retailers = nkt_get_product_retailers( $post->ID );

	if ( ! $retailers ) {
		$retailers[] = array(
			'retailer'    => 'Amazon UK',
			'url'         => '',
			'button_text' => __( 'View at Amazon UK', 'larder' ),
			'is_primary'  => true,
		);
	}
	?>
	<p><?php esc_html_e( 'Add one or more places where readers can view or buy this product. Retailer names and links are deliberately flexible so future shops and Nigel’s own products can be added without changing the theme.', 'larder' ); ?></p>
	<div class="nkt-retailer-table-wrap">
		<table class="widefat striped nkt-retailer-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Retailer', 'larder' ); ?></th>
					<th><?php esc_html_e( 'URL', 'larder' ); ?></th>
					<th><?php esc_html_e( 'Button text', 'larder' ); ?></th>
					<th><?php esc_html_e( 'Primary', 'larder' ); ?></th>
					<th><span class="screen-reader-text"><?php esc_html_e( 'Actions', 'larder' ); ?></span></th>
				</tr>
			</thead>
			<tbody data-retailer-rows data-next-index="<?php echo esc_attr( count( $retailers ) ); ?>">
				<?php foreach ( $retailers as $index => $retailer ) : ?>
					<?php nkt_render_product_retailer_row( $index, $retailer ); ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<p><button type="button" class="button" data-add-retailer><?php esc_html_e( 'Add retailer', 'larder' ); ?></button></p>
	<script type="text/html" id="tmpl-nkt-retailer-row">
		<?php
		nkt_render_product_retailer_row(
			'__INDEX__',
			array(
				'retailer'    => '',
				'url'         => '',
				'button_text' => __( 'View product', 'larder' ),
				'is_primary'  => false,
			)
		);
		?>
	</script>
	<?php
}

/**
 * Render product gallery editor.
 *
 * @param WP_Post $post Product post.
 */
function nkt_render_product_gallery_meta_box( $post ) {
	$gallery_ids = nkt_get_product_gallery_ids( $post->ID );
	?>
	<p><?php esc_html_e( 'The featured image is the primary product image. Add optional supporting images here.', 'larder' ); ?></p>
	<input type="hidden" name="nkt_product_gallery_ids" value="<?php echo esc_attr( implode( ',', $gallery_ids ) ); ?>" data-product-gallery-input>
	<ul class="nkt-product-gallery" data-product-gallery-list>
		<?php foreach ( $gallery_ids as $attachment_id ) : ?>
			<?php $thumbnail = wp_get_attachment_image( $attachment_id, 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
			<?php if ( $thumbnail ) : ?>
				<li data-attachment-id="<?php echo esc_attr( $attachment_id ); ?>"><?php echo wp_kses_post( $thumbnail ); ?><button type="button" class="button-link-delete" data-remove-gallery-image aria-label="<?php esc_attr_e( 'Remove image', 'larder' ); ?>">×</button></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
	<p>
		<button type="button" class="button" data-select-product-gallery><?php esc_html_e( 'Choose gallery images', 'larder' ); ?></button>
		<button type="button" class="button-link-delete nkt-product-gallery__clear" data-clear-product-gallery><?php esc_html_e( 'Clear gallery', 'larder' ); ?></button>
	</p>
	<?php
}

/**
 * Save product platform meta.
 *
 * @param int $post_id Product post ID.
 */
function nkt_save_product_meta( $post_id ) {
	if ( ! isset( $_POST['nkt_product_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nkt_product_meta_nonce'] ) ), 'nkt_save_product_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( NKT_PRODUCT_POST_TYPE !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$gallery_ids = isset( $_POST['nkt_product_gallery_ids'] ) ? nkt_sanitize_product_gallery_ids( sanitize_text_field( wp_unslash( $_POST['nkt_product_gallery_ids'] ) ) ) : array();
	if ( $gallery_ids ) {
		update_post_meta( $post_id, '_nkt_product_gallery_ids', $gallery_ids );
	} else {
		delete_post_meta( $post_id, '_nkt_product_gallery_ids' );
	}

	$raw_retailers = isset( $_POST['nkt_product_retailers'] ) && is_array( $_POST['nkt_product_retailers'] ) ? wp_unslash( $_POST['nkt_product_retailers'] ) : array();
	$primary_index = isset( $_POST['nkt_product_primary_retailer'] ) ? sanitize_key( wp_unslash( $_POST['nkt_product_primary_retailer'] ) ) : '';

	foreach ( $raw_retailers as $index => &$retailer ) {
		if ( is_array( $retailer ) ) {
			$retailer['is_primary'] = (string) $index === (string) $primary_index;
		}
	}
	unset( $retailer );

	$retailers = nkt_sanitize_product_retailers( $raw_retailers );
	if ( $retailers ) {
		update_post_meta( $post_id, '_nkt_product_retailers', $retailers );
	} else {
		delete_post_meta( $post_id, '_nkt_product_retailers' );
	}
}
add_action( 'save_post_' . NKT_PRODUCT_POST_TYPE, 'nkt_save_product_meta' );

/**
 * Load product editor assets only where needed.
 *
 * @param string $hook_suffix Current admin screen hook.
 */
function nkt_enqueue_product_admin_assets( $hook_suffix ) {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}

	$screen = get_current_screen();
	if ( ! $screen || NKT_PRODUCT_POST_TYPE !== $screen->post_type ) {
		return;
	}

	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_media();
	wp_enqueue_style( 'nkt-product-admin', get_template_directory_uri() . '/assets/css/admin-product-platform.css', array(), $version );
	wp_enqueue_script( 'nkt-product-admin', get_template_directory_uri() . '/assets/js/product-admin.js', array( 'jquery', 'wp-util' ), $version, true );
	wp_localize_script(
		'nkt-product-admin',
		'nktProductAdmin',
		array(
			'galleryTitle'  => __( 'Choose product images', 'larder' ),
			'galleryButton' => __( 'Use selected images', 'larder' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'nkt_enqueue_product_admin_assets' );

/**
 * Add useful product list columns.
 *
 * @param array $columns Existing columns.
 * @return array
 */
function nkt_product_admin_columns( $columns ) {
	$updated = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$updated['nkt_product_image'] = __( 'Image', 'larder' );
		}
		$updated[ $key ] = $label;
	}
	$updated['nkt_product_retailers'] = __( 'Retailers', 'larder' );
	return $updated;
}
add_filter( 'manage_' . NKT_PRODUCT_POST_TYPE . '_posts_columns', 'nkt_product_admin_columns' );

/**
 * Render product list column values.
 *
 * @param string $column Column name.
 * @param int    $post_id Product post ID.
 */
function nkt_product_admin_column_content( $column, $post_id ) {
	if ( 'nkt_product_image' === $column ) {
		echo get_the_post_thumbnail( $post_id, array( 60, 60 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	if ( 'nkt_product_retailers' === $column ) {
		$retailers = nkt_get_product_retailers( $post_id );
		if ( ! $retailers ) {
			esc_html_e( 'None', 'larder' );
			return;
		}

		echo esc_html( implode( ', ', wp_list_pluck( $retailers, 'retailer' ) ) );
	}
}
add_action( 'manage_' . NKT_PRODUCT_POST_TYPE . '_posts_custom_column', 'nkt_product_admin_column_content', 10, 2 );

/**
 * Add product platform classes for targeted styling and integrations.
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function nkt_product_body_classes( $classes ) {
	if ( is_singular( NKT_PRODUCT_POST_TYPE ) || is_post_type_archive( NKT_PRODUCT_POST_TYPE ) || is_tax( array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ) ) ) {
		$classes[] = 'nkt-product-platform';
	}
	return $classes;
}
add_filter( 'body_class', 'nkt_product_body_classes' );

/**
 * Load the product platform stylesheet only on product views.
 */
function nkt_enqueue_product_platform_assets() {
	if ( ! is_singular( NKT_PRODUCT_POST_TYPE ) && ! is_post_type_archive( NKT_PRODUCT_POST_TYPE ) && ! is_tax( array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ) ) ) {
		return;
	}

	wp_enqueue_style(
		'nkt-shop-my-kitchen',
		get_template_directory_uri() . '/assets/css/shop-my-kitchen.css',
		array( 'nkt-release-2-0-24' ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'nkt_enqueue_product_platform_assets', 20 );
