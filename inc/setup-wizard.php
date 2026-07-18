<?php
/**
 * Nigel's Kitchen Table setup assistant.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the setup screen beneath Appearance.
 */
function nkt_register_setup_page() {
	add_theme_page(
		__( "Nigel's Kitchen Table Setup", 'larder' ),
		__( 'Kitchen Table Setup', 'larder' ),
		'edit_theme_options',
		'nkt-setup',
		'nkt_render_setup_page'
	);
}
add_action( 'admin_menu', 'nkt_register_setup_page' );

/** Show a one-time setup prompt after activation. */
function nkt_mark_theme_activation() {
	set_transient( 'nkt_theme_activation_notice', 1, HOUR_IN_SECONDS );
}
add_action( 'after_switch_theme', 'nkt_mark_theme_activation' );

function nkt_theme_activation_notice() {
	if ( ! current_user_can( 'edit_theme_options' ) || ! get_transient( 'nkt_theme_activation_notice' ) ) { return; }
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_nkt-setup' === $screen->id ) { delete_transient( 'nkt_theme_activation_notice' ); return; }
	?>
	<div class="notice notice-success is-dismissible"><p><strong><?php esc_html_e( "Nigel's Kitchen Table is active.", 'larder' ); ?></strong> <?php esc_html_e( 'Complete the staging checklist before showing the new design to visitors.', 'larder' ); ?></p><p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=nkt-setup' ) ); ?>"><?php esc_html_e( 'Open Kitchen Table Setup', 'larder' ); ?></a></p></div>
	<?php
}
add_action( 'admin_notices', 'nkt_theme_activation_notice' );

function nkt_setup_find_page( $slugs ) {
	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page instanceof WP_Post && 'publish' === $page->post_status ) { return $page; }
	}
	return null;
}

function nkt_setup_ensure_page( $title, $slug, $aliases = array() ) {
	$page = nkt_setup_find_page( array_merge( array( $slug ), $aliases ) );
	if ( $page ) { return (int) $page->ID; }
	$page_id = wp_insert_post( array( 'post_title' => $title, 'post_name' => $slug, 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => '' ), true );
	return is_wp_error( $page_id ) ? 0 : (int) $page_id;
}

