<?php
/**
 * Single post template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="single-recipe">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'recipe-article' ); ?>>
			<header class="recipe-hero">
				<div class="container recipe-hero__grid">
					<div class="recipe-hero__content">
						<p class="eyebrow"><?php echo esc_html( get_the_category_list( ' · ' ) ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="recipe-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="recipe-actions">
							<a class="button button-primary" href="#recipe-card"><?php esc_html_e( 'Jump to recipe', 'larder' ); ?></a>
							<button class="button button-secondary" type="button" onclick="window.print()"><?php esc_html_e( 'Print', 'larder' ); ?></button>
						</div>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="recipe-hero__media"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
				</div>
			</header>

			<div class="container recipe-layout">
				<div class="recipe-content">
					<?php the_content(); ?>
				</div>
				<aside class="recipe-sidebar" aria-label="<?php esc_attr_e( 'Recipe information', 'larder' ); ?>">
					<div class="recipe-sidebar__card">
						<p class="eyebrow"><?php esc_html_e( 'Recipe notes', 'larder' ); ?></p>
						<p><?php esc_html_e( 'Use the recipe card for ingredients, timings, servings and printing options.', 'larder' ); ?></p>
					</div>
				</aside>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer();
