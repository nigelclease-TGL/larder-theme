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
$featured_categories    = nkt_get_recipe_discovery_categories( 12 );
$excluded_category_ids  = nkt_get_non_recipe_category_ids();
$selected_category_slug = isset( $_GET['recipe_category'] ) ? sanitize_title( wp_unslash( $_GET['recipe_category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$selected_category      = $selected_category_slug ? get_category_by_slug( $selected_category_slug ) : null;
$sort                   = nkt_get_requested_discovery_sort( 'newest' );
$paged                  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

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
	<header class="recipes-hub__hero nkt-discovery-hero">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( "From Nigel's kitchen table", 'larder' ); ?></p>
				<h1><?php esc_html_e( 'Find your next favourite recipe.', 'larder' ); ?></h1>
				<p><?php esc_html_e( 'Browse dependable bakes, seasonal dishes and recipes designed for real kitchens and good food around the table.', 'larder' ); ?></p>
				<div class="nkt-discovery-hero__meta">
					<span><?php echo esc_html( nkt_get_recipe_count_label( $recipes->found_posts ) ); ?></span>
					<span><?php esc_html_e( 'Tested carefully', 'larder' ); ?></span>
					<span><?php esc_html_e( 'Made to share', 'larder' ); ?></span>
				</div>
			</div>

			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Search the recipe box', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'What would you like to cook?', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
				<p class="nkt-discovery-search-card__hint"><?php esc_html_e( 'Try an ingredient, dish or occasion.', 'larder' ); ?></p>
			</div>
		</div>
	</header>

	<?php if ( $featured_categories ) : ?>
		<section class="nkt-category-navigation" aria-labelledby="recipes-categories-title">
			<div class="container">
				<header class="section-heading section-heading--split nkt-category-navigation__heading">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Browse by type', 'larder' ); ?></p>
						<h2 id="recipes-categories-title"><?php esc_html_e( 'Explore the recipe box', 'larder' ); ?></h2>
					</div>
					<p><?php esc_html_e( 'Start with a category, then refine the results below.', 'larder' ); ?></p>
				</header>

				<div class="nkt-category-links">
					<a class="nkt-category-link<?php echo $selected_category ? '' : ' is-active'; ?>" href="<?php echo esc_url( $page_url ); ?>">
						<span><?php esc_html_e( 'All recipes', 'larder' ); ?></span>
						<small><?php esc_html_e( 'Browse everything', 'larder' ); ?></small>
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
						);
						?>
						<a class="nkt-category-link<?php echo $selected_category && (int) $selected_category->term_id === (int) $category->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( $category_url ); ?>">
							<span><?php echo esc_html( $category->name ); ?></span>
							<small><?php echo esc_html( nkt_get_recipe_count_label( $category->count ) ); ?></small>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="nkt-discovery-results" aria-labelledby="all-recipes-title">
		<div class="container">
			<header class="nkt-results-header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'The full recipe box', 'larder' ); ?></p>
					<h2 id="all-recipes-title">
						<?php
						if ( $selected_category ) {
							printf( esc_html__( '%s recipes', 'larder' ), esc_html( $selected_category->name ) );
						} else {
							esc_html_e( 'All recipes', 'larder' );
						}
						?>
					</h2>
					<p class="nkt-results-count"><?php echo esc_html( nkt_get_recipe_count_label( $recipes->found_posts ) ); ?></p>
				</div>

				<form class="nkt-discovery-toolbar" method="get" action="<?php echo esc_url( $page_url ); ?>">
					<label>
						<span><?php esc_html_e( 'Category', 'larder' ); ?></span>
						<select name="recipe_category">
							<option value=""><?php esc_html_e( 'All recipes', 'larder' ); ?></option>
							<?php foreach ( $featured_categories as $category ) : ?>
								<option value="<?php echo esc_attr( $category->slug ); ?>" <?php selected( $selected_category ? $selected_category->slug : '', $category->slug ); ?>><?php echo esc_html( $category->name ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
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
						<a class="nkt-clear-filters" href="<?php echo esc_url( $page_url ); ?>"><?php esc_html_e( 'Clear', 'larder' ); ?></a>
					<?php endif; ?>
				</form>
			</header>

			<?php if ( $recipes->have_posts() ) : ?>
				<div class="recipe-grid nkt-discovery-grid">
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
								'total'     => $recipes->max_num_pages,
								'current'   => $paged,
								'mid_size'  => 1,
								'prev_text' => __( 'Previous', 'larder' ),
								'next_text' => __( 'Next', 'larder' ),
								'add_args'  => $pagination_args,
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
					<a class="button button-primary" href="<?php echo esc_url( $page_url ); ?>"><?php esc_html_e( 'Browse all recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
