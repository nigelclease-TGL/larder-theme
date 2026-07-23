<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#66785F">
	<meta name="application-name" content="<?php esc_attr_e( "Nigel's Kitchen Table", 'larder' ); ?>">
	<meta name="apple-mobile-web-app-title" content="<?php esc_attr_e( "Nigel's Kitchen Table", 'larder' ); ?>">
	<link rel="manifest" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/site.webmanifest' ); ?>">
	<?php if ( ! has_site_icon() ) : ?>
		<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/favicon.ico' ); ?>" sizes="any">
		<link rel="icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/favicon.svg' ); ?>" type="image/svg+xml">
		<link rel="apple-touch-icon" href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/apple-touch-icon.png' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'larder' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<div class="site-branding">
			<a class="site-branding__fallback" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php esc_attr_e( "Nigel's Kitchen Table home", 'larder' ); ?>">
				<picture>
					<source media="(max-width: 620px)" srcset="<?php echo esc_url( get_template_directory_uri() . '/assets/images/nkt-logo-compact.svg' ); ?>">
					<img class="site-branding__mark" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/nkt-logo-horizontal.svg' ); ?>" alt="<?php esc_attr_e( "Nigel's Kitchen Table", 'larder' ); ?>" width="520" height="132" decoding="async" fetchpriority="high">
				</picture>
			</a>
		</div>

		<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'larder' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => 'nkt_primary_menu_fallback',
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<button class="search-toggle" type="button" aria-expanded="false" aria-controls="site-search-dialog" aria-haspopup="dialog">
				<span class="screen-reader-text"><?php esc_html_e( 'Open recipe search', 'larder' ); ?></span>
				<svg aria-hidden="true" viewBox="0 0 24 24" width="21" height="21" focusable="false"><circle cx="11" cy="11" r="6.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4 4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
			</button>

			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?php esc_attr_e( 'Open menu', 'larder' ); ?>">
				<span class="menu-toggle__label"><?php esc_html_e( 'Menu', 'larder' ); ?></span>
				<span class="menu-toggle__icon" aria-hidden="true"><span></span><span></span></span>
			</button>
		</div>
	</div>
</header>

<div id="site-search-dialog" class="search-dialog" aria-hidden="true" inert>
	<div class="search-dialog__backdrop" data-search-close></div>
	<div class="search-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="site-search-title" tabindex="-1">
		<button class="search-dialog__close" type="button" data-search-close>
			<span aria-hidden="true">×</span>
			<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'larder' ); ?></span>
		</button>
		<p class="eyebrow"><?php esc_html_e( 'Find a recipe', 'larder' ); ?></p>
		<h2 id="site-search-title"><?php esc_html_e( 'What would you like to cook?', 'larder' ); ?></h2>
		<p class="search-dialog__intro"><?php esc_html_e( 'Search by recipe name, ingredient or occasion.', 'larder' ); ?></p>
		<?php get_search_form(); ?>
	</div>
</div>
