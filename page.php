<?php
/**
 * Static page template.
 *
 * @package Larder
 */

get_header();
?>

<main id="primary" class="page-shell">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'page-article' ); ?>>
			<header class="page-hero">
				<div class="container page-hero__inner">
					<p class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
					<h1><?php the_title(); ?></h1>
					<?php if ( has_excerpt() ) : ?>
						<p class="page-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="container page-featured-image">
					<?php the_post_thumbnail( 'full' ); ?>
				</div>
			<?php endif; ?>

			<div class="container page-content prose">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'larder' ),
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</article>
	<?php endwhile; ?>
</main>

<?php
get_footer();
