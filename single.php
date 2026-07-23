<?php
/**
 * Branded single recipe template.
 *
 * @package Larder
 */

get_header();
?>
<div class="nkt-reading-progress" aria-hidden="true"><span></span></div>
<main id="primary" class="single-recipe nkt-recipe-template">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php
		$current_post_id = get_the_ID();
		$category_ids    = wp_get_post_categories( $current_post_id );
		$share_url       = rawurlencode( get_permalink() );
		$share_title     = rawurlencode( get_the_title() );
		$share_image     = has_post_thumbnail() ? rawurlencode( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ) : '';
		?>
		<article <?php post_class( 'recipe-article' ); ?> data-recipe-post-id="<?php echo esc_attr( $current_post_id ); ?>">
			<header class="recipe-hero nkt-recipe-hero">
				<div class="nkt-recipe-hero__grid">
					<div class="nkt-recipe-hero__copy">
						<p class="nkt-recipe-hero__brandline"><?php esc_html_e( "From Nigel's Kitchen Table", 'larder' ); ?></p>
						<div class="nkt-recipe-hero__categories"><?php echo wp_kses_post( get_the_category_list( ' · ' ) ); ?></div>
						<h1><?php the_title(); ?></h1>
						<?php nkt_post_meta(); ?>
						<?php if ( has_excerpt() ) : ?>
							<p class="recipe-intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php endif; ?>
						<div class="recipe-actions" aria-label="<?php esc_attr_e( 'Recipe actions', 'larder' ); ?>">
							<a class="button button-primary" href="#recipe-card"><?php esc_html_e( 'Jump to recipe', 'larder' ); ?></a>
							<button class="button button-secondary" type="button" data-print-recipe><?php esc_html_e( 'Print', 'larder' ); ?></button>
							<button class="button button-secondary" type="button" data-cook-mode aria-pressed="false"><?php esc_html_e( 'Cook mode', 'larder' ); ?></button>
							<button class="button button-secondary" type="button" data-share-recipe><?php esc_html_e( 'Share', 'larder' ); ?></button>
						</div>
					</div>

					<div class="nkt-recipe-hero__media-wrap">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php
							$hero_image_id  = get_post_thumbnail_id();
							$hero_image_url = wp_get_attachment_url( $hero_image_id );
							$hero_image_alt = trim( (string) get_post_meta( $hero_image_id, '_wp_attachment_image_alt', true ) );
							if ( '' === $hero_image_alt ) {
								$hero_image_alt = get_the_title();
							}
							?>
							<div class="recipe-hero__media nkt-recipe-hero__media">
								<img class="wp-post-image skip-lazy" src="<?php echo esc_url( $hero_image_url ); ?>" alt="<?php echo esc_attr( $hero_image_alt ); ?>" loading="eager" fetchpriority="high" decoding="async" data-no-lazy="1">
							</div>
						<?php else : ?>
							<div class="recipe-hero__media nkt-recipe-hero__media"></div>
						<?php endif; ?>
						<div class="nkt-recipe-hero__stamp" aria-hidden="true">
							<div>
								<span><?php esc_html_e( 'Kitchen-tested', 'larder' ); ?></span>
								<strong><?php esc_html_e( 'Made to share', 'larder' ); ?></strong>
							</div>
						</div>
					</div>
				</div>
			</header>

			<section class="nkt-recipe-standards" aria-label="<?php esc_attr_e( 'Recipe standards', 'larder' ); ?>">
				<div class="nkt-recipe-standards__inner">
					<div class="nkt-recipe-standard">
						<span class="nkt-recipe-standard__eyebrow"><?php esc_html_e( 'Reliable', 'larder' ); ?></span>
						<strong><?php esc_html_e( 'Kitchen-tested instructions', 'larder' ); ?></strong>
					</div>
					<div class="nkt-recipe-standard">
						<span class="nkt-recipe-standard__eyebrow"><?php esc_html_e( 'Practical', 'larder' ); ?></span>
						<strong><?php esc_html_e( 'Clear timings and measurements', 'larder' ); ?></strong>
					</div>
					<div class="nkt-recipe-standard">
						<span class="nkt-recipe-standard__eyebrow"><?php esc_html_e( 'Helpful', 'larder' ); ?></span>
						<strong><?php esc_html_e( 'Tips, storage and variations', 'larder' ); ?></strong>
					</div>
				</div>
			</section>

			<nav class="nkt-recipe-toolbar" aria-label="<?php esc_attr_e( 'Quick recipe actions', 'larder' ); ?>">
				<div class="nkt-recipe-toolbar__inner">
					<a class="nkt-recipe-toolbar__primary" href="#recipe-card"><?php esc_html_e( 'Jump to recipe', 'larder' ); ?></a>
					<button type="button" data-toggle-recipe-guide aria-expanded="false" aria-controls="recipe-guide" hidden><?php esc_html_e( 'Sections', 'larder' ); ?></button>
					<button type="button" data-print-recipe><?php esc_html_e( 'Print', 'larder' ); ?></button>
					<button type="button" data-cook-mode aria-pressed="false"><?php esc_html_e( 'Cook mode', 'larder' ); ?></button>
					<button type="button" data-share-recipe><?php esc_html_e( 'Share', 'larder' ); ?></button>
					<button type="button" data-reset-ingredients hidden><?php esc_html_e( 'Reset ingredients', 'larder' ); ?></button>
					<span class="nkt-recipe-toolbar__status" data-share-status aria-live="polite"></span>
				</div>
			</nav>

			<section id="recipe-guide" class="nkt-recipe-guide" data-recipe-guide hidden aria-labelledby="recipe-guide-title">
				<div class="nkt-recipe-guide__inner">
					<div class="nkt-recipe-guide__intro">
						<p><?php esc_html_e( 'Navigate the recipe', 'larder' ); ?></p>
						<h2 id="recipe-guide-title"><?php esc_html_e( 'On this page', 'larder' ); ?></h2>
					</div>
					<nav aria-label="<?php esc_attr_e( 'Recipe sections', 'larder' ); ?>">
						<ol class="nkt-recipe-guide__list" data-recipe-toc></ol>
					</nav>
				</div>
			</section>

			<div class="nkt-recipe-body">
				<div class="recipe-layout">
					<div class="recipe-content">
						<?php echo do_shortcode( '[nkt_affiliate_disclosure]' ); ?>
						<p class="nkt-recipe-story-label"><?php esc_html_e( 'The recipe, step by step', 'larder' ); ?></p>
						<?php the_content(); ?>

						<?php if ( is_active_sidebar( 'recipe-inline-ad' ) ) : ?>
							<aside class="nkt-ad-zone" aria-label="<?php esc_attr_e( 'Advertisement', 'larder' ); ?>">
								<?php dynamic_sidebar( 'recipe-inline-ad' ); ?>
							</aside>
						<?php endif; ?>

						<section class="recipe-share nkt-recipe-share-card" aria-labelledby="recipe-share-title">
							<p class="eyebrow"><?php esc_html_e( 'Made it?', 'larder' ); ?></p>
							<h2 id="recipe-share-title" class="recipe-share__title"><?php esc_html_e( 'Share it from your kitchen', 'larder' ); ?></h2>
							<p><?php esc_html_e( 'Save the recipe or share it with someone who would enjoy making it too.', 'larder' ); ?></p>
							<div class="recipe-share__links">
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $share_url ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
								<a href="https://pinterest.com/pin/create/button/?url=<?php echo esc_attr( $share_url ); ?>&media=<?php echo esc_attr( $share_image ); ?>&description=<?php echo esc_attr( $share_title ); ?>" target="_blank" rel="noopener noreferrer">Pinterest</a>
								<a href="mailto:?subject=<?php echo esc_attr( $share_title ); ?>&body=<?php echo esc_attr( $share_url ); ?>"><?php esc_html_e( 'Email', 'larder' ); ?></a>
							</div>
						</section>
					</div>
				</div>
			</div>

			<?php get_template_part( 'template-parts/home/newsletter' ); ?>

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
					<header class="recipe-comments__intro">
						<p class="eyebrow"><?php esc_html_e( 'Around the table', 'larder' ); ?></p>
						<h2><?php esc_html_e( 'Questions and kitchen notes', 'larder' ); ?></h2>
						<p><?php esc_html_e( 'Share how the recipe went, ask a question or leave a useful tip for the next person making it.', 'larder' ); ?></p>
					</header>
					<?php comments_template(); ?>
				</div>
			<?php endif; ?>

			<button class="nkt-back-to-top" type="button" data-back-to-top hidden aria-label="<?php esc_attr_e( 'Back to top', 'larder' ); ?>">↑</button>
		</article>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>