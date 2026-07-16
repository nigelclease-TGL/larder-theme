<?php
/**
 * Seasonal recipe collection.
 *
 * @package Larder
 */

$seasonal_categories = array(
	'christmas' => __( 'Christmas baking', 'larder' ),
	'easter'    => __( 'Easter favourites', 'larder' ),
	'summer'    => __( 'Summer desserts', 'larder' ),
	'autumn'    => __( 'Autumn comfort', 'larder' ),
);
?>
<section class="home-section seasonal-collections" aria-labelledby="seasonal-title">
	<div class="container">
		<header class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Cook with the seasons', 'larder' ); ?></p>
				<h2 id="seasonal-title"><?php esc_html_e( 'Seasonal collections', 'larder' ); ?></h2>
			</div>
		</header>

		<div class="seasonal-grid">
			<?php foreach ( $seasonal_categories as $slug => $label ) :
				$category = get_category_by_slug( $slug );
				$url      = $category ? get_category_link( $category ) : home_url( '/category/' . $slug . '/' );
			?>
				<a class="seasonal-card seasonal-card--<?php echo esc_attr( $slug ); ?>" href="<?php echo esc_url( $url ); ?>">
					<span class="seasonal-card__label"><?php echo esc_html( $label ); ?></span>
					<span class="seasonal-card__link"><?php esc_html_e( 'Explore recipes', 'larder' ); ?> →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
