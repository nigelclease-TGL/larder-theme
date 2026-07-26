<?php
/**
 * Recipes landing page.
 *
 * Automatically used for a WordPress page with the slug "recipes".
 *
 * @package Larder
 */

get_header();

$page_url               = get_permalink();
$featured_categories    = nkt_get_recipe_discovery_categories( 100 );
$excluded_category_ids  = nkt_get_non_recipe_category_ids();
$selected_category_slug = isset( $_GET['recipe_category'] ) ? sanitize_title( wp_unslash( $_GET['recipe_category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$selected_category      = $selected_category_slug ? get_category_by_slug( $selected_category_slug ) : null;
$sort                   = nkt_get_requested_discovery_sort( 'newest' );
$paged                  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$results_anchor         = '#recipe-results';

if ( $selected_category && in_array( (int) $selected_category->term_id, $excluded_category_ids, true ) ) {
	$selected_category = null;
}

$query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 12,
	'paged'               => $paged,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => false,
);

if ( $selected_category ) {
	$query_args['cat'] = (int) $selected_category->term_id;
} else {
	$query_args['category__not_in'] = $excluded_category_ids;
}

$query_args = array_merge( $query_args, nkt_get_discovery_order_args( $sort ) );
$recipes    = new WP_Query( $query_args );

$pagination_args = array();
if ( $selected_category ) {
	$pagination_args['recipe_category'] = $selected_category->slug;
}
if ( 'newest' !== $sort ) {
	$pagination_args['sort'] = $sort;
}
?>

<main id="primary" class="recipes-hub nkt-discovery-page">
	<section class="nkt-recipe-box" aria-labelledby="recipe-box-title">
		<div class="container">
			<header class="nkt-recipe-box__header">
				<h1 id="recipe-box-title"><?php esc_html_e( 'Explore the recipe box', 'larder' ); ?></h1>
				<p><?php esc_html_e( 'Find the perfect recipe for every occasion.', 'larder' ); ?></p>
			</header>

			<div class="nkt-recipe-box__layout">
				<section class="nkt-recipe-box__categories" aria-labelledby="recipe-category-title">
					<h2 id="recipe-category-title"><?php esc_html_e( 'Browse by category', 'larder' ); ?></h2>

					<div class="nkt-recipe-box__category-grid">
						<a class="nkt-recipe-box__category<?php echo $selected_category ? '' : ' is-active'; ?>" href="<?php echo esc_url( $page_url . $results_anchor ); ?>">
							<span><?php esc_html_e( 'All recipes', 'larder' ); ?></span>
							<span class="nkt-recipe-box__category-arrow" aria-hidden="true">→</span>
						</a>

						<?php foreach ( $featured_categories as $category ) : ?>
							<?php
							$category_url = add_query_arg(
								array_filter(
									array(
										'recipe_category' => $category->slug,
										'sort'            => 'newest' !== $sort ? $sort : false,
									)
								),
								$page_url
							) . $results_anchor;
							?>
							<a class="nkt-recipe-box__category<?php echo $selected_category && (int) $selected_category->term_id === (int) $category->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( $category_url ); ?>">
								<span><?php echo esc_html( $category->name ); ?></span>
								<span class="nkt-recipe-box__category-arrow" aria-hidden="true">→</span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>

				<aside class="nkt-recipe-box__search" aria-labelledby="recipe-search-title">
					<p class="eyebrow"><?php esc_html_e( 'Find a recipe', 'larder' ); ?></p>
					<h2 id="recipe-search-title"><?php esc_html_e( 'Search by recipe, ingredient or occasion.', 'larder' ); ?></h2>

					<form role="search" method="get" class="nkt-recipe-box__search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<label>
							<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'larder' ); ?></span>
							<input type="search" class="nkt-recipe-box__search-field" placeholder="<?php echo esc_attr_x( 'Search recipes or ingredients…', 'placeholder', 'larder' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off">
						</label>
						<button type="submit" class="nkt-recipe-box__search-submit" aria-label="<?php echo esc_attr_x( 'Search recipes', 'submit button', 'larder' ); ?>">
							<svg aria-hidden="true" viewBox="0 0 24 24" width="22" height="22" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
						</button>
					</form>
				</aside>
			</div>
		</div>
	</section>

	<section id="recipe-results" class="nkt-discovery-results nkt-recipes-results" aria-labelledby="all-recipes-title">
		<div class="container">
			<header class="nkt-results-header nkt-recipes-results__header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Recipes you’ll love', 'larder' ); ?></p>
					<h2 id="all-recipes-title">
						<?php
						if ( $selected_category ) {
							printf( esc_html__( '%s recipes', 'larder' ), esc_html( $selected_category->name ) );
						} else {
							esc_html_e( 'Browse all recipes', 'larder' );
						}
						?>
					</h2>
				</div>

				<form class="nkt-discovery-toolbar nkt-recipes-sort" method="get" action="<?php echo esc_url( $page_url . $results_anchor ); ?>">
					<?php if ( $selected_category ) : ?>
						<input type="hidden" name="recipe_category" value="<?php echo esc_attr( $selected_category->slug ); ?>">
					<?php endif; ?>
					<label>
						<span><?php esc_html_e( 'Sort by', 'larder' ); ?></span>
						<select name="sort">
							<?php foreach ( nkt_get_discovery_sort_options() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<button class="button button-primary" type="submit"><?php esc_html_e( 'Apply', 'larder' ); ?></button>
					<?php if ( $selected_category || 'newest' !== $sort ) : ?>
						<a class="nkt-clear-filters" href="<?php echo esc_url( $page_url . $results_anchor ); ?>"><?php esc_html_e( 'Clear', 'larder' ); ?></a>
					<?php endif; ?>
				</form>
			</header>

			<?php if ( $recipes->have_posts() ) : ?>
				<div class="recipe-grid nkt-discovery-grid nkt-recipes-results__grid">
					<?php
					while ( $recipes->have_posts() ) :
						$recipes->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<nav class="recipes-pagination pagination" aria-label="<?php esc_attr_e( 'Recipes pagination', 'larder' ); ?>">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'total'        => $recipes->max_num_pages,
								'current'      => $paged,
								'mid_size'     => 1,
								'prev_text'    => __( 'Previous', 'larder' ),
								'next_text'    => __( 'Next', 'larder' ),
								'add_args'     => $pagination_args,
								'add_fragment' => $results_anchor,
							)
						)
					);
					?>
				</nav>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="nkt-empty-state">
					<p class="eyebrow"><?php esc_html_e( 'Nothing here yet', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'Try another category.', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'This part of the recipe box is still being filled. Browse all recipes to find something delicious.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( $page_url . $results_anchor ); ?>"><?php esc_html_e( 'Browse all recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
