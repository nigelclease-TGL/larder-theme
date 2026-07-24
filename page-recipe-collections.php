<?php
/**
 * Recipe collections landing page.
 *
 * Automatically used for WordPress pages with the slugs "recipe-collections",
 * "collections" and "seasons" through the compatibility templates.
 *
 * @package Larder
 */

get_header();

$collection_cards       = array();
$collection_descriptions = array(
	'spring_summer' => __( 'Fresh flavours, lighter bakes and recipes made for longer days around the table.', 'larder' ),
	'autumn_winter' => __( 'Comforting recipes, warming spices and dependable favourites for cooler days.', 'larder' ),
	'cakes'         => __( 'Celebration cakes, everyday bakes and muffins worth sharing.', 'larder' ),
	'biscuits'      => __( 'Cookies and biscuits for afternoon tea, gifting and the everyday biscuit tin.', 'larder' ),
	'bread'         => __( 'Reliable loaves, rolls and savoury breads for confident home baking.', 'larder' ),
);

foreach ( nkt_homepage_collection_definitions() as $index => $definition ) {
	$category = nkt_homepage_collection_category( $definition );

	if ( ! $category instanceof WP_Term ) {
		continue;
	}

	$count = function_exists( 'nkt_get_discovery_category_recipe_count' )
		? nkt_get_discovery_category_recipe_count( $category->term_id )
		: (int) $category->count;

	if ( $count < 1 ) {
		continue;
	}

	$selected_recipe_id = nkt_homepage_recipe_id(
		'larder_home_collection_' . $definition['key'] . '_recipe_id',
		nkt_homepage_collection_legacy_settings( $definition['key'], $index + 1 )
	);
	$image_url = '';

	if ( $selected_recipe_id && has_post_thumbnail( $selected_recipe_id ) ) {
		$image_url = get_the_post_thumbnail_url( $selected_recipe_id, 'large' );
	}

	if ( ! $image_url ) {
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
			$image_url = get_the_post_thumbnail_url( $fallback_posts[0], 'large' );
		}
	}

	$description = trim( wp_strip_all_tags( category_description( $category->term_id ) ) );
	if ( '' === $description ) {
		$description = isset( $collection_descriptions[ $definition['key'] ] )
			? $collection_descriptions[ $definition['key'] ]
			: __( 'Browse recipes gathered around this collection.', 'larder' );
	}

	$collection_cards[] = array(
		'name'        => $category->name,
		'url'         => get_category_link( $category->term_id ),
		'description' => $description,
		'count'       => $count,
		'image_url'   => $image_url,
	);
}
?>
<main id="primary" class="collections-index nkt-discovery-page collections-index--curated">
	<header class="collection-hero nkt-discovery-hero nkt-discovery-hero--compact">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( 'Browse by mood, season and occasion', 'larder' ); ?></p>
				<h1><?php esc_html_e( 'Recipe collections', 'larder' ); ?></h1>
				<div class="collection-description">
					<p><?php esc_html_e( 'Explore five carefully gathered collections, each using the cover recipe selected in the homepage Customizer.', 'larder' ); ?></p>
				</div>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Search the recipe box', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Know what you are looking for?', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<section class="collection-content" aria-labelledby="collections-list-title">
		<div class="container">
			<div class="collection-content__heading">
				<p class="eyebrow"><?php esc_html_e( 'Choose a collection', 'larder' ); ?></p>
				<h2 id="collections-list-title"><?php esc_html_e( 'Cook what suits the moment', 'larder' ); ?></h2>
			</div>

			<?php if ( $collection_cards ) : ?>
				<div class="collections-grid collections-grid--image-led">
					<?php foreach ( $collection_cards as $index => $collection ) : ?>
						<a class="collection-card collection-card--image" href="<?php echo esc_url( $collection['url'] ); ?>">
							<span
								class="collection-card__media<?php echo $collection['image_url'] ? '' : ' collection-card__media--fallback'; ?>"
								<?php if ( $collection['image_url'] ) : ?>style="background-image:url('<?php echo esc_url( $collection['image_url'] ); ?>')"<?php endif; ?>
							></span>
							<span class="collection-card__shade"></span>
							<span class="collection-card__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<span class="collection-card__body">
								<span class="collection-card__eyebrow"><?php esc_html_e( 'Recipe collection', 'larder' ); ?></span>
								<strong class="collection-card__title"><?php echo esc_html( $collection['name'] ); ?></strong>
								<span class="collection-card__description"><?php echo esc_html( $collection['description'] ); ?></span>
								<span class="collection-card__count"><?php echo esc_html( sprintf( _n( '%s recipe', '%s recipes', $collection['count'], 'larder' ), number_format_i18n( $collection['count'] ) ) ); ?></span>
								<span class="collection-card__link"><?php esc_html_e( 'View collection', 'larder' ); ?> →</span>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="collection-empty">
					<h2><?php esc_html_e( 'Collections are being prepared', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Browse all recipes while the first curated collections are assembled.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( nkt_page_url( array( 'recipes' ), '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
