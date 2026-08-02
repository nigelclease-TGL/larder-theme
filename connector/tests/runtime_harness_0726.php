<?php
error_reporting( E_ALL );
define( 'ABSPATH', __DIR__ . '/' );
define( 'NKT_GPT_CONNECTOR_VERSION', '0.7.26' );
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

require dirname(__DIR__) . '/artifacts/generated/protected-lifecycle-0.7.26.php';

$checks=array();
function t( $name, $condition ) { global $checks; $checks[$name]=(bool)$condition; if(!$condition){fwrite(STDERR,"FAIL: $name\n");} }
function heading_block( $html, $level=3, $inner=array() ) { return array('blockName'=>'core/heading','attrs'=>array('level'=>$level),'innerHTML'=>$html,'innerBlocks'=>$inner); }

$serving = '<h3 id="serving-one-brownie" class="wp-block-heading">Serving: one pumpkin chocolate chip cookie</h3>';
$table = '<p><strong>Nutrition per serving</strong></p>'
	. '<figure class="wp-block-table"><table><tbody>'
	. '<tr><td><strong>Calories</strong></td><td>249.5&nbsp;kcal&nbsp;(12%)</td></tr>'
	. '<tr><td><strong>Total Fat</strong></td><td>11.5&nbsp;g&nbsp;(16%)</td></tr>'
	. '<tr><td><strong>Carbs</strong></td><td>34.9&nbsp;g&nbsp;(13%)</td></tr>'
	. '<tr><td><strong>Sugars</strong></td><td>20.7&nbsp;g&nbsp;(23%)</td></tr>'
	. '<tr><td><strong>Protein</strong></td><td>2.3&nbsp;g&nbsp;(5%)</td></tr>'
	. '</tbody></table><figcaption>% Daily Values based on a 2,000 calorie diet</figcaption></figure>';
$production = $serving . $table;
$GLOBALS['legacy_nutrition_sections']=array();
$GLOBALS['block_fixture']=array(array('blockName'=>'core/group','attrs'=>array(),'innerHTML'=>'','innerBlocks'=>array(heading_block($serving))));

$e=nkt_gpt_par_0723_structured_nutrient_evidence($table);
t('production table distinct count', $e['count']===5);
t('production table labels', $e['labels']===array('Calories','Carbs','Protein','Sugars','Total Fat'));
t('production table mode', in_array('table_cells',$e['modes']['Calories'],true));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading($production,0);
t('production label', $r['serving_label']==='one pumpkin chocolate chip cookie');
t('production source', $r['serving_label_source']==='unique_article_serving_h3_with_unique_nutrition_presentation');
t('production accepted', $r['serving_fallback_accepted']===true);
t('production gate', $r['serving_fallback_gate']==='legacy_parser_zero_unique_nutrition_presentation');
t('production nutrient count', $r['serving_fallback_nutrient_label_count']===5);
t('production markers', $r['serving_fallback_segment_nutrition_marker_count']===1 && $r['serving_fallback_article_nutrition_marker_count']===1);
t('production disclaimer evidence', $r['serving_fallback_daily_value_marker_count']===1 && $r['serving_fallback_calorie_basis_count']===1);

$colon='<p>Calories: 10 kcal</p><p>Total Fat: 2 g</p><p>Carbohydrates: 3 g</p><p>Sugars: 1 g</p><p>Protein: 1 g</p>';
$e=nkt_gpt_par_0723_structured_nutrient_evidence($colon);
t('colon form count', $e['count']===5);
t('colon form mode', in_array('colon_text',$e['modes']['Calories'],true) || in_array('same_record',$e['modes']['Calories'],true));

$records='<p><strong>Calories</strong></p><p>10 kcal</p><li><strong>Total Fat</strong> 2 g</li><li>Carbs 3 g</li><li>Sugars 1 g</li><li>Protein 1 g</li>';
$e=nkt_gpt_par_0723_structured_nutrient_evidence($records);
t('record form count', $e['count']===5);
t('adjacent record mode', in_array('adjacent_record',$e['modes']['Calories'],true));

$weak='<p>Calories are discussed here.</p><p>Total Fat may vary.</p><p>Protein source.</p>';
$e=nkt_gpt_par_0723_structured_nutrient_evidence($weak);
t('unpaired prose rejected', $e['count']===0);

