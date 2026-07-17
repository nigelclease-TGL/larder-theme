<?php
/**
 * Front page template.
 *
 * @package Larder
 */

get_header();
$hero_image_id = absint( get_theme_mod( 'larder_hero_image', 0 ) );
$recipes_url   = nkt_page_url( array( 'recipes' ), '/recipes/' );
$about_url     = nkt_page_url( array( 'about-nigel', 'my-story', 'about' ), '/my-story/' );
?>

<main id="primary">
	<section class="hero">
		<div class="container hero-grid">
			<div class="hero-content">
				<p class="eyebrow"><?php esc_html_e( "Welcome to Nigel's Kitchen Table", 'larder' ); ?></p>
				<h1><?php echo esc_html( get_theme_mod( 'larder_hero_title', __( 'Seasonal recipes, made to be shared.', 'larder' ) ) ); ?></h1>
				<p class="hero-copy"><?php echo esc_html( get_theme_mod( 'larder_hero_copy', __( 'Beautiful bakes, comforting food and practical kitchen knowledge—tested carefully and written for real life.', 'larder' ) ) ); ?></p>
				<div class="button-row">
					<a class="button button-primary" href="<?php echo esc_url( $recipes_url ); ?>">
						<?php esc_html_e( 'Browse recipes', 'larder' ); ?>
					</a>
					<a class="button button-secondary" href="<?php echo esc_url( $about_url ); ?>">
						<?php esc_html_e( 'About Nigel', 'larder' ); ?>
					</a>
				</div>
			</div>

			<div class="hero-media">
				<?php if ( $hero_image_id ) : ?>
					<?php echo wp_get_attachment_image( $hero_image_id, 'larder-hero', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 900px) 92vw, 48vw' ) ); ?>
				<?php else : ?>
					<div class="hero-media__placeholder">
						<span><?php esc_html_e( 'Add your hero recipe image in Appearance → Customise → Nigel’s Kitchen Table Homepage.', 'larder' ); ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="home-section latest-recipes" aria-labelledby="latest-recipes-title">
		<div class="container">
			<header class="section-heading section-heading--split">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
					<h2 id="latest-recipes-title"><?php esc_html_e( 'Latest recipes', 'larder' ); ?></h2>
				</div>
				<a class="text-link" href="<?php echo esc_url( $recipes_url ); ?>">
					<?php esc_html_e( 'View all recipes', 'larder' ); ?>
				</a>
			</header>

			<?php
			$latest_recipes = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 6,
					'ignore_sticky_posts' => true,
				)
			);

			if ( $latest_recipes->have_posts() ) :
				?>
				<div class="recipe-grid">
					<?php
					while ( $latest_recipes->have_posts() ) :
						$latest_recipes->the_post();
						get_template_part( 'template-parts/content', 'card' );
					endwhile;
					?>
				</div>
				<?php
				wp_reset_postdata();
			else :
				?>
				<p><?php esc_html_e( 'Your latest recipes will appear here.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<?php get_template_part( 'template-parts/home/seasonal' ); ?>
	<?php get_template_part( 'template-parts/home/categories' ); ?>
	<?php get_template_part( 'template-parts/home/popular' ); ?>
	<?php get_template_part( 'template-parts/home/kitchen-notes' ); ?>
	<?php get_template_part( 'template-parts/home/about' ); ?>
	<?php get_template_part( 'template-parts/home/newsletter' ); ?>
	<?php get_template_part( 'template-parts/home/social' ); ?>
</main>

<?php get_footer(); ?>