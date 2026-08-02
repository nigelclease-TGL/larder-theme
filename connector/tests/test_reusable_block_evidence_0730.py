#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import tempfile

ROOT = Path(__file__).resolve().parents[1]
FRAGMENT = ROOT / 'src' / 'parts' / '08f-reusable-block-evidence.phpfrag'
fragment_text = FRAGMENT.read_text(encoding='utf-8')

for forbidden in [
    'current_user_can(',
    'wp_update_post',
    'wp_insert_post',
    'wp_delete_post',
    'wp_trash_post',
    'update_post_meta',
    'delete_post_meta',
    'add_post_meta',
]:
    assert forbidden not in fragment_text, f'Forbidden callback dependency or mutation present: {forbidden}'

assert "nkt_gpt_par_0730_reusable_block_evidence_access" in fragment_text
assert "inspectReusableBlockEvidence' => true" in fragment_text

harness = r'''<?php
define( 'ABSPATH', __DIR__ );
define( 'OBJECT', 'OBJECT' );
define( 'NKT_GPT_PAR_0723_VERSION', '0.7.30' );

class WP_Error {
    private $code;
    private $message;
    public function __construct( $code, $message, $data = array() ) {
        $this->code = $code;
        $this->message = $message;
    }
    public function get_error_code() { return $this->code; }
    public function get_error_message() { return $this->message; }
}
class WP_REST_Request {
    private $params = array();
    private $route = '';
    public function __construct( $params = array(), $route = '' ) { $this->params = $params; $this->route = $route; }
    public function get_param( $name ) { return array_key_exists( $name, $this->params ) ? $this->params[ $name ] : null; }
    public function get_route() { return $this->route; }
}
class WP_REST_Response {
    private $data;
    public function __construct( $data = array(), $status = 200 ) { $this->data = $data; }
    public function get_data() { return $this->data; }
    public function set_data( $data ) { $this->data = $data; }
}
class WP_REST_Server { const READABLE = 'GET'; }

function absint( $value ) { return abs( (int) $value ); }
function wp_parse_args( $args, $defaults = array() ) { return array_merge( $defaults, (array) $args ); }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function __( $text, $domain = null ) { return $text; }
function register_rest_route() { return true; }
function add_action() { return true; }
function add_filter() { return true; }
function nkt_gpt_connector_no_store( $response ) { return $response; }
function nkt_gpt_par_0723_version_guard() { return true; }
function nkt_gpt_par_0723_canonicalize( $value ) {
    if ( is_array( $value ) ) {
        $keys = array_keys( $value );
        $is_list = $keys === range( 0, count( $value ) - 1 );
        if ( ! $is_list ) { ksort( $value, SORT_STRING ); }
        foreach ( $value as $key => $item ) { $value[ $key ] = nkt_gpt_par_0723_canonicalize( $item ); }
    }
    return $value;
}
function nkt_gpt_par_0723_hash( $value ) {
    return hash( 'sha256', json_encode( nkt_gpt_par_0723_canonicalize( $value ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION ) );
}
function do_blocks( $content ) { return '<rendered>' . $content . '</rendered>'; }

function make_post( $id, $type, $status, $content ) {
    return (object) array(
        'ID' => $id,
        'post_type' => $type,
        'post_status' => $status,
        'post_title' => 'Object ' . $id,
        'post_name' => 'object-' . $id,
        'post_author' => 2,
        'post_parent' => 0,
        'post_date' => '2026-08-02 10:00:00',
        'post_date_gmt' => '2026-08-02 08:00:00',
        'post_modified' => '2026-08-02 10:30:00',
        'post_modified_gmt' => '2026-08-02 08:30:00',
        'post_content' => $content,
    );
}

$GLOBALS['fixtures'] = array(
    1 => make_post( 1, 'wp_block', 'publish', '<!-- wp:block {"ref":8} /--><!-- wp:block {"ref":7} /--><!-- wp:block {"ref":8} /-->' ),
    2 => make_post( 2, 'wp_block', 'draft', '<p>Draft block</p>' ),
    3 => make_post( 3, 'wp_block', 'private', '<p>Private block</p>' ),
    4 => make_post( 4, 'wp_block', 'trash', '<p>Trashed block</p>' ),
    5 => make_post( 5, 'post', 'publish', '<p>Ordinary post</p>' ),
    6 => make_post( 6, 'wp_block', 'pending', '<p>Denied block</p>' ),
);
function get_post( $id, $output = OBJECT, $filter = 'raw' ) {
    return $GLOBALS['fixtures'][ (int) $id ] ?? null;
}
function apply_filters( $tag, $value ) {
    $args = func_get_args();
    if ( 'nkt_gpt_par_0730_reusable_block_evidence_access' === $tag ) {
        $post = $args[2] ?? null;
        if ( $post && 6 === (int) $post->ID ) {
            return new WP_Error( 'fixture_access_denied', 'Fixture access denied.' );
        }
    }
    return $value;
}

require $argv[1];

$options = array(
    'include_raw_content' => true,
    'include_reference_scan' => true,
    'include_public_render_evidence' => true,
);
$published = nkt_gpt_par_0730_reusable_block_evidence( 1, $options );
$repeat = nkt_gpt_par_0730_reusable_block_evidence( 1, $options );
$draft = nkt_gpt_par_0730_reusable_block_evidence( 2 );
$private = nkt_gpt_par_0730_reusable_block_evidence( 3 );
$trash = nkt_gpt_par_0730_reusable_block_evidence( 4 );
$wrong = nkt_gpt_par_0730_reusable_block_evidence( 5 );
$denied = nkt_gpt_par_0730_reusable_block_evidence( 6 );
$missing = nkt_gpt_par_0730_reusable_block_evidence( 999 );

$before_independent = $published['status_independent_object_hash'];
$before_inclusive = $published['status_inclusive_object_hash'];
$GLOBALS['fixtures'][1]->post_status = 'draft';
$GLOBALS['fixtures'][1]->post_modified = '2026-08-02 11:30:00';
$GLOBALS['fixtures'][1]->post_modified_gmt = '2026-08-02 09:30:00';
$status_changed = nkt_gpt_par_0730_reusable_block_evidence( 1 );

$result = array(
    'published' => $published,
    'repeat' => $repeat,
    'draft' => $draft,
    'private' => $private,
    'trash' => $trash,
    'wrong' => $wrong,
    'denied' => $denied,
    'missing' => $missing,
    'status_hashes' => array(
        'independent_before' => $before_independent,
        'independent_after' => $status_changed['status_independent_object_hash'],
        'inclusive_before' => $before_inclusive,
        'inclusive_after' => $status_changed['status_inclusive_object_hash'],
    ),
);
echo json_encode( $result, JSON_UNESCAPED_SLASHES );
'''

