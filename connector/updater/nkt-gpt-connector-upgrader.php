<?php
/**
 * Plugin Name: NKT GPT Connector 0.7.23 Update Runtime Guard Upgrader
 * Description: One-time guarded upgrade from Nigel's Kitchen Table GPT Connector 0.7.22 to 0.7.23, aligning protected update runtime guards, Nutrition serving evidence, and dry-run-first exact cleanup.
 * Version: 0.7.23.1
 * Author: Nigel's Kitchen Table
 * Requires at least: 6.6
 * Requires PHP: 8.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const NKT_GPT_UPGRADER_0723_TARGET_VERSION = '0.7.23';
const NKT_GPT_UPGRADER_0723_SOURCE_VERSION = '0.7.22';
const NKT_GPT_UPGRADER_0723_NOTICE_OPTION  = 'nkt_gpt_connector_0723_upgrade_notice';

/** Find the single installed primary connector plugin file. */
function nkt_gpt_upgrader_0723_find_primary_plugin() {
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
			'nkt_gpt_upgrader_0723_connector_count',
			sprintf( 'Expected exactly one installed Nigel\'s Kitchen Table GPT Connector, found %d.', count( $candidates ) )
		);
	}

	$file     = $candidates[0];
	$relative = plugin_basename( $file );
	if ( ! is_plugin_active( $relative ) ) {
		return new WP_Error( 'nkt_gpt_upgrader_0723_connector_inactive', 'The existing Nigel\'s Kitchen Table GPT Connector must be active before upgrading.' );
	}

	return $file;
}

/** Write a file atomically in its existing directory. */
function nkt_gpt_upgrader_0723_atomic_write( $path, $contents ) {
	$directory = dirname( $path );
	$temp      = tempnam( $directory, '.nkt-0723-' );
	if ( ! $temp ) {
		return new WP_Error( 'nkt_gpt_upgrader_0723_temp_failed', 'Could not create a temporary upgrade file.' );
	}
	$written = file_put_contents( $temp, $contents, LOCK_EX );
	if ( false === $written || strlen( $contents ) !== $written ) {
		@unlink( $temp );
		return new WP_Error( 'nkt_gpt_upgrader_0723_write_failed', 'Could not write the complete temporary upgrade file.' );
	}
	@chmod( $temp, 0644 );
	if ( ! @rename( $temp, $path ) ) {
		@unlink( $temp );
		return new WP_Error( 'nkt_gpt_upgrader_0723_rename_failed', 'Could not atomically replace the connector file.' );
	}
	return true;
}

/** Write a web-safe encoded backup that cannot expose PHP source if requested directly. */
function nkt_gpt_upgrader_0723_secure_backup( $source_path, $backup_path ) {
	$contents = file_get_contents( $source_path );
	if ( false === $contents ) {
		return new WP_Error( 'nkt_gpt_upgrader_0723_backup_read_failed', 'Could not read a file for protected backup.' );
	}
	$payload = "<?php exit; ?>\n" . base64_encode( $contents );
	$result  = nkt_gpt_upgrader_0723_atomic_write( $backup_path, $payload );
	if ( ! is_wp_error( $result ) ) {
		@chmod( $backup_path, 0600 );
	}
	return $result;
}

/** Restore one web-safe encoded backup through the same atomic writer. */
function nkt_gpt_upgrader_0723_restore_secure_backup( $backup_path, $target_path ) {
	$payload = file_get_contents( $backup_path );
	if ( false === $payload || 0 !== strpos( $payload, "<?php exit; ?>\n" ) ) {
		return new WP_Error( 'nkt_gpt_upgrader_0723_backup_invalid', 'The protected backup could not be verified.' );
	}
	$decoded = base64_decode( substr( $payload, strlen( "<?php exit; ?>\n" ) ), true );
	if ( false === $decoded ) {
		return new WP_Error( 'nkt_gpt_upgrader_0723_backup_decode_failed', 'The protected backup could not be decoded.' );
	}
	return nkt_gpt_upgrader_0723_atomic_write( $target_path, $decoded );
}

