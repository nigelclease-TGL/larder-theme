<?php
/**
 * Homepage author introduction.
 *
 * @package Larder
 */

$about_page = get_page_by_path( 'about' );
$about_url  = $about_page ? get_permalink( $about_page ) : home_url( '/about/' );
?>
<section class="home-section home-about" aria-labelledby="home-about-title">
	<div class="container about-grid">
		<div class="about-portrait" role="img" aria-label="<?php esc_attr_e( 'Portrait of Nigel', 'larder' ); ?>">
			<span><?php esc_html_e( 'Portrait coming soon', 'larder' ); ?></span>
		</div>

		<div class="about-copy">
			<p class="eyebrow"><?php esc_html_e( 'Meet Nigel', 'larder' ); ?></p>
			<h2 id="home-about-title"><?php esc_html_e( "Hello, I'm Nigel", 'larder' ); ?></h2>
			<p><?php esc_html_e( 'Welcome to The Gourmet Larder. Every recipe is developed and tested to help home bakers create delicious food with confidence.', 'larder' ); ?></p>
			<p><?php esc_html_e( 'From simple everyday bakes to impressive celebration desserts, my aim is to make baking reliable, enjoyable and inspiring.', 'larder' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_url( $about_url ); ?>">
				<?php esc_html_e( 'Read my story', 'larder' ); ?>
			</a>
		</div>
	</div>
</section>
