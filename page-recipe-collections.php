<?php
/**
 * Recipe collections landing page.
 *
 * Automatically used for a WordPress page with the slug "recipe-collections".
 *
 * @package Larder
 */

get_header();

$collection_cards = array();
$terms            = get_terms(
	array(
		'taxonomy'   => 'recipe_collection',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);

if ( ! is_wp_error( $terms ) ) {
	foreach ( $terms as $term ) {
		$collection_cards[] = array(
			'name'        => $term->name,
			'url'         => get_term_link( $term ),
			'description' => $term->description ?: __( 'A curated collection of dependable recipes to make, share and return to.', 'larder' ),
			'count'       => (int) $term->count,
		);
	}
}

if ( empty( $collection_cards ) ) {
	$legacy_collections = array(
		array( 'name' => __( 'Cakes', 'larder' ), 'slugs' => array( 'cakes' ) ),
		array( 'name' => __( 'Biscuits & Cookies', 'larder' ), 'slugs' => array( 'biscuits-cookies', 'biscuits-and-cookies' ) ),
		array( 'name' => __( 'Brownies & Bars', 'larder' ), 'slugs' => array( 'brownies-bars', 'brownies-and-bars' ) ),
		array( 'name' => __( 'Bread', 'larder' ), 'slugs' => array( 'bread' ) ),
		array( 'name' => __( 'Pastry', 'larder' ), 'slugs' => array( 'pastry' ) ),
		array( 'name' => __( 'Desserts & Puddings', 'larder' ), 'slugs' => array( 'desserts-puddings', 'desserts-and-puddings' ) ),
		array( 'name' => __( 'Spring & Summer', 'larder' ), 'slugs' => array( 'spring-summer' ) ),
		array( 'name' => __( 'Autumn & Winter', 'larder' ), 'slugs' => array( 'autumn-winter' ) ),
		array( 'name' => __( 'Christmas', 'larder' ), 'slugs' => array( 'christmas' ) ),
		array( 'name' => __( 'Gluten Free', 'larder' ), 'slugs' => array( 'gluten-free' ) ),
		array( 'name' => __( 'Vegan', 'larder' ), 'slugs' => array( 'vegan' ) ),
		array( 'name' => __( 'Weekend Baking', 'larder' ), 'slugs' => array( 'weekend-baking' ) ),
	);

	foreach ( $legacy_collections as $legacy ) {
		$resolved = false;

		foreach ( $legacy['slugs'] as $slug ) {
			$category = get_category_by_slug( $slug );
			if ( $category ) {
				$collection_cards[] = array(
					'name'        => $legacy['name'],
					'url'         => get_category_link( $category->term_id ),
					'description' => category_description( $category->term_id ) ?: __( 'Browse recipes gathered around this theme.', 'larder' ),
					'count'       => (int) $category->count,
				);
				$resolved = true;
				break;
			}
		}

		if ( $resolved ) {
			continue;
		}

		$page = nkt_setup_find_page( $legacy['slugs'] );
		if ( $page ) {
			$collection_cards[] = array(
				'name'        => $legacy['name'],
				'url'         => get_permalink( $page ),
				'description' => has_excerpt( $page ) ? get_the_excerpt( $page ) : __( 'Browse recipes gathered around this theme.', 'larder' ),
				'count'       => 0,
			);
		}
	}
}
?>
<main id="primary" class="collections-index">
	<header class="collection-hero">
		<div class="container collection-hero__inner">
			<p class="eyebrow"><?php esc_html_e( 'Browse by mood, season and occasion', 'larder' ); ?></p>
			<h1><?php esc_html_e( 'Recipe collections', 'larder' ); ?></h1>
			<div class="collection-description">
				<p><?php esc_html_e( 'Explore seasonal favourites, dependable bakes and useful collections designed to help you find the right recipe quickly.', 'larder' ); ?></p>
			</div>
		</div>
	</header>

	<section class="collection-content" aria-labelledby="collections-list-title">
		<div class="container">
			<h2 id="collections-list-title" class="screen-reader-text"><?php esc_html_e( 'All recipe collections', 'larder' ); ?></h2>
			<?php if ( $collection_cards ) : ?>
				<div class="collections-grid">
					<?php foreach ( $collection_cards as $index => $collection ) : ?>
						<a class="collection-card" href="<?php echo esc_url( $collection['url'] ); ?>">
							<span class="collection-card__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div>
								<h2><?php echo esc_html( $collection['name'] ); ?></h2>
								<p><?php echo wp_kses_post( wp_trim_words( wp_strip_all_tags( $collection['description'] ), 22 ) ); ?></p>
								<?php if ( $collection['count'] ) : ?>
									<span class="collection-card__count"><?php echo esc_html( sprintf( _n( '%s recipe', '%s recipes', $collection['count'], 'larder' ), number_format_i18n( $collection['count'] ) ) ); ?></span>
								<?php endif; ?>
							</div>
							<span class="collection-card__arrow" aria-hidden="true">→</span>
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
