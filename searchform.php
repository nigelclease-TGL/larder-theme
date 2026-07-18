<?php
/**
 * Search form.
 *
 * @package Larder
 */
?>
<form role="search" method="get" class="search-form larder-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'larder' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search recipes or ingredients…', 'placeholder', 'larder' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" autocomplete="off">
	</label>
	<button type="submit" class="search-submit button button-primary"><?php echo esc_html_x( 'Search', 'submit button', 'larder' ); ?></button>
</form>
