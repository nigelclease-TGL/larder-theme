<?php
/**
 * Optional homepage business or seasonal promotion.
 *
 * @package Larder
 */

$promotion = nkt_get_home_promotion();

if ( ! is_active_sidebar( 'homepage-promotion' ) && ! $promotion['enabled'] ) {
	return;
}
?>
<section class="home-section nkt-growth-promotion" aria-label="<?php esc_attr_e( 'Featured promotion', 'larder' ); ?>" data-nkt-location="homepage_promotion">
	<div class="container">
		<?php if ( is_active_sidebar( 'homepage-promotion' ) ) : ?>
			<div class="nkt-growth-promotion__widget">
				<?php dynamic_sidebar( 'homepage-promotion' ); ?>
			</div>
		<?php else : ?>
			<div class="nkt-growth-promotion__panel">
				<div class="nkt-growth-promotion__copy">
					<p class="eyebrow"><?php echo esc_html( $promotion['eyebrow'] ); ?></p>
					<h2 id="growth-promotion-title"><?php echo esc_html( $promotion['title'] ); ?></h2>
					<p><?php echo esc_html( $promotion['copy'] ); ?></p>
				</div>
				<a class="button button-primary" href="<?php echo esc_url( $promotion['url'] ); ?>" data-nkt-event="promotion_click" data-nkt-label="homepage_promotion"><?php echo esc_html( $promotion['label'] ); ?> <span aria-hidden="true">→</span></a>
			</div>
		<?php endif; ?>
	</div>
</section>
