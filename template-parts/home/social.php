<?php
/**
 * Homepage social follow section.
 *
 * @package Larder
 */

$instagram_url = get_theme_mod( 'larder_instagram_url', 'https://www.instagram.com/thegourmetlarder/' );
$pinterest_url = get_theme_mod( 'larder_pinterest_url', 'https://hu.pinterest.com/thegourmetlarder/' );
$facebook_url  = get_theme_mod( 'larder_facebook_url', 'https://www.facebook.com/thegourmetlarder/' );
?>
<section class="home-section home-social" aria-labelledby="home-social-title">
	<div class="container home-social__inner">
		<div class="home-social__copy">
			<p class="eyebrow"><?php esc_html_e( 'Follow along', 'larder' ); ?></p>
			<h2 id="home-social-title"><?php esc_html_e( 'More from Nigel’s Kitchen Table', 'larder' ); ?></h2>
			<p><?php esc_html_e( 'Seasonal ideas, new recipes and a little behind-the-scenes inspiration from the kitchen.', 'larder' ); ?></p>
		</div>
		<div class="home-social__links" aria-label="<?php esc_attr_e( 'Social media', 'larder' ); ?>">
			<?php if ( $instagram_url ) : ?><a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer"><span>Instagram</span><strong>@thegourmetlarder</strong></a><?php endif; ?>
			<?php if ( $pinterest_url ) : ?><a href="<?php echo esc_url( $pinterest_url ); ?>" target="_blank" rel="noopener noreferrer"><span>Pinterest</span><strong><?php esc_html_e( 'Save your favourites', 'larder' ); ?></strong></a><?php endif; ?>
			<?php if ( $facebook_url ) : ?><a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer"><span>Facebook</span><strong><?php esc_html_e( 'Join the community', 'larder' ); ?></strong></a><?php endif; ?>
		</div>
	</div>
</section>
