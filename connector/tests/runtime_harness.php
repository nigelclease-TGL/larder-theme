<?php
error_reporting( E_ALL );
define( 'ABSPATH', __DIR__ . '/' );
define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.23' );
define( 'ARRAY_A', 'ARRAY_A' );

class WP_Error {
	private $code; private $message; private $data;
	public function __construct( $code, $message, $data = array() ) { $this->code=$code; $this->message=$message; $this->data=$data; }
	public function get_error_code() { return $this->code; }
	public function get_error_message() { return $this->message; }
	public function get_error_data() { return $this->data; }
}
class WP_REST_Request {
	private $params;
	public function __construct( $params = array() ) { $this->params=$params; }
	public function get_param( $key ) { return $this->params[$key] ?? null; }
	public function has_param( $key ) { return array_key_exists( $key, $this->params ); }
}
class WP_REST_Response {
	public $data; public $status;
	public function __construct( $data, $status=200 ) { $this->data=$data; $this->status=$status; }
}
class WP_REST_Server { const READABLE='GET'; const CREATABLE='POST'; }

$GLOBALS['routes']=array();
$GLOBALS['posts']=array();
$GLOBALS['meta']=array();
$GLOBALS['published_contents']=array();
$GLOBALS['write_count']=0;
$GLOBALS['block_fixture']=array();

function add_action() {}
function register_rest_route( $namespace, $path, $args, $override=false ) { $GLOBALS['routes'][$namespace.$path]=$args; return true; }
function absint( $v ) { return abs( (int) $v ); }
function sanitize_key( $v ) { return strtolower( preg_replace('/[^a-z0-9_\-]/','',(string)$v) ); }
function sanitize_text_field( $v ) { return trim((string)$v); }
function sanitize_textarea_field( $v ) { return trim((string)$v); }
function rest_sanitize_boolean( $v ) { return filter_var($v,FILTER_VALIDATE_BOOLEAN); }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function __( $v ) { return $v; }
function wp_json_encode( $v, $flags=0 ) { return json_encode($v,$flags); }
function wp_strip_all_tags( $v ) { return strip_tags($v); }
function get_bloginfo( $key ) { return 'UTF-8'; }
function get_post_stati() { return array('publish'=>'publish','draft'=>'draft','pending'=>'pending','private'=>'private','future'=>'future','trash'=>'trash','auto-draft'=>'auto-draft','inherit'=>'inherit'); }
function parse_blocks( $content ) { return $GLOBALS['block_fixture']; }
function nkt_gpt_connector_no_store( $response ) { return $response; }
function get_post( $id, $format=null ) { return $GLOBALS['posts'][$id] ?? null; }
function get_post_status( $id ) { return isset($GLOBALS['posts'][$id]) ? $GLOBALS['posts'][$id]->post_status : false; }
function get_post_meta( $id, $key='', $single=false ) {
	if ( '' === $key ) { return $GLOBALS['meta'][$id] ?? array(); }
	return $GLOBALS['meta'][$id][$key] ?? ( $single ? '' : array() );
}
function update_post_meta( $id, $key, $value ) { $GLOBALS['write_count']++; $GLOBALS['meta'][$id][$key]=$value; return true; }
function delete_post_meta( $id, $key ) { $GLOBALS['write_count']++; unset($GLOBALS['meta'][$id][$key]); return true; }
function get_posts( $args=array() ) { return array_keys($GLOBALS['published_contents']); }
function get_post_field( $field, $id ) { return $GLOBALS['published_contents'][$id] ?? ''; }
function wp_delete_post( $id, $force=false ) { $GLOBALS['write_count']++; unset($GLOBALS['posts'][$id]); return (object)array('ID'=>$id); }
function current_time( $type, $gmt=false ) { return '2026-08-01 16:00:00'; }
function clean_post_cache() {}
function wp_cache_delete() {}
function get_post_type( $id ) { return isset($GLOBALS['posts'][$id]) ? $GLOBALS['posts'][$id]->post_type : false; }
function get_option() { return array(); }
function delete_option() {}
function current_user_can() { return false; }

require dirname(__DIR__) . '/src/protected-lifecycle-0.7.23.php';

