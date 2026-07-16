<?php
/**
 * Single post template.
 *
 * @package Larder
 */

get_header();
?>
<main id="primary" class="single-recipe">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'recipe-article' ); ?>>
			<header class="recipe-hero">
				<div class="container recipe-hero__grid">
					<div class="recipe-hero__content">
						<p class="eyebrow"><?php echo wp_kses_post( get_the_category_list( ' · ' ) ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php if ( has_excerpt() ) : ?>
							<p class="recipe-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="recipe-actions">
							<a class="button button-primary" href="#recipe-card"><?php esc_html_e( 'Jump to recipe', 'larder' ); ?></a>
							<button class="button button-secondary" type="button" onclick="window.print()"><?php esc_html_e( 'Print', 'larder' ); ?></button>
						</div>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="recipe-hero__media"><?php the_post_thumbnail( 'large' ); ?></div>
					<?php endif; ?>
				</div>
			</header>

			<div class="container recipe-layout">
				<div class="recipe-content">
					<?php the_content(); ?>

					<?php
					$share_url   = rawurlencode( get_permalink() );
					$share_title = rawurlencode( get_the_title() );
					$share_image = has_post_thumbnail() ? rawurlencode( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ) : '';
					?>
					<section class="recipe-share" aria-labelledby="recipe-share-title">
						<h2 id="recipe-share-title" class="recipe-share__title"><?php esc_html_e( 'Share this recipe', 'larder' ); ?></h2>
						<div class="recipe-share__links">
							<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $share_url ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
							<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_attr( $share_url ); ?>&media=<?php echo esc_attr( $share_image ); ?>&description=<?php echo esc_attr( $share_title ); ?>" target="_blank" rel="noopener noreferrer">Pinterest</a>
							<a href="mailto:?subject=<?php echo esc_attr( $share_title ); ?>&body=<?php echo esc_attr( $share_url ); ?>"><?php esc_html_e( 'Email', 'larder' ); ?></a>
						</div>
					</section>
				</div>
				<aside class="recipe-sidebar" aria-label="<?php esc_attr_e( 'Recipe information', 'larder' ); ?>">
					<?php if ( is_active_sidebar( 'recipe-sidebar' ) ) : ?>
						<?php dynamic_sidebar( 'recipe-sidebar' ); ?>
					<?php else : ?>
						<div class="recipe-sidebar__card">
							<p class="eyebrow"><?php esc_html_e( 'From the kitchen table', 'larder' ); ?></p>
							<p><?php esc_html_e( 'Use the recipe card for ingredients, timings, servings and printing options.', 'larder' ); ?></p>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer();
