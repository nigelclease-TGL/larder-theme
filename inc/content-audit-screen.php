<?php
/** Editorial audit screen. @package Larder */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function nkt_render_content_audit_page() {
	if ( ! current_user_can( 'edit_posts' ) ) { wp_die( esc_html__( 'You do not have permission to view this audit.', 'larder' ) ); }
	$type   = isset( $_GET['nkt_type'] ) ? sanitize_key( wp_unslash( $_GET['nkt_type'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$status = isset( $_GET['nkt_status'] ) ? sanitize_key( wp_unslash( $_GET['nkt_status'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$page   = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$rows   = array();
	foreach ( nkt_get_content_audit_post_ids() as $post_id ) {
		$audit = nkt_get_content_audit( $post_id ); $title = get_the_title( $post_id );
		if ( 'all' !== $type && $type !== $audit['type'] ) { continue; }
		if ( 'ready' === $status && ! $audit['ready'] ) { continue; }
		if ( 'attention' === $status && $audit['ready'] ) { continue; }
		if ( 'critical' === $status && $audit['publish_ready'] ) { continue; }
		if ( $search && false === stripos( $title, $search ) ) { continue; }
		$rows[] = array( 'post_id' => $post_id, 'title' => $title, 'audit' => $audit );
	}
	usort( $rows, static function ( $a, $b ) {
		$issues = count( $b['audit']['issues'] ) <=> count( $a['audit']['issues'] );
		return $issues ?: strcasecmp( $a['title'], $b['title'] );
	} );
	$per_page = 25; $pages = max( 1, (int) ceil( count( $rows ) / $per_page ) ); $page = min( $page, $pages );
	$visible = array_slice( $rows, ( $page - 1 ) * $per_page, $per_page );
	$summary = nkt_get_content_audit_summary();
	$export  = wp_nonce_url( admin_url( 'admin-post.php?action=nkt_export_content_audit' ), 'nkt_export_content_audit' );
	?>
	<div class="wrap nkt-audit-wrap">
		<div class="nkt-audit-heading"><div><h1><?php esc_html_e( 'Recipe Content Audit', 'larder' ); ?></h1><p><?php esc_html_e( 'Review every published recipe and Kitchen Note without changing content or URLs. Publication dates are deliberately excluded from this workflow.', 'larder' ); ?></p></div><a class="button" href="<?php echo esc_url( $export ); ?>"><?php esc_html_e( 'Export CSV', 'larder' ); ?></a></div>
		<div class="nkt-audit-summary" role="list">
		<?php foreach ( array( 'total' => __( 'Published content', 'larder' ), 'recipes' => __( 'Recipes', 'larder' ), 'notes' => __( 'Kitchen Notes', 'larder' ), 'ready' => __( 'Complete', 'larder' ), 'needs_attention' => __( 'Needs attention', 'larder' ), 'critical' => __( 'Missing essentials', 'larder' ) ) as $key => $label ) : ?>
			<div class="nkt-audit-summary__card" role="listitem"><strong><?php echo esc_html( number_format_i18n( $summary[ $key ] ) ); ?></strong><span><?php echo esc_html( $label ); ?></span></div>
		<?php endforeach; ?>
		</div>
		<form class="nkt-audit-filters" method="get"><input type="hidden" name="page" value="nkt-content-audit">
			<label><span><?php esc_html_e( 'Content type', 'larder' ); ?></span><select name="nkt_type"><option value="all" <?php selected( $type, 'all' ); ?>><?php esc_html_e( 'All content', 'larder' ); ?></option><option value="recipe" <?php selected( $type, 'recipe' ); ?>><?php esc_html_e( 'Recipes', 'larder' ); ?></option><option value="note" <?php selected( $type, 'note' ); ?>><?php esc_html_e( 'Kitchen Notes', 'larder' ); ?></option></select></label>
			<label><span><?php esc_html_e( 'Readiness', 'larder' ); ?></span><select name="nkt_status"><option value="all" <?php selected( $status, 'all' ); ?>><?php esc_html_e( 'All statuses', 'larder' ); ?></option><option value="critical" <?php selected( $status, 'critical' ); ?>><?php esc_html_e( 'Missing essentials', 'larder' ); ?></option><option value="attention" <?php selected( $status, 'attention' ); ?>><?php esc_html_e( 'Needs attention', 'larder' ); ?></option><option value="ready" <?php selected( $status, 'ready' ); ?>><?php esc_html_e( 'Complete', 'larder' ); ?></option></select></label>
			<label class="nkt-audit-filters__search"><span><?php esc_html_e( 'Search title', 'larder' ); ?></span><input type="search" name="s" value="<?php echo esc_attr( $search ); ?>"></label>
			<button class="button button-primary" type="submit"><?php esc_html_e( 'Filter', 'larder' ); ?></button><a class="button" href="<?php echo esc_url( admin_url( 'tools.php?page=nkt-content-audit' ) ); ?>"><?php esc_html_e( 'Clear', 'larder' ); ?></a>
		</form>
		<div class="nkt-audit-table-wrap"><table class="widefat striped nkt-audit-table"><thead><tr><th><?php esc_html_e( 'Title', 'larder' ); ?></th><th><?php esc_html_e( 'Type', 'larder' ); ?></th><th><?php esc_html_e( 'Image', 'larder' ); ?></th><th><?php esc_html_e( 'Excerpt', 'larder' ); ?></th><th><?php esc_html_e( 'Category', 'larder' ); ?></th><th><?php esc_html_e( 'Recipe card', 'larder' ); ?></th><th><?php esc_html_e( 'Status', 'larder' ); ?></th></tr></thead><tbody>
		<?php if ( $visible ) : foreach ( $visible as $row ) : $audit = $row['audit']; ?>
			<tr><td><strong><a href="<?php echo esc_url( get_edit_post_link( $row['post_id'] ) ); ?>"><?php echo esc_html( $row['title'] ); ?></a></strong><div class="row-actions"><span><a href="<?php echo esc_url( get_permalink( $row['post_id'] ) ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View', 'larder' ); ?></a></span></div></td><td><?php echo esc_html( $audit['type_label'] ); ?></td>
			<td><?php nkt_content_audit_badge( $audit['checks']['featured_image']['complete'], $audit['checks']['featured_image']['complete'] ? __( 'Ready', 'larder' ) : __( 'Missing', 'larder' ) ); if ( ! $audit['checks']['image_alt']['complete'] ) : ?><small><?php esc_html_e( 'Alt text needed', 'larder' ); ?></small><?php endif; ?></td>
			<td><?php nkt_content_audit_badge( $audit['checks']['excerpt']['complete'], $audit['checks']['excerpt']['complete'] ? __( 'Ready', 'larder' ) : __( 'Missing', 'larder' ) ); ?></td>
			<td><?php nkt_content_audit_badge( $audit['checks']['category']['complete'], $audit['checks']['category']['complete'] ? __( 'Ready', 'larder' ) : __( 'Missing', 'larder' ) ); ?></td>
			<td><?php if ( 'note' === $audit['type'] ) : ?><span class="nkt-audit-muted"><?php esc_html_e( 'Not required', 'larder' ); ?></span><?php else : nkt_content_audit_badge( $audit['checks']['recipe_card']['complete'], $audit['checks']['recipe_card']['complete'] ? __( 'Ready', 'larder' ) : __( 'Missing', 'larder' ) ); endif; ?></td>
			<td><?php nkt_content_audit_badge( $audit['ready'], $audit['ready'] ? __( 'Complete', 'larder' ) : sprintf( _n( '%d issue', '%d issues', count( $audit['issues'] ), 'larder' ), count( $audit['issues'] ) ) ); ?><small><?php echo esc_html( $audit['score'] . '%' ); ?></small></td></tr>
		<?php endforeach; else : ?><tr><td colspan="7"><?php esc_html_e( 'No published content matches these filters.', 'larder' ); ?></td></tr><?php endif; ?>
		</tbody></table></div>
		<?php if ( $pages > 1 ) : ?><div class="tablenav"><div class="tablenav-pages"><?php echo wp_kses_post( paginate_links( array( 'base' => add_query_arg( 'paged', '%#%' ), 'format' => '', 'current' => $page, 'total' => $pages, 'prev_text' => __( 'Previous', 'larder' ), 'next_text' => __( 'Next', 'larder' ) ) ) ); ?></div></div><?php endif; ?>
	</div>
	<?php
}
