<?php
/**
 * Plugin Name: NKT GPT Connector 0.7.24 Serving Heading Extraction Upgrader
 * Description: One-time guarded upgrade from Nigel's Kitchen Table GPT Connector 0.7.23 to 0.7.24 with robust unique Serving H3 extraction and protected-baseline compatibility.
 * Version: 0.7.24.1
 * Author: Nigel's Kitchen Table
 * Requires at least: 6.6
 * Requires PHP: 8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_GPT_UPGRADER_0724_TARGET_VERSION = '0.7.24';
const NKT_GPT_UPGRADER_0724_SOURCE_VERSION = '0.7.23';
const NKT_GPT_UPGRADER_0724_NOTICE_OPTION  = 'nkt_gpt_connector_0724_upgrade_notice';

/** Find the single installed primary connector plugin file. */
function nkt_gpt_upgrader_0724_find_primary_plugin() {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$candidates = array();
	$preferred  = WP_PLUGIN_DIR . '/nkt-gpt-connector/nkt-gpt-connector.php';
	if ( is_file( $preferred ) ) {
		$candidates[] = $preferred;
	}
	foreach ( glob( WP_PLUGIN_DIR . '/*/*.php' ) ?: array() as $file ) {
		if ( realpath( $file ) === realpath( __FILE__ ) || in_array( $file, $candidates, true ) ) {
			continue;
		}
		$data = get_file_data( $file, array( 'Name' => 'Plugin Name', 'Version' => 'Version' ), 'plugin' );
		if ( "Nigel's Kitchen Table GPT Connector" === $data['Name'] ) {
			$candidates[] = $file;
		}
	}
	$candidates = array_values( array_unique( array_filter( $candidates, 'is_file' ) ) );
	if ( 1 !== count( $candidates ) ) {
		return new WP_Error(
			'nkt_gpt_upgrader_0724_connector_count',
			sprintf( 'Expected exactly one installed Nigel\'s Kitchen Table GPT Connector, found %d.', count( $candidates ) )
		);
	}
	$file = $candidates[0];
	if ( ! is_plugin_active( plugin_basename( $file ) ) ) {
		return new WP_Error( 'nkt_gpt_upgrader_0724_connector_inactive', 'The existing 0.7.23 connector must be active before upgrading.' );
	}
	return $file;
}

/** Write a file atomically in its existing directory. */
function nkt_gpt_upgrader_0724_atomic_write( $path, $contents ) {
	$temp = tempnam( dirname( $path ), '.nkt-0724-' );
	if ( ! $temp ) {
		return new WP_Error( 'nkt_gpt_upgrader_0724_temp_failed', 'Could not create a temporary upgrade file.' );
	}
	$written = file_put_contents( $temp, $contents, LOCK_EX );
	if ( false === $written || strlen( $contents ) !== $written ) {
		@unlink( $temp );
		return new WP_Error( 'nkt_gpt_upgrader_0724_write_failed', 'Could not write the complete temporary upgrade file.' );
	}
	@chmod( $temp, 0644 );
	if ( ! @rename( $temp, $path ) ) {
		@unlink( $temp );
		return new WP_Error( 'nkt_gpt_upgrader_0724_rename_failed', 'Could not atomically replace the connector file.' );
	}
	return true;
}

/** Write a web-safe encoded backup. */
function nkt_gpt_upgrader_0724_secure_backup( $source_path, $backup_path ) {
	$contents = file_get_contents( $source_path );
	if ( false === $contents ) {
		return new WP_Error( 'nkt_gpt_upgrader_0724_backup_read_failed', 'Could not read a file for protected backup.' );
	}
	$result = nkt_gpt_upgrader_0724_atomic_write( $backup_path, "<?php exit; ?>\n" . base64_encode( $contents ) );
	if ( ! is_wp_error( $result ) ) {
		@chmod( $backup_path, 0600 );
	}
	return $result;
}

/** Restore one web-safe encoded backup. */
function nkt_gpt_upgrader_0724_restore_secure_backup( $backup_path, $target_path ) {
	$payload = file_get_contents( $backup_path );
	$prefix  = "<?php exit; ?>\n";
	if ( false === $payload || 0 !== strpos( $payload, $prefix ) ) {
		return new WP_Error( 'nkt_gpt_upgrader_0724_backup_invalid', 'The protected backup could not be verified.' );
	}
	$decoded = base64_decode( substr( $payload, strlen( $prefix ) ), true );
	if ( false === $decoded ) {
		return new WP_Error( 'nkt_gpt_upgrader_0724_backup_decode_failed', 'The protected backup could not be decoded.' );
	}
	return nkt_gpt_upgrader_0724_atomic_write( $target_path, $decoded );
}

