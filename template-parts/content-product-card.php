<?php
/**
 * Kitchen product card.
 *
 * @package Larder
 */

$product_url = get_permalink();
$retailers   = nkt_get_product_retailers();
$brands      = get_the_terms( get_the_ID(), NKT_PRODUCT_BRAND_TAX );
$categories  = get_the_terms( get_the_ID(), NKT_PRODUCT_CATEGORY_TAX );
$brand       = $brands && ! is_wp_error( $brands ) ? $brands[0] : null;
$category    = $categories && ! is_wp_error( $categories ) ? $categories[0] : null;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nkt-product-card' ); ?>>
	<a class="nkt-product-card__media-link" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
		<div class="nkt-product-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'larder-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 620px) 92vw, (max-width: 900px) 46vw, 31vw' ) ); ?>
			<?php else : ?>
				<div class="nkt-product-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
			<?php if ( $category ) : ?>
				<span class="nkt-product-card__category"><?php echo esc_html( $category->name ); ?></span>
			<?php endif; ?>
		</div>
	</a>

	<div class="nkt-product-card__content">
		<?php if ( $brand ) : ?>
			<p class="nkt-product-card__brand"><?php echo esc_html( $brand->name ); ?></p>
		<?php endif; ?>
		<h2 class="nkt-product-card__title"><a href="<?php echo esc_url( $product_url ); ?>"><?php the_title(); ?></a></h2>
		<?php if ( has_excerpt() ) : ?>
			<p class="nkt-product-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
		<?php endif; ?>
		<div class="nkt-product-card__footer">
			<a class="nkt-product-card__cta" href="<?php echo esc_url( $product_url ); ?>"><?php esc_html_e( 'View product', 'larder' ); ?> <span aria-hidden="true">→</span></a>
			<?php if ( $retailers ) : ?>
				<span class="nkt-product-card__retailers">
					<?php
					printf(
						/* translators: %d: number of retailer links. */
						esc_html( _n( '%d retailer', '%d retailers', count( $retailers ), 'larder' ) ),
						(int) count( $retailers )
					);
					?>
				</span>
			<?php endif; ?>
		</div>
	</div>
</article>
