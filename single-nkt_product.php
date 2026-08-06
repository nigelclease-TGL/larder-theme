<?php
/**
 * Single Kitchen Product template.
 *
 * @package Larder
 */

get_header();

while ( have_posts() ) :
	the_post();
	$retailers  = nkt_get_product_retailers();
	$gallery    = nkt_get_product_gallery_ids();
	$brands     = get_the_terms( get_the_ID(), NKT_PRODUCT_BRAND_TAX );
	$categories = get_the_terms( get_the_ID(), NKT_PRODUCT_CATEGORY_TAX );
	$brand      = $brands && ! is_wp_error( $brands ) ? $brands[0] : null;
	?>
	<main id="primary" class="nkt-product-single">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'nkt-product-detail' ); ?>>
			<div class="container nkt-product-detail__breadcrumbs">
				<a href="<?php echo esc_url( nkt_get_product_archive_url() ); ?>"><?php esc_html_e( 'Shop My Kitchen', 'larder' ); ?></a>
				<span aria-hidden="true">/</span>
				<span><?php the_title(); ?></span>
			</div>

			<div class="container nkt-product-detail__hero">
				<div class="nkt-product-detail__media">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="nkt-product-detail__primary-image">
							<?php the_post_thumbnail( 'large', array( 'loading' => 'eager', 'decoding' => 'async', 'fetchpriority' => 'high', 'sizes' => '(max-width: 820px) 100vw, 52vw' ) ); ?>
						</figure>
					<?php else : ?>
						<div class="nkt-product-detail__placeholder" aria-hidden="true"></div>
					<?php endif; ?>

					<?php if ( $gallery ) : ?>
						<div class="nkt-product-gallery-grid" aria-label="<?php esc_attr_e( 'Additional product images', 'larder' ); ?>">
							<?php foreach ( $gallery as $attachment_id ) : ?>
								<?php echo wp_get_attachment_image( $attachment_id, 'medium_large', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<header class="nkt-product-detail__summary">
					<p class="eyebrow"><?php echo $brand ? esc_html( $brand->name ) : esc_html__( 'Nigel recommends', 'larder' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<div class="nkt-product-detail__excerpt"><?php the_excerpt(); ?></div>
					<?php endif; ?>

					<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
						<div class="nkt-product-detail__terms" aria-label="<?php esc_attr_e( 'Product categories', 'larder' ); ?>">
							<?php foreach ( $categories as $category ) : ?>
								<a href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $retailers ) : ?>
						<div class="nkt-product-retailers" aria-labelledby="nkt-product-retailers-title">
							<h2 id="nkt-product-retailers-title"><?php esc_html_e( 'Where to find it', 'larder' ); ?></h2>
							<div class="nkt-product-retailers__links">
								<?php foreach ( $retailers as $retailer ) : ?>
									<?php $button_text = $retailer['button_text'] ? $retailer['button_text'] : sprintf( __( 'View at %s', 'larder' ), $retailer['retailer'] ); ?>
									<a class="button <?php echo $retailer['is_primary'] ? 'button-primary' : 'button-secondary'; ?> nkt-product-retailer-link" href="<?php echo esc_url( $retailer['url'] ); ?>" target="_blank" rel="nofollow sponsored noopener noreferrer" data-nkt-event="affiliate_product_click" data-nkt-label="<?php echo esc_attr( get_the_title() . ' – ' . $retailer['retailer'] ); ?>">
										<?php echo esc_html( $button_text ); ?>
									</a>
								<?php endforeach; ?>
							</div>
							<p class="nkt-product-retailers__note"><?php esc_html_e( 'Retailer availability and prices can change. Some links may be affiliate links.', 'larder' ); ?></p>
						</div>
					<?php endif; ?>
				</header>
			</div>

			<div class="container nkt-product-detail__body">
				<div class="nkt-product-detail__content entry-content">
					<?php the_content(); ?>
				</div>
				<?php echo do_shortcode( '[nkt_affiliate_disclosure]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
