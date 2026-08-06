<?php
/**
 * Recipe-to-product relationships and reusable recommended-product output.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_RECIPE_PRODUCT_META = '_nkt_recipe_product_ids';

/**
 * Register the recipe-to-product relationship as structured REST-visible meta.
 */
function nkt_register_recipe_product_meta() {
	register_post_meta(
		'post',
		NKT_RECIPE_PRODUCT_META,
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'sanitize_callback' => 'nkt_sanitize_recipe_product_ids',
			'auth_callback'     => static function () {
				return current_user_can( 'edit_posts' );
			},
			'show_in_rest'      => array(
				'schema' => array(
					'type'    => 'array',
					'items'   => array( 'type' => 'integer' ),
					'default' => array(),
				),
			),
		)
	);
}
add_action( 'init', 'nkt_register_recipe_product_meta' );

/**
 * Sanitize a list of Kitchen Product post IDs.
 *
 * @param mixed $value Raw relationship value.
 * @return array
 */
function nkt_sanitize_recipe_product_ids( $value ) {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}

	if ( ! is_array( $value ) ) {
		return array();
	}

	$product_ids = array_values( array_unique( array_filter( array_map( 'absint', $value ) ) ) );

	return array_values(
		array_filter(
			$product_ids,
			static function ( $product_id ) {
				return NKT_PRODUCT_POST_TYPE === get_post_type( $product_id );
			}
		)
	);
}

/**
 * Return linked Kitchen Product IDs for a recipe post.
 *
 * @param int  $post_id Recipe post ID.
 * @param bool $published_only Limit the result to public products.
 * @return array
 */
function nkt_get_recipe_product_ids( $post_id = 0, $published_only = false ) {
	$post_id     = $post_id ? absint( $post_id ) : get_the_ID();
	$product_ids = nkt_sanitize_recipe_product_ids( get_post_meta( $post_id, NKT_RECIPE_PRODUCT_META, true ) );

	if ( ! $published_only ) {
		return $product_ids;
	}

	return array_values(
		array_filter(
			$product_ids,
			static function ( $product_id ) {
				return 'publish' === get_post_status( $product_id );
			}
		)
	);
}

/**
 * Add the product relationship panel to recipe posts.
 */
