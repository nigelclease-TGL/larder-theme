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
		<?php
		$current_post_id = get_the_ID();
		$category_ids    = wp_get_post_categories( $current_post_id );
		?>
		<article <?php post_class( 'recipe-article' ); ?>>
			<header class="recipe-hero">
				<div class="container recipe-hero__grid">
					<div class="recipe-hero__content">
						<p class="eyebrow"><?php echo wp_kses_post( get_the_category_list( ' · ' ) ); ?></p>
						<h1><?php the_title(); ?></h1>
						<?php nkt_post_meta(); ?>
						<?php if ( has_excerpt() ) : ?>
							<p class="recipe-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="recipe-actions" aria-label="<?php esc_attr_e( 'Recipe actions', 'larder' ); ?>">
							<a class="button button-primary" href="#recipe-card"><?php esc_html_e( 'Jump to recipe', 'larder' ); ?></a>
							<button class="button button-secondary" type="button" data-print-recipe><?php esc_html_e( 'Print', 'larder' ); ?></button>
							<button class="button button-secondary" type="button" data-cook-mode aria-pressed="false"><?php esc_html_e( 'Cook mode', 'larder' ); ?></button>
						</div>
					</div>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="recipe-hero__media"><?php the_post_thumbnail( 'larder-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 900px) 92vw, 44vw' ) ); ?></div>
					<?php endif; ?>
				</div>
			</header>

			<div class="recipe-layout">
				<div class="recipe-content">
					<?php echo do_shortcode( '[nkt_affiliate_disclosure]' ); ?>
					<?php the_content(); ?>

					<?php if ( is_active_sidebar( 'recipe-inline-ad' ) ) : ?>
						<aside class="nkt-ad-zone" aria-label="<?php esc_attr_e( 'Advertisement', 'larder' ); ?>">
							<?php dynamic_sidebar( 'recipe-inline-ad' ); ?>
						</aside>
					<?php endif; ?>

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
			</div>

			<?php
			$related_recipes = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 3,
					'post__not_in'        => array( $current_post_id ),
					'category__in'        => $category_ids,
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			?>
			<?php if ( $related_recipes->have_posts() ) : ?>
				<section class="related-recipes home-section" aria-labelledby="related-recipes-title">
					<div class="container">
						<header class="section-heading">
							<p class="eyebrow"><?php esc_html_e( 'Keep cooking', 'larder' ); ?></p>
							<h2 id="related-recipes-title"><?php esc_html_e( 'You may also like', 'larder' ); ?></h2>
						</header>
						<div class="recipe-grid">
							<?php while ( $related_recipes->have_posts() ) : $related_recipes->the_post(); ?>
								<?php get_template_part( 'template-parts/content', 'card' ); ?>
							<?php endwhile; ?>
						</div>
					</div>
				</section>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<div class="container recipe-comments">
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
