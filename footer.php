<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-brand">
			<a class="footer-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">The Gourmet Larder</a>
			<p><?php esc_html_e( 'Beautiful baking recipes, seasonal inspiration and practical guidance for confident home bakers.', 'larder' ); ?></p>
		</div>

		<div class="footer-column">
			<h2><?php esc_html_e( 'Explore', 'larder' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'footer-menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</div>

		<div class="footer-column">
			<h2><?php esc_html_e( 'Connect', 'larder' ); ?></h2>
			<ul class="footer-menu">
				<li><a href="https://www.instagram.com/" rel="noopener noreferrer"><?php esc_html_e( 'Instagram', 'larder' ); ?></a></li>
				<li><a href="https://www.pinterest.com/" rel="noopener noreferrer"><?php esc_html_e( 'Pinterest', 'larder' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'larder' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="container footer-bottom">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> The Gourmet Larder</p>
		<div class="footer-legal">
			<a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy', 'larder' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>"><?php esc_html_e( 'Terms', 'larder' ); ?></a>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
