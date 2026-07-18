<?php
/**
 * Affiliate and sponsored content disclosure page.
 *
 * @package Larder
 */

get_header();

$contact_url = nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' );
?>
<main id="primary" class="nkt-growth-page nkt-trust-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="nkt-growth-hero nkt-growth-hero--compact">
			<div class="container nkt-growth-hero__copy-only">
				<p class="eyebrow"><?php esc_html_e( 'Commercial transparency', 'larder' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p class="nkt-growth-hero__lead"><?php esc_html_e( 'A clear explanation of affiliate links, gifted products and sponsored work on Nigel’s Kitchen Table.', 'larder' ); ?></p>
			</div>
		</header>

		<section class="nkt-growth-page-content">
			<div class="container prose nkt-trust-copy">
				<h2><?php esc_html_e( 'Affiliate links', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'Some pages may contain affiliate links. When a purchase is made through one of those links, Nigel’s Kitchen Table may receive a small commission at no extra cost to the reader.', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Sponsored work and gifted products', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'Sponsored content, paid partnerships and gifted products are identified clearly. Payment or a supplied product does not guarantee a positive recommendation or continued coverage.', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Editorial independence', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'The usefulness of the content and the trust of readers come first. Commercial relationships do not purchase control over honest conclusions or practical recipe guidance.', 'larder' ); ?></p>
				<?php if ( trim( get_the_content() ) ) : ?><?php the_content(); ?><?php endif; ?>
				<p><?php printf( wp_kses_post( __( 'Questions about a specific relationship can be sent through the <a href="%s">contact page</a>.', 'larder' ) ), esc_url( $contact_url ) ); ?></p>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