$checks=array();
function t( $name, $condition ) { global $checks; $checks[$name]=(bool)$condition; if(!$condition){fwrite(STDERR,"FAIL: $name\n");} }
function req_base() {
	return array(
		'connector_version'=>'0.7.23',
		'expected_ordered_wprm_recipe_ids'=>array(30780,30800),
		'expected_recipe_statuses'=>array('30780'=>'publish','30800'=>'publish'),
		'expected_recipe_object_hashes'=>array('30780'=>str_repeat('a',64),'30800'=>str_repeat('b',64)),
		'expected_status_independent_recipe_hashes'=>array('30780'=>str_repeat('c',64),'30800'=>str_repeat('d',64)),
		'preserve_intentional_multi_recipe_exception'=>true,
	);
}

nkt_gpt_par_0723_register_routes();
$route=$GLOBALS['routes']['nkt-gpt/v1/protected-article-revisions/update'] ?? array();
t('route callback aligned', ($route['callback']??'')==='nkt_gpt_par_0723_update');
t('route has recipe status map', isset($route['args']['expected_recipe_statuses']));
t('route has status-independent map', isset($route['args']['expected_status_independent_recipe_hashes']));

$valid=nkt_gpt_par_0723_validate_recipe_guard_maps(new WP_REST_Request(req_base()));
t('valid maps accepted', !is_wp_error($valid));
$bad=req_base(); $bad['expected_status_independent_recipe_hashes']['30780']=str_repeat('A',64);
t('malformed lowercase hash rejected', is_wp_error(nkt_gpt_par_0723_validate_recipe_guard_maps(new WP_REST_Request($bad))));
$missing=req_base(); unset($missing['expected_recipe_statuses']['30800']);
t('missing recipe map ID rejected', is_wp_error(nkt_gpt_par_0723_validate_recipe_guard_maps(new WP_REST_Request($missing))));
$extra=req_base(); $extra['expected_recipe_statuses']['99999']='draft';
t('extra recipe map ID rejected', is_wp_error(nkt_gpt_par_0723_validate_recipe_guard_maps(new WP_REST_Request($extra))));
t('map failures made no writes', $GLOBALS['write_count']===0);

$baseline=array(
	'ordered_wprm_recipe_ids'=>array(30780,30800),'wprm_reference_count'=>2,
	'recipe_identities'=>array(30780=>array('literal_post_type'=>'wprm_recipe','post_modified_gmt'=>'same'),30800=>array('literal_post_type'=>'wprm_recipe','post_modified_gmt'=>'same')),
	'recipe_statuses'=>array(30780=>'publish',30800=>'publish'),
	'recipe_object_hashes'=>array(30780=>str_repeat('a',64),30800=>str_repeat('b',64)),
	'recipe_status_independent_hashes'=>array(30780=>str_repeat('c',64),30800=>str_repeat('d',64)),
	'wprm_nutrition_hashes'=>array(30780=>str_repeat('e',64),30800=>str_repeat('e',64)),
	'recipe_component_hashes'=>array(30780=>array('meta'=>'x'),30800=>array('meta'=>'y')),
	'intentional_multi_recipe_exception'=>array('valid'=>true,'stale'=>false,'exact_recipe_ids_match'=>true),
);
$current=$baseline; $current['recipe_statuses'][30780]='draft';
$e=nkt_gpt_par_0723_update_recipe_guard_evidence(new WP_REST_Request(req_base()),$baseline,$current,$valid);
t('recipe status drift preflight rejected', in_array('recipe_30780_status_matches',$e['failed'],true));
$current=$baseline; $current['recipe_status_independent_hashes'][30800]=str_repeat('f',64);
$e=nkt_gpt_par_0723_update_recipe_guard_evidence(new WP_REST_Request(req_base()),$baseline,$current,$valid);
t('status-independent drift preflight rejected', in_array('recipe_30800_status_independent_hash_matches',$e['failed'],true));
$after=$baseline; $after['recipe_component_hashes'][30780]['meta']='changed'; $after['recipe_identities'][30780]['post_modified_gmt']='later';
$d=nkt_gpt_par_0723_recipe_drift_evidence($baseline,$after);
t('post-write component drift identified', $d[30780]['changed'] && in_array('meta',$d[30780]['changed_components'],true));
t('post-write modified timestamp drift identified', $d[30780]['post_modified_gmt_changed']);

