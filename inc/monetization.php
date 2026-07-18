<?php
/**
 * Ethical monetization helpers.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Affiliate disclosure shortcode.
 *
 * Usage: [nkt_affiliate_disclosure]
 */
function nkt_affiliate_disclosure_shortcode() {
	$disclosure_page = nkt_growth_page_url( array( 'affiliate-disclosure', 'disclosure' ) );
	$more             = $disclosure_page ? ' <a href="' . esc_url( $disclosure_page ) . '">' . esc_html__( 'Read the full disclosure.', 'larder' ) . '</a>' : '';

	return '<aside class="nkt-disclosure" role="note"><strong>' . esc_html__( 'Affiliate disclosure:', 'larder' ) . '</strong> ' . esc_html__( 'Some links on this page may be affiliate links. If you buy through them, Nigel’s Kitchen Table may earn a small commission at no extra cost to you. Recommendations are based on genuine use or careful research.', 'larder' ) . $more . '</aside>';
}
add_shortcode( 'nkt_affiliate_disclosure', 'nkt_affiliate_disclosure_shortcode' );

/**
 * Sponsored content disclosure shortcode.
 *
 * Usage: [nkt_sponsor_disclosure sponsor="Brand name"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function nkt_sponsor_disclosure_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'sponsor' => '',
		),
		$atts,
		'nkt_sponsor_disclosure'
	);

	$sponsor = sanitize_text_field( $atts['sponsor'] );
	$message = $sponsor
		? sprintf(
			/* translators: %s: sponsor name. */
			__( 'This content was produced in partnership with %s. The relationship is disclosed clearly and the practical conclusions remain editorially independent.', 'larder' ),
			$sponsor
		)
		: __( 'This is sponsored content. The commercial relationship is disclosed clearly and the practical conclusions remain editorially independent.', 'larder' );

	return '<aside class="nkt-disclosure nkt-disclosure--sponsored" role="note"><strong>' . esc_html__( 'Sponsored content:', 'larder' ) . '</strong> ' . esc_html( $message ) . '</aside>';
}
add_shortcode( 'nkt_sponsor_disclosure', 'nkt_sponsor_disclosure_shortcode' );

/**
 * Product recommendation shortcode.
 *
 * Usage: [nkt_product title="Digital kitchen scales" url="https://example.com" note="Accurate to 1 g."]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function nkt_product_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'title' => '',
			'url'   => '',
			'note'  => '',
			'label' => __( 'View product', 'larder' ),
		),
		$atts,
		'nkt_product'
	);

	if ( empty( $atts['title'] ) || empty( $atts['url'] ) ) {
		return '';
	}

	ob_start();
	?>
	<aside class="nkt-product" aria-label="<?php esc_attr_e( 'Recommended product', 'larder' ); ?>" data-nkt-location="product_recommendation">
		<p class="nkt-product__eyebrow"><?php esc_html_e( 'Nigel recommends', 'larder' ); ?></p>
		<h3 class="nkt-product__title"><?php echo esc_html( $atts['title'] ); ?></h3>
		<?php if ( $atts['note'] ) : ?>
			<p class="nkt-product__note"><?php echo esc_html( $atts['note'] ); ?></p>
		<?php endif; ?>
		<a class="button button-secondary nkt-product__link" href="<?php echo esc_url( $atts['url'] ); ?>" target="_blank" rel="nofollow sponsored noopener noreferrer" data-nkt-event="affiliate_product_click" data-nkt-label="<?php echo esc_attr( $atts['title'] ); ?>">
			<?php echo esc_html( $atts['label'] ); ?>
		</a>
	</aside>
	<?php
	return ob_get_clean();
}
add_shortcode( 'nkt_product', 'nkt_product_shortcode' );

/**
 * Add sponsored and nofollow attributes to links explicitly marked as affiliate.
 * Editors can add the CSS class "affiliate-link" to a link in WordPress.
 *
 * @param string $content Post content.
 * @return string
 */
function nkt_filter_affiliate_links( $content ) {
	if ( false === strpos( $content, 'affiliate-link' ) ) {
		return $content;
	}

	$filtered = preg_replace_callback(
		'/<a\s+([^>]*class=(?:"|\')[^"\']*affiliate-link[^"\']*(?:"|\')[^>]*)>/i',
		function ( $matches ) {
			$attributes = $matches[1];
			if ( preg_match( '/\srel=("|\')(.*?)(\1)/i', $attributes, $rel_match ) ) {
				$rels       = array_filter( preg_split( '/\s+/', $rel_match[2] ) );
				$rels       = array_unique( array_merge( $rels, array( 'nofollow', 'sponsored', 'noopener' ) ) );
				$attributes = preg_replace( '/\srel=("|\')(.*?)(\1)/i', ' rel="' . esc_attr( implode( ' ', $rels ) ) . '"', $attributes );
			} else {
				$attributes .= ' rel="nofollow sponsored noopener"';
			}

			if ( false === strpos( $attributes, 'data-nkt-event=' ) ) {
				$attributes .= ' data-nkt-event="affiliate_link_click"';
			}

			return '<a ' . $attributes . '>';
		},
		$content
	);

	return is_string( $filtered ) ? $filtered : $content;
}
add_filter( 'the_content', 'nkt_filter_affiliate_links', 20 );
