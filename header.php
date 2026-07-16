<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container header-inner">
		<a class="site-branding" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="site-title">The Gourmet Larder</span>
			<span class="site-tagline">Bake · Create · Share</span>
		</a>

		<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'larder' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<button class="search-toggle" type="button" aria-expanded="false" aria-controls="site-search-dialog">
				<span class="screen-reader-text"><?php esc_html_e( 'Open search', 'larder' ); ?></span>
				<span aria-hidden="true">⌕</span>
			</button>

			<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
				<?php esc_html_e( 'Menu', 'larder' ); ?>
			</button>
		</div>
	</div>
</header>

<div id="site-search-dialog" class="search-dialog" aria-hidden="true">
	<div class="search-dialog__backdrop" data-search-close></div>
	<div class="search-dialog__panel" role="dialog" aria-modal="true" aria-labelledby="site-search-title">
		<button class="search-dialog__close" type="button" data-search-close>
			<span aria-hidden="true">×</span>
			<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'larder' ); ?></span>
		</button>
		<p class="eyebrow"><?php esc_html_e( 'Find a recipe', 'larder' ); ?></p>
		<h2 id="site-search-title"><?php esc_html_e( 'What would you like to bake?', 'larder' ); ?></h2>
		<?php get_search_form(); ?>
	</div>
</div>