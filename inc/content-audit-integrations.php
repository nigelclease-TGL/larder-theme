<?php
/** Editorial audit exports and editor integrations. @package Larder */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nkt_export_content_audit() {
	if ( ! current_user_can( 'edit_posts' ) ) { wp_die( esc_html__( 'You do not have permission to export this audit.', 'larder' ) ); }
	check_admin_referer( 'nkt_export_content_audit' );
	nocache_headers(); header( 'Content-Type: text/csv; charset=utf-8' ); header( 'Content-Disposition: attachment; filename=nigels-kitchen-table-content-audit.csv' );
	$output = fopen( 'php://output', 'w' );
	if ( false === $output ) { wp_die( esc_html__( 'The audit export could not be created.', 'larder' ) ); }
	fwrite( $output, "\xEF\xBB\xBF" );
	fputcsv( $output, array( 'Title', 'Type', 'Edit URL', 'Score', 'Missing essentials', 'Other recommendations' ) );
	foreach ( nkt_get_content_audit_post_ids() as $post_id ) {
		$audit = nkt_get_content_audit( $post_id ); $recommendations = array_diff_key( $audit['issues'], $audit['critical_issues'] );
		fputcsv( $output, array( get_the_title( $post_id ), $audit['type_label'], get_edit_post_link( $post_id, 'raw' ), $audit['score'] . '%', implode( '; ', $audit['critical_issues'] ), implode( '; ', $recommendations ) ) );
	}
	fclose( $output ); exit;
}
add_action( 'admin_post_nkt_export_content_audit', 'nkt_export_content_audit' );

function nkt_content_audit_posts_columns( $columns ) {
	$columns['nkt_content_type'] = __( 'Kitchen Table type', 'larder' ); $columns['nkt_readiness'] = __( 'Content readiness', 'larder' ); return $columns;
}
add_filter( 'manage_post_posts_columns', 'nkt_content_audit_posts_columns' );

function nkt_content_audit_posts_column_content( $column, $post_id ) {
	if ( ! in_array( $column, array( 'nkt_content_type', 'nkt_readiness' ), true ) ) { return; }
	$audit = nkt_get_content_audit( $post_id );
	if ( 'nkt_content_type' === $column ) { echo esc_html( $audit['type_label'] ); return; }
	nkt_content_audit_badge( $audit['ready'], $audit['ready'] ? __( 'Complete', 'larder' ) : sprintf( _n( '%d issue', '%d issues', count( $audit['issues'] ), 'larder' ), count( $audit['issues'] ) ) );
}
add_action( 'manage_post_posts_custom_column', 'nkt_content_audit_posts_column_content', 10, 2 );

function nkt_content_audit_add_meta_box() {
	add_meta_box( 'nkt-content-readiness', __( 'Kitchen Table readiness', 'larder' ), 'nkt_content_audit_render_meta_box', 'post', 'side', 'default' );
}
add_action( 'add_meta_boxes_post', 'nkt_content_audit_add_meta_box' );

function nkt_content_audit_render_meta_box( $post ) {
	$audit = nkt_get_content_audit( $post->ID );
	?>
	<p class="nkt-audit-editor-score"><strong><?php echo esc_html( $audit['score'] . '%' ); ?></strong> <?php echo esc_html( $audit['ready'] ? __( 'complete', 'larder' ) : __( 'ready after the items below are addressed', 'larder' ) ); ?></p>
	<ul class="nkt-audit-editor-list"><?php foreach ( $audit['checks'] as $check ) : ?><li class="<?php echo $check['complete'] ? 'is-complete' : 'is-incomplete'; ?>"><span aria-hidden="true"><?php echo $check['complete'] ? '✓' : '•'; ?></span><?php echo esc_html( $check['label'] ); ?></li><?php endforeach; ?></ul>
	<p><a href="<?php echo esc_url( admin_url( 'tools.php?page=nkt-content-audit' ) ); ?>"><?php esc_html_e( 'Open full content audit', 'larder' ); ?></a></p>
	<?php
}
