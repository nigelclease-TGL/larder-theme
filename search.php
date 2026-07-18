<?php
/**
 * Search results template.
 *
 * @package Larder
 */

get_header();

global $wp_query;

$query_text = get_search_query();
$count      = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
$sort       = nkt_get_requested_discovery_sort( 'relevance', true );
$categories = nkt_get_recipe_discovery_categories( 8 );
$latest     = null;

if ( ! have_posts() ) {
	$latest = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'category__not_in'    => nkt_get_non_recipe_category_ids(),
			'no_found_rows'       => true,
		)
	);
}
?>
<main id="primary" class="search-results-page nkt-discovery-page">
	<header class="archive-header nkt-discovery-hero nkt-discovery-hero--search">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( 'Search the kitchen table', 'larder' ); ?></p>
				<h1><?php printf( esc_html__( 'Results for “%s”', 'larder' ), esc_html( $query_text ) ); ?></h1>
				<p><?php echo esc_html( nkt_get_recipe_count_label( $count ) ); ?></p>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Try another search', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Search by recipe or ingredient.', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<section class="content-section nkt-discovery-results">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<header class="nkt-results-header nkt-results-header--small">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Search results', 'larder' ); ?></p>
						<h2><?php esc_html_e( 'What we found', 'larder' ); ?></h2>
					</div>
					<form class="nkt-discovery-toolbar nkt-discovery-toolbar--sort" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="hidden" name="s" value="<?php echo esc_attr( $query_text ); ?>">
						<label>
							<span><?php esc_html_e( 'Sort by', 'larder' ); ?></span>
							<select name="sort">
								<?php foreach ( nkt_get_discovery_sort_options( true ) as $value => $label ) : ?>
									<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
						</label>
						<button class="button button-primary" type="submit"><?php esc_html_e( 'Apply', 'larder' ); ?></button>
					</form>
				</header>

				<div class="recipe-grid nkt-discovery-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination( array( 'add_args' => array_filter( array( 'sort' => 'relevance' !== $sort ? $sort : false ) ) ) ); ?></div>
			<?php else : ?>
				<div class="nkt-empty-state nkt-empty-state--search">
					<p class="eyebrow"><?php esc_html_e( 'Nothing matched', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'Let’s try a different route.', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Try a shorter phrase, a main ingredient or one of the popular categories below.', 'larder' ); ?></p>
					<?php if ( $categories ) : ?>
						<div class="nkt-empty-state__links">
							<?php foreach ( $categories as $category ) : ?>
								<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $latest && $latest->have_posts() ) : ?>
					<section class="nkt-search-suggestions" aria-labelledby="search-suggestions-title">
						<header class="section-heading">
							<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
							<h2 id="search-suggestions-title"><?php esc_html_e( 'Try one of these instead', 'larder' ); ?></h2>
						</header>
						<div class="recipe-grid">
							<?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
								<?php get_template_part( 'template-parts/content', 'card' ); ?>
							<?php endwhile; ?>
						</div>
						<?php wp_reset_postdata(); ?>
					</section>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