$bad_table='<table><tr><td>Calories</td><td>unknown</td></tr><tr><td>Protein</td><td>none</td></tr></table>';
$e=nkt_gpt_par_0723_structured_nutrient_evidence($bad_table);
t('table labels require numeric values', $e['count']===0);

$duplicate='<p>Calories: 10 kcal</p><p>Calories: 10 kcal</p><p>Total Fat: 2 g</p><p>Carbs: 3 g</p><p>Sugars: 1 g</p><p>Protein: 1 g</p>';
$e=nkt_gpt_par_0723_structured_nutrient_evidence($duplicate);
t('duplicate labels counted distinctly', $e['count']===5);

$GLOBALS['block_fixture']=array(heading_block('<h3>Serving: one cookie</h3>'));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('<h3>Serving: one cookie</h3><p>Nutrition per serving</p><p>Calories: 10 kcal</p><p>% Daily Values based on a 2,000 calorie diet</p>',0);
t('weak nutrient signature rejected', $r['serving_label']==='' && $r['serving_fallback_rejection_reason']==='insufficient_nutrient_labels');

$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('<h3>Serving: one cookie</h3>'.$table.'<p>Nutrition per serving</p>',0);
t('duplicate presentation rejected', $r['serving_label']==='' && $r['serving_fallback_rejection_reason']==='nutrition_per_serving_marker_not_unique');

$GLOBALS['block_fixture']=array(heading_block('<h3>Serving: one</h3>'),heading_block('<h3>Serving: two</h3>'));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',1);
t('ambiguous serving rejected', $r['serving_label']==='' && $r['matching_visible_serving_h3_count']===2);

$GLOBALS['block_fixture']=array(heading_block('<h3>Serving: one cookie</h3>'));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',2);
t('multiple parser sections rejected', $r['serving_fallback_rejection_reason']==='nutrition_section_count_not_unique');

$GLOBALS['block_fixture']=array(heading_block('<h2>NUTRITION</h2>',2),heading_block('<h3>Serving: primary cookie</h3>',3),heading_block('<h2>METHOD</h2>',2));
$r=nkt_gpt_par_0723_extract_nutrition_serving_heading('',1);
t('primary path retained', $r['serving_label']==='primary cookie' && $r['serving_label_source']==='nutrition_heading');

$old=array('content_hash'=>str_repeat('a',64),'nutrition_section_hash'=>str_repeat('b',64),'serving_label'=>'','serving_label_hash'=>hash('sha256',''),'serving_label_source'=>null);
$new=$old;
$new['serving_label']='one cookie';
$new['serving_label_hash']=hash('sha256','one cookie');
$new['serving_label_source']='unique_article_serving_h3_with_unique_nutrition_presentation';
$new['serving_fallback_nutrient_label_count']=5;
t('baseline ignores parser evidence', nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($old))===nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($new)));
$new['content_hash']=str_repeat('c',64);
t('baseline retains content guard', nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($old))!==nkt_gpt_par_0723_hash(nkt_gpt_par_0723_baseline_comparison_state($new)));

$policy=array('allow_article_nutrition_change'=>true,'expected_serving_label_after'=>'one cookie','allowed_removed_media_ids'=>array(),'allowed_added_media_ids'=>array(),'allowed_removed_media_urls'=>array(),'allowed_added_media_urls'=>array(),'allow_media_alt_text_change'=>false,'allow_media_non_alt_markup_change'=>false);
$before=array('nutrition_section_hash'=>'before','serving_label'=>'one cookie','media_manifest'=>array('attachment_ids'=>array(),'upload_urls'=>array(),'alt_text_hash'=>'a','non_alt_hash'=>'b','reference_hash'=>'c'));
$after=array('nutrition_section_hash'=>'after','serving_label'=>'wrong','media_manifest'=>$before['media_manifest']);
$p=nkt_gpt_par_0723_change_policy_checks($policy,$before,$after);
t('serving mismatch remains protected', in_array('expected_serving_label_after',$p['failed'],true));

$failed=array_keys(array_filter($checks,fn($v)=>!$v));
echo (count($checks)-count($failed)).'/'.count($checks)." runtime checks passed\n";
exit($failed?1:0);
