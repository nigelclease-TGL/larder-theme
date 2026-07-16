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
	return '<aside class="nkt-disclosure" role="note"><strong>' . esc_html__( 'Affiliate disclosure:', 'larder' ) . '</strong> ' . esc_html__( 'Some links on this page may be affiliate links. If you buy through them, Nigel’s Kitchen Table may earn a small commission at no extra cost to you. Recommendations are based on genuine use or careful research.', 'larder' ) . '</aside>';
}
add_shortcode( 'nkt_affiliate_disclosure', 'nkt_affiliate_disclosure_shortcode' );

/**
 * Product recommendation shortcode.
 *
 * Usage: [nkt_product title="Digital kitchen scales" url="https://example.com" note="Accurate to 1 g."]
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
	<aside class="nkt-product" aria-label="<?php esc_attr_e( 'Recommended product', 'larder' ); ?>">
		<p class="nkt-product__eyebrow"><?php esc_html_e( 'Nigel recommends', 'larder' ); ?></p>
		<h3 class="nkt-product__title"><?php echo esc_html( $atts['title'] ); ?></h3>
		<?php if ( $atts['note'] ) : ?>
			<p class="nkt-product__note"><?php echo esc_html( $atts['note'] ); ?></p>
		<?php endif; ?>
		<a class="button button-secondary nkt-product__link" href="<?php echo esc_url( $atts['url'] ); ?>" target="_blank" rel="nofollow sponsored noopener noreferrer">
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
 */
function nkt_filter_affiliate_links( $content ) {
	if ( false === strpos( $content, 'affiliate-link' ) ) {
		return $content;
	}

	return preg_replace_callback(
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
			return '<a ' . $attributes . '>';
		},
		$content
	);
}
add_filter( 'the_content', 'nkt_filter_affiliate_links', 20 );
