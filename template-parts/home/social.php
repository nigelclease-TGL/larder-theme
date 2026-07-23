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
	<div class="container">
		<header class="home-social__header">
			<div class="home-social__copy">
				<p class="eyebrow"><?php esc_html_e( 'Beyond the website', 'larder' ); ?></p>
				<h2 id="home-social-title"><?php esc_html_e( 'Keep a place at the table', 'larder' ); ?></h2>
			</div>
			<p class="home-social__intro"><?php esc_html_e( 'Follow along for seasonal ideas, new recipes and the quieter moments behind the food.', 'larder' ); ?></p>
		</header>

		<div class="home-social__links" aria-label="<?php esc_attr_e( 'Social media', 'larder' ); ?>">
			<?php if ( $instagram_url ) : ?>
				<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="home-social__number">01</span>
					<span class="home-social__platform">
						<small>Instagram</small>
						<strong><?php esc_html_e( "Nigel's Kitchen Table", 'larder' ); ?></strong>
					</span>
					<span class="home-social__arrow" aria-hidden="true">↗</span>
				</a>
			<?php endif; ?>

			<?php if ( $pinterest_url ) : ?>
				<a href="<?php echo esc_url( $pinterest_url ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="home-social__number">02</span>
					<span class="home-social__platform">
						<small>Pinterest</small>
						<strong><?php esc_html_e( 'Save recipes for later', 'larder' ); ?></strong>
					</span>
					<span class="home-social__arrow" aria-hidden="true">↗</span>
				</a>
			<?php endif; ?>

			<?php if ( $facebook_url ) : ?>
				<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer">
					<span class="home-social__number">03</span>
					<span class="home-social__platform">
						<small>Facebook</small>
						<strong><?php esc_html_e( 'Join the conversation', 'larder' ); ?></strong>
					</span>
					<span class="home-social__arrow" aria-hidden="true">↗</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>