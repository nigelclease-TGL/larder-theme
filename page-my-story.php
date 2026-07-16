<?php
/**
 * Editorial About Nigel page.
 * Automatically used by the my-story, about-nigel and about pages.
 *
 * @package Larder
 */

get_header();
$portrait_id = absint( get_theme_mod( 'larder_portrait_image', 0 ) );
$recipes_url = nkt_page_url( array( 'recipes' ), '/recipes/' );
?>
<main id="primary" class="about-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<section class="about-page__hero">
			<div class="container about-page__hero-grid">
				<div class="about-page__intro">
					<p class="eyebrow"><?php esc_html_e( "Welcome to Nigel's Kitchen Table", 'larder' ); ?></p>
					<h1><?php esc_html_e( 'Good food, made to be shared.', 'larder' ); ?></h1>
					<p class="about-page__lead"><?php esc_html_e( 'A personal collection of dependable recipes, seasonal ideas and the small details that make time around the kitchen table feel special.', 'larder' ); ?></p>
				</div>

				<?php if ( $portrait_id ) : ?>
					<figure class="about-page__portrait">
						<?php echo wp_get_attachment_image( $portrait_id, 'medium_large', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 720px) 160px, 260px' ) ); ?>
					</figure>
				<?php endif; ?>
			</div>
		</section>

		<section class="about-page__body">
			<div class="container about-page__content-grid">
				<article class="prose about-page__story">
					<?php the_content(); ?>
				</article>

				<aside class="about-page__aside">
					<div class="about-page__note">
						<p class="eyebrow"><?php esc_html_e( 'At the table', 'larder' ); ?></p>
						<h2><?php esc_html_e( 'Seasonal recipes. Made to share.', 'larder' ); ?></h2>
						<p><?php esc_html_e( 'The recipes here are written for real home kitchens: clear instructions, practical advice and food worth making again.', 'larder' ); ?></p>
						<a class="text-link" href="<?php echo esc_url( $recipes_url ); ?>"><?php esc_html_e( 'Explore the recipes', 'larder' ); ?></a>
					</div>
				</aside>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
