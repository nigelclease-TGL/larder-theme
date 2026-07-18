<?php
/**
 * Archive template.
 *
 * @package Larder
 */

get_header();

global $wp_query;

$sort       = nkt_get_requested_discovery_sort( 'newest' );
$categories = nkt_get_recipe_discovery_categories( 8 );
$count      = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>
<main id="primary" class="archive-page nkt-discovery-page">
	<header class="archive-header nkt-discovery-hero nkt-discovery-hero--compact">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( 'Browse the kitchen table', 'larder' ); ?></p>
				<?php the_archive_title( '<h1>', '</h1>' ); ?>
				<?php if ( get_the_archive_description() ) : ?>
					<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
				<?php else : ?>
					<p><?php esc_html_e( 'Reliable recipes and practical kitchen inspiration, gathered together in one place.', 'larder' ); ?></p>
				<?php endif; ?>
				<p class="nkt-results-count"><?php echo esc_html( nkt_get_recipe_count_label( $count ) ); ?></p>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Search instead', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Looking for something specific?', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<?php if ( $categories ) : ?>
		<nav class="nkt-quick-categories" aria-label="<?php esc_attr_e( 'Popular recipe categories', 'larder' ); ?>">
			<div class="container nkt-quick-categories__inner">
				<span><?php esc_html_e( 'Explore:', 'larder' ); ?></span>
				<?php foreach ( $categories as $category ) : ?>
					<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
				<?php endforeach; ?>
			</div>
		</nav>
	<?php endif; ?>

	<section class="content-section nkt-discovery-results">
		<div class="container">
			<header class="nkt-results-header nkt-results-header--small">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Browse the collection', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'Recipes and notes', 'larder' ); ?></h2>
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
				<div class="recipe-grid nkt-discovery-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination( array( 'add_args' => 'newest' !== $sort ? array( 'sort' => $sort ) : array() ) ); ?></div>
			<?php else : ?>
				<div class="nkt-empty-state">
					<h2><?php esc_html_e( 'No recipes were found.', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Try another category or search the full recipe box.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( nkt_page_url( array( 'recipes' ), '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse all recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
