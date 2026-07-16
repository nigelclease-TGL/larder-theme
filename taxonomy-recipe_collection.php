<?php
/**
 * Recipe collection archive template.
 *
 * @package Larder
 */

get_header();
$recipes_url = nkt_page_url( array( 'recipes' ), '/recipes/' );
?>
<main id="primary" class="collection-archive">
	<header class="collection-hero">
		<div class="container collection-hero__inner">
			<p class="eyebrow"><?php esc_html_e( 'Curated at the kitchen table', 'larder' ); ?></p>
			<h1><?php single_term_title(); ?></h1>
			<?php if ( term_description() ) : ?>
				<div class="collection-description"><?php echo wp_kses_post( term_description() ); ?></div>
			<?php else : ?>
				<p><?php esc_html_e( 'A hand-picked collection of dependable recipes to bake, share and return to.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<section class="collection-content" aria-labelledby="collection-recipes-title">
		<div class="container">
			<h2 id="collection-recipes-title" class="screen-reader-text"><?php esc_html_e( 'Recipes in this collection', 'larder' ); ?></h2>
			<?php if ( have_posts() ) : ?>
				<div class="recipe-grid collection-recipe-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination(); ?></div>
			<?php else : ?>
				<div class="collection-empty">
					<h2><?php esc_html_e( 'Recipes are coming soon', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'This collection is being prepared. Browse the latest recipes in the meantime.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $recipes_url ); ?>"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<section class="collection-footer-cta">
		<div class="container collection-footer-cta__inner">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Explore more', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Browse every recipe collection', 'larder' ); ?></h2>
			</div>
			<a class="button button-secondary" href="<?php echo esc_url( nkt_get_collections_url() ); ?>"><?php esc_html_e( 'View collections', 'larder' ); ?></a>
		</div>
	</section>
</main>
<?php get_footer(); ?>
