<?php
/**
 * Homepage newsletter call to action.
 *
 * @package Larder
 */

$mailchimp_form_id = absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) );
$newsletter_page   = nkt_setup_find_page( array( 'newsletter' ) );
$newsletter_url    = $newsletter_page ? get_permalink( $newsletter_page ) : '';
?>
<section class="home-section home-newsletter" aria-labelledby="newsletter-title">
	<div class="container">
		<div class="newsletter-panel">
			<div class="newsletter-panel__copy">
				<p class="eyebrow"><?php esc_html_e( 'A place at the table', 'larder' ); ?></p>
				<h2 id="newsletter-title"><?php esc_html_e( 'Join the Kitchen Table', 'larder' ); ?></h2>
				<p><?php esc_html_e( 'Seasonal recipes, practical kitchen notes and thoughtful inspiration—delivered occasionally.', 'larder' ); ?></p>
				<span class="newsletter-panel__promise"><?php esc_html_e( 'No clutter. Just something worth cooking.', 'larder' ); ?></span>
			</div>

			<div class="newsletter-form-wrap">
				<?php if ( shortcode_exists( 'mc4wp_form' ) && $mailchimp_form_id ) : ?>
					<?php echo do_shortcode( '[mc4wp_form id="' . $mailchimp_form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
					<p class="newsletter-setup-note">
						<?php esc_html_e( 'Mailchimp setup: install and connect Mailchimp for WordPress, create a form, then enter the numeric form ID under Appearance → Customise → Mailchimp Newsletter.', 'larder' ); ?>
					</p>
				<?php elseif ( $newsletter_url ) : ?>
					<a class="button newsletter-button" href="<?php echo esc_url( $newsletter_url ); ?>"><?php esc_html_e( 'Join the Kitchen Table', 'larder' ); ?> →</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>