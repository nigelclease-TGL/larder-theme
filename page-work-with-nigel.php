<?php
/**
 * Partnership and collaboration page.
 *
 * @package Larder
 */

get_header();

$contact_url = nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' );
?>
<main id="primary" class="nkt-growth-page nkt-partnership-page" data-nkt-location="partnership_page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="nkt-growth-hero nkt-growth-hero--partnerships">
			<div class="container nkt-growth-hero__grid">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Thoughtful food collaborations', 'larder' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<p class="nkt-growth-hero__lead"><?php esc_html_e( 'Nigel’s Kitchen Table is open to considered partnerships that are useful, honest and a natural fit for the people who cook from this site.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $contact_url ); ?>" data-nkt-event="partnership_contact" data-nkt-label="hero"><?php esc_html_e( 'Start a conversation', 'larder' ); ?></a>
				</div>
				<div class="nkt-growth-principles" aria-label="<?php esc_attr_e( 'Partnership principles', 'larder' ); ?>">
					<div><span>01</span><strong><?php esc_html_e( 'Genuine fit', 'larder' ); ?></strong></div>
					<div><span>02</span><strong><?php esc_html_e( 'Clear disclosure', 'larder' ); ?></strong></div>
					<div><span>03</span><strong><?php esc_html_e( 'Useful to readers', 'larder' ); ?></strong></div>
				</div>
			</div>
		</header>

		<section class="nkt-growth-offers" aria-labelledby="partnership-options-title">
			<div class="container">
				<header class="section-heading section-heading--split">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Ways to work together', 'larder' ); ?></p>
						<h2 id="partnership-options-title"><?php esc_html_e( 'Useful ideas, beautifully presented.', 'larder' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'Every project is considered individually. Commercial relationships are disclosed clearly and editorial trust comes first.', 'larder' ); ?></p>
				</header>
				<div class="nkt-growth-offers__grid">
					<article><span>01</span><h3><?php esc_html_e( 'Recipe development', 'larder' ); ?></h3><p><?php esc_html_e( 'Original recipes and practical methods developed around a suitable ingredient, product or seasonal brief.', 'larder' ); ?></p></article>
					<article><span>02</span><h3><?php esc_html_e( 'Editorial partnerships', 'larder' ); ?></h3><p><?php esc_html_e( 'Helpful sponsored stories, guides or collections that remain relevant to the Kitchen Table audience.', 'larder' ); ?></p></article>
					<article><span>03</span><h3><?php esc_html_e( 'Food and kitchen projects', 'larder' ); ?></h3><p><?php esc_html_e( 'Selected collaborations involving cooking, hospitality, practical kitchen knowledge or content development.', 'larder' ); ?></p></article>
				</div>
			</div>
		</section>

		<section class="nkt-growth-page-content nkt-growth-page-content--split">
			<div class="container nkt-growth-content-grid">
				<article class="prose">
					<?php if ( trim( get_the_content() ) ) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<h2><?php esc_html_e( 'A good partnership should feel natural.', 'larder' ); ?></h2>
						<p><?php esc_html_e( 'Please include the organisation, project outline, expected timing, deliverables and budget range when getting in touch. Products or services are never guaranteed coverage simply because they are supplied.', 'larder' ); ?></p>
					<?php endif; ?>
				</article>
				<aside class="nkt-growth-contact-card">
					<p class="eyebrow"><?php esc_html_e( 'Have a project in mind?', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'Let’s discuss whether it belongs at the table.', 'larder' ); ?></h2>
					<a class="text-link" href="<?php echo esc_url( $contact_url ); ?>" data-nkt-event="partnership_contact" data-nkt-label="page_card"><?php esc_html_e( 'Contact Nigel', 'larder' ); ?> →</a>
				</aside>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
