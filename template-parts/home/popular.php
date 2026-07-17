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

$recipes_url = nkt_page_url( array( 'recipes' ), '/recipes/' );
$rank        = 1;
?>
<section class="home-section reader-favourites" aria-labelledby="favourites-title">
	<div class="container">
		<header class="section-heading section-heading--split reader-favourites__heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Most loved', 'larder' ); ?></p>
				<h2 id="favourites-title"><?php esc_html_e( 'Recipes readers return to', 'larder' ); ?></h2>
			</div>
			<div class="reader-favourites__intro">
				<p><?php esc_html_e( 'Reliable favourites, shared often and made again and again.', 'larder' ); ?></p>
				<a class="text-link" href="<?php echo esc_url( $recipes_url ); ?>"><?php esc_html_e( 'Browse every recipe', 'larder' ); ?> →</a>
			</div>
		</header>

		<div class="reader-favourites__grid">
			<?php while ( $popular_recipes->have_posts() ) : $popular_recipes->the_post(); ?>
				<div class="reader-favourite">
					<span class="reader-favourite__rank" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $rank ) ); ?></span>
					<?php get_template_part( 'template-parts/content', 'card' ); ?>
				</div>
				<?php $rank++; ?>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
