<?php
/**
 * Front page template.
 *
 * @package Larder
 */

get_header();

$hero_image_id = absint( get_theme_mod( 'larder_hero_image', 0 ) );
$recipes_url   = nkt_page_url( array( 'recipes' ), '/recipes/' );
$notes_url     = nkt_page_url( array( 'kitchen-notes', 'notes' ), '/kitchen-notes/' );

$excluded_category_ids = array();
foreach ( array( 'kitchen-notes', 'baking-guides' ) as $excluded_slug ) {
	$excluded_category = get_category_by_slug( $excluded_slug );
	if ( $excluded_category ) {
		$excluded_category_ids[] = (int) $excluded_category->term_id;
	}
}

$featured_recipe_id = nkt_homepage_recipe_id(
	'larder_home_hero_recipe_id',
	array(
		'larder_hero_recipe_id',
		'larder_featured_recipe_id',
		'larder_homepage_hero_recipe_id',
		'nkt_hero_recipe_id',
		'nkt_home_hero_recipe_id',
	)
);

$display_image_id    = $hero_image_id ? $hero_image_id : ( $featured_recipe_id ? get_post_thumbnail_id( $featured_recipe_id ) : 0 );
$featured_title      = $featured_recipe_id ? get_the_title( $featured_recipe_id ) : '';
$featured_url        = $featured_recipe_id ? get_permalink( $featured_recipe_id ) : '';
$featured_categories = $featured_recipe_id ? get_the_category( $featured_recipe_id ) : array();
$featured_label      = ! empty( $featured_categories ) ? $featured_categories[0]->name : __( 'Featured recipe', 'larder' );
?>

<main id="primary">
	<section class="hero">
		<div class="container hero-grid">
			<div class="hero-content">
				<p class="eyebrow"><?php esc_html_e( "Welcome to Nigel's Kitchen Table", 'larder' ); ?></p>
				<h1><span><?php esc_html_e( 'Seasonal cooking.', 'larder' ); ?></span><span><?php esc_html_e( 'Beautiful recipes.', 'larder' ); ?></span><span><?php esc_html_e( 'Made for real life.', 'larder' ); ?></span></h1>
				<p class="hero-copy"><?php esc_html_e( 'Discover seasonal recipes, practical kitchen knowledge and thoughtful cooking inspiration—from everyday meals to special occasions.', 'larder' ); ?></p>
				<div class="button-row">
					<a class="button button-primary" href="<?php echo esc_url( $recipes_url ); ?>" data-nkt-event="recipes_open" data-nkt-label="homepage_hero"><?php esc_html_e( 'Explore recipes', 'larder' ); ?> <span aria-hidden="true">→</span></a>
					<a class="button button-secondary" href="<?php echo esc_url( $notes_url ); ?>" data-nkt-event="kitchen_notes_open" data-nkt-label="homepage_hero"><?php esc_html_e( 'Kitchen Notes', 'larder' ); ?></a>
				</div>
			</div>

			<div class="hero-media">
				<?php if ( $display_image_id ) : ?>
					<?php echo wp_get_attachment_image( $display_image_id, 'larder-hero', false, array( 'loading' => 'eager', 'decoding' => 'async', 'fetchpriority' => 'high', 'sizes' => '(max-width: 900px) 92vw, 54vw' ) ); ?>
				<?php else : ?>
					<div class="hero-media__placeholder"><span><?php esc_html_e( 'Add a hero image in Appearance → Customise → Nigel’s Kitchen Table Homepage.', 'larder' ); ?></span></div>
				<?php endif; ?>

				<?php if ( $featured_recipe_id ) : ?>
					<a class="hero-feature-card" href="<?php echo esc_url( $featured_url ); ?>" data-nkt-event="featured_recipe_open" data-nkt-label="homepage_hero" aria-label="<?php echo esc_attr( sprintf( __( 'View featured recipe: %s', 'larder' ), $featured_title ) ); ?>">
						<p class="hero-feature-card__label"><?php echo esc_html( $featured_label ); ?></p>
						<strong><?php echo esc_html( $featured_title ); ?></strong>
						<div class="hero-feature-card__meta">
							<span><?php esc_html_e( 'Featured recipe', 'larder' ); ?></span>
							<span><?php esc_html_e( 'Tested carefully', 'larder' ); ?></span>
						</div>
						<span class="hero-feature-card__cta"><?php esc_html_e( 'View recipe', 'larder' ); ?> <span aria-hidden="true">→</span></span>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="home-section latest-recipes" aria-labelledby="latest-recipes-title">
		<div class="container">
			<header class="section-heading section-heading--split">
				<div><p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p><h2 id="latest-recipes-title"><?php esc_html_e( 'Latest recipes', 'larder' ); ?></h2></div>
				<a class="text-link" href="<?php echo esc_url( $recipes_url ); ?>"><?php esc_html_e( 'View all recipes', 'larder' ); ?> <span aria-hidden="true">→</span></a>
			</header>
			<?php
			$selected_latest_ids = nkt_homepage_recipe_ids(
				'larder_home_latest_recipe_',
				6,
				array(
					'larder_latest_recipe_',
					'larder_homepage_latest_recipe_',
					'nkt_latest_recipe_',
					'nkt_home_latest_recipe_',
				),
				array(
					'larder_home_latest_recipe_ids',
					'larder_latest_recipe_ids',
					'nkt_home_latest_recipe_ids',
				)
			);

			$latest_args = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $selected_latest_ids ? count( $selected_latest_ids ) : 6,
				'ignore_sticky_posts' => true,
				'category__not_in'    => $excluded_category_ids,
				'no_found_rows'       => true,
			);

			if ( $selected_latest_ids ) {
				$latest_args['post__in'] = $selected_latest_ids;
				$latest_args['orderby']  = 'post__in';
			} elseif ( $featured_recipe_id ) {
				$latest_args['post__not_in'] = array( $featured_recipe_id );
			}

			$latest_recipes = new WP_Query( $latest_args );
			if ( $latest_recipes->have_posts() ) : ?>
				<div class="recipe-grid">
				<?php while ( $latest_recipes->have_posts() ) : $latest_recipes->the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?><p><?php esc_html_e( 'Your latest recipes will appear here.', 'larder' ); ?></p><?php endif; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/home/seasonal' ); ?>
	<?php get_template_part( 'template-parts/home/categories' ); ?>
	<?php get_template_part( 'template-parts/home/popular' ); ?>
	<?php get_template_part( 'template-parts/home/kitchen-notes' ); ?>
	<?php get_template_part( 'template-parts/home/promotion' ); ?>
	<?php get_template_part( 'template-parts/home/about' ); ?>
	<?php get_template_part( 'template-parts/home/newsletter' ); ?>
	<?php get_template_part( 'template-parts/home/social' ); ?>
</main>

<?php get_footer(); ?>
