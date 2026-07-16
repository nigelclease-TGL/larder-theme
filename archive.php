<?php
/**
 * Archive template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="archive-page">
	<header class="archive-header">
		<div class="container">
			<p class="eyebrow"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></p>
			<?php the_archive_title( '<h1>', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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
				<p><?php esc_html_e( 'No recipes were found.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
