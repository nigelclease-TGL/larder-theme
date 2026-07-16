<?php
/**
 * Recipes landing page.
 *
 * Automatically used for a WordPress page with the slug "recipes".
 *
 * @package Larder
 */

get_header();

$excluded_category_ids = array_filter( array( (int) get_option( 'default_category' ) ) );
foreach ( array( 'baking-guides', 'kitchen-notes' ) as $excluded_slug ) {
	$excluded_category = get_category_by_slug( $excluded_slug );
	if ( $excluded_category ) {
		$excluded_category_ids[] = (int) $excluded_category->term_id;
	}
}

$featured_categories = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 10,
		'exclude'    => array_unique( $excluded_category_ids ),
	)
);

$paged   = max( 1, get_query_var( 'paged' ) );
$recipes = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 12,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
	)
);
?>

<main id="primary" class="recipes-hub">
	<header class="recipes-hub__hero">
		<div class="container recipes-hub__hero-inner">
			<div>
				<p class="eyebrow"><?php esc_html_e( "From Nigel's kitchen table", 'larder' ); ?></p>
				<h1><?php esc_html_e( 'Recipes for every season', 'larder' ); ?></h1>
				<p><?php esc_html_e( 'Browse reliable bakes, desserts, comforting dishes and seasonal favourites made to be shared.', 'larder' ); ?></p>
			</div>
			<div class="recipes-hub__search">
				<p class="recipes-hub__search-label"><?php esc_html_e( 'Find something delicious', 'larder' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<?php if ( $featured_categories ) : ?>
		<section class="recipes-hub__categories" aria-labelledby="recipes-categories-title">
			<div class="container">
				<header class="section-heading section-heading--split">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'Browse by type', 'larder' ); ?></p>
						<h2 id="recipes-categories-title"><?php esc_html_e( 'Recipe categories', 'larder' ); ?></h2>
					</div>
				</header>
				<div class="recipes-category-list">
					<?php foreach ( $featured_categories as $category ) : ?>
						<a class="recipes-category-pill" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
							<span><?php echo esc_html( $category->name ); ?></span>
							<small><?php echo esc_html( sprintf( _n( '%s recipe', '%s recipes', $category->count, 'larder' ), number_format_i18n( $category->count ) ) ); ?></small>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section class="recipes-hub__latest" aria-labelledby="all-recipes-title">
		<div class="container">
			<header class="section-heading section-heading--split">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'The full recipe box', 'larder' ); ?></p>
					<h2 id="all-recipes-title"><?php esc_html_e( 'All recipes', 'larder' ); ?></h2>
				</div>
			</header>

			<?php if ( $recipes->have_posts() ) : ?>
				<div class="recipe-grid">
					<?php
					while ( $recipes->have_posts() ) :
						$recipes->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>

				<nav class="recipes-pagination" aria-label="<?php esc_attr_e( 'Recipes pagination', 'larder' ); ?>">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'total'     => $recipes->max_num_pages,
								'current'   => $paged,
								'mid_size'  => 1,
								'prev_text' => __( 'Previous', 'larder' ),
								'next_text' => __( 'Next', 'larder' ),
							)
						)
					);
					?>
				</nav>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No recipes are available yet.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