$GLOBALS['block_fixture']=array(
	array('blockName'=>'core/paragraph','attrs'=>array(),'innerHTML'=>'<p>great on their own or served with milk</p>','innerBlocks'=>array()),
	array('blockName'=>'core/heading','attrs'=>array('level'=>2),'innerHTML'=>'<h2>NUTRITION</h2>','innerBlocks'=>array()),
	array('blockName'=>'core/columns','attrs'=>array(),'innerHTML'=>'','innerBlocks'=>array(
		array('blockName'=>'core/column','attrs'=>array(),'innerHTML'=>'','innerBlocks'=>array(
			array('blockName'=>'core/heading','attrs'=>array('level'=>3),'innerHTML'=>'<h3>Serving: one vanilla chocolate chunk cookie</h3>','innerBlocks'=>array())
		))
	)),
	array('blockName'=>'core/heading','attrs'=>array('level'=>2),'innerHTML'=>'<h2>METHOD</h2>','innerBlocks'=>array()),
);
$s=nkt_gpt_par_0723_extract_nutrition_serving_heading('fixture');
t('serving literal from Nutrition H3', $s['serving_label']==='one vanilla chocolate chunk cookie');
t('serving hash from same literal', $s['serving_label_hash']===hash('sha256',$s['serving_label']));
t('serving source reported', $s['serving_label_source']==='nutrition_heading');
t('serving block path reported', $s['serving_label_block_path']==='2.0.0.0');

// Eligible applied draft dry-run.
$GLOBALS['posts'][500]=(object)array('ID'=>500,'post_type'=>'post','post_status'=>'draft');
$GLOBALS['meta'][500]=array('_nkt_revision_source_post_id'=>10,'_nkt_par_0723_lifecycle'=>array('application_status'=>'applied_verified'));
$cleanup=new WP_REST_Request(array('connector_version'=>'0.7.23','dry_run'=>true,'cleanup_mode'=>'archive','archive_batch_id'=>'batch-1','cleanup_reason'=>'test','draft_post_ids'=>array(500)));
$before_writes=$GLOBALS['write_count'];
$r=nkt_gpt_par_0723_cleanup_revision_objects($cleanup);
t('cleanup dry-run succeeds', $r instanceof WP_REST_Response && $r->data['dry_run']===true);
t('cleanup dry-run makes no changes', $GLOBALS['write_count']===$before_writes && $r->data['writes_attempted']===false);

// Live-referenced clone refusal.
$GLOBALS['posts'][600]=(object)array('ID'=>600,'post_type'=>'wprm_recipe','post_status'=>'draft');
$GLOBALS['meta'][600]=array('_nkt_revision_source_recipe_id'=>100);
$GLOBALS['published_contents'][1]='<!-- wp:shortcode -->[wprm-recipe id="600"]<!-- /wp:shortcode -->';
$r=nkt_gpt_par_0723_cleanup_revision_objects(new WP_REST_Request(array('connector_version'=>'0.7.23','dry_run'=>true,'cleanup_mode'=>'archive','archive_batch_id'=>'batch-2','cleanup_reason'=>'test','cloned_recipe_ids'=>array(600))));
t('cleanup refuses live-referenced object', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_par_cleanup_preflight_failed');

// Protected ID refusal and permanent-delete authorisation.
$GLOBALS['posts'][41019]=(object)array('ID'=>41019,'post_type'=>'post','post_status'=>'draft');
$GLOBALS['meta'][41019]=array('_nkt_revision_source_post_id'=>30752,'_nkt_par_0723_lifecycle'=>array('application_status'=>'applied_verified'));
$r=nkt_gpt_par_0723_cleanup_revision_objects(new WP_REST_Request(array('connector_version'=>'0.7.23','dry_run'=>true,'cleanup_mode'=>'archive','archive_batch_id'=>'batch-3','cleanup_reason'=>'test','draft_post_ids'=>array(41019))));
t('protected IDs refused without explicit authorisation', is_wp_error($r));
$r=nkt_gpt_par_0723_cleanup_revision_objects(new WP_REST_Request(array('connector_version'=>'0.7.23','dry_run'=>true,'cleanup_mode'=>'permanent_delete','archive_batch_id'=>'batch-4','cleanup_reason'=>'test','draft_post_ids'=>array(500))));
t('permanent delete refused without separate authorisation', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_par_cleanup_delete_not_authorised');

$failed=array_keys(array_filter($checks,fn($v)=>!$v));
echo (count($checks)-count($failed)).'/'.count($checks)." runtime checks passed\n";
exit($failed?1:0);
