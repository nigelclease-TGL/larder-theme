<?php
/**
 * Curated reader favourites section.
 *
 * @package Larder
 */

$excluded_category_ids = array();
foreach ( array( 'kitchen-notes', 'baking-guides' ) as $excluded_slug ) {
	$excluded_category = get_category_by_slug( $excluded_slug );
	if ( $excluded_category ) {
		$excluded_category_ids[] = (int) $excluded_category->term_id;
	}
}

$selected_recipe_ids = nkt_homepage_recipe_ids(
	'larder_home_favourite_recipe_',
	4,
	array(
		'larder_popular_recipe_',
		'larder_favourite_recipe_',
		'larder_homepage_popular_recipe_',
		'nkt_popular_recipe_',
		'nkt_home_popular_recipe_',
	),
	array(
		'larder_home_favourite_recipe_ids',
		'larder_home_popular_recipe_ids',
		'larder_popular_recipe_ids',
		'nkt_home_popular_recipe_ids',
	)
);

$query_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $selected_recipe_ids ? count( $selected_recipe_ids ) : 4,
	'ignore_sticky_posts' => true,
	'category__not_in'    => $excluded_category_ids,
	'no_found_rows'       => true,
);

if ( $selected_recipe_ids ) {
	$query_args['post__in'] = $selected_recipe_ids;
	$query_args['orderby']  = 'post__in';
} else {
	$query_args['orderby'] = array(
		'comment_count' => 'DESC',
		'date'          => 'DESC',
	);
}

$popular_recipes = new WP_Query( $query_args );

if ( ! $popular_recipes->have_posts() ) {
	return;
}

$recipes_url = nkt_page_url( array( 'recipes' ), '/recipes/' );
?>
<section class="home-section reader-favourites" aria-labelledby="favourites-title">
	<div class="container">
		<header class="section-heading section-heading--split reader-favourites__heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Tried, tested and loved', 'larder' ); ?></p>
				<h2 id="favourites-title"><?php esc_html_e( "This is what everyone’s cooking", 'larder' ); ?></h2>
			</div>
			<div class="reader-favourites__intro">
				<p><?php esc_html_e( 'These are the recipes readers come back to, share and make again. Tried, trusted and worth keeping close.', 'larder' ); ?></p>
				<a class="text-link" href="<?php echo esc_url( $recipes_url ); ?>"><?php esc_html_e( 'View all recipes', 'larder' ); ?> <span aria-hidden="true">→</span></a>
			</div>
		</header>

		<div class="reader-favourites__grid reader-favourites__grid--curated">
			<?php while ( $popular_recipes->have_posts() ) : $popular_recipes->the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'card' ); ?>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
