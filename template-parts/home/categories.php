<?php
/**
 * Homepage editorial recipe collections.
 *
 * @package Larder
 */

$categories = get_categories(
	array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
		'number'     => 5,
	)
);

if ( empty( $categories ) ) {
	return;
}
?>
<section class="home-section home-categories" aria-labelledby="home-categories-title">
	<div class="container">
		<header class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Seasonal collections', 'larder' ); ?></p>
				<h2 id="home-categories-title"><?php esc_html_e( 'Cook by mood, occasion and ingredient', 'larder' ); ?></h2>
			</div>
			<p class="home-categories__intro"><?php esc_html_e( 'Thoughtfully gathered recipes for the way you want to cook today.', 'larder' ); ?></p>
		</header>

		<div class="category-grid category-grid--editorial">
			<?php foreach ( $categories as $index => $category ) : ?>
				<?php
				$cover_query = new WP_Query(
					array(
						'post_type'           => 'post',
						'posts_per_page'      => 1,
						'ignore_sticky_posts' => true,
						'cat'                 => $category->term_id,
						'meta_key'            => '_thumbnail_id',
						'no_found_rows'       => true,
					)
				);

				$cover_url = '';
				if ( $cover_query->have_posts() ) {
					$cover_query->the_post();
					$cover_url = get_the_post_thumbnail_url( get_the_ID(), 'large' );
				}
				wp_reset_postdata();
				?>
				<a class="category-card category-card--<?php echo esc_attr( $index + 1 ); ?>" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
					<span
						class="category-card__media<?php echo $cover_url ? '' : ' category-card__media--fallback'; ?>"
						<?php if ( $cover_url ) : ?>style="background-image:url('<?php echo esc_url( $cover_url ); ?>')"<?php endif; ?>
					></span>
					<span class="category-card__shade"></span>
					<span class="category-card__content">
						<span class="category-card__eyebrow"><?php esc_html_e( 'Collection', 'larder' ); ?></span>
						<strong class="category-card__name"><?php echo esc_html( $category->name ); ?></strong>
						<span class="category-card__count">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: number of recipes. */
									_n( '%s recipe', '%s recipes', $category->count, 'larder' ),
									number_format_i18n( $category->count )
								)
							);
							?>
						</span>
						<span class="category-card__cta"><?php esc_html_e( 'View collection', 'larder' ); ?> →</span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>