<?php
/**
 * Dedicated newsletter landing page.
 *
 * @package Larder
 */

get_header();

$mailchimp_form_id = absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) );
$lead_magnet       = nkt_get_lead_magnet();
$privacy_url       = get_privacy_policy_url();
$title             = (string) get_theme_mod( 'larder_newsletter_title', __( 'Join the Kitchen Table', 'larder' ) );
$copy              = (string) get_theme_mod( 'larder_newsletter_copy', __( 'Seasonal recipes, practical kitchen notes and thoughtful inspiration—delivered occasionally.', 'larder' ) );
$promise           = (string) get_theme_mod( 'larder_newsletter_promise', __( 'No clutter. Just something worth cooking.', 'larder' ) );
?>
<main id="primary" class="nkt-growth-page nkt-newsletter-page" data-nkt-location="newsletter_page">
	<?php while ( have_posts() ) : the_post(); ?>
		<header class="nkt-growth-hero">
			<div class="container nkt-growth-hero__grid">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'A place at the table', 'larder' ); ?></p>
					<h1><?php echo esc_html( $title ); ?></h1>
					<p class="nkt-growth-hero__lead"><?php echo esc_html( $copy ); ?></p>
					<ul class="nkt-growth-benefits">
						<li><?php esc_html_e( 'Seasonal recipes selected with care', 'larder' ); ?></li>
						<li><?php esc_html_e( 'Practical notes for the home kitchen', 'larder' ); ?></li>
						<li><?php esc_html_e( 'Occasional emails, never daily clutter', 'larder' ); ?></li>
					</ul>
					<p class="nkt-growth-promise"><?php echo esc_html( $promise ); ?></p>
				</div>

				<div class="nkt-growth-form-card" data-nkt-location="newsletter_landing_form">
					<p class="nkt-growth-form-card__eyebrow"><?php esc_html_e( 'Pull up a chair', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'Sign up for the next note from the kitchen.', 'larder' ); ?></h2>
					<?php if ( shortcode_exists( 'mc4wp_form' ) && $mailchimp_form_id ) : ?>
						<?php echo do_shortcode( '[mc4wp_form id="' . $mailchimp_form_id . '"]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
						<p class="newsletter-setup-note"><?php esc_html_e( 'Enter the Mailchimp for WordPress form ID under Appearance → Customise → Newsletter & Welcome Gift.', 'larder' ); ?></p>
					<?php else : ?>
						<p><?php esc_html_e( 'The newsletter form is being prepared. Please check back shortly.', 'larder' ); ?></p>
					<?php endif; ?>
					<?php if ( $privacy_url ) : ?>
						<p class="newsletter-privacy"><?php printf( wp_kses_post( __( 'Your details are handled according to the <a href="%s">Privacy Policy</a>.', 'larder' ) ), esc_url( $privacy_url ) ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		</header>

		<?php if ( $lead_magnet['enabled'] ) : ?>
			<section class="nkt-growth-gift-section" aria-labelledby="newsletter-gift-title">
				<div class="container nkt-growth-gift-section__inner">
					<div>
						<p class="eyebrow"><?php esc_html_e( 'A welcome gift', 'larder' ); ?></p>
						<h2 id="newsletter-gift-title"><?php echo esc_html( $lead_magnet['title'] ); ?></h2>
						<p><?php echo esc_html( $lead_magnet['copy'] ); ?></p>
					</div>
					<a class="button button-secondary" href="<?php echo esc_url( $lead_magnet['url'] ); ?>" data-nkt-event="lead_magnet_click" data-nkt-label="newsletter_page"><?php echo esc_html( $lead_magnet['label'] ); ?></a>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( trim( get_the_content() ) ) : ?>
			<section class="nkt-growth-page-content">
				<div class="container prose"><?php the_content(); ?></div>
			</section>
		<?php endif; ?>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
