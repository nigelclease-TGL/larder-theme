<?php
/**
 * Related recipe cards.
 *
 * @package Larder
 */

$category_ids = wp_get_post_categories( get_the_ID() );

$related = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'category__in'        => $category_ids,
		'ignore_sticky_posts' => true,
	)
);

if ( ! $related->have_posts() ) {
	return;
}
?>
<section class="related-recipes" aria-labelledby="related-recipes-title">
	<div class="container">
		<div class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Keep baking', 'larder' ); ?></p>
				<h2 id="related-recipes-title"><?php esc_html_e( 'You may also like', 'larder' ); ?></h2>
			</div>
		</div>
		<div class="recipe-grid">
			<?php
			while ( $related->have_posts() ) {
				$related->the_post();
				get_template_part( 'template-parts/content', 'card' );
			}
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
