<?php
error_reporting( E_ALL );
define( 'ABSPATH', __DIR__ . '/' );
define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.24' );
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

$GLOBALS['block_fixture']=array();
$GLOBALS['legacy_nutrition_sections']=array();
$GLOBALS['write_count']=0;
$GLOBALS['routes']=array();

function add_action() {}
function register_rest_route( $namespace, $path, $args, $override=false ) { $GLOBALS['routes'][$namespace.$path]=$args; return true; }
function absint( $v ) { return abs( (int) $v ); }
function sanitize_key( $v ) { return strtolower( preg_replace('/[^a-z0-9_\-]/','',(string)$v) ); }
function sanitize_text_field( $v ) { return trim((string)$v); }
function sanitize_textarea_field( $v ) { return trim((string)$v); }
function rest_sanitize_boolean( $v ) { return filter_var($v,FILTER_VALIDATE_BOOLEAN); }
function is_wp_error( $v ) { return $v instanceof WP_Error; }
function __( $v, $domain=null ) { return $v; }
function wp_json_encode( $v, $flags=0 ) { return json_encode($v,$flags); }
function wp_strip_all_tags( $v ) { return strip_tags($v); }
function get_bloginfo( $key ) { return 'UTF-8'; }
function parse_blocks( $content ) { return $GLOBALS['block_fixture']; }
function nkt_gpt_connector_legacy_nutrition_article_sections( $content, $compact=true ) {
	return array('nutrition_sections'=>$GLOBALS['legacy_nutrition_sections'],'nutrition_like_blocks_outside_sections'=>array());
}
function untrailingslashit( $v ) { return rtrim((string)$v,'/'); }
function get_post_meta() { return array(); }
function update_post_meta() { $GLOBALS['write_count']++; return true; }

require dirname(__DIR__) . '/artifacts/generated/protected-lifecycle-0.7.24.php';

$checks=array();
function t( $name, $condition ) { global $checks; $checks[$name]=(bool)$condition; if(!$condition){fwrite(STDERR,"FAIL: $name\n");} }
function heading_block( $html, $level=3, $inner=array() ) { return array('blockName'=>'core/heading','attrs'=>array('level'=>$level),'innerHTML'=>$html,'innerBlocks'=>$inner); }

// Fixture 1: confirmed production structure with no usable top-level NUTRITION H2.
$GLOBALS['legacy_nutrition_sections']=array(array('heading'=>'NUTRITION','blocks'=>array()));
$GLOBALS['block_fixture']=array(
	array('blockName'=>'core/group','attrs'=>array(),'innerHTML'=>'','innerBlocks'=>array(
		heading_block('<h3 id="serving-one-brownie" class="wp-block-heading">Serving: one pumpkin chocolate chip cookie</h3>')
	)),
);
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('<h3 id="serving-one-brownie" class="wp-block-heading">Serving: one pumpkin chocolate chip cookie</h3>',1);
t('production fallback label', $r['serving_label']==='one pumpkin chocolate chip cookie');
t('production fallback source', $r['serving_label_source']==='unique_article_serving_h3_with_single_nutrition_section');
t('production matching count', $r['matching_visible_serving_h3_count']===1);
t('production fallback accepted', $r['serving_fallback_accepted']===true);
t('production block path', $r['serving_label_block_path']==='0.0');

// Fixture 2: nested inline markup through raw fallback.
$GLOBALS['block_fixture']=array();
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('<h3 class="wp-block-heading"><strong>Serving:</strong> one cookie</h3>',1);
t('nested markup label', $r['serving_label']==='one cookie');
t('nested markup accepted', $r['serving_fallback_accepted']===true);

// Fixture 3: ambiguous headings.
$GLOBALS['block_fixture']=array(
	heading_block('<h3>Serving: one cookie</h3>'),
	heading_block('<h3>Serving: two cookies</h3>'),
);
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',1);
t('ambiguous empty label', $r['serving_label']==='');
t('ambiguous count', $r['matching_visible_serving_h3_count']===2);
t('ambiguous reason', $r['serving_fallback_rejection_reason']==='multiple_visible_serving_h3_candidates');

// Fixture 4: no Serving heading.
$GLOBALS['block_fixture']=array(heading_block('<h3>NOTES</h3>'));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',1);
t('no heading empty', $r['serving_label']==='');
t('no heading count', $r['matching_visible_serving_h3_count']===0);
t('no heading reason', $r['serving_fallback_rejection_reason']==='no_visible_serving_h3_candidate');

// Fixture 5: multiple Nutrition sections reject the article-wide fallback.
$GLOBALS['block_fixture']=array(heading_block('<h3>Serving: one cookie</h3>'));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',2);
t('multiple sections rejected', $r['serving_label']==='');
t('multiple sections reason', $r['serving_fallback_rejection_reason']==='nutrition_section_count_not_unique');

// Existing section-scoped primary behavior remains supported.
$GLOBALS['block_fixture']=array(
	heading_block('<h2>NUTRITION</h2>',2),
	heading_block('<h3>Serving: one primary cookie</h3>',3),
	heading_block('<h2>METHOD</h2>',2),
);
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',1);
t('primary label', $r['serving_label']==='one primary cookie');
t('primary source', $r['serving_label_source']==='nutrition_heading');
t('primary fallback not used', $r['serving_fallback_accepted']===false);

// Fixture 6: corrected parser evidence does not invalidate an unchanged 0.7.23 baseline.
$old=array(
	'content_hash'=>str_repeat('a',64),'nutrition_section_hash'=>str_repeat('b',64),
	'serving_label'=>'','serving_label_hash'=>hash('sha256',''),'serving_label_source'=>null,'serving_label_block_path'=>null,
);
$new=$old;
$new['serving_label']='one pumpkin chocolate chip cookie';
$new['serving_label_hash']=hash('sha256',$new['serving_label']);
$new['serving_label_source']='unique_article_serving_h3_with_single_nutrition_section';
$new['serving_label_block_path']='0.0';
$new['parsed_nutrition_section_count']=1;
$new['matching_visible_serving_h3_count']=1;
$new['serving_fallback_accepted']=true;
$new['serving_fallback_rejection_reason']=null;
t('baseline compatibility ignores parser evidence', nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($old))===nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($new)));
$new['content_hash']=str_repeat('c',64);
t('baseline compatibility keeps content protection', nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($old))!==nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($new)));

// Fixture 7: serving mismatch remains a protected policy failure.
$policy=array(
	'allow_article_nutrition_change'=>true,
	'expected_serving_label_after'=>'one cookie',
	'allowed_removed_media_ids'=>array(),'allowed_added_media_ids'=>array(),
	'allowed_removed_media_urls'=>array(),'allowed_added_media_urls'=>array(),
	'allow_media_alt_text_change'=>false,'allow_media_non_alt_markup_change'=>false,
);
$before=array('nutrition_section_hash'=>'before','serving_label'=>'one cookie','media_manifest'=>array('attachment_ids'=>array(),'upload_urls'=>array(),'alt_text_hash'=>'a','non_alt_hash'=>'b','reference_hash'=>'c'));
$after=array('nutrition_section_hash'=>'after','serving_label'=>'wrong serving','media_manifest'=>$before['media_manifest']);
$p=nkt_gpt_par_0723_change_policy_checks($policy,$before,$after);
t('serving mismatch fails policy', in_array('expected_serving_label_after',$p['failed'],true));

$failed=array_keys(array_filter($checks,fn($v)=>!$v));
echo (count($checks)-count($failed)).'/'.count($checks)." runtime checks passed\n";
exit($failed?1:0);