/** Invalidate PHP opcode cache for a replaced file when available. */
function nkt_gpt_upgrader_0724_invalidate_opcode( $path ) {
	if ( function_exists( 'opcache_invalidate' ) ) {
		@opcache_invalidate( $path, true );
	}
}

/** Restore all protected files after a failed write or verification. */
function nkt_gpt_upgrader_0724_restore_all( $main_backup, $main_file, $lifecycle_backup, $old_lifecycle, $new_lifecycle, $schema_backup, $schema_target ) {
	nkt_gpt_upgrader_0724_restore_secure_backup( $main_backup, $main_file );
	nkt_gpt_upgrader_0724_restore_secure_backup( $lifecycle_backup, $old_lifecycle );
	@unlink( $new_lifecycle );
	if ( $schema_backup ) {
		nkt_gpt_upgrader_0724_restore_secure_backup( $schema_backup, $schema_target );
	} else {
		@unlink( $schema_target );
	}
	nkt_gpt_upgrader_0724_invalidate_opcode( $main_file );
	nkt_gpt_upgrader_0724_invalidate_opcode( $old_lifecycle );
	wp_cache_flush();
}

/** Perform the guarded one-time source upgrade. */
function nkt_gpt_upgrader_0724_activate() {
	$main_file = nkt_gpt_upgrader_0724_find_primary_plugin();
	if ( is_wp_error( $main_file ) ) {
		wp_die( esc_html( $main_file->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$plugin_dir       = dirname( $main_file );
	$old_lifecycle    = $plugin_dir . '/protected-lifecycle-0.7.23.php';
	$new_lifecycle    = $plugin_dir . '/protected-lifecycle-0.7.24.php';
	$lifecycle_source = __DIR__ . '/protected-lifecycle-0.7.24.php';
	$schema_source    = __DIR__ . '/openapi-0.7.24.json';
	$schema_target    = $plugin_dir . '/openapi-0.7.24.json';

	if ( ! is_file( $old_lifecycle ) || ! is_readable( $lifecycle_source ) ) {
		wp_die( 'The installed 0.7.23 lifecycle or bundled 0.7.24 lifecycle file is unavailable.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}
	$main_source = file_get_contents( $main_file );
	$new_source  = file_get_contents( $lifecycle_source );
	if ( false === $main_source || false === $new_source || '' === trim( $new_source ) ) {
		wp_die( 'The connector or bundled lifecycle source could not be read.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$source_constant = "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.23' );";
	$old_marker      = "/* NKT protected article lifecycle 0.7.23 */";
	$new_marker      = "/* NKT protected article lifecycle 0.7.24 */";
	if ( 1 !== preg_match_all( '/^\s*\*\s*Version:\s*0\.7\.23\s*$/m', $main_source )
		|| 1 !== substr_count( $main_source, $source_constant )
		|| false === strpos( $main_source, $old_marker )
		|| false !== strpos( $main_source, $new_marker ) ) {
		wp_die( 'The active connector did not match the exact expected 0.7.23 source markers. No files were changed.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$stamp            = gmdate( 'YmdHis' );
	$main_backup      = $main_file . '.pre-0.7.24-' . $stamp . '.nktbak.php';
	$lifecycle_backup = $old_lifecycle . '.pre-0.7.24-' . $stamp . '.nktbak.php';
	$schema_backup    = is_file( $schema_target ) ? $schema_target . '.pre-0.7.24-' . $stamp . '.nktbak.php' : null;

	$backup_result = nkt_gpt_upgrader_0724_secure_backup( $main_file, $main_backup );
	if ( is_wp_error( $backup_result ) ) {
		wp_die( esc_html( $backup_result->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}
	$backup_result = nkt_gpt_upgrader_0724_secure_backup( $old_lifecycle, $lifecycle_backup );
	if ( is_wp_error( $backup_result ) ) {
		@unlink( $main_backup );
		wp_die( esc_html( $backup_result->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}
	if ( $schema_backup ) {
		$backup_result = nkt_gpt_upgrader_0724_secure_backup( $schema_target, $schema_backup );
		if ( is_wp_error( $backup_result ) ) {
			wp_die( esc_html( $backup_result->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
		}
	}

	$patched = preg_replace( '/(^\s*\*\s*Version:\s*)0\.7\.23(\s*$)/m', '${1}0.7.24${2}', $main_source, 1, $header_replacements );
	$patched = str_replace( $source_constant, "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.24' );", $patched, $constant_replacements );
	$loader_pattern = "~\s*/\* NKT protected article lifecycle 0\.7\.23 \*/\s*require_once __DIR__ \. '/protected-lifecycle-0\.7\.23\.php';\s*~";
	$new_loader = "\n\n/* NKT protected article lifecycle 0.7.24 */\nrequire_once __DIR__ . '/protected-lifecycle-0.7.24.php';\n";
	$patched = preg_replace( $loader_pattern, $new_loader, $patched, 1, $loader_replacements );
	if ( 1 !== $header_replacements || 1 !== $constant_replacements || 1 !== $loader_replacements ) {
		wp_die( 'The connector header, constant or lifecycle loader could not be replaced exactly once. No files were changed.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$new_write  = nkt_gpt_upgrader_0724_atomic_write( $new_lifecycle, $new_source );
	$main_write = is_wp_error( $new_write ) ? $new_write : nkt_gpt_upgrader_0724_atomic_write( $main_file, $patched );
	if ( is_wp_error( $main_write ) ) {
		nkt_gpt_upgrader_0724_restore_all( $main_backup, $main_file, $lifecycle_backup, $old_lifecycle, $new_lifecycle, $schema_backup, $schema_target );
		wp_die( esc_html( $main_write->get_error_message() ), 'NKT Connector upgrade rolled back', array( 'back_link' => true ) );
	}

	if ( is_readable( $schema_source ) ) {
		$schema_contents = file_get_contents( $schema_source );
		$schema_write = false === $schema_contents ? new WP_Error( 'nkt_gpt_upgrader_0724_schema_read_failed', 'The schema could not be read.' ) : nkt_gpt_upgrader_0724_atomic_write( $schema_target, $schema_contents );
		if ( is_wp_error( $schema_write ) ) {
			nkt_gpt_upgrader_0724_restore_all( $main_backup, $main_file, $lifecycle_backup, $old_lifecycle, $new_lifecycle, $schema_backup, $schema_target );
			wp_die( 'The 0.7.24 OpenAPI schema could not be installed. Previous files were restored.', 'NKT Connector upgrade rolled back', array( 'back_link' => true ) );
		}
	}

	nkt_gpt_upgrader_0724_invalidate_opcode( $main_file );
	nkt_gpt_upgrader_0724_invalidate_opcode( $new_lifecycle );
	wp_cache_flush();

	$verification             = file_get_contents( $main_file );
	$expected_lifecycle_sha    = hash( 'sha256', $new_source );
	$installed_lifecycle_sha   = is_file( $new_lifecycle ) ? hash_file( 'sha256', $new_lifecycle ) : '';
	$main_ok = false !== $verification
		&& false !== strpos( $verification, 'Version: 0.7.24' )
		&& false !== strpos( $verification, "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.24' );" )
		&& false !== strpos( $verification, $new_marker );
	if ( ! $main_ok || ! hash_equals( $expected_lifecycle_sha, (string) $installed_lifecycle_sha ) ) {
		nkt_gpt_upgrader_0724_restore_all( $main_backup, $main_file, $lifecycle_backup, $old_lifecycle, $new_lifecycle, $schema_backup, $schema_target );
		wp_die( 'Post-write verification failed. The original 0.7.23 connector files were restored.', 'NKT Connector upgrade rolled back', array( 'back_link' => true ) );
	}

	update_option(
		NKT_GPT_UPGRADER_0724_NOTICE_OPTION,
		array(
			'success'               => true,
			'upgraded_at'           => gmdate( DATE_ATOM ),
			'from_version'          => NKT_GPT_UPGRADER_0724_SOURCE_VERSION,
			'to_version'            => NKT_GPT_UPGRADER_0724_TARGET_VERSION,
			'main_backup_file'      => basename( $main_backup ),
			'lifecycle_backup_file' => basename( $lifecycle_backup ),
			'lifecycle_sha256'      => $installed_lifecycle_sha,
		),
		false
	);

	foreach ( get_plugins() as $relative_plugin => $plugin_data ) {
		if ( 'NKT GPT Connector 0.7.23 Update Runtime Guard Upgrader' === ( $plugin_data['Name'] ?? '' ) && is_plugin_active( $relative_plugin ) ) {
			deactivate_plugins( $relative_plugin, true );
		}
	}
	deactivate_plugins( plugin_basename( __FILE__ ), true );
}
register_activation_hook( __FILE__, 'nkt_gpt_upgrader_0724_activate' );
