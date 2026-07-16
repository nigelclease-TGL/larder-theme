<?php
/**
 * Homepage recipe categories.
 *
 * @package Larder
 */

$categories = get_categories(
	array(
		'orderby'    => 'count',
		'order'      => 'DESC',
		'hide_empty' => true,
		'number'     => 6,
	)
);

if ( empty( $categories ) ) {
	return;
}
?>
<section class="home-section home-categories" aria-labelledby="home-categories-title">
	<div class="container">
		<header class="section-heading">
			<p class="eyebrow"><?php esc_html_e( 'Browse by category', 'larder' ); ?></p>
			<h2 id="home-categories-title"><?php esc_html_e( 'Find your next bake', 'larder' ); ?></h2>
		</header>

		<div class="category-grid">
			<?php foreach ( $categories as $category ) : ?>
				<a class="category-card" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>">
					<span class="category-card__name"><?php echo esc_html( $category->name ); ?></span>
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
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
