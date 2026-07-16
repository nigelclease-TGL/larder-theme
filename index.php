<?php
/**
 * Main fallback template.
 *
 * @package Larder
 */

get_header();
?>

<main id="primary" class="content-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="recipe-grid">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No posts found.', 'larder' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
