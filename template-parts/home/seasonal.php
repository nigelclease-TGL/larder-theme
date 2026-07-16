<?php
/**
 * Seasonal recipe collection.
 *
 * @package Larder
 */

$seasonal_definitions = array(
	array(
		'label'          => __( 'Christmas baking', 'larder' ),
		'class'          => 'christmas',
		'category_slugs' => array( 'christmas' ),
		'page_slugs'     => array( 'christmas' ),
	),
	array(
		'label'          => __( 'Easter favourites', 'larder' ),
		'class'          => 'easter',
		'category_slugs' => array( 'easter', 'easter-baking' ),
		'page_slugs'     => array( 'easter-baking', 'easter' ),
	),
	array(
		'label'          => __( 'Spring & summer', 'larder' ),
		'class'          => 'summer',
		'category_slugs' => array( 'spring-summer', 'summer' ),
		'page_slugs'     => array( 'spring-summer', 'summer' ),
	),
	array(
		'label'          => __( 'Autumn & winter', 'larder' ),
		'class'          => 'autumn',
		'category_slugs' => array( 'autumn-winter', 'autumn' ),
		'page_slugs'     => array( 'autumn-winter', 'autumn' ),
	),
);

$seasonal_links = array();

foreach ( $seasonal_definitions as $definition ) {
	$url = '';

	foreach ( $definition['category_slugs'] as $category_slug ) {
		$category = get_category_by_slug( $category_slug );
		if ( $category ) {
			$url = get_category_link( $category->term_id );
			break;
		}
	}

	if ( ! $url ) {
		$page = nkt_setup_find_page( $definition['page_slugs'] );
		if ( $page ) {
			$url = get_permalink( $page );
		}
	}

	if ( $url ) {
		$definition['url'] = $url;
		$seasonal_links[]  = $definition;
	}
}

if ( empty( $seasonal_links ) ) {
	return;
}
?>
<section class="home-section seasonal-collections" aria-labelledby="seasonal-title">
	<div class="container">
		<header class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Cook with the seasons', 'larder' ); ?></p>
				<h2 id="seasonal-title"><?php esc_html_e( 'Seasonal collections', 'larder' ); ?></h2>
			</div>
		</header>

		<div class="seasonal-grid">
			<?php foreach ( $seasonal_links as $collection ) : ?>
				<a class="seasonal-card seasonal-card--<?php echo esc_attr( $collection['class'] ); ?>" href="<?php echo esc_url( $collection['url'] ); ?>">
					<span class="seasonal-card__label"><?php echo esc_html( $collection['label'] ); ?></span>
					<span class="seasonal-card__link"><?php esc_html_e( 'Explore recipes', 'larder' ); ?> →</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
