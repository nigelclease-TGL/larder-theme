<?php
/**
 * Recipe collection archive template.
 *
 * @package Larder
 */

get_header();

global $wp_query;

$recipes_url = nkt_page_url( array( 'recipes' ), '/recipes/' );
$sort        = nkt_get_requested_discovery_sort( 'newest' );
$count       = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>
<main id="primary" class="collection-archive nkt-discovery-page">
	<header class="collection-hero nkt-discovery-hero nkt-discovery-hero--compact">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( 'Curated at the kitchen table', 'larder' ); ?></p>
				<h1><?php single_term_title(); ?></h1>
				<?php if ( term_description() ) : ?>
					<div class="collection-description"><?php echo wp_kses_post( term_description() ); ?></div>
				<?php else : ?>
					<p><?php esc_html_e( 'A hand-picked collection of dependable recipes to cook, share and return to.', 'larder' ); ?></p>
				<?php endif; ?>
				<p class="nkt-results-count"><?php echo esc_html( nkt_get_recipe_count_label( $count ) ); ?></p>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Keep exploring', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Search the full recipe box.', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<section class="collection-content nkt-discovery-results" aria-labelledby="collection-recipes-title">
		<div class="container">
			<header class="nkt-results-header nkt-results-header--small">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Inside this collection', 'larder' ); ?></p>
					<h2 id="collection-recipes-title"><?php esc_html_e( 'Recipes to make next', 'larder' ); ?></h2>
				</div>
				<form class="nkt-discovery-toolbar nkt-discovery-toolbar--sort" method="get">
					<label>
						<span><?php esc_html_e( 'Sort by', 'larder' ); ?></span>
						<select name="sort">
							<?php foreach ( nkt_get_discovery_sort_options() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<button class="button button-primary" type="submit"><?php esc_html_e( 'Apply', 'larder' ); ?></button>
				</form>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="recipe-grid collection-recipe-grid nkt-discovery-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination( array( 'add_args' => 'newest' !== $sort ? array( 'sort' => $sort ) : array() ) ); ?></div>
			<?php else : ?>
				<div class="nkt-empty-state">
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
