<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-brand">
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">Nigel's Kitchen Table</a>
			<p><?php esc_html_e( 'Seasonal recipes, comforting food and beautiful bakes made with simple ingredients and plenty of heart.', 'larder' ); ?></p>
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
			<h2><?php esc_html_e( 'Connect', 'larder' ); ?></h2>
			<ul class="footer-menu">
				<li><a href="https://www.instagram.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Instagram', 'larder' ); ?></a></li>
				<li><a href="https://hu.pinterest.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Pinterest', 'larder' ); ?></a></li>
				<li><a href="https://www.facebook.com/thegourmetlarder/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook', 'larder' ); ?></a></li>
				<li><a href="<?php echo esc_url( nkt_page_url( array( 'contact', 'contact-me' ), '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'larder' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Nigel's Kitchen Table</p>
		<p class="footer-domain"><?php esc_html_e( 'At thegourmetlarder.com', 'larder' ); ?></p>
		<div class="footer-legal">
			<a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'larder' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'larder' ); ?></a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
