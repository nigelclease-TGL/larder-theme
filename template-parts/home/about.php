<?php
/**
 * Homepage author introduction.
 *
 * @package Larder
 */

$about_url         = nkt_page_url( array( 'about-nigel', 'my-story', 'about' ), '/my-story/' );
$portrait_image_id = absint( get_theme_mod( 'larder_portrait_image', 0 ) );
?>
<section class="home-section home-about" aria-labelledby="home-about-title">
	<div class="container home-about__grid">
		<div class="home-about__portrait-column">
			<?php if ( $portrait_image_id ) : ?>
				<figure class="about-portrait about-portrait--editorial">
					<?php echo wp_get_attachment_image( $portrait_image_id, 'larder-card', false, array( 'loading' => 'lazy', 'sizes' => '(max-width: 900px) 92vw, 44vw' ) ); ?>
				</figure>
			<?php else : ?>
				<div class="about-portrait about-portrait--placeholder" aria-hidden="true"><span>NK</span></div>
			<?php endif; ?>
			<p class="home-about__caption"><?php esc_html_e( 'Recipes, stories and knowledge shared from my kitchen table.', 'larder' ); ?></p>
		</div>

		<div class="about-copy home-about__copy">
			<p class="eyebrow"><?php esc_html_e( 'Meet Nigel', 'larder' ); ?></p>
			<h2 id="home-about-title"><?php esc_html_e( 'Pull up a chair. Welcome to my kitchen.', 'larder' ); ?></h2>
			<p class="home-about__lead"><?php esc_html_e( 'I believe good cooking should feel generous, achievable and worth sharing.', 'larder' ); ?></p>
			<p><?php esc_html_e( 'Nigel’s Kitchen Table brings together seasonal recipes, reliable methods and the small lessons that make time in the kitchen more rewarding. Every recipe is developed for real homes, real occasions and people who simply love good food.', 'larder' ); ?></p>
			<p class="home-about__signature" aria-label="Nigel"><?php esc_html_e( 'Nigel', 'larder' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_url( $about_url ); ?>">
				<?php esc_html_e( 'Read my story', 'larder' ); ?> →
			</a>
		</div>
	</div>
</section>