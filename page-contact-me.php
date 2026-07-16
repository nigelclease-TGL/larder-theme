<?php
/**
 * Contact page template for the existing "contact-me" page.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="contact-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="contact-page__hero">
			<div class="container contact-page__hero-inner">
				<p class="eyebrow"><?php esc_html_e( 'Come and say hello', 'larder' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'Questions about a recipe, collaboration ideas or simply something you would like to share? Send a message below.', 'larder' ); ?></p>
			</div>
		</header>

		<section class="contact-page__body">
			<div class="container contact-page__grid">
				<article class="prose contact-page__form">
					<?php the_content(); ?>
				</article>

				<aside class="contact-page__aside">
					<div class="contact-page__card">
						<p class="eyebrow"><?php esc_html_e( 'Elsewhere', 'larder' ); ?></p>
						<h2><?php esc_html_e( 'Follow the kitchen', 'larder' ); ?></h2>
						<ul class="contact-page__socials">
							<li><a href="https://www.instagram.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer">Instagram</a></li>
							<li><a href="https://hu.pinterest.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer">Pinterest</a></li>
							<li><a href="https://www.facebook.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer">Facebook</a></li>
						</ul>
					</div>
				</aside>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer();