function nkt_handle_quick_setup() {
	if ( ! current_user_can( 'edit_theme_options' ) ) { wp_die( esc_html__( 'You do not have permission to run theme setup.', 'larder' ) ); }
	check_admin_referer( 'nkt_quick_setup' );
	$home_id = nkt_setup_ensure_page( __( 'Home', 'larder' ), 'home' );
	nkt_setup_ensure_page( __( 'Recipes', 'larder' ), 'recipes' );
	nkt_setup_ensure_page( __( 'Recipe Collections', 'larder' ), 'recipe-collections', array( 'collections', 'seasons' ) );
	nkt_setup_ensure_page( __( 'Kitchen Notes', 'larder' ), 'kitchen-notes', array( 'baking-guides' ) );
	nkt_setup_ensure_page( __( 'About Nigel', 'larder' ), 'about-nigel', array( 'my-story', 'about' ) );
	nkt_setup_ensure_page( __( 'Contact', 'larder' ), 'contact', array( 'contact-me' ) );
	nkt_setup_ensure_page( __( 'Newsletter', 'larder' ), 'newsletter' );
	nkt_setup_ensure_page( __( 'Work with Nigel', 'larder' ), 'work-with-nigel', array( 'partnerships', 'work-with-me' ) );
	nkt_setup_ensure_page( __( 'Editorial Standards', 'larder' ), 'editorial-standards', array( 'recipe-standards' ) );
	nkt_setup_ensure_page( __( 'Affiliate Disclosure', 'larder' ), 'affiliate-disclosure', array( 'disclosure' ) );
	if ( $home_id ) { update_option( 'show_on_front', 'page' ); update_option( 'page_on_front', $home_id ); }
	set_transient( 'nkt_setup_complete_notice', 1, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?page=nkt-setup' ) ); exit;
}
add_action( 'admin_post_nkt_quick_setup', 'nkt_handle_quick_setup' );

function nkt_setup_status_item( $complete, $label, $help = '' ) {
	$class = $complete ? 'nkt-status--good' : 'nkt-status--todo'; $icon = $complete ? '✓' : '•';
	?>
	<li class="nkt-status <?php echo esc_attr( $class ); ?>"><span class="nkt-status__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span><div><strong><?php echo esc_html( $label ); ?></strong><?php if ( $help ) : ?><p><?php echo esc_html( $help ); ?></p><?php endif; ?></div></li>
	<?php
}

function nkt_render_setup_page() {
	$required_plugins = array(
		'WP Recipe Maker' => class_exists( 'WPRM_Recipe_Manager' ) || defined( 'WPRM_VERSION' ),
		'SEO plugin' => nkt_has_seo_plugin(),
		'Contact Form 7' => defined( 'WPCF7_VERSION' ),
		'Mailchimp for WordPress' => defined( 'MC4WP_VERSION' ) || function_exists( 'mc4wp_show_form' ),
	);
	$pages = array(
		'Recipes' => (bool) nkt_setup_find_page( array( 'recipes' ) ),
		'Recipe Collections' => (bool) nkt_setup_find_page( array( 'recipe-collections', 'collections', 'seasons' ) ),
		'Kitchen Notes' => (bool) nkt_setup_find_page( array( 'kitchen-notes', 'baking-guides' ) ),
		'About Nigel' => (bool) nkt_setup_find_page( array( 'about-nigel', 'my-story', 'about' ) ),
		'Contact' => (bool) nkt_setup_find_page( array( 'contact', 'contact-me' ) ),
		'Newsletter' => (bool) nkt_setup_find_page( array( 'newsletter' ) ),
		'Work with Nigel' => (bool) nkt_setup_find_page( array( 'work-with-nigel', 'partnerships', 'work-with-me' ) ),
		'Editorial Standards' => (bool) nkt_setup_find_page( array( 'editorial-standards', 'recipe-standards' ) ),
		'Affiliate Disclosure' => (bool) nkt_setup_find_page( array( 'affiliate-disclosure', 'disclosure' ) ),
	);
	$hero_ready = (bool) absint( get_theme_mod( 'larder_hero_image', 0 ) );
	$portrait_ready = (bool) absint( get_theme_mod( 'larder_portrait_image', 0 ) );
	$primary_menu = has_nav_menu( 'primary' ); $footer_menu = has_nav_menu( 'footer' );
	$static_homepage = 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0;
	$mailchimp_form = absint( get_theme_mod( 'larder_mailchimp_form_id', 0 ) );
	$lead_magnet = nkt_get_lead_magnet(); $home_promotion = nkt_get_home_promotion(); $measurement_ready = nkt_has_measurement_integration();
	$privacy_ready = (bool) get_privacy_policy_url(); $permalinks_ready = '' !== (string) get_option( 'permalink_structure' );
	$visibility_ready = nkt_is_staging_site() ? ! (bool) get_option( 'blog_public' ) : (bool) get_option( 'blog_public' );
	$https_ready = is_ssl(); $content_audit = nkt_get_content_audit_summary();
	?>
	<div class="wrap nkt-setup-wrap"><h1><?php esc_html_e( "Nigel's Kitchen Table Setup", 'larder' ); ?></h1><p class="nkt-setup-lead"><?php esc_html_e( 'Use this checklist after installing the theme on staging. Nothing here changes your recipes, categories or existing recipe URLs.', 'larder' ); ?></p>
	<?php if ( get_transient( 'nkt_setup_complete_notice' ) ) : delete_transient( 'nkt_setup_complete_notice' ); ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Essential pages were checked and the static homepage was configured.', 'larder' ); ?></p></div><?php endif; ?>
	<div class="nkt-setup-grid">
	<section class="nkt-setup-card"><h2><?php esc_html_e( '1. Site structure', 'larder' ); ?></h2><ul class="nkt-status-list"><?php nkt_setup_status_item( $static_homepage, __( 'Static homepage selected', 'larder' ), __( 'The editorial homepage requires a static front page.', 'larder' ) ); foreach ( $pages as $label => $complete ) { nkt_setup_status_item( $complete, $label . ' ' . __( 'page found', 'larder' ) ); } ?></ul><form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post"><input type="hidden" name="action" value="nkt_quick_setup"><?php wp_nonce_field( 'nkt_quick_setup' ); submit_button( __( 'Create missing pages and set homepage', 'larder' ), 'primary', 'submit', false ); ?></form></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '2. Brand and navigation', 'larder' ); ?></h2><ul class="nkt-status-list"><?php nkt_setup_status_item( $hero_ready, __( 'Homepage hero image selected', 'larder' ) ); nkt_setup_status_item( $portrait_ready, __( 'Small About Nigel portrait selected', 'larder' ) ); nkt_setup_status_item( has_site_icon(), __( 'WordPress Site Icon selected', 'larder' ) ); nkt_setup_status_item( $primary_menu, __( 'Primary menu assigned', 'larder' ) ); nkt_setup_status_item( $footer_menu, __( 'Footer menu assigned', 'larder' ) ); ?></ul><p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'larder' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Manage menus', 'larder' ); ?></a></p></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '3. Plugins and newsletter', 'larder' ); ?></h2><ul class="nkt-status-list"><?php foreach ( $required_plugins as $label => $complete ) { nkt_setup_status_item( $complete, $label ); } nkt_setup_status_item( $mailchimp_form > 0, __( 'Mailchimp form ID entered', 'larder' ) ); ?></ul><p><a class="button" href="<?php echo esc_url( admin_url( 'plugin-install.php' ) ); ?>"><?php esc_html_e( 'Install plugins', 'larder' ); ?></a></p></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '4. Business growth', 'larder' ); ?></h2><ul class="nkt-status-list"><?php nkt_setup_status_item( $mailchimp_form > 0, __( 'Newsletter form is connected', 'larder' ) ); nkt_setup_status_item( $lead_magnet['enabled'], __( 'Optional welcome gift is configured', 'larder' ) ); nkt_setup_status_item( $home_promotion['enabled'] || is_active_sidebar( 'homepage-promotion' ), __( 'Optional homepage promotion is configured', 'larder' ) ); nkt_setup_status_item( $measurement_ready, __( 'Measurement integration detected', 'larder' ) ); ?></ul><p><a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=larder_newsletter' ) ); ?>"><?php esc_html_e( 'Configure newsletter', 'larder' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'customize.php?autofocus[section]=larder_growth' ) ); ?>"><?php esc_html_e( 'Configure promotion', 'larder' ); ?></a></p></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '5. Launch readiness', 'larder' ); ?></h2><ul class="nkt-status-list"><?php nkt_setup_status_item( $https_ready, __( 'HTTPS is active', 'larder' ) ); nkt_setup_status_item( $permalinks_ready, __( 'Pretty permalinks are configured', 'larder' ) ); nkt_setup_status_item( $privacy_ready, __( 'Privacy Policy page is assigned', 'larder' ) ); nkt_setup_status_item( $visibility_ready, nkt_is_staging_site() ? __( 'Staging is hidden from search engines', 'larder' ) : __( 'Production is visible to search engines', 'larder' ) ); ?></ul><p><a class="button" href="<?php echo esc_url( admin_url( 'site-health.php' ) ); ?>"><?php esc_html_e( 'Run full Site Health checks', 'larder' ); ?></a></p></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '6. Editorial readiness', 'larder' ); ?></h2><ul class="nkt-status-list"><?php nkt_setup_status_item( 0 === $content_audit['critical'], __( 'Every published recipe has its essential content', 'larder' ), sprintf( _n( '%d published item is missing an essential', '%d published items are missing essentials', $content_audit['critical'], 'larder' ), $content_audit['critical'] ) ); nkt_setup_status_item( 0 === $content_audit['needs_attention'], __( 'Every published item passes the full editorial checklist', 'larder' ), sprintf( _n( '%d item still has a recommendation', '%d items still have recommendations', $content_audit['needs_attention'], 'larder' ), $content_audit['needs_attention'] ) ); ?></ul><p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'tools.php?page=nkt-content-audit' ) ); ?>"><?php esc_html_e( 'Open Recipe Content Audit', 'larder' ); ?></a></p></section>
	<section class="nkt-setup-card"><h2><?php esc_html_e( '7. Final manual checks', 'larder' ); ?></h2><ol><li><?php esc_html_e( 'Run an UpdraftPlus backup.', 'larder' ); ?></li><li><?php esc_html_e( 'Regenerate thumbnails.', 'larder' ); ?></li><li><?php esc_html_e( 'Check representative recipes on desktop and mobile.', 'larder' ); ?></li><li><?php esc_html_e( 'Confirm WP Recipe Maker print, ratings and schema output.', 'larder' ); ?></li><li><?php esc_html_e( 'Test the contact form and Mailchimp confirmation email.', 'larder' ); ?></li><li><?php esc_html_e( 'Clear WP Super Cache after the final review.', 'larder' ); ?></li></ol></section>
	</div></div>
	<style>.nkt-setup-wrap{max-width:1180px}.nkt-setup-lead{font-size:16px;max-width:800px}.nkt-setup-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-top:24px}.nkt-setup-card{background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,.04)}.nkt-setup-card h2{margin-top:0}.nkt-status-list{margin:0 0 22px}.nkt-status{display:flex;gap:12px;padding:10px 0;border-bottom:1px solid #f0f0f1}.nkt-status:last-child{border:0}.nkt-status__icon{display:grid;place-items:center;width:24px;height:24px;border-radius:50%;font-weight:700;flex:0 0 24px}.nkt-status--good .nkt-status__icon{background:#dff2e1;color:#176b25}.nkt-status--todo .nkt-status__icon{background:#fff2cc;color:#8a6100}.nkt-status p{margin:3px 0 0;color:#646970}.nkt-setup-card ol{padding-left:20px;line-height:1.7}@media(max-width:782px){.nkt-setup-grid{grid-template-columns:1fr}}</style>
	<?php
}
