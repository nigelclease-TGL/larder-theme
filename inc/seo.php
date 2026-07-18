<?php
/**
 * Lightweight SEO and social metadata for Nigel's Kitchen Table.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detect whether a dedicated SEO plugin is already handling metadata.
 *
 * @return bool
 */
function nkt_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Build a useful description for the current request.
 *
 * @return string
 */
function nkt_current_meta_description() {
	$description = get_bloginfo( 'description' );

	if ( is_singular() ) {
		if ( has_excerpt() ) {
			$description = get_the_excerpt();
		} else {
			$description = wp_trim_words( wp_strip_all_tags( get_the_content() ), 28 );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$term_description = term_description();
		$description      = $term_description ? $term_description : sprintf(
			/* translators: %s: archive name. */
			__( 'Browse %s recipes from Nigel’s Kitchen Table.', 'larder' ),
			wp_strip_all_tags( get_the_archive_title() )
		);
	} elseif ( is_search() ) {
		$description = sprintf(
			/* translators: %s: search phrase. */
			__( 'Recipe search results for %s at Nigel’s Kitchen Table.', 'larder' ),
			get_search_query()
		);
	} elseif ( is_404() ) {
		$description = __( 'The requested page could not be found. Search recipes and cooking guides from Nigel’s Kitchen Table.', 'larder' );
	}

	$description = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $description ) ) );
	return $description ? $description : __( 'Seasonal recipes, beautiful bakes and practical kitchen knowledge.', 'larder' );
}

/**
 * Resolve a suitable social image and its metadata.
 *
 * @return array{url:string,width:int,height:int,alt:string}
 */
function nkt_social_image_data() {
	$attachment_id = 0;

	if ( is_singular() && has_post_thumbnail() ) {
		$attachment_id = (int) get_post_thumbnail_id();
	}

	if ( ! $attachment_id ) {
		$attachment_id = absint( get_theme_mod( 'larder_hero_image', 0 ) );
	}

	if ( ! $attachment_id ) {
		return array( 'url' => '', 'width' => 0, 'height' => 0, 'alt' => '' );
	}

	$image = wp_get_attachment_image_src( $attachment_id, 'large' );
	return array(
		'url'    => $image ? (string) $image[0] : '',
		'width'  => $image ? (int) $image[1] : 0,
		'height' => $image ? (int) $image[2] : 0,
		'alt'    => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	);
}

/**
 * Resolve the current public URL for social and canonical metadata.
 *
 * @return string
 */
function nkt_current_public_url() {
	if ( is_singular() ) {
		return (string) get_permalink();
	}
	if ( is_search() ) {
		return (string) get_search_link( get_search_query() );
	}
	if ( is_archive() || is_home() ) {
		return (string) get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ) );
	}
	return home_url( '/' );
}

/**
 * Output social metadata only when no dedicated SEO plugin is active.
 */
function nkt_output_social_meta() {
	if ( is_admin() || nkt_has_seo_plugin() ) {
		return;
	}

	$title       = wp_get_document_title();
	$description = nkt_current_meta_description();
	$url         = nkt_current_public_url();
	$image       = nkt_social_image_data();
	$type        = is_singular( 'post' ) ? 'article' : 'website';
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr( "Nigel's Kitchen Table" ); ?>">
	<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>">
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:type" content="<?php echo esc_attr( $type ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
	<?php if ( is_singular( 'post' ) ) : ?>
		<meta property="article:published_time" content="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>">
	<?php endif; ?>
	<?php if ( $image['url'] ) : ?>
		<meta property="og:image" content="<?php echo esc_url( $image['url'] ); ?>">
		<meta property="og:image:secure_url" content="<?php echo esc_url( $image['url'] ); ?>">
		<?php if ( $image['width'] && $image['height'] ) : ?>
			<meta property="og:image:width" content="<?php echo esc_attr( (string) $image['width'] ); ?>">
			<meta property="og:image:height" content="<?php echo esc_attr( (string) $image['height'] ); ?>">
		<?php endif; ?>
		<?php if ( $image['alt'] ) : ?><meta property="og:image:alt" content="<?php echo esc_attr( $image['alt'] ); ?>"><?php endif; ?>
		<meta name="twitter:card" content="summary_large_image">
		<meta name="twitter:image" content="<?php echo esc_url( $image['url'] ); ?>">
	<?php else : ?>
		<meta name="twitter:card" content="summary">
	<?php endif; ?>
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
	<meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>">
	<?php
}
add_action( 'wp_head', 'nkt_output_social_meta', 5 );

/**
 * Output canonical URLs for non-singular pages that WordPress core does not cover.
 */
function nkt_output_archive_canonical() {
	if ( is_admin() || nkt_has_seo_plugin() || is_singular() || is_search() || is_404() ) {
		return;
	}

	printf( '<link rel="canonical" href="%s">' . "\n", esc_url( nkt_current_public_url() ) );
}
add_action( 'wp_head', 'nkt_output_archive_canonical', 6 );

/**
 * Add a small WebSite graph for search engines when no SEO plugin is active.
 */
function nkt_output_structured_data() {
	if ( is_admin() || nkt_has_seo_plugin() ) {
		return;
	}

	$site_url      = home_url( '/' );
	$about_url     = nkt_page_url( array( 'about-nigel', 'my-story', 'about' ), '/my-story/' );
	$instagram_url = get_theme_mod( 'larder_instagram_url', 'https://www.instagram.com/thegourmetlarder/' );
	$pinterest_url = get_theme_mod( 'larder_pinterest_url', 'https://hu.pinterest.com/thegourmetlarder/' );
	$facebook_url  = get_theme_mod( 'larder_facebook_url', 'https://www.facebook.com/thegourmetlarder/' );
	$same_as       = array_values( array_filter( array( $instagram_url, $pinterest_url, $facebook_url ) ) );
	$person        = array(
		'@type'       => 'Person',
		'@id'         => $site_url . '#nigel',
		'name'        => 'Nigel Clease',
		'url'         => $about_url,
		'description' => __( 'Recipe developer and creator of Nigel’s Kitchen Table.', 'larder' ),
	);
	if ( $same_as ) {
		$person['sameAs'] = $same_as;
	}

	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$person,
			array(
				'@type'       => 'WebSite',
				'@id'         => $site_url . '#website',
				'url'         => $site_url,
				'name'        => "Nigel's Kitchen Table",
				'description' => get_bloginfo( 'description' ),
				'inLanguage'  => get_bloginfo( 'language' ),
				'publisher'   => array( '@id' => $site_url . '#nigel' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?s={search_term_string}' ),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);

	if ( is_singular( 'post' ) ) {
		$items = array(
			array(
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => __( 'Home', 'larder' ),
				'item'     => $site_url,
			),
		);
		$category = nkt_get_primary_category();
		if ( $category ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => $category->name,
				'item'     => get_category_link( $category->term_id ),
			);
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => count( $items ) + 1,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
		$graph['@graph'][] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => $items,
		);
	}

	printf( '<script type="application/ld+json">%s</script>' . "\n", wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
}
add_action( 'wp_head', 'nkt_output_structured_data', 20 );

/**
 * Search and error pages should not be indexed as standalone content.
 *
 * @param array $robots Existing robot directives.
 * @return array
 */
function nkt_robots_directives( $robots ) {
	if ( nkt_has_seo_plugin() ) {
		return $robots;
	}

	if ( is_search() || is_404() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'nkt_robots_directives' );
