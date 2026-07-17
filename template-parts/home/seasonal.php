<?php
/**
 * Seasonal recipe collection.
 *
 * @package Larder
 */

$seasonal_definitions = array(
	array(
		'label'          => __( 'Christmas baking', 'larder' ),
		'description'    => __( 'Festive bakes, generous puddings and recipes made for gathering.', 'larder' ),
		'class'          => 'christmas',
		'category_slugs' => array( 'christmas' ),
		'page_slugs'     => array( 'christmas' ),
	),
	array(
		'label'          => __( 'Easter favourites', 'larder' ),
		'description'    => __( 'Bright spring flavours, celebratory bakes and relaxed holiday cooking.', 'larder' ),
		'class'          => 'easter',
		'category_slugs' => array( 'easter', 'easter-baking' ),
		'page_slugs'     => array( 'easter-baking', 'easter' ),
	),
	array(
		'label'          => __( 'Spring & summer', 'larder' ),
		'description'    => __( 'Fresh produce, lighter plates and recipes for long days around the table.', 'larder' ),
		'class'          => 'summer',
		'category_slugs' => array( 'spring-summer', 'summer' ),
		'page_slugs'     => array( 'spring-summer', 'summer' ),
	),
	array(
		'label'          => __( 'Autumn & winter', 'larder' ),
		'description'    => __( 'Comforting food, warming spices and slow weekends in the kitchen.', 'larder' ),
		'class'          => 'autumn',
		'category_slugs' => array( 'autumn-winter', 'autumn' ),
		'page_slugs'     => array( 'autumn-winter', 'autumn' ),
	),
);

$seasonal_links = array();

foreach ( $seasonal_definitions as $definition ) {
	$url   = '';
	$image = '';
	$count = 0;

	foreach ( $definition['category_slugs'] as $category_slug ) {
		$category = get_category_by_slug( $category_slug );

		if ( ! $category ) {
			continue;
		}

		$url   = get_category_link( $category->term_id );
		$count = (int) $category->count;

		$featured_posts = get_posts(
			array(
				'category'         => $category->term_id,
				'numberposts'      => 1,
				'meta_key'         => '_thumbnail_id',
				'post_status'      => 'publish',
				'suppress_filters' => false,
			)
		);

		if ( ! empty( $featured_posts ) ) {
			$image = get_the_post_thumbnail_url( $featured_posts[0]->ID, 'large' );
		}

		break;
	}

	if ( ! $url ) {
		$page = nkt_setup_find_page( $definition['page_slugs'] );

		if ( $page ) {
			$url = get_permalink( $page );

			if ( has_post_thumbnail( $page ) ) {
				$image = get_the_post_thumbnail_url( $page, 'large' );
			}
		}
	}

	if ( $url ) {
		$definition['url']   = $url;
		$definition['image'] = $image;
		$definition['count'] = $count;
		$seasonal_links[]    = $definition;
	}
}

if ( empty( $seasonal_links ) ) {
	return;
}
?>
<section class="home-section seasonal-collections" aria-labelledby="seasonal-title">
	<div class="container">
		<header class="section-heading section-heading--split seasonal-collections__heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Cook with the seasons', 'larder' ); ?></p>
				<h2 id="seasonal-title"><?php esc_html_e( 'A year around the table', 'larder' ); ?></h2>
			</div>
			<p class="seasonal-collections__intro"><?php esc_html_e( 'Thoughtful collections for the ingredients, celebrations and rhythms of every season.', 'larder' ); ?></p>
		</header>

		<div class="seasonal-grid seasonal-grid--editorial">
			<?php foreach ( $seasonal_links as $index => $collection ) : ?>
				<a class="seasonal-card seasonal-card--<?php echo esc_attr( $collection['class'] ); ?>" href="<?php echo esc_url( $collection['url'] ); ?>">
					<?php if ( $collection['image'] ) : ?>
						<span class="seasonal-card__media" style="background-image:url('<?php echo esc_url( $collection['image'] ); ?>')"></span>
					<?php else : ?>
						<span class="seasonal-card__media seasonal-card__media--fallback"></span>
					<?php endif; ?>
					<span class="seasonal-card__shade"></span>
					<span class="seasonal-card__number" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></span>
					<span class="seasonal-card__content">
						<span class="seasonal-card__eyebrow"><?php esc_html_e( 'Seasonal collection', 'larder' ); ?></span>
						<span class="seasonal-card__label"><?php echo esc_html( $collection['label'] ); ?></span>
						<span class="seasonal-card__description"><?php echo esc_html( $collection['description'] ); ?></span>
						<?php if ( $collection['count'] ) : ?>
							<span class="seasonal-card__count">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %s: number of recipes. */
										_n( '%s recipe', '%s recipes', $collection['count'], 'larder' ),
										number_format_i18n( $collection['count'] )
									)
								);
								?>
							</span>
						<?php endif; ?>
						<span class="seasonal-card__link"><?php esc_html_e( 'Explore collection', 'larder' ); ?> →</span>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
