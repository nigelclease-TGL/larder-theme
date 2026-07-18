<?php
/**
 * Contact page template for the contact-me and contact pages.
 *
 * @package Larder
 */

get_header();
$instagram_url = get_theme_mod( 'larder_instagram_url', 'https://www.instagram.com/thegourmetlarder/' );
$pinterest_url = get_theme_mod( 'larder_pinterest_url', 'https://hu.pinterest.com/thegourmetlarder/' );
$facebook_url  = get_theme_mod( 'larder_facebook_url', 'https://www.facebook.com/thegourmetlarder/' );
$recipes_url   = nkt_page_url( array( 'recipes' ), '/recipes/' );
?>
<main id="primary" class="contact-page contact-page--editorial" data-nkt-location="contact_page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="contact-page__hero">
			<div class="container contact-page__hero-grid">
				<div class="contact-page__hero-inner">
					<p class="eyebrow"><?php esc_html_e( 'Come and say hello', 'larder' ); ?></p>
					<h1><?php the_title(); ?></h1>
					<p><?php esc_html_e( 'Questions about a recipe, collaboration ideas or simply something you would like to share? Send a message below.', 'larder' ); ?></p>
				</div>
				<div class="contact-page__topics" aria-label="<?php esc_attr_e( 'Reasons to get in touch', 'larder' ); ?>">
					<div><span>01</span><strong><?php esc_html_e( 'Recipe questions', 'larder' ); ?></strong></div>
					<div><span>02</span><strong><?php esc_html_e( 'Partnerships', 'larder' ); ?></strong></div>
					<div><span>03</span><strong><?php esc_html_e( 'A note from your kitchen', 'larder' ); ?></strong></div>
				</div>
			</div>
		</header>

		<section class="contact-page__body">
			<div class="container contact-page__grid">
				<article class="prose contact-page__form">
					<p class="contact-page__form-intro"><?php esc_html_e( 'Use the form below and include the recipe name or page link when your message relates to something on the site.', 'larder' ); ?></p>
					<?php the_content(); ?>
				</article>

				<aside class="contact-page__aside">
					<div class="contact-page__card">
						<p class="eyebrow"><?php esc_html_e( 'Elsewhere', 'larder' ); ?></p>
						<h2><?php esc_html_e( 'Follow the kitchen', 'larder' ); ?></h2>
						<p><?php esc_html_e( 'New recipes, seasonal ideas and a little behind-the-scenes inspiration.', 'larder' ); ?></p>
						<ul class="contact-page__socials">
							<?php if ( $instagram_url ) : ?><li><a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a></li><?php endif; ?>
							<?php if ( $pinterest_url ) : ?><li><a href="<?php echo esc_url( $pinterest_url ); ?>" target="_blank" rel="noopener noreferrer">Pinterest</a></li><?php endif; ?>
							<?php if ( $facebook_url ) : ?><li><a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a></li><?php endif; ?>
						</ul>
					</div>
					<a class="contact-page__recipe-link" href="<?php echo esc_url( $recipes_url ); ?>" data-nkt-event="recipes_open" data-nkt-label="contact_page"><span><?php esc_html_e( 'Looking for cooking inspiration?', 'larder' ); ?></span><strong><?php esc_html_e( 'Browse the recipe box', 'larder' ); ?> →</strong></a>
				</aside>
			</div>
		</section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
