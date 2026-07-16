<?php
/**
 * Search results template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="archive-page">
	<header class="archive-header">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Search', 'larder' ); ?></p>
			<h1><?php printf( esc_html__( 'Results for “%s”', 'larder' ), esc_html( get_search_query() ) ); ?></h1>
			<?php get_search_form(); ?>
		</div>
	</header>

	<section class="content-section">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="recipe-grid">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination(); ?></div>
			<?php else : ?>
				<div class="empty-state">
					<h2><?php esc_html_e( 'Nothing found', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Try another recipe name, ingredient or category.', 'larder' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
