<?php
/**
 * Reader favourites section.
 *
 * @package Larder
 */

$popular_recipes = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'orderby'             => array(
			'comment_count' => 'DESC',
			'date'          => 'DESC',
		),
		'no_found_rows'       => true,
	)
);

if ( ! $popular_recipes->have_posts() ) {
	return;
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

		<div class="recipe-grid recipe-grid--four">
			<?php while ( $popular_recipes->have_posts() ) : $popular_recipes->the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'card' ); ?>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
