<?php
/** Editorial recipe and Kitchen Note audit helpers. @package Larder */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nkt_content_audit_is_note( $post_id ) {
	return has_category( array( 'kitchen-notes', 'baking-guides' ), $post_id );
}

function nkt_content_audit_has_recipe_card( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( has_shortcode( $content, 'wprm-recipe' ) ) { return true; }
	foreach ( array( 'wp-recipe-maker/recipe', 'wp-recipe-maker/recipe-snippet', 'wp-recipe-maker/recipe-roundup-item' ) as $block ) {
		if ( has_block( $block, $content ) ) { return true; }
	}
	return false !== stripos( $content, 'wprm-recipe' );
}

function nkt_get_content_audit( $post_id ) {
	$post_id       = absint( $post_id );
	$is_note       = nkt_content_audit_is_note( $post_id );
	$thumbnail_id  = get_post_thumbnail_id( $post_id );
	$image_alt     = $thumbnail_id ? trim( (string) get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ) : '';
	$categories    = wp_get_post_categories( $post_id, array( 'fields' => 'all' ) );
	$useful_cats   = array_filter( $categories, static fn( $term ) => $term instanceof WP_Term && 'uncategorized' !== $term->slug );
	$content       = (string) get_post_field( 'post_content', $post_id );
	$word_count    = str_word_count( wp_strip_all_tags( strip_shortcodes( $content ) ) );
	$minimum_words = $is_note ? 120 : 180;
	$checks        = array(
		'featured_image' => array( 'label' => __( 'Featured image', 'larder' ), 'complete' => (bool) $thumbnail_id, 'critical' => true ),
		'image_alt'      => array( 'label' => __( 'Image alt text', 'larder' ), 'complete' => ! $thumbnail_id || '' !== $image_alt, 'critical' => false ),
		'excerpt'        => array( 'label' => __( 'Editorial excerpt', 'larder' ), 'complete' => '' !== trim( (string) get_post_field( 'post_excerpt', $post_id ) ), 'critical' => true ),
		'category'       => array( 'label' => __( 'Useful category', 'larder' ), 'complete' => ! empty( $useful_cats ), 'critical' => true ),
		'content_depth'  => array( 'label' => sprintf( __( 'At least %d words', 'larder' ), $minimum_words ), 'complete' => $word_count >= $minimum_words, 'critical' => false ),
	);
	if ( ! $is_note ) {
		$checks['recipe_card'] = array( 'label' => __( 'WP Recipe Maker card', 'larder' ), 'complete' => nkt_content_audit_has_recipe_card( $post_id ), 'critical' => true );
	}
	$issues = $critical = array();
	foreach ( $checks as $key => $check ) {
		if ( ! $check['complete'] ) {
			$issues[ $key ] = $check['label'];
			if ( $check['critical'] ) { $critical[ $key ] = $check['label']; }
		}
	}
	return array(
		'post_id' => $post_id, 'type' => $is_note ? 'note' : 'recipe',
		'type_label' => $is_note ? __( 'Kitchen Note', 'larder' ) : __( 'Recipe', 'larder' ),
		'checks' => $checks, 'issues' => $issues, 'critical_issues' => $critical,
		'ready' => empty( $issues ), 'publish_ready' => empty( $critical ), 'word_count' => $word_count,
		'score' => count( $checks ) ? (int) round( ( ( count( $checks ) - count( $issues ) ) / count( $checks ) ) * 100 ) : 100,
	);
}

function nkt_get_content_audit_post_ids() {
	static $ids = null;
	if ( null === $ids ) {
		$ids = get_posts( array(
			'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids',
			'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true,
			'update_post_meta_cache' => false, 'update_post_term_cache' => false,
		) );
		$ids = array_map( 'absint', $ids );
	}
	return $ids;
}

function nkt_get_content_audit_summary() {
	$summary = array( 'total' => 0, 'recipes' => 0, 'notes' => 0, 'ready' => 0, 'needs_attention' => 0, 'critical' => 0 );
	foreach ( nkt_get_content_audit_post_ids() as $post_id ) {
		$audit = nkt_get_content_audit( $post_id );
		++$summary['total']; ++$summary[ 'note' === $audit['type'] ? 'notes' : 'recipes' ];
		++$summary[ $audit['ready'] ? 'ready' : 'needs_attention' ];
		if ( ! $audit['publish_ready'] ) { ++$summary['critical']; }
	}
	return $summary;
}

function nkt_register_content_audit_page() {
	add_management_page( __( 'Recipe Content Audit', 'larder' ), __( 'Recipe Content Audit', 'larder' ), 'edit_posts', 'nkt-content-audit', 'nkt_render_content_audit_page' );
}
add_action( 'admin_menu', 'nkt_register_content_audit_page' );

function nkt_content_audit_admin_assets( $hook ) {
	if ( in_array( $hook, array( 'tools_page_nkt-content-audit', 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_style( 'nkt-content-audit-admin', get_template_directory_uri() . '/assets/css/admin-content-audit.css', array(), wp_get_theme()->get( 'Version' ) );
	}
}
add_action( 'admin_enqueue_scripts', 'nkt_content_audit_admin_assets' );

function nkt_content_audit_badge( $complete, $label ) {
	$class = $complete ? 'nkt-audit-badge--good' : 'nkt-audit-badge--todo';
	printf( '<span class="nkt-audit-badge %1$s"><span aria-hidden="true">%2$s</span>%3$s</span>', esc_attr( $class ), $complete ? '✓' : '•', esc_html( $label ) );
}
