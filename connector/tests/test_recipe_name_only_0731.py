#!/usr/bin/env python3
from pathlib import Path
import json
import subprocess
import tempfile

ROOT = Path(__file__).resolve().parents[1]
FRAGMENT = ROOT / 'src' / 'parts' / '08g-recipe-name-only-revision.phpfrag'
fragment_text = FRAGMENT.read_text(encoding='utf-8')

# The extension must never introduce cleanup/destruction paths.
for forbidden in [
    'wp_trash_post', 'wp_delete_post', 'archiveRevisionPairs',
    'cleanupRevisionObjects', 'repairProtectedRecipeStatusSideEffects',
]:
    assert forbidden not in fragment_text, forbidden

harness = r'''<?php
error_reporting( E_ALL );
define( 'ABSPATH', __DIR__ . '/' );
define( 'ARRAY_A', 'ARRAY_A' );
define( 'OBJECT', 'OBJECT' );
define( 'DATE_ATOM', 'Y-m-d\\TH:i:sP' );
define( 'NKT_GPT_PAR_0723_VERSION', '0.7.31' );

class WP_Error {
    private $code; private $message; private $data;
    public function __construct( $code, $message, $data=array() ) { $this->code=$code; $this->message=$message; $this->data=$data; }
    public function get_error_code(){ return $this->code; }
    public function get_error_message(){ return $this->message; }
    public function get_error_data(){ return $this->data; }
}
class WP_REST_Request {
    private $method; private $route; private $params=array(); private $headers=array();
    public function __construct( $method='GET', $route='' ) { $this->method=$method; $this->route=$route; }
    public function get_method(){ return $this->method; }
    public function get_route(){ return $this->route; }
    public function set_param($k,$v){ $this->params[$k]=$v; }
    public function get_param($k){ return $this->params[$k] ?? null; }
    public function get_params(){ return $this->params; }
    public function set_header($k,$v){ $this->headers[$k]=$v; }
    public function get_headers(){ return $this->headers; }
}
class WP_REST_Response {
    private $data; private $status;
    public function __construct($data=array(),$status=200){ $this->data=$data; $this->status=$status; }
    public function get_data(){ return $this->data; }
    public function set_data($data){ $this->data=$data; }
    public function get_status(){ return $this->status; }
}
class WP_REST_Server { const READABLE='GET'; const CREATABLE='POST'; }
class FixtureWpdb {
    public $posts='posts';
    public function update($table,$row,$where){
        $id=(int)$where['ID'];
        if(!isset($GLOBALS['posts'][$id])) return false;
        foreach($row as $k=>$v){ $GLOBALS['posts'][$id][$k]=$v; }
        return 1;
    }
}
$GLOBALS['wpdb']=new FixtureWpdb();
$GLOBALS['posts']=array(); $GLOBALS['meta']=array(); $GLOBALS['terms']=array();
$GLOBALS['next_draft']=41000; $GLOBALS['next_clone']=42000;
$GLOBALS['original_calls']=array('start'=>0,'update'=>0,'audit'=>0,'review'=>0,'apply'=>0);
$GLOBALS['update_drift_key']=''; $GLOBALS['update_error_after_write']=false;
$GLOBALS['apply_drift']=false; $GLOBALS['apply_error_after_write']=false;
$GLOBALS['start_extra_pair']=false; $GLOBALS['start_error_after_write']=false; $GLOBALS['start_source_drift']=''; $GLOBALS['start_live_drift']='';

function add_action(){ return true; } function add_filter(){ return true; }
function register_rest_route(){ return true; }
function rest_get_server(){ return null; }
function __($s,$d=null){ return $s; }
function is_wp_error($v){ return $v instanceof WP_Error; }
function absint($v){ return abs((int)$v); }
function sanitize_key($v){ return strtolower(preg_replace('/[^a-z0-9_\-]/','',(string)$v)); }
function wp_strip_all_tags($v){ return strip_tags((string)$v); }
function rest_sanitize_boolean($v){ return filter_var($v,FILTER_VALIDATE_BOOLEAN); }
function maybe_unserialize($v){ return $v; }
function wp_json_encode($v,$flags=0){ return json_encode($v,$flags); }
function clean_post_cache(){ return true; }
function metadata_exists($type,$id,$key){ return array_key_exists($key,$GLOBALS['meta'][(int)$id] ?? array()); }
function get_post($id,$output=OBJECT,$filter='raw'){
    $id=(int)$id; if(!isset($GLOBALS['posts'][$id])) return null;
    $row=$GLOBALS['posts'][$id]; $row['ID']=$id;
    return ARRAY_A===$output ? $row : (object)$row;
}
function get_post_type($id){ $p=get_post($id); return $p?$p->post_type:false; }
function get_post_meta($id,$key='',$single=false){
    $all=$GLOBALS['meta'][(int)$id] ?? array();
    if($key===''){
        $out=array(); foreach($all as $k=>$v){ $out[$k]=is_array($v)&&array_key_exists(0,$v)?$v:array($v); }
        return $out;
    }
    if(!array_key_exists($key,$all)) return $single?'':array();
    $v=$all[$key];
    if($single) return is_array($v)&&array_key_exists(0,$v)?$v[0]:$v;
    return is_array($v)&&array_key_exists(0,$v)?$v:array($v);
}
function update_post_meta($id,$key,$value){ $GLOBALS['meta'][(int)$id][$key]=array($value); return true; }
function add_post_meta($id,$key,$value){ $GLOBALS['meta'][(int)$id][$key][]=$value; return true; }
function delete_post_meta($id,$key){ unset($GLOBALS['meta'][(int)$id][$key]); return true; }
function get_object_taxonomies($type,$mode='names'){ return array('wprm_course','wprm_cuisine'); }
function wp_get_object_terms($id,$tax,$args=array()){ return $GLOBALS['terms'][(int)$id][$tax] ?? array(); }
function wp_set_object_terms($id,$ids,$tax,$append=false){ $GLOBALS['terms'][(int)$id][$tax]=array_values(array_map('intval',(array)$ids)); return $GLOBALS['terms'][(int)$id][$tax]; }
function get_posts($args){
    $out=array(); $type=$args['post_type']??null; $mq=$args['meta_query']??array();
    foreach($GLOBALS['posts'] as $id=>$row){
        if($type && $row['post_type']!==$type) continue;
        $match=false;
        foreach($mq as $q){
            if(!is_array($q)||!isset($q['key'])) continue;
            $v=get_post_meta($id,$q['key'],true);
            if((string)$v===(string)($q['value']??'')){ $match=true; break; }
        }
        if($match) $out[]=(int)$id;
    }
    sort($out,SORT_NUMERIC); return $out;
}
function nkt_gpt_par_0723_canonicalize($value){
    if(is_array($value)){
        $keys=array_keys($value); $is_list=$keys===range(0,count($value)-1);
        if(!$is_list) ksort($value,SORT_STRING);
        foreach($value as $k=>$v) $value[$k]=nkt_gpt_par_0723_canonicalize($v);
    }
    return $value;
}
function nkt_gpt_par_0723_hash($value){ return hash('sha256',json_encode(nkt_gpt_par_0723_canonicalize($value),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE)); }
function nkt_gpt_par_0723_version_guard($request){ return $request->get_param('connector_version')==='0.7.31' ? true : new WP_Error('nkt_gpt_par_version_mismatch','version mismatch',array('status'=>409)); }
function nkt_gpt_par_0723_post_meta_snapshot($id){
    $all=get_post_meta($id); ksort($all,SORT_STRING); return $all;
}
function nkt_gpt_par_0723_restore_post_meta_snapshot($id,$snapshot){
    $GLOBALS['meta'][(int)$id]=array();
    foreach((array)$snapshot as $k=>$values) foreach((array)$values as $v) add_post_meta($id,$k,$v);
}
function recipe_payload($id,$ignore_status=false){
    $p=get_post($id); if(!$p) return array();
    $meta=array(); foreach(get_post_meta($id) as $k=>$values){
        if(in_array($k,array('_edit_lock','_edit_last'),true)) continue;
        if(strpos($k,'wprm_')!==0 && $k!=='_thumbnail_id') continue;
        $meta[$k]=$values;
    } ksort($meta);
    $r=array('id'=>(int)$id,'post_title'=>$p->post_title,'post_name'=>$p->post_name,'post_content'=>$p->post_content,'post_excerpt'=>$p->post_excerpt,'post_parent'=>(int)$p->post_parent,'menu_order'=>(int)$p->menu_order,'meta'=>$meta);
    if(!$ignore_status)$r['post_status']=$p->post_status; return $r;
}
function nkt_gpt_par_0723_recipe_object_hash($id,$status_override=null){
    $p=recipe_payload($id,false); if(!$p)return ''; if($status_override!==null)$p['post_status']=$status_override; return nkt_gpt_par_0723_hash($p);
}
function nkt_gpt_par_0723_recipe_component_manifest($id){
    $p=recipe_payload($id,false); if(!$p)return array(); $q=$p; unset($q['post_status']);
    return array('status_independent_hash'=>nkt_gpt_par_0723_hash($q),'object_hash'=>nkt_gpt_par_0723_hash($p));
}
function nkt_gpt_par_0723_wprm_nutrition_hash($id){ return nkt_gpt_par_0723_hash(get_post_meta($id,'wprm_nutrition',true)); }
function nkt_gpt_par_0723_recipe_snapshot($id){
    $p=get_post($id,ARRAY_A); if(!$p)return array(); return array('post'=>$p,'meta'=>nkt_gpt_par_0723_post_meta_snapshot($id),'object_hash'=>nkt_gpt_par_0723_recipe_object_hash($id),'nutrition_hash'=>nkt_gpt_par_0723_wprm_nutrition_hash($id));
}
function nkt_gpt_par_0723_restore_recipe_snapshot($id,$snapshot){
    if(empty($snapshot['post'])) return new WP_Error('bad_snapshot','bad');
    $GLOBALS['posts'][(int)$id]=$snapshot['post']; unset($GLOBALS['posts'][(int)$id]['ID']);
    nkt_gpt_par_0723_restore_post_meta_snapshot($id,$snapshot['meta']??array()); return true;
}
function recipe_ids_from_content($content){
    preg_match_all('/recipe-id="(\d+)"/',(string)$content,$m); return array_map('intval',$m[1]??array());
}
function article_state_common($id,$draft=false){
    $p=get_post($id); if(!$p)return new WP_Error('missing','missing');
    $ids=recipe_ids_from_content($p->post_content);
    $base=array(
        ($draft?'draft_post_id':'live_post_id')=>(int)$id,
        ($draft?'draft_status':'status')=>$p->post_status,
        'slug'=>$p->post_name,'publication_date'=>$p->post_date_gmt,
        'comment_status'=>$p->comment_status,'ping_status'=>$p->ping_status,
        'title_hash'=>hash('sha256',$p->post_title),'content_hash'=>hash('sha256',$p->post_content),'excerpt_hash'=>hash('sha256',$p->post_excerpt),
        'ordered_wprm_recipe_ids'=>$ids,'wprm_reference_count'=>count($ids),
        'nutrition_section_hash'=>hash('sha256','article-nutrition'),'media_reference_hash'=>hash('sha256','media-ref'),
        'media_alt_text_hash'=>hash('sha256','media-alt'),'media_non_alt_hash'=>hash('sha256','media-other'),
        'amazon_manifest_hash'=>hash('sha256','amazon'),'amazon_destinations'=>array('https://example.test/item'),
        'affiliate_identifiers'=>array('tag-21'),'reusable_block_ids'=>array(), 'reusable_block_hashes'=>array(),
        'unresolved_reusable_blocks'=>array(),'link_manifest'=>array(array('url'=>'https://example.test')),
    );
    return $base;
}
function nkt_gpt_par_0723_capture_live_state($id){ return article_state_common($id,false); }
function nkt_gpt_par_0723_capture_draft_state($id){ return article_state_common($id,true); }

function make_row($type,$status,$title,$content='',$excerpt=''){
    return array('post_type'=>$type,'post_status'=>$status,'post_title'=>$title,'post_name'=>strtolower(str_replace(' ','-',preg_replace('/[^A-Za-z0-9 ]/','',$title))),
        'post_content'=>$content,'post_excerpt'=>$excerpt,'post_parent'=>0,'menu_order'=>0,'comment_status'=>'closed','ping_status'=>'closed','post_password'=>'','post_mime_type'=>'',
        'post_author'=>1,'post_date'=>'2026-07-26 10:00:00','post_date_gmt'=>'2026-07-26 08:00:00','post_modified'=>'2026-07-26 10:00:00','post_modified_gmt'=>'2026-07-26 08:00:00',
        'to_ping'=>'','pinged'=>'','post_content_filtered'=>'','guid'=>'','comment_count'=>0);
}
function initialise_fixture(){
    $GLOBALS['posts']=array(); $GLOBALS['meta']=array(); $GLOBALS['terms']=array();
    $GLOBALS['next_draft']=41000; $GLOBALS['next_clone']=42000;
    $GLOBALS['update_drift_key']=''; $GLOBALS['update_error_after_write']=false; $GLOBALS['apply_drift']=false; $GLOBALS['apply_error_after_write']=false;
    $GLOBALS['start_extra_pair']=false; $GLOBALS['start_error_after_write']=false; $GLOBALS['start_source_drift']=''; $GLOBALS['start_live_drift']='';
$GLOBALS['start_extra_pair']=false; $GLOBALS['start_error_after_write']=false; $GLOBALS['start_source_drift']=''; $GLOBALS['start_live_drift']='';
    $GLOBALS['posts'][8657]=make_row('post','publish','Apricot Cinnamon Cake','Before <recipe recipe-id="38952"></recipe> After','Article excerpt');
    $GLOBALS['posts'][8664]=make_row('wprm_recipe','publish','Apricot Cinnamon Cake','recipe body');
    $GLOBALS['posts'][38952]=make_row('wprm_recipe','publish','Apricot Cinnamon Cake – Revision','recipe body');
    $GLOBALS['posts'][38951]=make_row('post','draft','Applied Revision: Apricot Cinnamon Cake','Before <recipe recipe-id="38952"></recipe> After','Article excerpt');
    $recipe_meta=array(
        'wprm_name'=>array('Apricot Cinnamon Cake – Revision'), 'wprm_summary'=>array('summary'),
        'wprm_ingredients'=>array(array(array('name'=>'apricots'))), 'wprm_instructions'=>array(array(array('text'=>'mix'))),
        'wprm_prep_time'=>array('20'), 'wprm_cook_time'=>array('40'), 'wprm_total_time'=>array('60'),
        'wprm_servings'=>array('8'), 'wprm_servings_unit'=>array('slices'), 'wprm_nutrition'=>array(array('calories'=>'250')),
        '_thumbnail_id'=>array(900), '_nkt_revision_source_recipe_id'=>array(8664),
    );
    $GLOBALS['meta'][38952]=$recipe_meta;
    $GLOBALS['meta'][8664]=$recipe_meta; $GLOBALS['meta'][8664]['wprm_name']=array('Apricot Cinnamon Cake'); unset($GLOBALS['meta'][8664]['_nkt_revision_source_recipe_id']);
    $GLOBALS['meta'][8657]=array('_nkt_live_protected'=>array('keep'));
    $GLOBALS['terms'][8657]=array('wprm_course'=>array(12));
    $GLOBALS['meta'][38951]=array('_nkt_revision_source_post_id'=>array(8657),'_nkt_revision_application_status'=>array('applied'));
    $GLOBALS['terms'][38952]=array('wprm_course'=>array(3),'wprm_cuisine'=>array(4));
    $GLOBALS['terms'][8664]=array('wprm_course'=>array(3),'wprm_cuisine'=>array(4));
}

function original_start($request){
    $GLOBALS['original_calls']['start']++;
    $live_id=(int)$request->get_param('live_post_id'); $live=get_post($live_id); $source_id=recipe_ids_from_content($live->post_content)[0];
    $draft_id=$GLOBALS['next_draft']++; $clone_id=$GLOBALS['next_clone']++;
    $source=get_post($source_id,ARRAY_A); $clone=$source; $clone['post_status']='draft'; $clone['post_title']=$source['post_title'].' – Revision';
    $GLOBALS['posts'][$clone_id]=$clone; $GLOBALS['meta'][$clone_id]=$GLOBALS['meta'][$source_id];
    $GLOBALS['meta'][$clone_id]['wprm_name']=array($clone['post_title']); $GLOBALS['meta'][$clone_id]['_nkt_revision_source_recipe_id']=array($source_id);
    $GLOBALS['terms'][$clone_id]=$GLOBALS['terms'][$source_id]??array();
    $draft=get_post($live_id,ARRAY_A); $draft['post_status']='draft'; $draft['post_title']='Revision: '.$draft['post_title'];
    $draft['post_content']=preg_replace('/(?<!\d)'.$source_id.'(?!\d)/',(string)$clone_id,$draft['post_content']);
    $GLOBALS['posts'][$draft_id]=$draft; $GLOBALS['meta'][$draft_id]=array('_nkt_revision_source_post_id'=>array($live_id),'_nkt_revision_source_recipe_id'=>array($source_id),'_nkt_revision_cloned_recipe_id'=>array($clone_id),'_nkt_revision_correction_scope'=>array('nutrition_section_only'));
    if($GLOBALS['start_source_drift']==='meta') $GLOBALS['meta'][$source_id]['_nkt_protected_marker']=array('changed');
    if($GLOBALS['start_source_drift']==='terms') $GLOBALS['terms'][$source_id]['wprm_course']=array(99);
    if($GLOBALS['start_live_drift']==='meta') $GLOBALS['meta'][$live_id]['_nkt_live_protected']=array('changed');
    if($GLOBALS['start_live_drift']==='terms') $GLOBALS['terms'][$live_id]['wprm_course']=array(99);
    if($GLOBALS['start_extra_pair']){
        $extra_clone=$GLOBALS['next_clone']++; $GLOBALS['posts'][$extra_clone]=$clone; $GLOBALS['meta'][$extra_clone]=$GLOBALS['meta'][$clone_id]; $GLOBALS['meta'][$extra_clone]['_nkt_revision_source_recipe_id']=array($source_id); $GLOBALS['terms'][$extra_clone]=$GLOBALS['terms'][$clone_id]??array();
        $extra_draft=$GLOBALS['next_draft']++; $GLOBALS['posts'][$extra_draft]=$draft; $GLOBALS['meta'][$extra_draft]=array('_nkt_revision_source_post_id'=>array($live_id),'_nkt_revision_source_recipe_id'=>array($source_id),'_nkt_revision_cloned_recipe_id'=>array($extra_clone),'_nkt_revision_correction_scope'=>array('nutrition_section_only'));
    }
    if($GLOBALS['start_error_after_write']) return new WP_Error('fixture_start_error','error after write');
    return new WP_REST_Response(array('draft_post_id'=>$draft_id,'cloned_recipe_id'=>$clone_id,'source_recipe_id'=>$source_id),201);
}
function original_update($request){
    $GLOBALS['original_calls']['update']++; $item=$request->get_param('items')[0]; $id=(int)$item['cloned_recipe_id']; $name=(string)$item['name'];
    $GLOBALS['posts'][$id]['post_title']=$name; $GLOBALS['meta'][$id]['wprm_name']=array($name);
    if($GLOBALS['update_drift_key']!=='') $GLOBALS['meta'][$id][$GLOBALS['update_drift_key']]=array('unexpected');
    if($GLOBALS['update_error_after_write']) return new WP_Error('fixture_update_error','error after write');
    return new WP_REST_Response(array('updated'=>true,'cloned_recipe_id'=>$id),200);
}
function original_audit($request){ $GLOBALS['original_calls']['audit']++; return new WP_REST_Response(array('audit_passed'=>true),200); }
function original_review($request){ $GLOBALS['original_calls']['review']++; update_post_meta((int)$request->get_param('draft_post_id'),'_nkt_revision_review_status',$request->get_param('decision')); return new WP_REST_Response(array('decision'=>$request->get_param('decision')),200); }
function original_apply($request){
    $GLOBALS['original_calls']['apply']++; $live_id=(int)$request->get_param('live_post_id'); $draft_id=(int)$request->get_param('draft_post_id');
    $draft=get_post($draft_id); $ids=recipe_ids_from_content($draft->post_content); $clone_id=$ids[0];
    $GLOBALS['posts'][$live_id]['post_content']=$draft->post_content; $GLOBALS['posts'][$clone_id]['post_status']='publish';
    if($GLOBALS['apply_drift']) $GLOBALS['meta'][$clone_id]['wprm_summary']=array('unexpected apply drift');
    if($GLOBALS['apply_error_after_write']) return new WP_Error('fixture_apply_error','error after write');
    return new WP_REST_Response(array('applied'=>true),200);
}

require $argv[1];
$GLOBALS['nkt_gpt_crr_0731_original_routes']=array(
    'start'=>array('callback'=>'original_start'),'update'=>array('callback'=>'original_update'),'audit'=>array('callback'=>'original_audit'),
    'review'=>array('callback'=>'original_review'),'apply'=>array('callback'=>'original_apply'),
);

$checks=array();
function t($name,$condition){ global $checks; $checks[$name]=(bool)$condition; if(!$condition) fwrite(STDERR,"FAIL: $name\n"); }
function req($method,$route,$params){ $r=new WP_REST_Request($method,$route); if(!array_key_exists('connector_version',$params)) $params['connector_version']='0.7.31'; foreach($params as $k=>$v)$r->set_param($k,$v); return $r; }
function start_params(){ return array('live_post_id'=>8657,'source_content'=>'current_live_post','source_recipe'=>'current_live_recipe','correction_scope'=>'recipe_name_only','preserve_existing_revisions'=>true,'allow_existing_revision_drafts'=>false,'skip_live_connector_clones'=>false,'allow_current_live_connector_clone_source'=>true); }
function complete_to_audit(){
    $s=nkt_gpt_crr_0731_start(req('POST','/nkt-gpt/v1/workflow/revisions/start',start_params())); $d=$s->get_data();
    $u=nkt_gpt_crr_0731_update(req('POST','/nkt-gpt/v1/workflow/revisions/recipes/update',array('items'=>array(array('draft_post_id'=>$d['draft_post_id'],'cloned_recipe_id'=>$d['cloned_recipe_id'],'name'=>'Apricot Cinnamon Cake')))));
    $a=nkt_gpt_crr_0731_audit(req('GET','/nkt-gpt/v1/workflow/revisions/recipes/audit',array('draft_post_id'=>$d['draft_post_id'],'cloned_recipe_id'=>$d['cloned_recipe_id'])));
    return array($d,$u,$a);
}

initialise_fixture();
$source_hash=nkt_gpt_par_0723_recipe_object_hash(38952); $historical_hash=nkt_gpt_par_0723_recipe_object_hash(8664); $prior_hash=hash('sha256',get_post(38951)->post_content);

// Unknown scope rejected; established nutrition scope delegates unchanged.
$r=nkt_gpt_crr_0731_start(req('POST','/x',array('correction_scope'=>'generic_recipe_edit')));
t('generic scope rejected', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_crr_0731_scope_invalid');
$before_calls=$GLOBALS['original_calls']['start'];
$r=nkt_gpt_crr_0731_start(req('POST','/x',array('correction_scope'=>'nutrition_section_only','live_post_id'=>8657)));
t('nutrition scope delegates unchanged', $r instanceof WP_REST_Response && $GLOBALS['original_calls']['start']===$before_calls+1);
// Reset because delegation fixture created one pair.
initialise_fixture(); $source_hash=nkt_gpt_par_0723_recipe_object_hash(38952); $historical_hash=nkt_gpt_par_0723_recipe_object_hash(8664); $prior_hash=hash('sha256',get_post(38951)->post_content);

$before_calls=$GLOBALS['original_calls']['start']; $wrong=start_params(); $wrong['connector_version']='0.7.30';
$r=nkt_gpt_crr_0731_start(req('POST','/x',$wrong));
t('wrong connector version rejected before start write',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_par_version_mismatch'&&$GLOBALS['original_calls']['start']===$before_calls&&!isset($GLOBALS['posts'][41000]));

$GLOBALS['start_extra_pair']=true;
$r=nkt_gpt_crr_0731_start(req('POST','/x',start_params()));
t('multiple fresh pairs rejected',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_fresh_pair_unproven');
t('every extra object retained as failure evidence',metadata_exists('post',41000,'_nkt_crr_0731_initialisation_failed')&&metadata_exists('post',41001,'_nkt_crr_0731_initialisation_failed')&&metadata_exists('post',42000,'_nkt_crr_0731_initialisation_failed')&&metadata_exists('post',42001,'_nkt_crr_0731_initialisation_failed'));
initialise_fixture();

$source_before=nkt_gpt_crr_0731_recipe_snapshot(38952); $live_before=nkt_gpt_crr_0731_post_snapshot(8657); $GLOBALS['start_error_after_write']=true; $GLOBALS['start_source_drift']='meta'; $GLOBALS['start_live_drift']='meta';
$r=nkt_gpt_crr_0731_start(req('POST','/x',start_params()));
t('delegated start error restored',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_start_failed_restored');
t('delegated start restores source and live exactly',nkt_gpt_crr_0731_snapshot_hash(nkt_gpt_crr_0731_recipe_snapshot(38952))===nkt_gpt_crr_0731_snapshot_hash($source_before)&&nkt_gpt_crr_0731_snapshot_hash(nkt_gpt_crr_0731_post_snapshot(8657))===nkt_gpt_crr_0731_snapshot_hash($live_before));
t('delegated start retains failed pair evidence',metadata_exists('post',41000,'_nkt_crr_0731_initialisation_failed')&&metadata_exists('post',42000,'_nkt_crr_0731_initialisation_failed'));
initialise_fixture();

$source_before=nkt_gpt_crr_0731_recipe_snapshot(38952); $GLOBALS['start_source_drift']='meta';
$r=nkt_gpt_crr_0731_start(req('POST','/x',start_params()));
t('source protected metadata drift fails creation verification',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_creation_verification_failed');
t('source protected metadata drift restored exactly',nkt_gpt_crr_0731_snapshot_hash(nkt_gpt_crr_0731_recipe_snapshot(38952))===nkt_gpt_crr_0731_snapshot_hash($source_before));
initialise_fixture();

$live_before=nkt_gpt_crr_0731_post_snapshot(8657); $GLOBALS['start_live_drift']='meta';
$r=nkt_gpt_crr_0731_start(req('POST','/x',start_params()));
t('live protected metadata drift fails creation verification',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_creation_verification_failed');
t('live protected metadata drift restored exactly',nkt_gpt_crr_0731_snapshot_hash(nkt_gpt_crr_0731_post_snapshot(8657))===nkt_gpt_crr_0731_snapshot_hash($live_before));
initialise_fixture(); $source_hash=nkt_gpt_par_0723_recipe_object_hash(38952); $historical_hash=nkt_gpt_par_0723_recipe_object_hash(8664); $prior_hash=hash('sha256',get_post(38951)->post_content);

$p=start_params(); $p['allow_current_live_connector_clone_source']=false;
$r=nkt_gpt_crr_0731_start(req('POST','/x',$p));
t('connector clone source requires explicit authorisation', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_crr_0731_start_guard_failed');
t('failed preflight creates nothing', !isset($GLOBALS['posts'][41000]) && !isset($GLOBALS['posts'][42000]));

$s=nkt_gpt_crr_0731_start(req('POST','/x',start_params()));
t('fresh start succeeds', $s instanceof WP_REST_Response);
$sd=$s->get_data(); $draft=$sd['draft_post_id']; $clone=$sd['cloned_recipe_id'];
t('one fresh draft and clone created', $draft===41000 && $clone===42000);
t('source recipe remains unchanged after creation', nkt_gpt_par_0723_recipe_object_hash(38952)===$source_hash);
t('historical recipe preserved', nkt_gpt_par_0723_recipe_object_hash(8664)===$historical_hash);
t('prior applied draft preserved', isset($GLOBALS['posts'][38951]) && hash('sha256',get_post(38951)->post_content)===$prior_hash);
t('fresh draft references clone once', recipe_ids_from_content(get_post($draft)->post_content)===array($clone));
$baseline=nkt_gpt_crr_0731_baseline($draft);
t('baseline records prior evidence', in_array(38951,$baseline['preserved_prior_draft_ids'],true) && $baseline['source_is_connector_clone']===true);

$base_item=array('draft_post_id'=>$draft,'cloned_recipe_id'=>$clone);
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'')))));
t('empty name rejected', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_crr_0731_name_empty');
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake – Revision')))));
t('unchanged name rejected', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_crr_0731_name_unchanged');
$update_calls=$GLOBALS['original_calls']['update'];
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake','summary'=>'bad')))));
t('name plus summary rejected before write', is_wp_error($r) && $GLOBALS['original_calls']['update']===$update_calls);
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake','ingredient_replacements'=>array(array('find'=>'a','replace'=>'b')))))));
t('name plus ingredient rejected before write', is_wp_error($r) && $GLOBALS['original_calls']['update']===$update_calls);

$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake')))));
t('name-only update succeeds', $r instanceof WP_REST_Response && nkt_gpt_crr_0731_recipe_name($clone)==='Apricot Cinnamon Cake');
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake')))));
t('repeated persisted clone name rejected',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_name_unchanged');
$good_snapshot=nkt_gpt_crr_0731_recipe_snapshot($clone);
$GLOBALS['update_drift_key']='_nkt_protected_marker';
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake Protected Drift')))));
t('hook-induced protected metadata mutation blocked',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_unexpected_clone_difference');
t('protected metadata drift exactly restored',nkt_gpt_crr_0731_snapshot_hash(nkt_gpt_crr_0731_recipe_snapshot($clone))===nkt_gpt_crr_0731_snapshot_hash($good_snapshot));
$GLOBALS['update_drift_key']='';
$GLOBALS['update_drift_key']='wprm_summary';
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array($base_item+array('name'=>'Apricot Cinnamon Cake Final')))));
t('hook-induced extra mutation blocked', is_wp_error($r) && $r->get_error_code()==='nkt_gpt_crr_0731_unexpected_clone_difference');
t('hook-induced drift exactly restored', nkt_gpt_par_0723_hash(nkt_gpt_crr_0731_recipe_snapshot($clone))===nkt_gpt_par_0723_hash($good_snapshot));
$GLOBALS['update_drift_key']='';

$a=nkt_gpt_crr_0731_audit(req('GET','/x',array('draft_post_id'=>$draft,'cloned_recipe_id'=>$clone)));
$ad=$a->get_data();
t('name-only audit passes', $ad['recipe_name_only_audit_passed']===true && count($ad['unexpected_difference_manifest'])===0);
t('authorised manifest reports name', $ad['authorised_difference_manifest'][0]['component']==='recipe_name');

// Every protected recipe category fails the strict audit.
$drifts=array(
    'ingredient'=>function()use($clone){update_post_meta($clone,'wprm_ingredients',array(array('name'=>'changed')));},
    'instruction'=>function()use($clone){update_post_meta($clone,'wprm_instructions',array(array('text'=>'changed')));},
    'time'=>function()use($clone){update_post_meta($clone,'wprm_prep_time','99');},
    'serving'=>function()use($clone){update_post_meta($clone,'wprm_servings','99');},
    'nutrition'=>function()use($clone){update_post_meta($clone,'wprm_nutrition',array('calories'=>'999'));},
    'image'=>function()use($clone){update_post_meta($clone,'_thumbnail_id',999);},
    'taxonomy'=>function()use($clone){wp_set_object_terms($clone,array(99),'wprm_course',false);},
    'metadata'=>function()use($clone){update_post_meta($clone,'custom_meta','changed');},
    'comment_status'=>function()use($clone){$GLOBALS['posts'][$clone]['comment_status']='open';},
);
foreach($drifts as $name=>$mutate){ $snap=nkt_gpt_crr_0731_recipe_snapshot($clone); $mutate(); $ev=nkt_gpt_crr_0731_evaluate(nkt_gpt_crr_0731_baseline($draft)); t('audit fails '.$name, $ev['passed']===false); nkt_gpt_crr_0731_restore_recipe_snapshot($clone,$snap); }
$draft_snap=nkt_gpt_crr_0731_post_snapshot($draft); $GLOBALS['posts'][$draft]['post_content'].='<p>unexpected</p>';
$ev=nkt_gpt_crr_0731_evaluate(nkt_gpt_crr_0731_baseline($draft)); t('article audit rejects non-ID drift',$ev['passed']===false); nkt_gpt_crr_0731_restore_post_snapshot($draft,$draft_snap);
$draft_snap=nkt_gpt_crr_0731_post_snapshot($draft); $GLOBALS['posts'][$draft]['post_content'].=' <recipe recipe-id="'.$clone.'"></recipe>';
$ev=nkt_gpt_crr_0731_evaluate(nkt_gpt_crr_0731_baseline($draft)); t('article audit requires exactly one recipe ID substitution',$ev['passed']===false&&!$ev['checks']['article_only_recipe_id_substitution']); nkt_gpt_crr_0731_restore_post_snapshot($draft,$draft_snap);
$prior_snap=nkt_gpt_crr_0731_post_snapshot(38951); $GLOBALS['posts'][38951]['post_excerpt']='mutated evidence';
$ev=nkt_gpt_crr_0731_evaluate(nkt_gpt_crr_0731_baseline($draft)); t('prior draft evidence mutation fails audit',$ev['passed']===false&&!$ev['checks']['prior_draft_evidence_unchanged']); nkt_gpt_crr_0731_restore_post_snapshot(38951,$prior_snap);
$prior_recipe_snap=nkt_gpt_crr_0731_recipe_snapshot(8664); $GLOBALS['meta'][8664]['_nkt_protected_marker']=array('mutated evidence');
$ev=nkt_gpt_crr_0731_evaluate(nkt_gpt_crr_0731_baseline($draft)); t('prior recipe evidence mutation fails audit',$ev['passed']===false&&!$ev['checks']['prior_clone_evidence_unchanged']); nkt_gpt_crr_0731_restore_recipe_snapshot(8664,$prior_recipe_snap);

$before_audit_meta=metadata_exists('post',$draft,NKT_GPT_CRR_0731_AUDIT_META); $before_calls=$GLOBALS['original_calls']['audit'];
$r=nkt_gpt_crr_0731_audit(req('GET','/x',array('connector_version'=>'0.7.30','draft_post_id'=>$draft,'cloned_recipe_id'=>$clone)));
t('wrong connector version audit writes nothing',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_par_version_mismatch'&&metadata_exists('post',$draft,NKT_GPT_CRR_0731_AUDIT_META)===$before_audit_meta&&$GLOBALS['original_calls']['audit']===$before_calls);
$before_lifecycle=nkt_gpt_crr_0731_lifecycle($draft); $before_calls=$GLOBALS['original_calls']['review'];
$r=nkt_gpt_crr_0731_review(req('POST','/x',array('connector_version'=>'0.7.30','draft_post_id'=>$draft,'decision'=>'approved')));
t('wrong connector version review writes nothing',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_par_version_mismatch'&&nkt_gpt_par_0723_hash(nkt_gpt_crr_0731_lifecycle($draft))===nkt_gpt_par_0723_hash($before_lifecycle)&&$GLOBALS['original_calls']['review']===$before_calls);

// Stale state blocks approval and apply.
$a=nkt_gpt_crr_0731_audit(req('GET','/x',array('draft_post_id'=>$draft,'cloned_recipe_id'=>$clone)));
$stale=nkt_gpt_crr_0731_recipe_snapshot($clone); $GLOBALS['posts'][$clone]['post_title']='Stale'; $GLOBALS['meta'][$clone]['wprm_name']=array('Stale');
$r=nkt_gpt_crr_0731_review(req('POST','/x',array('draft_post_id'=>$draft,'decision'=>'approved','note'=>'ok')));
t('stale audit blocks approval',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_audit_stale');
$r=nkt_gpt_crr_0731_apply(req('POST','/x',array('live_post_id'=>8657,'draft_post_id'=>$draft)));
t('stale audit blocks apply',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_apply_guard_failed');
nkt_gpt_crr_0731_restore_recipe_snapshot($clone,$stale);

$a=nkt_gpt_crr_0731_audit(req('GET','/x',array('draft_post_id'=>$draft,'cloned_recipe_id'=>$clone)));
$r=nkt_gpt_crr_0731_review(req('POST','/x',array('draft_post_id'=>$draft,'decision'=>'approved','note'=>'approved')));
t('fresh audit permits approval',$r instanceof WP_REST_Response);
$r=nkt_gpt_crr_0731_apply(req('POST','/x',array('live_post_id'=>8657,'draft_post_id'=>$draft)));
t('apply succeeds',$r instanceof WP_REST_Response);
$rd=$r->get_data();
t('apply changes live reference exactly once',recipe_ids_from_content(get_post(8657)->post_content)===array($clone));
t('public and schema names equal target',$rd['final_public_recipe_name']==='Apricot Cinnamon Cake'&&$rd['final_schema_recipe_name']==='Apricot Cinnamon Cake');
t('source retained unchanged',nkt_gpt_par_0723_recipe_object_hash(38952)===$source_hash);
t('applied draft retained',isset($GLOBALS['posts'][$draft]));
$r=nkt_gpt_crr_0731_apply(req('POST','/x',array('live_post_id'=>8657,'draft_post_id'=>$draft)));
t('second apply prevented',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_already_applied');

// A post-write verification failure restores live, draft, source and clone.
initialise_fixture(); $source_hash=nkt_gpt_par_0723_recipe_object_hash(38952);
list($d2,$u2,$a2)=complete_to_audit(); $draft2=$d2['draft_post_id']; $clone2=$d2['cloned_recipe_id'];
$r=nkt_gpt_crr_0731_review(req('POST','/x',array('draft_post_id'=>$draft2,'decision'=>'approved','note'=>'approved')));
$pre_live=get_post(8657)->post_content; $pre_clone=nkt_gpt_crr_0731_recipe_snapshot($clone2); $GLOBALS['apply_drift']=true;
$r=nkt_gpt_crr_0731_apply(req('POST','/x',array('live_post_id'=>8657,'draft_post_id'=>$draft2)));
t('post-write verification failure reported',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_apply_verification_failed');
t('failed apply restores live article',get_post(8657)->post_content===$pre_live&&recipe_ids_from_content(get_post(8657)->post_content)===array(38952));
t('failed apply restores clone',nkt_gpt_par_0723_hash(nkt_gpt_crr_0731_recipe_snapshot($clone2))===nkt_gpt_par_0723_hash($pre_clone));
t('failed apply keeps source unchanged',nkt_gpt_par_0723_recipe_object_hash(38952)===$source_hash);

// Delegated update failure after a write also restores every snapshot.
$GLOBALS['apply_drift']=false; $GLOBALS['update_error_after_write']=true; $before=nkt_gpt_crr_0731_recipe_snapshot($clone2);
$r=nkt_gpt_crr_0731_update(req('POST','/x',array('items'=>array(array('draft_post_id'=>$draft2,'cloned_recipe_id'=>$clone2,'name'=>'Another Name')))));
t('delegated update error restored',is_wp_error($r)&&$r->get_error_code()==='nkt_gpt_crr_0731_update_failed_restored');
t('delegated update exact restoration',nkt_gpt_par_0723_hash(nkt_gpt_crr_0731_recipe_snapshot($clone2))===nkt_gpt_par_0723_hash($before));

$failed=array_keys(array_filter($checks,fn($v)=>!$v));
echo json_encode(array('passed'=>count($checks)-count($failed),'total'=>count($checks),'failed'=>$failed,'writes_reported'=>0));
exit($failed?1:0);
'''

with tempfile.TemporaryDirectory() as directory:
    directory = Path(directory)
    harness_path = directory / 'harness.php'
    fragment_path = directory / 'fragment.php'
    harness_path.write_text(harness, encoding='utf-8')
    fragment_path.write_text('<?php\n' + fragment_text, encoding='utf-8')
    proc = subprocess.run(['php', str(harness_path), str(fragment_path)], capture_output=True, text=True)
    if proc.returncode:
        raise AssertionError(f'PHP fixture failed:\nSTDOUT:\n{proc.stdout}\nSTDERR:\n{proc.stderr}')

result = json.loads(proc.stdout)
assert result['failed'] == []
assert result['passed'] == result['total']
print(f"{result['passed']}/{result['total']} recipe-name-only lifecycle fixtures passed; live WordPress calls: 0")