with tempfile.TemporaryDirectory() as directory:
    harness_path = Path(directory) / 'harness.php'
    harness_path.write_text(harness, encoding='utf-8')
    proc = subprocess.run(
        ['php', str(harness_path), str(FRAGMENT)],
        check=True,
        capture_output=True,
        text=True,
    )

result = json.loads(proc.stdout)

assert result['published']['classification'] == 'exists_accessible'
assert result['published']['literal_wordpress_status'] == 'publish'
assert result['published']['direct_core_block_references'] == [7, 8]
assert result['published']['raw_content_included'] is True
assert result['published']['public_render_evidence']['available'] is True
assert result['published']['writes_performed'] == 0
assert result['published']['changes_made'] is False
assert result['repeat']['status_inclusive_object_hash'] == result['published']['status_inclusive_object_hash']
assert result['draft']['literal_wordpress_status'] == 'draft'
assert result['private']['literal_wordpress_status'] == 'private'
assert result['trash']['literal_wordpress_status'] == 'trash'
assert result['wrong']['classification'] == 'exists_wrong_post_type'
assert result['wrong']['literal_post_type'] == 'post'
assert result['denied']['classification'] == 'exists_inaccessible'
assert result['denied']['exact_error_code'] == 'fixture_access_denied'
assert result['missing']['classification'] == 'missing_or_deleted'
assert result['missing']['object_exists'] is False
assert result['missing']['lookup_success'] is False
assert result['status_hashes']['independent_before'] == result['status_hashes']['independent_after']
assert result['status_hashes']['inclusive_before'] != result['status_hashes']['inclusive_after']

print('8 reusable-block object-state fixtures passed; hashes deterministic; writes performed: 0')
