<?php
/**
 * 404 template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="not-found-page">
	<section class="not-found">
		<div class="container not-found__inner">
			<p class="eyebrow">404</p>
			<h1><?php esc_html_e( 'This recipe has gone missing.', 'larder' ); ?></h1>
			<p><?php esc_html_e( 'Try searching for another bake or return to the homepage.', 'larder' ); ?></p>
			<?php get_search_form(); ?>
			<div class="button-row">
				<a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'larder' ); ?></a>
			</div>
		</div>
	</section>
</main>
<?php get_footer();
