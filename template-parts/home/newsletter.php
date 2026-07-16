<?php
/**
 * Homepage newsletter call to action.
 *
 * @package Larder
 */

$mailchimp_form_id = absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) );
?>
<section class="home-section home-newsletter" aria-labelledby="newsletter-title">
	<div class="container">
		<div class="newsletter-panel">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Weekly inspiration', 'larder' ); ?></p>
				<h2 id="newsletter-title"><?php esc_html_e( 'Join The Gourmet Larder', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'Receive seasonal recipes, baking inspiration and practical tips straight to your inbox.', 'larder' ); ?></p>
			</div>

			<div class="newsletter-form-wrap">
				<?php if ( shortcode_exists( 'mc4wp_form' ) && $mailchimp_form_id ) : ?>
					<?php echo do_shortcode( '[mc4wp_form id="' . $mailchimp_form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
					<p class="newsletter-setup-note">
						<?php esc_html_e( 'Mailchimp setup: install and connect Mailchimp for WordPress, create a form, then enter the form ID under Appearance → Customise → Mailchimp Newsletter.', 'larder' ); ?>
					</p>
				<?php else : ?>
					<form class="newsletter-form" action="<?php echo esc_url( home_url( '/newsletter/' ) ); ?>" method="get">
						<label class="screen-reader-text" for="larder-newsletter-email"><?php esc_html_e( 'Email address', 'larder' ); ?></label>
						<input id="larder-newsletter-email" name="email" type="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email address', 'larder' ); ?>" required>
						<button class="button newsletter-button" type="submit"><?php esc_html_e( 'Subscribe', 'larder' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
