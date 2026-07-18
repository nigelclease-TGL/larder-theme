<?php
$instagram_url   = get_theme_mod( 'larder_instagram_url', 'https://www.instagram.com/thegourmetlarder/' );
$pinterest_url   = get_theme_mod( 'larder_pinterest_url', 'https://hu.pinterest.com/thegourmetlarder/' );
$facebook_url    = get_theme_mod( 'larder_facebook_url', 'https://www.facebook.com/thegourmetlarder/' );
$terms_page      = nkt_setup_find_page( array( 'terms', 'terms-and-conditions' ) );
$collections_url = nkt_page_url( array( 'recipe-collections', 'collections', 'seasons' ), '/recipe-collections/' );
$notes_url       = nkt_page_url( array( 'kitchen-notes', 'baking-guides' ), '/kitchen-notes/' );
$about_url       = nkt_page_url( array( 'about-nigel', 'my-story', 'about' ), '/my-story/' );
$newsletter_page = nkt_setup_find_page( array( 'newsletter' ) );
$newsletter_url  = $newsletter_page ? get_permalink( $newsletter_page ) : '';
?>
<footer class="site-footer">
	<div class="container footer-introduction">
		<p class="footer-introduction__eyebrow"><?php esc_html_e( 'Nigel’s Kitchen Table', 'larder' ); ?></p>
		<p class="footer-introduction__statement"><?php esc_html_e( 'Seasonal cooking, beautiful recipes and practical kitchen knowledge—made to be shared.', 'larder' ); ?></p>
	</div>

	<div class="container footer-grid footer-grid--editorial">
		<div class="footer-brand">
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">Nigel's Kitchen Table</a>
			<p><?php esc_html_e( 'Pull up a chair for thoughtful recipes, useful kitchen notes and food worth making again.', 'larder' ); ?></p>
		</div>

		<div class="footer-column">
			<h2><?php esc_html_e( 'Explore', 'larder' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => 'nkt_footer_menu_fallback',
				)
			);
			?>
		</div>

		<div class="footer-column">
			<h2><?php esc_html_e( 'Discover', 'larder' ); ?></h2>
			<ul class="footer-menu">
				<li><a href="<?php echo esc_url( $collections_url ); ?>"><?php esc_html_e( 'Collections', 'larder' ); ?></a></li>
				<li><a href="<?php echo esc_url( $notes_url ); ?>"><?php esc_html_e( 'Kitchen Notes', 'larder' ); ?></a></li>
				<li><a href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'About Nigel', 'larder' ); ?></a></li>
				<?php if ( $newsletter_url ) : ?><li><a href="<?php echo esc_url( $newsletter_url ); ?>"><?php esc_html_e( 'Newsletter', 'larder' ); ?></a></li><?php endif; ?>
			</ul>
		</div>

		<div class="footer-column">
			<h2><?php esc_html_e( 'Connect', 'larder' ); ?></h2>
			<ul class="footer-menu">
				<?php if ( $instagram_url ) : ?><li><a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Instagram', 'larder' ); ?> ↗</a></li><?php endif; ?>
				<?php if ( $pinterest_url ) : ?><li><a href="<?php echo esc_url( $pinterest_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Pinterest', 'larder' ); ?> ↗</a></li><?php endif; ?>
				<?php if ( $facebook_url ) : ?><li><a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook', 'larder' ); ?> ↗</a></li><?php endif; ?>
				<li><a href="<?php echo esc_url( nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'larder' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Nigel's Kitchen Table</p>
		<p class="footer-domain">thegourmetlarder.com</p>
		<div class="footer-legal">
			<?php if ( get_privacy_policy_url() ) : ?><a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy', 'larder' ); ?></a><?php endif; ?>
			<?php if ( $terms_page ) : ?><a href="<?php echo esc_url( get_permalink( $terms_page ) ); ?>"><?php esc_html_e( 'Terms', 'larder' ); ?></a><?php endif; ?>
			<?php do_action( 'nkt_footer_legal_links' ); ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
