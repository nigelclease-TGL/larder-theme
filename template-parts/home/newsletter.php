<?php
/**
 * Homepage newsletter call to action.
 *
 * @package Larder
 */
?>
<section class="home-section home-newsletter" aria-labelledby="newsletter-title">
	<div class="container">
		<div class="newsletter-panel">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Weekly inspiration', 'larder' ); ?></p>
				<h2 id="newsletter-title"><?php esc_html_e( 'Join The Gourmet Larder', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'Receive seasonal recipes, baking inspiration and practical tips straight to your inbox.', 'larder' ); ?></p>
			</div>

			<form class="newsletter-form" action="#" method="post">
				<label class="screen-reader-text" for="larder-newsletter-email"><?php esc_html_e( 'Email address', 'larder' ); ?></label>
				<input id="larder-newsletter-email" name="email" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email address', 'larder' ); ?>" required>
				<button class="button newsletter-button" type="submit"><?php esc_html_e( 'Subscribe', 'larder' ); ?></button>
			</form>
		</div>
	</div>
</section>
