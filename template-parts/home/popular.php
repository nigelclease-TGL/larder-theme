<?php
/**
 * Reader favourites section.
 *
 * @package Larder
 */

$popular_recipes = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'meta_key'            => 'post_views_count',
		'orderby'             => array(
			'meta_value_num' => 'DESC',
			'comment_count'  => 'DESC',
			'date'           => 'DESC',
		),
	)
);

if ( ! $popular_recipes->have_posts() ) {
	$popular_recipes = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
			'orderby'             => 'comment_count',
		)
	);
}
?>
<section class="home-section reader-favourites" aria-labelledby="favourites-title">
	<div class="container">
		<header class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Most loved', 'larder' ); ?></p>
				<h2 id="favourites-title"><?php esc_html_e( 'Reader favourites', 'larder' ); ?></h2>
			</div>
		</header>

		<?php if ( $popular_recipes->have_posts() ) : ?>
			<div class="recipe-grid recipe-grid--four">
				<?php while ( $popular_recipes->have_posts() ) : $popular_recipes->the_post(); ?>
					<?php get_template_part( 'template-parts/content', 'card' ); ?>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Reader favourites will appear here as your audience grows.', 'larder' ); ?></p>
		<?php endif; ?>
	</div>
</section>
