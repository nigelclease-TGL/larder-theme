<?php
/**
 * Posts page template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="archive-page">
	<header class="archive-hero">
		<div class="container archive-hero__inner">
			<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
			<h1><?php single_post_title(); ?></h1>
			<p><?php esc_html_e( 'New recipes, baking guides and seasonal inspiration.', 'larder' ); ?></p>
		</div>
	</header>
	<section class="archive-content">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<div class="recipe-grid">
					<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
				</div>
				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No recipes have been published yet.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
