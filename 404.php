<?php
/**
 * 404 template.
 *
 * @package Larder
 */

get_header();

$latest_recipes = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'category__not_in'    => nkt_get_non_recipe_category_ids(),
		'no_found_rows'       => true,
	)
);
?>
<main id="primary" class="not-found-page nkt-discovery-page">
	<section class="not-found nkt-not-found-hero">
		<div class="container not-found__inner">
			<p class="eyebrow">404 · <?php esc_html_e( 'Page not found', 'larder' ); ?></p>
			<h1><?php esc_html_e( 'This recipe has wandered off.', 'larder' ); ?></h1>
			<p><?php esc_html_e( 'Search the recipe box, return to the homepage or try one of the latest recipes below.', 'larder' ); ?></p>
			<?php get_search_form(); ?>
			<div class="button-row">
				<a class="button button-primary" href="<?php echo esc_url( nkt_page_url( array( 'recipes' ), '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></a>
				<a class="button button-secondary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to homepage', 'larder' ); ?></a>
			</div>
		</div>
	</section>

	<?php if ( $latest_recipes->have_posts() ) : ?>
		<section class="nkt-404-suggestions" aria-labelledby="latest-404-title">
			<div class="container">
				<header class="section-heading section-heading--split">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
						<h2 id="latest-404-title"><?php esc_html_e( 'Perhaps one of these?', 'larder' ); ?></h2>
					</div>
				</header>
				<div class="recipe-grid">
					<?php while ( $latest_recipes->have_posts() ) : $latest_recipes->the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
