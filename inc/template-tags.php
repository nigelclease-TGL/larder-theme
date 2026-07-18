<?php
/**
 * Reusable presentation helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an estimated reading time for a post.
 *
 * @param int|null $post_id Post ID.
 * @return string
 */
function nkt_get_reading_time( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$words   = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	$minutes = max( 1, (int) ceil( $words / 220 ) );

	return sprintf(
		/* translators: %d: number of minutes. */
		_n( '%d min read', '%d min read', $minutes, 'larder' ),
		$minutes
	);
}

/**
 * Return the first assigned category, excluding Uncategorized where possible.
 *
 * @param int|null $post_id Post ID.
 * @return WP_Term|null
 */
function nkt_get_primary_category( $post_id = null ) {
	$post_id    = $post_id ?: get_the_ID();
	$categories = get_the_category( $post_id );

	if ( empty( $categories ) ) {
		return null;
	}

	foreach ( $categories as $category ) {
		if ( 'uncategorized' !== $category->slug ) {
			return $category;
		}
	}

	return $categories[0];
}

/**
 * Determine whether a post is editorial Kitchen Notes content rather than a recipe.
 *
 * @param int|null $post_id Post ID.
 * @return bool
 */
function nkt_is_kitchen_note( $post_id = null ) {
	$post_id = $post_id ?: get_the_ID();

	return has_category( array( 'kitchen-notes', 'baking-guides' ), $post_id );
}

/**
 * Render concise editorial metadata.
 * Recipe dates are deliberately omitted so evergreen recipes do not appear stale.
 * Kitchen Notes retain their publication date because they are editorial articles.
 *
 * @param int|null $post_id Post ID.
 * @return void
 */
function nkt_post_meta( $post_id = null ) {
	$post_id        = $post_id ?: get_the_ID();
	$category       = nkt_get_primary_category( $post_id );
	$is_kitchen_note = nkt_is_kitchen_note( $post_id );
	$items          = array();

	if ( $category ) {
		$items[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( get_category_link( $category ) ),
			esc_html( $category->name )
		);
	}

	if ( $is_kitchen_note ) {
		$items[] = sprintf(
			'<time datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C, $post_id ) ),
			esc_html( get_the_date( '', $post_id ) )
		);
	}

	$items[] = esc_html( nkt_get_reading_time( $post_id ) );
	?>
	<div class="nkt-post-meta">
		<?php echo wp_kses_post( implode( '<span aria-hidden="true">·</span>', $items ) ); ?>
	</div>
	<?php
}
