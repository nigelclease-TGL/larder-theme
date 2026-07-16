<?php
/**
 * Front page template.
 *
 * @package Larder
 */

get_header();
?>

<main id="primary">
	<section class="hero">
		<div class="container hero-grid">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Bake · Create · Share', 'larder' ); ?></p>
				<h1><?php esc_html_e( 'Beautiful baking recipes and seasonal desserts', 'larder' ); ?></h1>
				<p class="hero-copy"><?php esc_html_e( 'Reliable recipes, practical baking guidance and inspiration to help you bake with confidence.', 'larder' ); ?></p>
				<div class="button-row">
					<a class="button button-primary" href="<?php echo esc_url( home_url( '/recipes/' ) ); ?>">
						<?php esc_html_e( 'Browse recipes', 'larder' ); ?>
					</a>
					<a class="button button-secondary" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>">
						<?php esc_html_e( 'Latest recipes', 'larder' ); ?>
					</a>
				</div>
			</div>

			<div class="hero-media" aria-hidden="true"></div>
		</div>
	</section>

	<section class="content-section">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
			<h2><?php esc_html_e( 'Latest recipes', 'larder' ); ?></h2>
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
</main>

<?php
get_footer();
