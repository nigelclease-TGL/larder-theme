<?php
/**
 * Newsletter call to action.
 *
 * @package Larder
 */

$mailchimp_form_id  = absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) );
$newsletter_page    = nkt_setup_find_page( array( 'newsletter' ) );
$newsletter_url     = $newsletter_page ? get_permalink( $newsletter_page ) : '';
$privacy_url        = get_privacy_policy_url();
$lead_magnet        = nkt_get_lead_magnet();
$newsletter_title   = (string) get_theme_mod( 'larder_newsletter_title', __( 'Join the Kitchen Table', 'larder' ) );
$newsletter_copy    = (string) get_theme_mod( 'larder_newsletter_copy', __( 'Seasonal recipes, practical kitchen notes and thoughtful inspiration—delivered occasionally.', 'larder' ) );
$newsletter_promise = (string) get_theme_mod( 'larder_newsletter_promise', __( 'No clutter. Just something worth cooking.', 'larder' ) );
?>
<section class="home-section home-newsletter nkt-growth-newsletter" aria-labelledby="newsletter-title" data-nkt-location="newsletter_section">
	<div class="container">
		<div class="newsletter-panel newsletter-panel--growth">
			<div class="newsletter-panel__copy">
				<p class="eyebrow"><?php esc_html_e( 'A place at the table', 'larder' ); ?></p>
				<h2 id="newsletter-title"><?php echo esc_html( $newsletter_title ); ?></h2>
				<p><?php echo esc_html( $newsletter_copy ); ?></p>
				<ul class="newsletter-benefits" aria-label="<?php esc_attr_e( 'Newsletter benefits', 'larder' ); ?>">
					<li><?php esc_html_e( 'Seasonal recipe ideas', 'larder' ); ?></li>
					<li><?php esc_html_e( 'Occasional, considered emails', 'larder' ); ?></li>
					<li><?php esc_html_e( 'Unsubscribe whenever you like', 'larder' ); ?></li>
				</ul>
				<span class="newsletter-panel__promise"><?php echo esc_html( $newsletter_promise ); ?></span>
			</div>

			<div class="newsletter-form-wrap" data-nkt-location="newsletter_form">
				<?php if ( shortcode_exists( 'mc4wp_form' ) && $mailchimp_form_id ) : ?>
					<?php echo do_shortcode( '[mc4wp_form id="' . $mailchimp_form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
					<p class="newsletter-setup-note">
						<?php esc_html_e( 'Mailchimp setup: install and connect Mailchimp for WordPress, create a form, then enter the numeric form ID under Appearance → Customise → Newsletter & Welcome Gift.', 'larder' ); ?>
					</p>
				<?php elseif ( $newsletter_url ) : ?>
					<a class="button newsletter-button" href="<?php echo esc_url( $newsletter_url ); ?>" data-nkt-event="newsletter_open" data-nkt-label="newsletter_section"><?php esc_html_e( 'Join the Kitchen Table', 'larder' ); ?> →</a>
				<?php endif; ?>

				<?php if ( $privacy_url ) : ?>
					<p class="newsletter-privacy"><?php printf( wp_kses_post( __( 'Your details are handled according to the <a href="%s">Privacy Policy</a>.', 'larder' ) ), esc_url( $privacy_url ) ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $lead_magnet['enabled'] ) : ?>
				<aside class="newsletter-gift" aria-label="<?php esc_attr_e( 'Free kitchen guide', 'larder' ); ?>">
					<p class="newsletter-gift__eyebrow"><?php esc_html_e( 'A welcome gift', 'larder' ); ?></p>
					<h3><?php echo esc_html( $lead_magnet['title'] ); ?></h3>
					<p><?php echo esc_html( $lead_magnet['copy'] ); ?></p>
					<a class="text-link" href="<?php echo esc_url( $lead_magnet['url'] ); ?>" data-nkt-event="lead_magnet_click" data-nkt-label="newsletter_section"><?php echo esc_html( $lead_magnet['label'] ); ?> →</a>
				</aside>
			<?php endif; ?>
		</div>
	</div>
</section>
