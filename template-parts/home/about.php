<?php
/**
 * Homepage author introduction.
 *
 * @package Larder
 */

$about_page       = get_page_by_path( 'about' );
$about_url        = $about_page ? get_permalink( $about_page ) : home_url( '/about/' );
$portrait_image_id = absint( get_theme_mod( 'larder_portrait_image', 0 ) );
?>
<section class="home-section home-about" aria-labelledby="home-about-title">
	<div class="container about-grid about-grid--compact">
		<div class="about-copy">
			<p class="eyebrow"><?php esc_html_e( "Nigel's Kitchen Table", 'larder' ); ?></p>
			<h2 id="home-about-title"><?php echo esc_html( get_theme_mod( 'larder_about_title', __( 'A little about the kitchen', 'larder' ) ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'larder_about_copy', __( "I'm Nigel. I develop and test approachable recipes for good food that belongs around the table.", 'larder' ) ) ); ?></p>
			<p><?php esc_html_e( 'The focus is always the food: seasonal ingredients, clear methods and recipes worth making again.', 'larder' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_url( $about_url ); ?>">
				<?php esc_html_e( 'Read the story', 'larder' ); ?>
			</a>
		</div>

		<?php if ( $portrait_image_id ) : ?>
			<figure class="about-portrait about-portrait--small">
				<?php echo wp_get_attachment_image( $portrait_image_id, 'medium', false, array( 'loading' => 'lazy', 'alt' => __( 'Nigel', 'larder' ) ) ); ?>
			</figure>
		<?php endif; ?>
	</div>
</section>