/** Perform the guarded one-time source patch. */
function nkt_gpt_upgrader_0723_activate() {
	$main_file = nkt_gpt_upgrader_0723_find_primary_plugin();
	if ( is_wp_error( $main_file ) ) {
		wp_die( esc_html( $main_file->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$plugin_dir       = dirname( $main_file );
	$extension_source = __DIR__ . '/protected-lifecycle-0.7.23.php';
	$extension_target = $plugin_dir . '/protected-lifecycle-0.7.23.php';
	$schema_source    = __DIR__ . '/openapi-0.7.23.json';
	$schema_target    = $plugin_dir . '/openapi-0.7.23.json';
	$main_source      = file_get_contents( $main_file );

	if ( false === $main_source || ! is_readable( $extension_source ) ) {
		wp_die( 'The connector source or bundled lifecycle extension could not be read.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$version_header_matches = preg_match_all( '/^\s*\*\s*Version:\s*0\.7\.22\s*$/m', $main_source );
	$constant_marker        = "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.22' );";
	$constant_matches       = substr_count( $main_source, $constant_marker );
	$source_extension_marker= "/* NKT protected article lifecycle 0.7.22 */";
	$extension_marker       = "/* NKT protected article lifecycle 0.7.23 */";

	if ( 1 !== $version_header_matches || 1 !== $constant_matches || false === strpos( $main_source, $source_extension_marker ) || false !== strpos( $main_source, $extension_marker ) ) {
		wp_die(
			'The active connector did not match the exact expected 0.7.22 source markers. No files were changed.',
			'NKT Connector upgrade blocked',
			array( 'back_link' => true )
		);
	}

	$extension_contents = file_get_contents( $extension_source );
	if ( false === $extension_contents || '' === trim( $extension_contents ) ) {
		wp_die( 'The bundled lifecycle extension is empty or unreadable.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$backup_file = $main_file . '.pre-0.7.23-' . gmdate( 'YmdHis' ) . '.nktbak.php';
	$backup_result = nkt_gpt_upgrader_0723_secure_backup( $main_file, $backup_file );
	if ( is_wp_error( $backup_result ) ) {
		wp_die( esc_html( $backup_result->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$extension_backup = null;
	if ( is_file( $extension_target ) ) {
		$extension_backup = $extension_target . '.pre-upgrade-' . gmdate( 'YmdHis' ) . '.nktbak.php';
		$extension_backup_result = nkt_gpt_upgrader_0723_secure_backup( $extension_target, $extension_backup );
		if ( is_wp_error( $extension_backup_result ) ) {
			@unlink( $backup_file );
			wp_die( esc_html( $extension_backup_result->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
		}
	}

	$patched = preg_replace( '/(^\s*\*\s*Version:\s*)0\.7\.22(\s*$)/m', '${1}0.7.23${2}', $main_source, 1, $header_replacements );
	$patched = str_replace( $constant_marker, "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.23' );", $patched, $constant_replacements );
	$old_loader_pattern = "~\s*/\* NKT protected article lifecycle 0\.7\.22 \*/\s*require_once __DIR__ \. '/protected-lifecycle-0\.7\.22\.php';\s*~";
	$new_loader = "\n\n/* NKT protected article lifecycle 0.7.23 */\nrequire_once __DIR__ . '/protected-lifecycle-0.7.23.php';\n";
	$patched = preg_replace( $old_loader_pattern, $new_loader, $patched, 1, $loader_replacements );

	if ( 1 !== $header_replacements || 1 !== $constant_replacements || 1 !== $loader_replacements ) {
		@unlink( $backup_file );
		if ( $extension_backup ) {
			@unlink( $extension_backup );
		}
		wp_die( 'The connector version or 0.7.22 lifecycle loader could not be replaced exactly once. No files were changed.', 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$extension_write = nkt_gpt_upgrader_0723_atomic_write( $extension_target, $extension_contents );
	if ( is_wp_error( $extension_write ) ) {
		@unlink( $backup_file );
		if ( $extension_backup ) {
			@unlink( $extension_backup );
		}
		wp_die( esc_html( $extension_write->get_error_message() ), 'NKT Connector upgrade blocked', array( 'back_link' => true ) );
	}

	$main_write = nkt_gpt_upgrader_0723_atomic_write( $main_file, $patched );
	if ( is_wp_error( $main_write ) ) {
		nkt_gpt_upgrader_0723_restore_secure_backup( $backup_file, $main_file );
		if ( $extension_backup ) {
			nkt_gpt_upgrader_0723_restore_secure_backup( $extension_backup, $extension_target );
		} else {
			@unlink( $extension_target );
		}
		wp_die( esc_html( $main_write->get_error_message() ), 'NKT Connector upgrade rolled back', array( 'back_link' => true ) );
	}

	if ( is_readable( $schema_source ) ) {
		$schema_contents = file_get_contents( $schema_source );
		if ( false !== $schema_contents ) {
			nkt_gpt_upgrader_0723_atomic_write( $schema_target, $schema_contents );
		}
	}

	$verification = file_get_contents( $main_file );
	$extension_ok = is_file( $extension_target ) && hash_file( 'sha256', $extension_target ) === hash( 'sha256', $extension_contents );
	$main_ok      = false !== $verification
		&& false !== strpos( $verification, "Version: 0.7.23" )
		&& false !== strpos( $verification, "define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.23' );" )
		&& false !== strpos( $verification, $extension_marker );

	if ( ! $main_ok || ! $extension_ok ) {
		nkt_gpt_upgrader_0723_restore_secure_backup( $backup_file, $main_file );
		if ( $extension_backup ) {
			nkt_gpt_upgrader_0723_restore_secure_backup( $extension_backup, $extension_target );
		} else {
			@unlink( $extension_target );
		}
		wp_die( 'Post-write verification failed. The original connector was restored.', 'NKT Connector upgrade rolled back', array( 'back_link' => true ) );
	}

	update_option(
		NKT_GPT_UPGRADER_0723_NOTICE_OPTION,
		array(
			'success'          => true,
			'upgraded_at'      => gmdate( DATE_ATOM ),
			'from_version'     => NKT_GPT_UPGRADER_0723_SOURCE_VERSION,
			'to_version'       => NKT_GPT_UPGRADER_0723_TARGET_VERSION,
			'backup_file'      => basename( $backup_file ),
			'extension_sha256' => hash_file( 'sha256', $extension_target ),
		),
		false
	);

	wp_cache_flush();

	// Retire the prior one-time upgrader if it was left active. Its symbols are
	// deliberately not reused by this compatibility package.
	foreach ( get_plugins() as $relative_plugin => $plugin_data ) {
		if ( in_array( ( $plugin_data['Name'] ?? '' ), array( 'NKT GPT Connector 0.7.17 Upgrader', 'NKT GPT Connector 0.7.18 Compatibility Upgrader', 'NKT GPT Connector 0.7.19 Scoped Change Upgrader', 'NKT GPT Connector 0.7.20 Hook-Isolated Recipe Guard Upgrader', 'NKT GPT Connector 0.7.21 Recipe Status Repair Preview Upgrader', 'NKT GPT Connector 0.7.22 Protected Draft Creation Guard Upgrader' ), true ) && is_plugin_active( $relative_plugin ) ) {
			deactivate_plugins( $relative_plugin, true );
		}
	}
	deactivate_plugins( plugin_basename( __FILE__ ), true );
}
register_activation_hook( __FILE__, 'nkt_gpt_upgrader_0723_activate' );