function nkt_add_recipe_products_meta_box() {
	add_meta_box(
		'nkt-recipe-products',
		__( 'Recommended Products', 'larder' ),
		'nkt_render_recipe_products_meta_box',
		'post',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes_post', 'nkt_add_recipe_products_meta_box' );

/**
 * Render the recipe product relationship editor.
 *
 * @param WP_Post $post Recipe post.
 */
function nkt_render_recipe_products_meta_box( $post ) {
	wp_nonce_field( 'nkt_save_recipe_products', 'nkt_recipe_products_nonce' );

	$selected = nkt_get_recipe_product_ids( $post->ID );
	$products = get_posts(
		array(
			'post_type'              => NKT_PRODUCT_POST_TYPE,
			'post_status'            => array( 'publish', 'draft', 'pending', 'private' ),
			'posts_per_page'         => -1,
			'orderby'                => 'title',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'suppress_filters'       => false,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	?>
	<p><?php esc_html_e( 'Link the kitchen products genuinely used or recommended for this recipe. The Recommended Products block can display these selections anywhere in the article.', 'larder' ); ?></p>

	<?php if ( $products ) : ?>
		<label class="screen-reader-text" for="nkt-recipe-product-ids"><?php esc_html_e( 'Linked Kitchen Products', 'larder' ); ?></label>
		<select id="nkt-recipe-product-ids" name="nkt_recipe_product_ids[]" multiple size="10" style="width:100%;">
			<?php foreach ( $products as $product ) : ?>
				<?php
				$status_label = 'publish' === $product->post_status
					? ''
					: sprintf( ' — %s', get_post_status_object( $product->post_status )->label );
				?>
				<option value="<?php echo esc_attr( $product->ID ); ?>" <?php selected( in_array( (int) $product->ID, $selected, true ) ); ?>>
					<?php echo esc_html( get_the_title( $product ) . $status_label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description"><?php esc_html_e( 'Hold Ctrl on Windows or Command on Mac to select more than one product. Draft products stay hidden from readers until published.', 'larder' ); ?></p>
	<?php else : ?>
		<p><?php esc_html_e( 'No Kitchen Products are available yet.', 'larder' ); ?></p>
	<?php endif; ?>

	<p><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . NKT_PRODUCT_POST_TYPE ) ); ?>"><?php esc_html_e( 'Add a Kitchen Product', 'larder' ); ?></a></p>
	<?php
}

/**
 * Save recipe-to-product relationships.
 *
 * @param int $post_id Recipe post ID.
 */
function nkt_save_recipe_products( $post_id ) {
	if ( ! isset( $_POST['nkt_recipe_products_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nkt_recipe_products_nonce'] ) ), 'nkt_save_recipe_products' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || 'post' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw_ids     = isset( $_POST['nkt_recipe_product_ids'] ) && is_array( $_POST['nkt_recipe_product_ids'] ) ? wp_unslash( $_POST['nkt_recipe_product_ids'] ) : array();
	$product_ids = nkt_sanitize_recipe_product_ids( $raw_ids );

	if ( $product_ids ) {
		update_post_meta( $post_id, NKT_RECIPE_PRODUCT_META, $product_ids );
	} else {
		delete_post_meta( $post_id, NKT_RECIPE_PRODUCT_META );
	}
}
add_action( 'save_post_post', 'nkt_save_recipe_products' );

/**
 * Return a compact card for one public Kitchen Product.
 *
 * @param int  $product_id Product post ID.
 * @param bool $show_retailer_button Whether to show the primary retailer button.
 * @return string
 */
function nkt_get_recommended_product_card( $product_id, $show_retailer_button = true ) {
	$product = get_post( $product_id );

	if ( ! $product || NKT_PRODUCT_POST_TYPE !== $product->post_type || 'publish' !== $product->post_status ) {
		return '';
	}

	$product_url = get_permalink( $product );
	$retailers   = nkt_get_product_retailers( $product_id );
	$brands      = get_the_terms( $product_id, NKT_PRODUCT_BRAND_TAX );
	$categories  = get_the_terms( $product_id, NKT_PRODUCT_CATEGORY_TAX );
	$brand       = $brands && ! is_wp_error( $brands ) ? $brands[0] : null;
	$category    = $categories && ! is_wp_error( $categories ) ? $categories[0] : null;
	$retailer    = $retailers ? $retailers[0] : null;
	$excerpt     = has_excerpt( $product ) ? wp_trim_words( get_the_excerpt( $product ), 24 ) : '';

	ob_start();
	?>
	<article class="nkt-recommended-product-card">
		<a class="nkt-recommended-product-card__media" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( get_the_title( $product ) ); ?>">
			<?php if ( has_post_thumbnail( $product ) ) : ?>
				<?php echo get_the_post_thumbnail( $product, 'larder-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 720px) 92vw, 30vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php else : ?>
				<span class="nkt-recommended-product-card__placeholder" aria-hidden="true"></span>
			<?php endif; ?>
			<?php if ( $category ) : ?>
				<span class="nkt-recommended-product-card__category"><?php echo esc_html( $category->name ); ?></span>
			<?php endif; ?>
		</a>

		<div class="nkt-recommended-product-card__body">
			<?php if ( $brand ) : ?>
				<p class="nkt-recommended-product-card__brand"><?php echo esc_html( $brand->name ); ?></p>
			<?php endif; ?>
			<h3 class="nkt-recommended-product-card__title"><a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( get_the_title( $product ) ); ?></a></h3>
			<?php if ( $excerpt ) : ?>
				<p class="nkt-recommended-product-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
			<div class="nkt-recommended-product-card__actions">
				<a class="nkt-recommended-product-card__details" href="<?php echo esc_url( $product_url ); ?>"><?php esc_html_e( 'Why I recommend it', 'larder' ); ?> <span aria-hidden="true">→</span></a>
				<?php if ( $show_retailer_button && $retailer ) : ?>
					<?php $button_text = $retailer['button_text'] ? $retailer['button_text'] : sprintf( __( 'View at %s', 'larder' ), $retailer['retailer'] ); ?>
					<a class="button button-secondary nkt-recommended-product-card__retailer" href="<?php echo esc_url( $retailer['url'] ); ?>" target="_blank" rel="nofollow sponsored noopener noreferrer" data-nkt-event="affiliate_product_click" data-nkt-label="<?php echo esc_attr( get_the_title( $product ) . ' – ' . $retailer['retailer'] ); ?>"><?php echo esc_html( $button_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render a Recommended Products section.
 *
 * @param array $product_ids Product post IDs.
 * @param array $args Display arguments.
 * @return string
 */
function nkt_get_recommended_products_markup( $product_ids, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'heading'              => __( 'Tools I recommend', 'larder' ),
			'intro'                => '',
			'show_retailer_button' => true,
		)
	);

	$product_ids = nkt_sanitize_recipe_product_ids( $product_ids );
	$product_ids = array_values(
		array_filter(
			$product_ids,
			static function ( $product_id ) {
				return 'publish' === get_post_status( $product_id );
			}
		)
	);

	if ( ! $product_ids ) {
		return '';
	}

	$cards = '';
	foreach ( $product_ids as $product_id ) {
		$cards .= nkt_get_recommended_product_card( $product_id, (bool) $args['show_retailer_button'] );
	}

	if ( '' === $cards ) {
		return '';
	}

	$heading_id = wp_unique_id( 'nkt-recommended-products-' );

	ob_start();
	?>
	<section class="nkt-recommended-products" aria-labelledby="<?php echo esc_attr( $heading_id ); ?>">
		<header class="nkt-recommended-products__header">
			<p class="eyebrow"><?php esc_html_e( 'Nigel recommends', 'larder' ); ?></p>
			<h2 id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $args['heading'] ); ?></h2>
			<?php if ( $args['intro'] ) : ?>
				<p><?php echo esc_html( $args['intro'] ); ?></p>
			<?php endif; ?>
		</header>
		<div class="nkt-recommended-products__grid"><?php echo $cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<p class="nkt-recommended-products__disclosure"><?php esc_html_e( 'Some retailer links may be affiliate links. Recommendations remain editorially independent.', 'larder' ); ?></p>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render the dynamic Recommended Products block.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content Saved block content.
 * @param WP_Block $block Block instance.
 * @return string
 */
function nkt_render_recommended_products_block( $attributes, $content = '', $block = null ) {
	$use_linked = ! array_key_exists( 'useLinkedProducts', $attributes ) || (bool) $attributes['useLinkedProducts'];
	$post_id    = 0;

	if ( $block instanceof WP_Block && ! empty( $block->context['postId'] ) ) {
		$post_id = absint( $block->context['postId'] );
	} elseif ( get_the_ID() ) {
		$post_id = get_the_ID();
	}

	$product_ids = $use_linked
		? nkt_get_recipe_product_ids( $post_id, true )
		: nkt_sanitize_recipe_product_ids( isset( $attributes['productIds'] ) ? $attributes['productIds'] : array() );

	return nkt_get_recommended_products_markup(
		$product_ids,
		array(
			'heading'              => isset( $attributes['heading'] ) && $attributes['heading'] ? $attributes['heading'] : __( 'Tools I recommend', 'larder' ),
			'intro'                => isset( $attributes['intro'] ) ? $attributes['intro'] : '',
			'show_retailer_button' => ! array_key_exists( 'showRetailerButton', $attributes ) || (bool) $attributes['showRetailerButton'],
		)
	);
}

/**
 * Register the reusable dynamic Recommended Products block and its assets.
 */
function nkt_register_recommended_products_block() {
	$version = wp_get_theme()->get( 'Version' );

	wp_register_script(
		'nkt-recommended-products-editor',
		get_template_directory_uri() . '/assets/js/recommended-products-block.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-core-data', 'wp-data', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
		$version,
		true
	);

	wp_register_style(
		'nkt-recommended-products',
		get_template_directory_uri() . '/assets/css/recommended-products.css',
		array(),
		$version
	);

	register_block_type(
		'nkt/recommended-products',
		array(
			'api_version'     => 2,
			'editor_script'   => 'nkt-recommended-products-editor',
			'style'           => 'nkt-recommended-products',
			'render_callback' => 'nkt_render_recommended_products_block',
			'uses_context'    => array( 'postId', 'postType' ),
			'attributes'      => array(
				'useLinkedProducts' => array( 'type' => 'boolean', 'default' => true ),
				'productIds'         => array( 'type' => 'array', 'default' => array(), 'items' => array( 'type' => 'number' ) ),
				'heading'            => array( 'type' => 'string', 'default' => 'Tools I recommend' ),
				'intro'              => array( 'type' => 'string', 'default' => '' ),
				'showRetailerButton' => array( 'type' => 'boolean', 'default' => true ),
			),
			'supports'        => array(
				'html'   => false,
				'anchor' => true,
			),
		)
	);
}
add_action( 'init', 'nkt_register_recommended_products_block', 20 );

/**
 * Recommended Products shortcode for legacy and reusable content areas.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function nkt_recommended_products_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'ids'                  => '',
			'heading'              => __( 'Tools I recommend', 'larder' ),
			'intro'                => '',
			'use_linked'           => 'yes',
			'show_retailer_button' => 'yes',
		),
		$atts,
		'nkt_recommended_products'
	);

	$product_ids = 'yes' === strtolower( (string) $atts['use_linked'] )
		? nkt_get_recipe_product_ids( get_the_ID(), true )
		: nkt_sanitize_recipe_product_ids( $atts['ids'] );

	return nkt_get_recommended_products_markup(
		$product_ids,
		array(
			'heading'              => $atts['heading'],
			'intro'                => $atts['intro'],
			'show_retailer_button' => 'yes' === strtolower( (string) $atts['show_retailer_button'] ),
		)
	);
}
add_shortcode( 'nkt_recommended_products', 'nkt_recommended_products_shortcode' );

/**
 * Load recommended-product styling on recipe and product pages.
 */
function nkt_enqueue_recommended_products_assets() {
	if ( is_singular( array( 'post', NKT_PRODUCT_POST_TYPE ) ) ) {
		wp_enqueue_style( 'nkt-recommended-products' );
	}
}
add_action( 'wp_enqueue_scripts', 'nkt_enqueue_recommended_products_assets', 25 );

/**
 * Return published recipes linked to a Kitchen Product.
 *
 * @param int $product_id Kitchen Product post ID.
 * @param int $limit Maximum recipes to return.
 * @return array
 */
function nkt_get_recipes_for_product( $product_id, $limit = 6 ) {
	$product_id = absint( $product_id );

	if ( ! $product_id ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'posts_per_page'         => absint( $limit ),
			'orderby'                => 'modified',
			'order'                  => 'DESC',
			'no_found_rows'          => true,
			'suppress_filters'       => false,
			'update_post_meta_cache' => false,
			'meta_query'             => array(
				array(
					'key'     => NKT_RECIPE_PRODUCT_META,
					'value'   => 'i:' . $product_id . ';',
					'compare' => 'LIKE',
				),
			),
		)
	);
}

/**
 * Append linked public recipes to individual product editorial content.
 *
 * @param string $content Product content.
 * @return string
 */
function nkt_append_linked_recipes_to_product( $content ) {
	if ( ! is_singular( NKT_PRODUCT_POST_TYPE ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$recipes = nkt_get_recipes_for_product( get_the_ID() );

	if ( ! $recipes ) {
		return $content;
	}

	ob_start();
	?>
	<section class="nkt-product-recipes" aria-labelledby="nkt-product-recipes-title">
		<header class="nkt-product-recipes__header">
			<p class="eyebrow"><?php esc_html_e( 'From my kitchen', 'larder' ); ?></p>
			<h2 id="nkt-product-recipes-title"><?php esc_html_e( 'Recipes using this product', 'larder' ); ?></h2>
		</header>
		<div class="nkt-product-recipes__grid">
			<?php foreach ( $recipes as $recipe ) : ?>
				<article class="nkt-product-recipe-card">
					<a class="nkt-product-recipe-card__media" href="<?php echo esc_url( get_permalink( $recipe ) ); ?>">
						<?php if ( has_post_thumbnail( $recipe ) ) : ?>
							<?php echo get_the_post_thumbnail( $recipe, 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<span class="nkt-product-recipe-card__placeholder" aria-hidden="true"></span>
						<?php endif; ?>
					</a>
					<h3><a href="<?php echo esc_url( get_permalink( $recipe ) ); ?>"><?php echo esc_html( get_the_title( $recipe ) ); ?></a></h3>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<?php

	return $content . (string) ob_get_clean();
}
add_filter( 'the_content', 'nkt_append_linked_recipes_to_product', 25 );

/**
 * Add a linked-recipe count to the Kitchen Products list.
 *
 * @param array $columns Existing product columns.
 * @return array
 */
function nkt_add_product_recipe_column( $columns ) {
	$columns['nkt_product_recipes'] = __( 'Recipes', 'larder' );
	return $columns;
}
add_filter( 'manage_' . NKT_PRODUCT_POST_TYPE . '_posts_columns', 'nkt_add_product_recipe_column', 20 );

/**
 * Render the linked-recipe count in Kitchen Products administration.
 *
 * @param string $column Column key.
 * @param int    $post_id Product post ID.
 */
function nkt_render_product_recipe_column( $column, $post_id ) {
	if ( 'nkt_product_recipes' !== $column ) {
		return;
	}

	echo esc_html( (string) count( nkt_get_recipes_for_product( $post_id, 100 ) ) );
}
add_action( 'manage_' . NKT_PRODUCT_POST_TYPE . '_posts_custom_column', 'nkt_render_product_recipe_column', 20, 2 );
