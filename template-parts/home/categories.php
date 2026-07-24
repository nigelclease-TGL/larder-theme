<?php
/**
 * Homepage editorial recipe collections.
 *
 * @package Larder
 */

$collections = array();

foreach ( nkt_homepage_collection_definitions() as $index => $definition ) {
	$category = nkt_homepage_collection_category( $definition );

	/* Support the renamed Cakes & Muffins category even when its slug changed. */
	if ( ! $category && 'cakes' === $definition['key'] ) {
		$category = get_category_by_slug( 'cakes-and-muffins' );

		if ( ! $category ) {
			$category = get_term_by( 'name', 'Cakes & Muffins', 'category' );
		}
	}

	if ( ! $category instanceof WP_Term ) {
		continue;
	}

	/*
	 * Count recipes through the parent category query so recipes assigned only to
	 * child categories still keep their main collection card visible.
	 */
	$count_query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 1,
			'ignore_sticky_posts' => true,
			'cat'                 => $category->term_id,
			'fields'              => 'ids',
			'no_found_rows'       => false,
			'suppress_filters'    => false,
		)
	);
	$category_count = (int) $count_query->found_posts;
	wp_reset_postdata();

	if ( 0 === $category_count ) {
		continue;
	}

	$selected_recipe_id = nkt_homepage_recipe_id(
		'larder_home_collection_' . $definition['key'] . '_recipe_id',
		nkt_homepage_collection_legacy_settings( $definition['key'], $index + 1 )
	);

	$cover_url = '';

	if ( $selected_recipe_id && has_post_thumbnail( $selected_recipe_id ) ) {
		$cover_url = get_the_post_thumbnail_url( $selected_recipe_id, 'large' );
	}

	if ( ! $cover_url ) {
		$fallback_posts = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => 1,
				'ignore_sticky_posts' => true,
				'cat'                 => $category->term_id,
				'meta_key'            => '_thumbnail_id',
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'suppress_filters'    => false,
			)
		);

		if ( ! empty( $fallback_posts ) ) {
			$cover_url = get_the_post_thumbnail_url( $fallback_posts[0], 'large' );
		}
	}

	$collections[] = array(
		'category'       => $category,
		'category_count' => $category_count,
		'cover_url'      => $cover_url,
	);
}

if ( empty( $collections ) ) {
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
			<?php foreach ( $collections as $index => $collection ) : ?>
				<?php
				$category       = $collection['category'];
				$category_count = $collection['category_count'];
				$cover_url      = $collection['cover_url'];
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
									_n( '%s recipe', '%s recipes', $category_count, 'larder' ),
									number_format_i18n( $category_count )
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
