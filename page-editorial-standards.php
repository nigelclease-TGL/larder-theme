<?php
/**
 * Editorial standards and recipe testing page.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="nkt-growth-page nkt-trust-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="nkt-growth-hero nkt-growth-hero--compact">
			<div class="container nkt-growth-hero__copy-only">
				<p class="eyebrow"><?php esc_html_e( 'Trust at the Kitchen Table', 'larder' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p class="nkt-growth-hero__lead"><?php esc_html_e( 'How recipes, recommendations, corrections and commercial relationships are handled on Nigel’s Kitchen Table.', 'larder' ); ?></p>
			</div>
		</header>

		<section class="nkt-trust-standards" aria-labelledby="standards-title">
			<div class="container">
				<header class="section-heading">
					<p class="eyebrow"><?php esc_html_e( 'The standard', 'larder' ); ?></p>
					<h2 id="standards-title"><?php esc_html_e( 'Useful, clear and honest.', 'larder' ); ?></h2>
				</header>
				<div class="nkt-trust-standards__grid">
					<article><span>01</span><h3><?php esc_html_e( 'Recipe development', 'larder' ); ?></h3><p><?php esc_html_e( 'Recipes are written for real home kitchens, with clear measurements, practical methods and the intended result explained.', 'larder' ); ?></p></article>
					<article><span>02</span><h3><?php esc_html_e( 'Testing and updates', 'larder' ); ?></h3><p><?php esc_html_e( 'Recipes may be refined when testing, reader feedback or ingredient changes reveal a clearer or more dependable method.', 'larder' ); ?></p></article>
					<article><span>03</span><h3><?php esc_html_e( 'Corrections', 'larder' ); ?></h3><p><?php esc_html_e( 'Material errors are corrected promptly. Readers are encouraged to report anything unclear through the contact page.', 'larder' ); ?></p></article>
					<article><span>04</span><h3><?php esc_html_e( 'Recommendations', 'larder' ); ?></h3><p><?php esc_html_e( 'Products and services are included only when they are relevant. Affiliate or sponsored relationships are disclosed clearly.', 'larder' ); ?></p></article>
				</div>
			</div>
		</section>

		<?php if ( trim( get_the_content() ) ) : ?>
			<section class="nkt-growth-page-content">
				<div class="container prose"><?php the_content(); ?></div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
