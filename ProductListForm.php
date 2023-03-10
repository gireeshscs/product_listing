<?php
/**
 * @file
 * Contains \Drupal\product_listing\Form\ProductListForm.
 */
namespace Drupal\product_listing\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\AlertCommand;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Ajax\AfterCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\AppendCommand;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\Query\Condition;

class ProductListForm extends FormBase {

/**
* {@inheritdoc}
*/
public function getFormId() {
    return 'plform';
}
  

/**
 * {@inheritdoc}
 */
public function buildForm(array $form, FormStateInterface $form_state,  $sort=NULL, $colname=NULL) {

  $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
  $user_name = $account->get('name')->value; 

  //$user_name_arr=explode("-",$user_name);

  $first_letter=substr($user_name, 0, 2);
  $last_letter=substr($user_name, -1);

  //     $path = drupal_get_path('module', 'DFF_importer');
  //	if (user_access('access by administrator')){
  
  //	$form['#attributes'] = array('name' => 'ftp_con_form',
  //	'onsubmit' => 'return valid()',
  //	);
  
  //$sort = \Drupal::request()->query->get('sort'); 
  
  $path = \Drupal::request()->getpathInfo();
  $arg  = explode('/',$path);
  //$colname = end($arg);
  //$sort = prev($arg);

  $colname = $arg[5];
$sort = $arg[4];
$sk1 = $arg[6];


if($sk1!='null' && is_numeric($sk1)){
  //$page=0;
  //$field_page = '<input type="number" id="man_page_num" size="5px" value='.$page.'>';
  $sk1_val=$sk1;
}else if($sk1!='null' && !is_numeric($sk1)){
  //$field_page = '<input type="number" id="man_page_num" size="5px">';
  $sk1_val=$sk1;
}else{
  //$field_page = '<input type="number" id="man_page_num" size="5px" value='.$page.'>';
  $sk1_val='null';
}




  $from_which_page=\Drupal::request()->query->get('page');

  $operated_rows = \Drupal::request()->query->get('rows');
$operation_status = \Drupal::request()->query->get('status');
$operation_name = \Drupal::request()->query->get('op');

  $base_path=base_path();

  
  $inactive_op_lnk='';
  //$query = \Drupal::request()->query->get('token');
  
  //$man_ids=explode(',',$query);
  
  $form_state->setCached(FALSE);

  $field_name1='';
  $field_name2='';
  $field_name3='';
  $active_lnk='';
  $inactive_lnk='';
  $inactive_op_lnk='';
  $active_op_lnk='';
  $inactive_date_lnk='';
  $active_date_lnk='';
  $sort_asc='';
  $lastPage='';

  
  if($from_which_page=='vendor' && $sk1!="null"){


  $list_path = $base_path.'/admin/product/productlist/asc/name/'.$sk1_val.'?page=vendor';
  $rank_path=$base_path.'/admin/product/productrank/asc/name/'.$sk1_val;

  }else if($from_which_page=='dist' && $sk1!="null"){
    $list_path = $base_path.'/admin/product/productlist/asc/name/'.$sk1_val.'?page=dist';
    $rank_path=$base_path.'/admin/product/productrank/asc/name/'.$sk1_val;
  }else if($from_which_page=='prank' && $sk1!="null"){
    $list_path = $base_path.'/admin/product/productlist/asc/name/'.$sk1_val.'?page=prank';
    $rank_path=$base_path.'/admin/product/productrank/asc/name/'.$sk1_val;
  }else{
    $list_path = $base_path.'/admin/product/productlist/asc/name/'.$sk1_val;
    $rank_path=$base_path.'/admin/product/productrank/asc/name/'.$sk1_val;

  }


  //$host = \Drupal::request()->getHost()."/alphasite/admin/ranking/manufacture/";
  
  
  if($sort=='asc' && $colname=='name'){
    $field_name1='product_name';
    $field_name2='product_name';
    $field_name3='product_name';
    $active_lnk='sortnameasc';
    $inactive_lnk='';
  }
  if($sort=='desc' && $colname=='name'){
    $field_name1='product_name';
    $field_name2='product_name';
    $field_name3='product_name';
    $inactive_lnk='sortnameasc';
    $active_lnk='';
  }
  
  
  if($sort=='asc' && $colname=='date'){
    $field_name1='product_update';
    $field_name2='product_update';
    $field_name3='product_update';
    $active_date_lnk='sortdateasc';
    $inactive_lnk='';
  }
  if($sort=='desc' && $colname=='date'){
    $field_name1='product_update';
    $field_name2='product_update';
    $field_name3='product_update';
    $inactive_date_lnk='sortdateasc';
    $active_lnk='';
  }
  
  if($sort=='asc' && $colname=='operator'){
    $field_name1='product_operator_id';
    $field_name2='product_operator_id';
    $field_name3='product_operator_id';
    $active_op_lnk='sortopasc';
  //  $inactive_lnk='';
  }
  if($sort=='desc' && $colname=='operator'){
    $field_name1='product_operator_id';
    $field_name2='product_operator_id';
    $field_name3='product_operator_id';
    $inactive_op_lnk='sortopasc';
  //  $active_lnk='';
  }
  
  
  
  $form['#attributes'] = array('name' => 'plform','id' =>'plform',
           'autocomplete' => 'off', 
    'onsubmit' => 'return window.location.reload();',
    );
  
    $form['#prefix'] = '<div class="custom_error">Extension for DFF Name and DFF Remote File Name should match!</div><div id="my-form-wrapper" align="center" style="padding-left:100px !important;">';
    $form['#suffix'] = '</div>';
    
    $form['start'] = [
        '#type' => 'markup',
        '#markup' => '<div class="row" align="center">',
        '#weight' => '0',
     ];


     if($first_letter=='O-' || $first_letter=='A-'){ 

     $form['keyword'] = [
      '#type' => 'markup',
      '#markup' => '<a href="'.$base_path.'/admin/product/productkeyword/asc/name/null" class="btn btn-secondary btn-sm active" id="other_btn" role="button" aria-pressed="true">Product Keyword Listing</a>&nbsp;',
      '#weight' => '-4',
   ];

    
      $form['ftpcon'] = [
        '#type' => 'markup',
        '#markup' => '<a href="'.$base_path.'/admin/product/category/asc/name" class="btn btn-secondary btn-sm active" id="other_btn" role="button" aria-pressed="true">Product Category Listing</a>&nbsp;',
        '#weight' => '-4',
     ];


     }else{

      $form['keyword'] = [
        '#type' => 'markup',
        '#markup' => '<a href="#" class="btn btn-secondary btn-sm active" id="other_btn1" role="button" aria-pressed="true">Product Keyword Listing</a>&nbsp;',
        '#weight' => '-4',
     ];
  
      
        $form['ftpcon'] = [
          '#type' => 'markup',
          '#markup' => '<a href="#" class="btn btn-secondary btn-sm active" id="other_btn1" role="button" aria-pressed="true">Product Category Listing</a>&nbsp;',
          '#weight' => '-4',
       ];
  


     }
     
     if($first_letter=='O-' || $first_letter=='A-'){   
  
     $form['combine_csv_link'] = [
        '#type' => 'markup',
        '#markup' => '<a href="'.$base_path.'/admin/product/productrank/asc/name/null" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="other_btn">Product Rank Listing</a>&nbsp;',
        '#weight' => '-3',
     ];

    }else{
      $form['combine_csv_link'] = [
        '#type' => 'markup',
        '#markup' => '<a href="#" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="other_btn1">Product Rank Listing</a>&nbsp;',
        '#weight' => '-3',
     ];

    }
   
     $form['csvindb'] = [
        '#type' => 'markup',
        '#markup' => '<a href="'.$base_path.'/admin/product/productlist/asc/name/null" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="collection_btn">Product Sale Listing</a>',
        '#weight' => '-2',
     ];
   
      $form['end'] = [
        '#type' => 'markup',
        '#markup' => '</div>',
        '#weight' => '-1',
     ];
     
  /*
   $form['plist']['tbl_start'] = array(
      '#type' => 'markup',
      '#markup' => t('<div id = "form_content" style="padding-top:10px;"><table width = "100%" border = "0" id = "pl_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Sale Listing</h3></td></tr>
  <tr id="header_row"><th id = "plf_child">Select<br><input type="checkbox" id="sel" name="main_sel" onclick="checkAllPl(document.plform.total_values.value)"></th><th id = "s_child">Name <span id="'.$active_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/asc/name" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/desc/name"  id="'.$inactive_lnk.'">v</a></span><br/> Image<br>Price<br/>Vendor Name</th><th id = "t_child">Updated <span id="'.$active_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/asc/date" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/desc/date"  id="'.$inactive_date_lnk.'">v</a></span></br>Description</br>Shipping</br>SKU</th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/asc/operator" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productlist/desc/operator"  id="'.$inactive_op_lnk.'">v</a></span><br>UPC</br>Product Points/Flags</br></div></th></tr>'),
    ); 
*/
$pageno = \Drupal::request()->query->get('pageno');

$pmoderesult = \Drupal::database()->select('process_control', 'pc')
->fields('pc', array('process_control_id','process_read_max','process_page_max','process_page_row_max'))
->condition('process_name','prodsale', '=')
->execute()->fetchObject();

$process_read_max = $pmoderesult->process_read_max; 
$process_page_row_max = $pmoderesult->process_page_row_max; 
$process_page_max = $pmoderesult->process_page_max; 

$total_collections=$process_page_max*$process_page_row_max;

$perPage = $process_page_row_max;
$page = (isset($pageno)) ? (int)$pageno : 1;
$startAt = $perPage * ($page - 1);


  
$keys = array();
$newArray = array();



    // $table_path = \Drupal::service('file_system')->realpath("private://lists");
    $filename="lists/Categories.txt";
 
    if (($handle = fopen($filename, 'r')) !== FALSE) {
      $i = 0;
      while (($lineArray = fgetcsv($handle, 4000, "|", '"')) !== FALSE) {
        for ($j = 0; $j < count($lineArray); $j++) {
          $arr[$i][$j] = $lineArray[$j];
        }
        $i++;
      }
      fclose($handle);
    }
   
  
  $tmp = array();
  $category=array();
  foreach($arr as $arg)
  {
      $tmp[$arg[0]][] = $arg[1];
      
  }
  
  $output = array();
  $sub_category=array();
  
  foreach($tmp as $type => $labels)
  {
      $output[] = array(
          'category' => $type,
          'sub_category' => $labels
      );
      if($type!='All Departments' && $type!='REJECT' && $type!='No Override' && $type!='' && $type!='Vendor List'){
      $category[$type]=$type;
      $sub_category[$type]=$labels;
      }
  }



/*

':manufacturer_rank_id' => $manufacturer_rank_id,
':distributor_rank_id' => $distributor_rank_id,
':vendor_rank_id' => $vendor_rank_id,
':category_id' => $category_id,
':product_rank_id' => $product_rank_id,
':product_vend_name' =>$product_vend_name,
':product_name' => trim($productName[$pl]),
':product_sku' => trim($sku_number[$pl]),
':product_upc' => trim($upc[$pl]),
':keywords' => trim($keywords[$pl]),
':description' => trim($description[$pl]),
':currency' => trim($currency[$pl]),
':price' => trim($price_retail[$pl]),
':buy_url' => trim($product_url[$pl]),
':image_url' => trim($productImage[$pl]),
':in_stock' => trim($shipping_availability[$pl]),
':standard_shipping_cost' => trim($shipping_cost[$pl]),
':product_points' => 0,
':product_rank' => 0,
':product_sponsored' => '',
':product_spon_page' => 0,
':product_update' => '',
':product_operator_id' => $user_name,
*/





if($sk1!="null"){

  if($from_which_page=='dist'){

    $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);

$prev=$page-1;

if($prev==0){
$prev=1;
}

    $total_results = \Drupal::database()->select('product_sale', 'pl')
->fields('pl', array('product_sale_id'))
->condition('product_dist_over_rank_id',$sk1_val, '=')
->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort)
->execute()->fetchAll();

$tot_num_results = count($total_results);


$total_pages = ceil($tot_num_results / $no_of_records_per_page);

$lastPage = ceil($tot_num_results / $no_of_records_per_page);
if($page==$lastPage){
  $nxt=$page;
}

      if($tot_num_results>$total_collections){
        $tot_num_results=$total_collections;
      }
      if(is_array($total_results)){
        $tot_cnt =   $tot_num_results;
       
        }else{
        $tot_cnt=0;
        
        }


        if($first_letter=='M-'){

  $results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
  ->condition('product_mfr_rank_id',$last_letter, '=')
  ->orderBy($field_name1,$sort)
  ->orderBy($field_name2,$sort)
  ->orderBy($field_name3,$sort)
  ->range($offset, $no_of_records_per_page) 
  ->execute()->fetchAll();

        }else{
          $results = \Drupal::database()->select('product_sale', 'pl')
          ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
          ->condition('product_dist_over_rank_id',$sk1_val, '=')
          ->orderBy($field_name1,$sort)
          ->orderBy($field_name2,$sort)
          ->orderBy($field_name3,$sort)
          ->range($offset, $no_of_records_per_page) 
          ->execute()->fetchAll();


        }
  if($pageno>1){
    $sort_asc='?page=dist&pageno='.$pageno;
  }else{

    $sort_asc='?page=dist';
 // $sort_asc='?token='.$query;
  
  }





  }


  if($from_which_page=='man'){

    $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);

$prev=$page-1;

if($prev==0){
$prev=1;
}

    $total_results = \Drupal::database()->select('product_sale', 'pl')
->fields('pl', array('product_sale_id'))
->condition('product_mfr_rank_id',$sk1_val, '=')
->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort)
->execute()->fetchAll();

$tot_num_results = count($total_results);


$total_pages = ceil($tot_num_results / $no_of_records_per_page);

$lastPage = ceil($tot_num_results / $no_of_records_per_page);
if($page==$lastPage){
  $nxt=$page;
}

      if($tot_num_results>$total_collections){
        $tot_num_results=$total_collections;
      }
      if(is_array($total_results)){
        $tot_cnt =   $tot_num_results;
       
        }else{
        $tot_cnt=0;
        
        }


        if($first_letter=='M-'){

  $results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
  ->condition('product_mfr_rank_id',$last_letter, '=')
  ->orderBy($field_name1,$sort)
  ->orderBy($field_name2,$sort)
  ->orderBy($field_name3,$sort)
  ->range($offset, $no_of_records_per_page) 
  ->execute()->fetchAll();

        }else{
          $results = \Drupal::database()->select('product_sale', 'pl')
          ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
          ->condition('product_mfr_rank_id',$sk1_val, '=')
          ->orderBy($field_name1,$sort)
          ->orderBy($field_name2,$sort)
          ->orderBy($field_name3,$sort)
          ->range($offset, $no_of_records_per_page) 
          ->execute()->fetchAll();


        }
  if($pageno>1){
    $sort_asc='?page=man&pageno='.$pageno;
  }else{

    $sort_asc='?page=man';
 // $sort_asc='?token='.$query;
  
  }





  }

  if($from_which_page=='vendor'){

    $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);

$prev=$page-1;

if($prev==0){
$prev=1;
}

    $total_results = \Drupal::database()->select('product_sale', 'pl')
->fields('pl', array('product_sale_id'))
->condition('product_vend_rank_id',$sk1_val, '=')
    ->orderBy($field_name1,$sort)
    ->orderBy($field_name2,$sort)
    ->orderBy($field_name3,$sort)
->execute()->fetchAll();

$tot_num_results = count($total_results);


$total_pages = ceil($tot_num_results / $no_of_records_per_page);

$lastPage = ceil($tot_num_results / $no_of_records_per_page);
if($page==$lastPage){
  $nxt=$page;
}


      if($tot_num_results>$total_collections){
        $tot_num_results=$total_collections;
      }
      if(is_array($total_results)){
        $tot_cnt =   $tot_num_results;
       
        }else{
        $tot_cnt=0;
        
        }

    $results = \Drupal::database()->select('product_sale', 'pl')
    ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
    ->condition('product_vend_rank_id',$sk1_val, '=')
    ->orderBy($field_name1,$sort)
    ->orderBy($field_name2,$sort)
    ->orderBy($field_name3,$sort)
    ->range($startAt, $no_of_records_per_page)   
    ->execute()->fetchAll();


    if($pageno>1){
      $sort_asc='?page=vendor&pageno='.$pageno;
    }else{
  
      $sort_asc='?page=vendor';
   // $sort_asc='?token='.$query;
    
    }


    }

    if($from_which_page=='cat'){

      $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);
//$offset = 0;

$prev=$page-1;

if($prev==0){
$prev=1;
}

      $total_results = \Drupal::database()->select('product_sale', 'pl')
->fields('pl', array('product_sale_id'))
->condition('product_cat_id',$sk1_val, '=')
->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort)
->execute()->fetchAll();

$tot_num_results = count($total_results);


$total_pages = ceil($tot_num_results / $no_of_records_per_page);

$lastPage = ceil($tot_num_results / $no_of_records_per_page);
if($page==$lastPage){
  $nxt=$page;
}


      if($tot_num_results>$total_collections){
        $tot_num_results=$total_collections;
      }
      if(is_array($total_results)){
        $tot_cnt =   $tot_num_results;
       
        }else{
        $tot_cnt=0;
        
        }


      $results = \Drupal::database()->select('product_sale', 'pl')
      ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
      ->condition('product_cat_id',$sk1_val, '=')
      ->orderBy($field_name1,$sort)
      ->orderBy($field_name2,$sort)
      ->orderBy($field_name3,$sort)
      ->range($offset, $no_of_records_per_page)   
      ->execute()->fetchAll();

      if($pageno>1){
        $sort_asc='?page=cat&pageno='.$pageno;
      }else{
    
        $sort_asc='?page=cat';
     // $sort_asc='?token='.$query;
      
      }

      } 
      
      if($from_which_page=='prank'){


        $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);

$prev=$page-1;

if($prev==0){
$prev=1;
}
if($first_letter=='M-'){

  $total_results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id'))
  //->condition('product_prod_rank_id',$man_ids, 'IN')
  ->condition('product_mfr_rank_id',$last_letter, '=')
  ->orderBy($field_name1,$sort)
  ->orderBy($field_name2,$sort)
  ->orderBy($field_name3,$sort)
  ->execute()->fetchAll();


}else{
        $total_results = \Drupal::database()->select('product_sale', 'pl')
        ->fields('pl', array('product_sale_id'))
        ->condition('product_prod_rank_id',$sk1_val, '=')
        ->orderBy($field_name1,$sort)
        ->orderBy($field_name2,$sort)
        ->orderBy($field_name3,$sort)
        ->execute()->fetchAll();

}
      
        $tot_num_results = count($total_results);
      
      
        $total_pages = ceil($tot_num_results / $no_of_records_per_page);
        $lastPage = ceil($tot_num_results / $no_of_records_per_page);
        if($page==$lastPage){
          $nxt=$page;
        }
      
              if($tot_num_results>$total_collections){
                $tot_num_results=$total_collections;
              }
              if(is_array($total_results)){
                $tot_cnt =   $tot_num_results;
               
                }else{
                $tot_cnt=0;
                
                }

                if($first_letter=='M-'){

                  $results = \Drupal::database()->select('product_sale', 'pl')
                  ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
                  ->condition('product_mfr_rank_id',$last_letter, '=')
                  ->orderBy($field_name1,$sort)
                  ->orderBy($field_name2,$sort)
                  ->orderBy($field_name3,$sort)
                  ->range($offset, $no_of_records_per_page)   
                  ->execute()->fetchAll();
                  
                  
                }else{
        $results = \Drupal::database()->select('product_sale', 'pl')
        ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
        ->condition('product_prod_rank_id',$sk1_val, '=')
        ->orderBy($field_name1,$sort)
        ->orderBy($field_name2,$sort)
        ->orderBy($field_name3,$sort)
        ->range($offset, $no_of_records_per_page)   
        ->execute()->fetchAll();
                }


        if($pageno>1){
          $sort_asc='?page=prank&pageno='.$pageno;
        }else{
      
          $sort_asc='?page=prank';
       // $sort_asc='?token='.$query;
        
        }
  
        } 
        
        if($from_which_page==''){


          $nxt=$page+1;
  
  
  
  if (isset($pageno)!='') {
  $pageno = $pageno;
  } else {
  $pageno = 1;
  }
  $no_of_records_per_page = $process_page_row_max;
  $offset = $no_of_records_per_page*($pageno-1);
  
  $prev=$page-1;
  
  if($prev==0){
  $prev=1;
  }
  if($first_letter=='M-'){

    $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
  
    $total_results = \Drupal::database()->select('product_sale', 'pl');
    $total_results->fields('pl', array('product_sale_id'));
    //->condition('product_prod_rank_id',$man_ids, 'IN')
    $total_results->condition('product_mfr_rank_id',$last_letter, '=');
    $orConditions = $total_results->orConditionGroup()
    ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
    ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
    $total_results->condition($orConditions);
    $total_results->orderBy($field_name1,$sort);
    $total_results->orderBy($field_name2,$sort);
    $total_results->orderBy($field_name3,$sort);
    $total_results=$total_results->execute()->fetchAll();
  
  
  }else{

    if(is_numeric($sk1) && !strstr($sk1, '(')){
    $total_results = \Drupal::database()->select('product_sale', 'pl')
    ->fields('pl', array('product_sale_id'))
    ->orderBy($field_name1,$sort)
    ->orderBy($field_name2,$sort)
    ->orderBy($field_name3,$sort)
    ->range($sk1, 1)
    ->execute()->fetchAll();


  }else if(!is_numeric($sk1) && (strstr($sk1, '(') || strstr($sk1, ')'))){
    $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
    $total_results = \Drupal::database()->select('product_sale', 'pl');
    $total_results->fields('pl', array('product_sale_id'));
    $orConditions = $total_results->orConditionGroup()
    ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
    ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
    $total_results->condition($orConditions);
    $total_results->orderBy($field_name1,$sort);
    $total_results->orderBy($field_name2,$sort);
    $total_results->orderBy($field_name3,$sort);
    $total_results=$total_results->execute()->fetchAll();
    
    
    
  }else{


    $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
          $total_results = \Drupal::database()->select('product_sale', 'pl');
          $total_results->fields('pl', array('product_sale_id'));
          $orConditions = $total_results->orConditionGroup()
          ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
          ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
          $total_results->condition($orConditions);
          $total_results->orderBy($field_name1,$sort);
          $total_results->orderBy($field_name2,$sort);
          $total_results->orderBy($field_name3,$sort);
          $total_results=$total_results->execute()->fetchAll();

  }
  
  }
        
          $tot_num_results = count($total_results);
        
        
          $total_pages = ceil($tot_num_results / $no_of_records_per_page);
        
          $lastPage = ceil($tot_num_results / $no_of_records_per_page);
          if($page==$lastPage){
            $nxt=$page;
          }
          
                if($tot_num_results>$total_collections){
                  $tot_num_results=$total_collections;
                }
                if(is_array($total_results)){
                  $tot_cnt =   $tot_num_results;
                 
                  }else{
                  $tot_cnt=0;
                  
                  }
  
                  if($first_letter=='M-'){
                    $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
                    $results = \Drupal::database()->select('product_sale', 'pl');
                    $results->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'));
                    $results->condition('product_mfr_rank_id',$last_letter, '=');
                    $orConditions = $results->orConditionGroup()
                    ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
                    ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
                    $results->condition($orConditions);

                    $results->orderBy($field_name1,$sort);
                    $results->orderBy($field_name2,$sort);
                    $results->orderBy($field_name3,$sort);
                    $results->range($offset, $no_of_records_per_page);   
                    $results=$results->execute()->fetchAll();
                    
                    
                  }else{


                    if(is_numeric($sk1) && !strstr($sk1, '(')){

                      //$sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
                      $results = \Drupal::database()->select('product_sale', 'pl')
                      ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
                      ->orderBy($field_name1,$sort)
                      ->orderBy($field_name2,$sort)
                      ->orderBy($field_name3,$sort)
                      ->range($sk1, 1)   
                      ->execute()->fetchAll();


                    }else if(!is_numeric($sk1) && (strstr($sk1, '(') || strstr($sk1, ')'))){

                      $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
                      $results = \Drupal::database()->select('product_sale', 'pl');
                      $results->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'));
                      //->condition('product_prod_rank_id',$sk1_val, '=')
                      $orConditions = $results->orConditionGroup()
                      ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
                      ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
                      $results->condition($orConditions);
                      $results->orderBy($field_name1,$sort);
                      $results->orderBy($field_name2,$sort);
                      $results->orderBy($field_name3,$sort);
                      $results->range($offset, $no_of_records_per_page);   
                      $results=$results->execute()->fetchAll();
                    }else{


                    $sk1_arr=str_replace(array( '(', ')' ), '', $sk1);
          $results = \Drupal::database()->select('product_sale', 'pl');
          $results->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'));
          //->condition('product_prod_rank_id',$sk1_val, '=')
          $orConditions = $results->orConditionGroup()
          ->condition('product_name', '%' . Database::getConnection()->escapeLike(urldecode($sk1)) . '%', 'LIKE')
          ->condition('product_sale_id', '%' . Database::getConnection()->escapeLike(urldecode($sk1_arr)) . '%', 'LIKE');
          $results->condition($orConditions);
          $results->orderBy($field_name1,$sort);
          $results->orderBy($field_name2,$sort);
          $results->orderBy($field_name3,$sort);
          $results->range($offset, $no_of_records_per_page);   
          $results=$results->execute()->fetchAll();
                  
          }
        
        
        }
  
  
          if($pageno>1){
            $sort_asc='';
          }else{
        
            $sort_asc='';
         // $sort_asc='?token='.$query;
          
          }
    
          } 



}else{


  $nxt=$page+1;



if (isset($pageno)!='') {
$pageno = $pageno;
} else {
$pageno = 1;
}
$no_of_records_per_page = $process_page_row_max;
$offset = $no_of_records_per_page*($pageno-1);

$prev=$page-1;

if($prev==0){
$prev=1;
}

if($first_letter=='M-'){

  $total_results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id'))
  ->condition('product_mfr_rank_id',$last_letter, '=')
  ->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort) 
  ->execute()->fetchAll();


}else{

  $total_results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id'))
  ->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort) 
  ->execute()->fetchAll();

}

  $tot_num_results = count($total_results);

  $lastPage = ceil($tot_num_results / $no_of_records_per_page);


if($page==$lastPage){
  $nxt=$page;
}



  $total_pages = ceil($tot_num_results / $no_of_records_per_page);


        if($tot_num_results>$total_collections){
          $tot_num_results=$total_collections;
        }
        if(is_array($total_results)){
          $tot_cnt =   $tot_num_results;
         
          }else{
          $tot_cnt=0;
          
          }

 
          if($first_letter=='M-'){

            $results = \Drupal::database()->select('product_sale', 'pl')
            ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
            ->condition('product_mfr_rank_id',$last_letter, '=')
          //->orderBy('category_vendor_name','category_vendor_cat','category_vendor_sub', 'ASC')
          ->orderBy($field_name1,$sort)
          ->orderBy($field_name2,$sort)
          ->orderBy($field_name3,$sort)  
          ->range($offset, $no_of_records_per_page)             
          ->execute()->fetchAll();




          }else{
  $results = \Drupal::database()->select('product_sale', 'pl')
  ->fields('pl', array('product_sale_id','product_mfr_rank_id', 'product_dist_over_rank_id', 'product_vend_rank_id', 'product_cat_id','product_prod_rank_id','product_vend_name','product_cat','product_sub','product_name','product_sku','product_upc','product_keywords','product_description','product_currency','product_price','product_buy_url','product_image_url','product_in_stock','product_standard_shipping_cost','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires','product_update','product_operator_id'))
//->orderBy('category_vendor_name','category_vendor_cat','category_vendor_sub', 'ASC')
->orderBy($field_name1,$sort)
->orderBy($field_name2,$sort)
->orderBy($field_name3,$sort)  
->range($offset, $no_of_records_per_page)             
->execute()->fetchAll();

          }


if($pageno>1){
  $sort_asc='?pageno='.$pageno;
}

 
}
$num_results = count($results);




$i = 0;

if($pageno==1){
  $col_num=1;
}else{
  $col_num=$pageno*$no_of_records_per_page-$no_of_records_per_page+1;
}

if($from_which_page=='cat'){
  $parent_page = 'cat';
$left_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=cat&pageno='.$prev;
$right_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=cat&pageno='.$nxt;

}else if($from_which_page=='dist'){
  $parent_page = 'dist';
  $left_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=dist&pageno='.$prev;
  $right_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=dist&pageno='.$nxt;

}else if($from_which_page=='vendor'){
  $parent_page = 'vendor';
    $left_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=vendor&pageno='.$prev;
    $right_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=vendor&pageno='.$nxt;
}else if($from_which_page=='prank'){
  $parent_page = 'prank';
      $left_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=prank&pageno='.$prev;
      $right_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?page=prank&pageno='.$nxt;
}else{
  $parent_page = 'sale';
  $left_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?pageno='.$prev;
  $right_link = $base_path.'/admin/product/productlist/'.$sort.'/'.$colname.'/'.$sk1_val.'?pageno='.$nxt;
}


if($sk1!='null' && is_numeric($sk1)){
  //$page=0;
  $field_page = '<input type="number" id="man_page_num" size="5px" value='.$page.'>';
  //$sk1_val=$sk1;
}else if($sk1!='null' && !is_numeric($sk1)){
  $field_page = '<input type="number" id="man_page_num" size="5px">';
  //$sk1_val='null';
}else{
  $field_page = '<input type="number" id="man_page_num" size="5px" value='.$page.'>';
  //$sk1_val='null';
}

  /*  $form['plist']['tbl_start'] = array(
      '#type' => 'markup',
      '#markup' => t('<div id="productlist_form_content" style="padding-top:10px;"><table  border = "0" id = "pl_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Sale Listing</h3></td></tr>
  <tr id="header_row"><th id = "plf_child"><span>&nbsp;&nbsp;&nbsp;&nbsp;Page</span><div id=""><a href="'.$left_link.'" class="sale_man_page_num_left"><</a><input type="hidden" id="admin_page" size="5px" value="productlist"><input type="hidden" id="parentpage" size="5px" value='.$parent_page.'><input type="hidden" id="sk1val" size="5px" value='.$sk1_val.'><input type="hidden" id="existing_man_page_num" size="5px" value='.$page.'><input type="hidden" id="last_man_page" size="5px" value='.$lastPage.'><input type="hidden" id="sort_col1" size="5px" value='.$sort.'><input type="hidden" id="sort_col2" size="5px" value='.$colname.'>'.$field_page.'<a href="'.$right_link.'"" class="sale_man_page_num_right">></a></div>&nbsp;&nbsp;&nbsp;Select</th><th id = "sale_s_child">Name (Search)<span id="'.$active_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/name/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/name/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_lnk.'">v</a></span><br/> Image<br>Price<br/>Vendor Name<br/>Manufacturer Name<br/>Sponsored? Y/N</th><th id = "sale_t_child">Updated <span id="'.$active_date_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/date/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/date/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_date_lnk.'">v</a></span></br>Description</br>Shipping</br>SKU<br/>Product ID<br/>Category</th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/operator/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/operator/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_op_lnk.'">v</a></span><br>UPC</br>Product Points/Flags</br>Vendor Points/Flags<br/>Manufacturer+Product Points<br/>SubCategory</div></th><th id="fifth_child">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>Slot 1/2</th><th>&nbsp;Keywords<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;Expires YYYY-MM-DD</th></tr>'),
    ); 
  */
  $form['plist']['tbl_start'] = array(
    '#type' => 'markup',
    '#markup' => t('<div id="productlist_form_content" style="padding-top:10px;"><table  border = "0" id = "pl_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Sale Listing</h3></td></tr>
<tr id="header_row"><th id = "plf_child"><span>&nbsp;&nbsp;&nbsp;&nbsp;Page</span><div id=""><a href="'.$left_link.'" class="sale_man_page_num_left"><</a><input type="hidden" id="admin_page" size="5px" value="productlist"><input type="hidden" id="parentpage" size="5px" value='.$parent_page.'><input type="hidden" id="sk1val" size="5px" value='.$sk1_val.'><input type="hidden" id="existing_man_page_num" size="5px" value='.$page.'><input type="hidden" id="last_man_page" size="5px" value='.$lastPage.'><input type="hidden" id="sort_col1" size="5px" value='.$sort.'><input type="hidden" id="sort_col2" size="5px" value='.$colname.'>'.$field_page.'<a href="'.$right_link.'"" class="sale_man_page_num_right">></a></div>&nbsp;&nbsp;&nbsp;Select</br>&nbsp;&nbsp;&nbsp;Entry</br>&nbsp;&nbsp;(Rowid)</th><th id = "sale_s_child">Name (Search)<span id="'.$active_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/name/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/name/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_lnk.'">v</a></span><br/> Image<br>Price<br/>Vendor Name<br/>Manufacturer Name<br/>Sponsored? Y/N<br>UPC</th><th id = "sale_t_child">Updated <span id="'.$active_date_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/date/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/date/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_date_lnk.'">v</a></span></br>Category</br>Shipping</br>SKU<br/>Product ID<br/>Category</br>Description</th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="'.$base_path.'/admin/product/productlist/asc/operator/'.$sk1_val.'/'.$sort_asc.'" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="'.$base_path.'/admin/product/productlist/desc/operator/'.$sk1_val.'/'.$sort_asc.'"  id="'.$inactive_op_lnk.'">v</a></span><br/>SubCategory</br>Product Points/Flags</br>Vendor Points/Flags<br/>Manufacturer+Product Points<br/>SubCategory</br>Keywords</div></th><th id="fifth_child">&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>Slot 1/2</th><th>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;</br>&nbsp;Expires YYYY-MM-DD</th></tr>'),
  ); 
    $form_state->setCached(FALSE);

   $form['plist']['scroll_area_start'] = array(
      '#type' => 'markup',
      '#markup' => '<tr><td colspan = "4"><div id = "ps_scroll_area"><table id = "ps_scroll_table">',
     ); 
  
  if(is_array($results)){
    $cnt =   $num_results;
   
    }else{
    $cnt=0;
    }
    
   //if($cnt>=3){
   $trows = $cnt;
  //}//else{
  
  //$trows = 3;
  //}
  
  
  $product_sale_id=array(); 
  $manufacturer_rank_id=array();
   $distributor_rank_id =array();
    $vendor_rank_id=array();
    $category_id=array();
    $product_rank_id=array();
    $product_vend_name=array();

    $product_cat=array();
    $product_sub=array();

    $product_name=array();
    $product_sku=array();
    $product_upc=array();
    $keywords=array();
    $description=array();
  
    $currency=array();
    $price=array();
    $buy_url=array();
    $image_url=array();
    $in_stock=array();
    $standard_shipping_cost=array();
    $product_points=array();
    $product_rank=array();
    $product_sponsored=array();
    $product_spon_cat=array();
    $product_spon_sub=array();
    $product_spon_slot=array();
    $product_spon_expires=array();
    $product_spon_page=array();
    $product_update=array();
    $product_operator_id=array();
  
    $sponsored_slot_val='';
    $sponsored_expiry_val='';
  
   foreach($results as $key=>$node){
  
  $product_sale_id[$key] = $node->product_sale_id;  
  $manufacturer_rank_id[$key] = $node->product_mfr_rank_id;
  $distributor_rank_id[$key]=$node->product_dist_over_rank_id;
  $vendor_rank_id[$key]=$node->product_vend_rank_id;
  $category_id[$key]=$node->product_cat_id;
  $product_rank_id[$key]=$node->product_prod_rank_id;
  $product_vend_name[$key]=$node->product_vend_name;

  $product_cat[$key]=$node->product_cat;
  $product_sub[$key]=$node->product_sub;


  $product_name[$key]=$node->product_name;
  $product_sku[$key]=$node->product_sku;
  $product_upc[$key]=$node->product_upc;
  $keywords[$key]=$node->product_keywords;
  $description[$key]=$node->product_description;


  $currency[$key]=$node->product_currency;
  $price[$key]=$node->product_price;
  $buy_url[$key]=$node->product_buy_url;
  $image_url[$key]=$node->product_image_url;
  $in_stock[$key]=$node->product_in_stock;
  $standard_shipping_cost[$key]=$node->product_standard_shipping_cost;
  $product_points[$key]=$node->product_points;
  $product_rank[$key]=$node->product_rank;
  $product_sponsored[$key]=$node->product_sponsored;


  $product_spon_cat[$key]=$node->product_spon_cat;
  $product_spon_sub[$key]=$node->product_spon_sub;
  $product_spon_slot[$key]=$node->product_spon_slot;
  $product_spon_expires[$key]=$node->product_spon_expires;
  //$product_spon_page[$key]=$node->product_spon_page;
  $product_update[$key]=$node->product_update;
  $product_operator_id[$key]=$node->product_operator_id;


  //$dff_collection_status[$key]=$node->dff_collection_status;
  
  }
  
  for($k=0;$k<$trows;$k++){

    /*$total_results = \Drupal::database()->select('product_rank', 'pr')
    ->fields('pl', array('product_mfr_name','product_mfr_id','product_points'))
    ->condition('product_mfr_name',$last_letter, '=')
    ->orderBy($field_name1,$sort)
  ->orderBy($field_name2,$sort)
  ->orderBy($field_name3,$sort) 
    ->execute()->fetchAll();
*/
/*
$test_var = "SELECT pasn01_manufacturer_rank.manufacturer_owner,pasn01_manufacturer_rank.manufacturer_labor,pasn01_product_rank.product_mfr_name,pasn01_product_rank.product_mfr_id,pasn01_product_rank.product_points FROM pasn01_manufacturer_rank,pasn01_product_rank
where pasn01_product_rank.product_mfr_rank_id=pasn01_manufacturer_rank.manufacturer_rank_id and pasn01_manufacturer_rank.manufacturer_name=pasn01_product_rank.product_mfr_name and pasn01_product_rank.product_name='$product_name[$k]' and pasn01_product_rank.product_mfr_id='$manufacturer_rank_id[$k]' and pasn01_product_rank.product_rank_id='$product_rank_id[$k]'";
*/

//'mr.manufacturer_name = pr.product_mfr_name'
$mquery = \Drupal::database()->select('manufacturer_rank', 'mr');
$mquery->innerjoin('product_rank', 'pr', 'mr.manufacturer_rank_id = pr.product_mfr_rank_id');
//$query->innerjoin('field_data_comment_body', 'fc', 'fc.entity_id = c.cid');
$mquery->fields('mr', array('manufacturer_owner', 'manufacturer_labor'));
$mquery->fields('pr', array('product_mfr_name', 'product_mfr_id', 'product_points'));
//$query->fields('fc', array('comment_body_value'));
//$mquery->condition('product_name', $product_name[$k], '=');
//$mquery->condition('product_name', $product_name[$k], '=');
$mquery->condition('product_name', $product_name[$k], '=');
$mquery->condition('product_rank_id', $product_rank_id[$k], '=');
$mresult = $mquery->execute()->fetchObject();

/*
    $manufacture_data_results = \Drupal::database()->query("SELECT pasn01_manufacturer_rank.manufacturer_owner,pasn01_manufacturer_rank.manufacturer_labor,pasn01_product_rank.product_mfr_name,pasn01_product_rank.product_mfr_id,pasn01_product_rank.product_points FROM pasn01_manufacturer_rank,pasn01_product_rank
    where pasn01_product_rank.product_mfr_rank_id=pasn01_manufacturer_rank.manufacturer_rank_id and pasn01_manufacturer_rank.manufacturer_name=pasn01_product_rank.product_mfr_name and pasn01_product_rank.product_name='$product_name[$k]' and pasn01_product_rank.product_rank_id='$product_rank_id[$k]'")->fetchObject();

*/

/*
$keyword_query = \Drupal::database()->select('product_keyword', 'pk');
$keyword_query->fields('pk', array('product_keyword_phrase'));
$keyword_query->condition('product_keyword_cat', $product_cat[$k], '=');
$keyword_query->condition('product_keyword_sub', $product_sub[$k], '=');
$keyword_query_result = $keyword_query->execute()->fetchObject();
//pasn01_product_keyword

$product_keyword_phrase=$keyword_query_result->product_keyword_phrase;
*/

    $mowner=$mresult->manufacturer_owner;
    $mlabor=$mresult->manufacturer_labor;

    $product_mfr_name=$mresult->product_mfr_name;
    $product_mfr_id=$mresult->product_mfr_id;

    $product_points=$mresult->product_points;




    if($mowner==0){
      $mpoints = 0;
    }else if($mowner>0 && $mowner<=49){
      $mpoints = 1;
    }else if($mowner>49 && $mowner<=99){
      $mpoints = 2;
    }else{
      $mpoints = 3;
    }
  
    if($mlabor==0){
      $lpoints = 0;
    }else if($mlabor>0 && $mlabor<=49){
      $lpoints = 1;
    }else if($mlabor>49 && $mlabor<=99){
      $lpoints = 2;
    }else{
      $lpoints = 3;
    } 


  
  //foreach($results as $node){
   $form['plist']['tbl_row'.$i] = array(
      '#type' => 'markup',
      '#markup' => '<tr><td id = "pl_scroll_td">',
    );
     
       $form['plist']['id['.$i.']'] = array(
       '#type' => 'checkbox',  
       "#default_value" => 0,
       '#attributes' => array('onclick' => 'if(document.forms["plform"].hid'.$i.'.value==""){ document.forms["plform"].hid'.$i.'.value="'.$product_sale_id[$i].'"; }else{ document.forms["plform"].hid'.$i.'.value="'.$i.'"; }','class'=>array('chk')),
       );
              $form['plist']['hid'.$i] = array(
       '#type' => 'hidden',  
       "#default_value" => '', 
       );
  
       $form['plist']['eid'.$i] = array(
       '#type' => 'hidden',  
       "#default_value" => $product_sale_id[$i], 
       );
       
        $form['plist']['ch'.$i] = array(
      '#type' => 'markup',
      '#markup' => '</br>'.$col_num.'<br><br>('.$product_sale_id[$i].')</td><td id="sale_prodcol2">',
      
    );
    
  /*
    $form['plist']['product_name'.$i] = array(
      '#type' => 'markup',
      '#prefix' =>'<div id="pl_pname">',
      '#markup' => $product_name[$i],
      '#suffix' =>'<br><br><br></div>',         
      );
*/
       $form['plist']['product_name'.$i] = array(
        '#type' => 'textfield',
        '#default_value' => $product_name[$i],
        '#size' => 35, 
        '#maxlength' => 50,
        '#prefix' =>'<div id="pl_pname">',
        //'#markup' => $product_name[$i],
        '#suffix' =>'</div>',         
        );
        
        //$imgurl = str_ireplace( 'https://', 'http://', $image_url[$i] );

        $imgurl = $image_url[$i];
  
        $form['plist']['image_url'.$i] = array(
          '#type' => 'markup',
          '#prefix' =>'<br>',
          '#markup' => '<img src='.$imgurl.' class="prodthumb">',
          '#suffix' =>'<br>',         
          );


          $form['plist']['price'.$i] = array(
            '#type' => 'number',
            '#prefix' =>'<br>',
            '#attributes' => array('id'=>'sale_price'),
            '#default_value' => $price[$i],
            '#size' => 32, 
            '#maxlength' => 255,  
            '#suffix' =>'<br>',         
            );
  
  
       $form['plist']['product_vend_name'.$i] = array(
        '#type' => 'markup', 
        '#markup' => $product_vend_name[$i], 
        '#prefix' =>'',  
        '#suffix' =>'<br>',
    );


    $form['plist']['product_man_name'.$i] = array(
      '#type' => 'markup', 
      '#markup' => $product_mfr_name, 
      '#prefix' =>'<br>',  
      '#suffix' =>'<br>',
  );

    $form['plist']['product_sponsored'.$i] = array(
  		'#type' => 'textfield',
    '#arg' => $i,
    '#attributes' => array('class'=>array('spons_yesno')),
  	//	'#options' => array('CJ'=>t('Commission Junction (CJ)'),'LS'=>t('Linkshare (LS)'),),
      /*'#title' =>t('DFF Name'),*/
      '#default_value' => (isset($product_sponsored[$i]) ? $product_sponsored[$i] : ''),
      //'#options' => $category, 
    //  '#options' => array(t('N'), t('Y')),
		'#ajax' => array(
			'callback' => array($this, 'myAjaxCallback'), //alternative notation
			'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
			'event' => 'keyup',
			'method' => 'replace',
			'wrapper' => 'product_spon_cat'.$i, // This element is updated with this AJAX callback.
			'progress' => array(
				'type' => 'throbber',
				//'message' =>'Verifying entry...',
					),
				),
			//'#default_value' => $product_sponsored[$i],
      '#prefix' =>'<br>',  
      '#suffix' =>'<br>', 
			);
  

      $form['plist']['product_upc'.$i] = array(
        '#type' => 'markup', 
        '#markup' => $product_upc[$i],
        '#prefix' =>'<br>',
			'#suffix' =>'</td><td id="sale_prodcol3">',     
    
    );
  
  
       $form['plist']['product_update'.$i] = array(
           '#type' => 'markup', 
          /* '#title' => t('FTP Password'),*/ 
           '#markup' => $product_update[$i],
           '#prefix' => '<br>',
           '#suffix' =>'<br><br>',
  
       );


       $form['plist']['product_cat'.$i] = array(
        '#type' => 'markup', 
        '#markup'=>$product_cat[$i], 
          '#prefix' => '<div id="pcat"><br>',
          '#suffix' =>'</div>',
      
      );


      $form['plist']['standard_shipping_cost'.$i] = array(
        '#type' => 'number',
        '#prefix' => '&nbsp;',
        '#attributes' => array('id'=>'standard_shipping_cost'),
        '#default_value' => $standard_shipping_cost[$i],
        '#size' => 32, 
        '#maxlength' => 255,  
        '#suffix' => '',
    );


    $form['plist']['product_sku'.$i] = array(
      '#type' => 'markup', 
      '#markup'=>$product_sku[$i], 
        '#prefix' => '<br>',
        '#suffix' =>'<br>',
  
  );

  $form['plist']['product_id'.$i] = array(
    '#type' => 'markup', 
    '#markup'=>$product_mfr_id, 
      '#prefix' => '<br>',
      '#suffix' =>'<br>',

);


$options=array();

if($product_sponsored[$i]=='N'){
  $category_options=array(t(''));
  //$product_spon_cat[$i]='';
  $options=array(t(''));
}else{
  $category_options=$category;
  

  $selected_category=trim($product_spon_cat[$i]);
  if(isset($sub_category[$selected_category])){
    foreach($sub_category[$selected_category] as $skey=>$sval){
      $options[$sval]=$sval;
    }
    $options['None']='None';
  //$sub_options=$options;
}


}




$form['plist']['product_spon_cat'.$i] = array(
    '#type' => 'select', 
    '#arg' => $i,
    '#default_value' => (isset($product_spon_cat[$i]) ? $product_spon_cat[$i] : ''),
    '#options' => $category_options,  
    //'#attributes' => array('id'=>'product_spon_cat'.$i),
    '#prefix' =>'<br>',
    '#ajax' => array(
          'callback' => array($this, 'changeOptionsAjax'), //alternative notation
          'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
          'event' => 'change',
          'method' => 'replace',
          //'method' => 'html',
          'wrapper' => 'product_spon_sub'.$i, // This element is updated with this AJAX callback.
          'progress' => array(
            'type' => 'throbber',
            'message' =>'Verifying entry...',
              ),
            ),
            '#validated' => True,
    '#suffix' =>'<br>',

);
      
  
  
        $form['plist']['description'.$i] = array(
          '#type' => 'markup',
          '#prefix' => '<div id="pldescription">',
          '#markup' => $description[$i],
          '#suffix' => '</div></td><td id="prodcol4">',
      );

    /*  $form['plist']['standard_shipping_cost'.$i] = array(
        '#type' => 'number',
        '#prefix' => '',
        '#attributes' => array('id'=>'standard_shipping_cost'),
        '#default_value' => $standard_shipping_cost[$i],
        '#size' => 32, 
        '#maxlength' => 255,  
        '#suffix' => '',
    );
  
    
  
    $form['plist']['product_sku'.$i] = array(
      '#type' => 'markup', 
      '#markup'=>$product_sku[$i], 
        '#prefix' => '<br>',
        '#suffix' =>'<br>',
  
  );


  $form['plist']['product_id'.$i] = array(
    '#type' => 'markup', 
    '#markup'=>$product_mfr_id, 
      '#prefix' => '<br>',
      '#suffix' =>'<br>',

);

$form['plist']['product_cat'.$i] = array(
  '#type' => 'markup', 
  '#markup'=>$product_cat[$i], 
    '#prefix' => '<br>',
    '#suffix' =>'<br>',

);


$options=array();

if($product_sponsored[$i]=='N'){
  $category_options=array(t(''));
  //$product_spon_cat[$i]='';
  $options=array(t(''));
}else{
  $category_options=$category;
  

  $selected_category=trim($product_spon_cat[$i]);
  if(isset($sub_category[$selected_category])){
    foreach($sub_category[$selected_category] as $skey=>$sval){
      $options[$sval]=$sval;
    }
    $options['None']='None';
  //$sub_options=$options;
}


}




$form['plist']['product_spon_cat'.$i] = array(
    '#type' => 'select', 
    '#arg' => $i,
    '#default_value' => (isset($product_spon_cat[$i]) ? $product_spon_cat[$i] : ''),
    '#options' => $category_options,  
    //'#attributes' => array('id'=>'product_spon_cat'.$i),
    '#prefix' =>'&nbsp;',
    '#ajax' => array(
          'callback' => array($this, 'changeOptionsAjax'), //alternative notation
          'disable-refocus' => FALSE, // Or TRUE to prevent re-focusing on the triggering element.
          'event' => 'change',
          'method' => 'replace',
          //'method' => 'html',
          'wrapper' => 'product_spon_sub'.$i, // This element is updated with this AJAX callback.
          'progress' => array(
            'type' => 'throbber',
            'message' =>'Verifying entry...',
              ),
            ),
            '#validated' => True,
    '#suffix' =>'</td><td id="prodcol4">',

);
*/

  
    $form['plist']['product_operator'.$i] = array(
      '#type' => 'markup', 
      '#prefix' => '<br>',
     /* '#title' => t('FTP Password'),*/ 
      '#markup' => $product_operator_id[$i],
      '#suffix' =>'<br><br><br>',

  );
  
  /*
  $form['plist']['product_upc'.$i] = array(
    '#type' => 'markup', 
    '#prefix' => '<div id="plupc">',
    '#markup' => $product_upc[$i],
    '#suffix' =>'</div><br>',

);
 */


$module_handler = \Drupal::service('module_handler');
$module_path = $module_handler->getModule('product_listing')->getPath();
$zero_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.png');
$first_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.1.jpg');
$second_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.2.png');
$third_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.3.png');
$fourth_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.4.png');
$fifth_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.5.png');
$sixth_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.6.png');
$seventh_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.7.png');
$eighth_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.8.png');
$ninenth_path = file_create_url(drupal_get_path('module', 'product_listing') . '/images/0.9.png');

$flag_points =ProductListForm::truncate_decimals($product_rank[$i],2); 
$find_flag = $flag_points%2;
if($flag_points%2==0){

}



$vend_points = \Drupal::database()->select('vendor_rank', 'vr')
    ->fields('vr', array('vendor_rank'))
    ->condition('vendor_rank_id',$vendor_rank_id[$i], '=')
    ->execute()->fetchObject();

    $vendor_points = $vend_points->vendor_rank;

    $vendor_flag_points =ProductListForm::truncate_decimals($vendor_points,2); 

  


$vendor_display_flag ='No Flags'; 

$display_flag ='No Flags, No Sale'; 
if($flag_points==5.0){
//  10/2 = 5

  $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
}

if($flag_points==4.9){
  //9.9/2 =4.95
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }

  if($flag_points==4.9){
    //9.8/2 =4.9
    
      $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }  

//9.9/2 =4.95
//9.8/2=4.9


//9.7/2=4.85
if($flag_points==4.8){
  //9.7/2 =4.85
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }  


//9.6/2=4.8
if($flag_points==4.8){
  //9.6/2 =4.8
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }



//9.5/2=4.75 
if($flag_points==4.7){
  //9.5/2 =4.75
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }




//9.4/2=4.7
if($flag_points==4.7){
  //9.4/2 =4.7
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }

//9.3/2=4.65
if($flag_points==4.6){
  //9.3/2 =4.65
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }


//9.2/2=4.6
if($flag_points==4.6){
  //9.2/2 =4.6
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//9.1/2=4.55
//9.1/2=4.55
if($flag_points==4.5){
  //9.1/2 =4.55
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }



//9.0/2 = 4.5
if($flag_points==4.5){
  //9.0/2 =4.5
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }


/*

8.9/2 =4.45
*/
if($flag_points==4.4){
  //8.9/2 =4.45
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }





//8.8/2=4.4

if($flag_points==4.4){
  //8.8/2=4.4
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }


//8.7/2=4.35
if($flag_points==4.3){
  //8.7/2=4.35
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }



//8.6/2=4.3
if($flag_points==4.3){
  //8.6/2=4.3
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }



//8.5/2=4.25 
if($flag_points==4.2){
  //8.5/2=4.25
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }



//8.4/2=4.2
if($flag_points==4.2){
  //8.4/2=4.2
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }


//8.3/2=4.15
if($flag_points==4.1){
  //8.3/2=4.15
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }

//8.2/2=4.1
if($flag_points==4.1){
  //8.2/2=4.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }



//8.1/2=4.05
if($flag_points==4.0){
  //8.2/2=4.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }

//8.0/2 = 4

if($flag_points==4.0){
  //8.0/2 = 4
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }



//7.9/2 =3.95
if($flag_points==3.9){
  //7.9/2 =3.95
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }


//7.8/2=3.9
if($flag_points==3.9){
  //7.8/2=3.9
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }


//7.7/2=3.85
if($flag_points==3.8){
  //7.7/2=3.85
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }


//7.6/2=3.8
if($flag_points==3.8){
  //7.6/2=3.8
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }



//7.5/2=3.75
if($flag_points==3.7){
  //7.5/2=3.75
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }



//7.4/2=3.7
if($flag_points==3.7){
  //7.4/2=3.7
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }



//7.3/2=3.65
if($flag_points==3.6){
  //7.3/2=3.65
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//7.2/2=3.6
if($flag_points==3.6){
  //7.2/2=3.6
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//7.1/2=3.55
if($flag_points==3.5){
  //7.1/2=3.55
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }


//7.0/2=3.5
if($flag_points==3.5){
  //7.0/2=3.5
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }



//6.9/2 =3.45
if($flag_points==3.4){
  //6.9/2 =3.45
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }

//6.8/2=3.4
if($flag_points==3.4){
  //6.8/2=3.4
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }



//6.7/2=3.35
if($flag_points==3.3){
  //6.7/2=3.35
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }


//6.6/2=3.3

if($flag_points==3.3){
  //6.6/2=3.3
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }



//6.5/2=3.25 
if($flag_points==3.2){
  //6.5/2=3.25
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }


//6.4/2=3.2
if($flag_points==3.2){
  //6.4/2=3.2
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }



//6.3/2=3.15
if($flag_points==3.1){
  //6.3/2=3.15
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }


//6.2/2=3.1
if($flag_points==3.1){
  //6.2/2=3.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }


//6.1/2=3.05
if($flag_points==3.0){
  //6.1/2=3.05
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" id="flag_point1">';  
  }

//6.0/2=3
if($flag_points==3.0){
  //6.1/2=3.05
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }


//5.9/2 =2.95
if($flag_points==2.9){
  //5.9/2 =2.95
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }




//5.8/2=2.9
if($flag_points==2.9){
  //5.8/2 =2.9
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }



//5.7/2=2.85
if($flag_points==2.8){
  //5.7/2=2.85
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }



//5.6/2=2.8
if($flag_points==2.8){
  //5.6/2=2.8
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }



//5.5/2=2.75 
if($flag_points==2.7){
  //5.5/2=2.75
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }





//5.4/2=2.7

if($flag_points==2.7){
  //5.4/2=2.7
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }


//5.3/2=2.65
if($flag_points==2.6){
  //5.3/2=2.65
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//5.2/2=2.6
if($flag_points==2.6){
  //5.2/2=2.6
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }


//5.1/2=2.55
if($flag_points==2.5){
  //5.1/2=2.55
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }

//5.0/2=2.5
if($flag_points==2.5){
  //5.0/2=2.5
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }


//4.9/2 =2.45
if($flag_points==2.4){
  //4.9/2 =2.45
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }

//4.8/2=2.4
if($flag_points==2.4){
  //4.8/2=2.4
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }



//4.7/2=2.35
if($flag_points==2.3){
  //4.7/2=2.35
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }



//4.6/2=2.3
if($flag_points==2.3){
  //4.6/2=2.3
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }


//4.5/2=2.25 
if($flag_points==2.2){
  //4.5/2=2.25
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }

//4.4/2=2.2
if($flag_points==2.2){
  //4.4/2=2.2
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }



//4.3/2=2.15
if($flag_points==2.1){
  //4.3/2=2.15
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }

//4.2/2=2.1
if($flag_points==2.1){
  //4.2/2=2.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }



//4.1/2=2.05
if($flag_points==2.0){
  //4.2/2=2.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }


//4.0/2=2
if($flag_points==2.0){
  //4.2/2=2.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }



//3.9/2 =1.95
if($flag_points==1.9){
  //3.9/2 =1.95
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }


//3.8/2=1.9
if($flag_points==1.9){
  //3.8/2=1.9
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
  }


//3.7/2=1.85
if($flag_points==1.8){
  //3.7/2=1.85
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }



//3.6/2=1.8
if($flag_points==1.8){
  //3.6/2=1.8
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
  }




//3.5/2=1.75 
if($flag_points==1.7){
  //3.5/2=1.75
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }



//3.4/2=1.7
if($flag_points==1.7){
  //3.4/2=1.7
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
  }


//3.3/2=1.65

if($flag_points==1.6){
  //3.3/2=1.65
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//3.2/2=1.6
if($flag_points==1.6){
  //3.2/2=1.6
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
  }



//3.1/2=1.55
if($flag_points==1.5){
  //3.1/2=1.55
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }



//3.0/2=1.5
if($flag_points==1.5){
  //3.0/2=1.5
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
  }


//2.9/2 =1.45
if($flag_points==1.4){
  //2.9/2 =1.45
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }



//2.8/2=1.4
if($flag_points==1.4){
  //2.8/2=1.4
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
  }




//2.7/2=1.35
if($flag_points==1.3){
  //2.7/2=1.35
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }


//2.6/2=1.3
if($flag_points==1.3){
  //2.6/2=1.3
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
  }


//2.5/2=1.25 
if($flag_points==1.2){
  //2.5/2=1.25
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }


//2.4/2=1.2
if($flag_points==1.2){
  //2.4/2=1.2
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
  }



//2.3/2=1.15
if($flag_points==1.1){
  //2.3/2=1.15
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" class="flag_point1">';  
  }


//2.2/2=1.1
if($flag_points==1.1){
  //2.2/2=1.1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
  }


//2.1/2=1.05
if($flag_points==1.0){
  //2.1/2=1.05
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">';  
  }



//2.0/2=1

if($flag_points==1.0){
  //2.0/2=1
  
    $display_flag ='<img src="'.$zero_path.'" class="flag0">';  
  }

  

/*

1.9/2 =2.45
1.8/2=2.4
1.7/2=2.35
1.6/2=2.3
1.5/2=2.25 
1.4/2=2.2
1.3/2=2.15
1.2/2=2.1
1.1/2=2.05
1.0/2=2


.9/2 =2.45
.8/2=2.4
.7/2=2.35
.6/2=2.3
.5/2=2.25 
.4/2=2.2
.3/2=2.15
.2/2=2.1
.1/2=2.05

*/



if($vendor_flag_points==5.0){
  //  10/2 = 5
  
    $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
  }
  
  if($vendor_flag_points==4.9){
    //9.9/2 =4.95
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
    if($vendor_flag_points==4.9){
      //9.8/2 =4.9
      
        $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
      }  
  
  //9.9/2 =4.95
  //9.8/2=4.9
  
  
  //9.7/2=4.85
  if($vendor_flag_points==4.8){
    //9.7/2 =4.85
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }  
  
  
  //9.6/2=4.8
  if($vendor_flag_points==4.8){
    //9.6/2 =4.8
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  //9.5/2=4.75 
  if($vendor_flag_points==4.7){
    //9.5/2 =4.75
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  
  
  //9.4/2=4.7
  if($vendor_flag_points==4.7){
    //9.4/2 =4.7
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  //9.3/2=4.65
  if($vendor_flag_points==4.6){
    //9.3/2 =4.65
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  //9.2/2=4.6
  if($vendor_flag_points==4.6){
    //9.2/2 =4.6
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //9.1/2=4.55
  //9.1/2=4.55
  if($vendor_flag_points==4.5){
    //9.1/2 =4.55
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  
  //9.0/2 = 4.5
  if($vendor_flag_points==4.5){
    //9.0/2 =4.5
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  /*
  
  8.9/2 =4.45
  */
  if($vendor_flag_points==4.4){
    //8.9/2 =4.45
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  
  
  
  //8.8/2=4.4
  
  if($vendor_flag_points==4.4){
    //8.8/2=4.4
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  //8.7/2=4.35
  if($vendor_flag_points==4.3){
    //8.7/2=4.35
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  
  //8.6/2=4.3
  if($vendor_flag_points==4.3){
    //8.6/2=4.3
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  
  //8.5/2=4.25 
  if($vendor_flag_points==4.2){
    //8.5/2=4.25
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  
  //8.4/2=4.2
  if($vendor_flag_points==4.2){
    //8.4/2=4.2
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  //8.3/2=4.15
  if($vendor_flag_points==4.1){
    //8.3/2=4.15
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  //8.2/2=4.1
  if($vendor_flag_points==4.1){
    //8.2/2=4.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  
  
  //8.1/2=4.05
  if($vendor_flag_points==4.0){
    //8.2/2=4.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
    }
  
  //8.0/2 = 4
  
  if($vendor_flag_points==4.0){
    //8.0/2 = 4
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
    }
  
  
  
  //7.9/2 =3.95
  if($vendor_flag_points==3.9){
    //7.9/2 =3.95
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  //7.8/2=3.9
  if($vendor_flag_points==3.9){
    //7.8/2=3.9
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  //7.7/2=3.85
  if($vendor_flag_points==3.8){
    //7.7/2=3.85
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  //7.6/2=3.8
  if($vendor_flag_points==3.8){
    //7.6/2=3.8
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  //7.5/2=3.75
  if($vendor_flag_points==3.7){
    //7.5/2=3.75
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  
  //7.4/2=3.7
  if($vendor_flag_points==3.7){
    //7.4/2=3.7
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  
  //7.3/2=3.65
  if($vendor_flag_points==3.6){
    //7.3/2=3.65
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //7.2/2=3.6
  if($vendor_flag_points==3.6){
    //7.2/2=3.6
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //7.1/2=3.55
  if($vendor_flag_points==3.5){
    //7.1/2=3.55
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  //7.0/2=3.5
  if($vendor_flag_points==3.5){
    //7.0/2=3.5
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  
  //6.9/2 =3.45
  if($vendor_flag_points==3.4){
    //6.9/2 =3.45
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  //6.8/2=3.4
  if($vendor_flag_points==3.4){
    //6.8/2=3.4
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  
  //6.7/2=3.35
  if($vendor_flag_points==3.3){
    //6.7/2=3.35
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  //6.6/2=3.3
  
  if($vendor_flag_points==3.3){
    //6.6/2=3.3
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  
  //6.5/2=3.25 
  if($vendor_flag_points==3.2){
    //6.5/2=3.25
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  //6.4/2=3.2
  if($vendor_flag_points==3.2){
    //6.4/2=3.2
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  
  //6.3/2=3.15
  if($vendor_flag_points==3.1){
    //6.3/2=3.15
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  
  //6.2/2=3.1
  if($vendor_flag_points==3.1){
    //6.2/2=3.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  
  //6.1/2=3.05
  if($vendor_flag_points==3.0){
    //6.1/2=3.05
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" id="flag_point1">';  
    }
  
  //6.0/2=3
  if($vendor_flag_points==3.0){
    //6.1/2=3.05
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
    }
  
  
  //5.9/2 =2.95
  if($vendor_flag_points==2.9){
    //5.9/2 =2.95
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  
  
  //5.8/2=2.9
  if($vendor_flag_points==2.9){
    //5.8/2 =2.9
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  
  //5.7/2=2.85
  if($vendor_flag_points==2.8){
    //5.7/2=2.85
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  //5.6/2=2.8
  if($vendor_flag_points==2.8){
    //5.6/2=2.8
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  //5.5/2=2.75 
  if($vendor_flag_points==2.7){
    //5.5/2=2.75
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  
  
  
  //5.4/2=2.7
  
  if($vendor_flag_points==2.7){
    //5.4/2=2.7
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  //5.3/2=2.65
  if($vendor_flag_points==2.6){
    //5.3/2=2.65
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //5.2/2=2.6
  if($vendor_flag_points==2.6){
    //5.2/2=2.6
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  //5.1/2=2.55
  if($vendor_flag_points==2.5){
    //5.1/2=2.55
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  //5.0/2=2.5
  if($vendor_flag_points==2.5){
    //5.0/2=2.5
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  //4.9/2 =2.45
  if($vendor_flag_points==2.4){
    //4.9/2 =2.45
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  //4.8/2=2.4
  if($vendor_flag_points==2.4){
    //4.8/2=2.4
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  
  //4.7/2=2.35
  if($vendor_flag_points==2.3){
    //4.7/2=2.35
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  
  //4.6/2=2.3
  if($vendor_flag_points==2.3){
    //4.6/2=2.3
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  //4.5/2=2.25 
  if($vendor_flag_points==2.2){
    //4.5/2=2.25
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  //4.4/2=2.2
  if($vendor_flag_points==2.2){
    //4.4/2=2.2
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  
  //4.3/2=2.15
  if($vendor_flag_points==2.1){
    //4.3/2=2.15
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  //4.2/2=2.1
  if($vendor_flag_points==2.1){
    //4.2/2=2.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  
  
  //4.1/2=2.05
  if($vendor_flag_points==2.0){
    //4.2/2=2.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
    }
  
  
  //4.0/2=2
  if($vendor_flag_points==2.0){
    //4.2/2=2.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$zero_path.'" class="flag0">';  
    }
  
  
  
  //3.9/2 =1.95
  if($vendor_flag_points==1.9){
    //3.9/2 =1.95
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  //3.8/2=1.9
  if($vendor_flag_points==1.9){
    //3.8/2=1.9
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$ninenth_path.'" id="flag_point9">';  
    }
  
  
  //3.7/2=1.85
  if($vendor_flag_points==1.8){
    //3.7/2=1.85
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  //3.6/2=1.8
  if($vendor_flag_points==1.8){
    //3.6/2=1.8
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$eighth_path.'" id="flag_point8">';  
    }
  
  
  
  
  //3.5/2=1.75 
  if($vendor_flag_points==1.7){
    //3.5/2=1.75
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  
  //3.4/2=1.7
  if($vendor_flag_points==1.7){
    //3.4/2=1.7
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$seventh_path.'" id="flag_point7">';  
    }
  
  
  //3.3/2=1.65
  
  if($vendor_flag_points==1.6){
    //3.3/2=1.65
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //3.2/2=1.6
  if($vendor_flag_points==1.6){
    //3.2/2=1.6
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$sixth_path.'" id="flag_point6">';  
    }
  
  
  
  //3.1/2=1.55
  if($vendor_flag_points==1.5){
    //3.1/2=1.55
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  
  //3.0/2=1.5
  if($vendor_flag_points==1.5){
    //3.0/2=1.5
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fifth_path.'" id="flag_point5">';  
    }
  
  
  //2.9/2 =1.45
  if($vendor_flag_points==1.4){
    //2.9/2 =1.45
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  
  //2.8/2=1.4
  if($vendor_flag_points==1.4){
    //2.8/2=1.4
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$fourth_path.'" id="flag_point4">';  
    }
  
  
  
  
  //2.7/2=1.35
  if($vendor_flag_points==1.3){
    //2.7/2=1.35
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  //2.6/2=1.3
  if($vendor_flag_points==1.3){
    //2.6/2=1.3
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$third_path.'" id="flag_point3">';  
    }
  
  
  //2.5/2=1.25 
  if($vendor_flag_points==1.2){
    //2.5/2=1.25
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  //2.4/2=1.2
  if($vendor_flag_points==1.2){
    //2.4/2=1.2
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$second_path.'" id="flag_point2">';  
    }
  
  
  
  //2.3/2=1.15
  if($vendor_flag_points==1.1){
    //2.3/2=1.15
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" class="flag_point1">';  
    }
  
  
  //2.2/2=1.1
  if($vendor_flag_points==1.1){
    //2.2/2=1.1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">&nbsp;<img src="'.$first_path.'" id="flag_point1">';  
    }
  
  
  //2.1/2=1.05
  if($vendor_flag_points==1.0){
    //2.1/2=1.05
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">';  
    }
  
  
  
  //2.0/2=1
  
  if($vendor_flag_points==1.0){
    //2.0/2=1
    
      $vendor_display_flag ='<img src="'.$zero_path.'" class="flag0">';  
    }





    $form['plist']['product_sub'.$i] = array(
      '#type' => 'markup', 
      '#markup'=>$product_sub[$i], 
        '#prefix' => '<div class="fheight">',
        '#suffix' =>'</div>',
    
    );






if($flag_points!=0){



$form['plist']['product_points'.$i] = array(
  '#type' => 'markup', 
  '#prefix' => '<div id="plflag">',
 /* '#title' => t('FTP Password'),*/ 
  '#markup' => "Product ".ProductListForm::truncate_decimals($flag_points,2).'&nbsp;'.$display_flag,
  '#suffix' =>'</div>',

);

}else{

  $form['plist']['product_points'.$i] = array(
    '#type' => 'markup', 
    '#prefix' => '<div id="plflag">',
   /* '#title' => t('FTP Password'),*/ 
    '#markup' => 'Product 0.0'.'&nbsp;'.$display_flag,
    '#suffix' =>'</div>',
  
  );
  

}


if($vendor_flag_points!=0){



  $form['plist']['vendor_points'.$i] = array(
    '#type' => 'markup', 
    '#prefix' => '<div id="plvendorflag">',
   /* '#title' => t('FTP Password'),*/ 
    '#markup' => "Vendor ".ProductListForm::truncate_decimals($vendor_points,2).'&nbsp;'.$vendor_display_flag,
    '#suffix' =>'</div>&nbsp;',
  
  );
  
  }else{
  
    $form['plist']['vendor_points'.$i] = array(
      '#type' => 'markup', 
      '#prefix' => '<div id="plvendorflag">',
     /* '#title' => t('FTP Password'),*/ 
      '#markup' => 'Vendor 0.0'.'&nbsp;'.$vendor_display_flag,
      '#suffix' =>'</div>&nbsp;',
    
    );
    
  
  }


  $form['plist']['man_points_prod_points'.$i] = array(
    '#type' => 'markup', 
    '#prefix' => '<br>',
   /* '#title' => t('FTP Password'),*/ 
    '#markup' => "Manufacturer + Product Points: ".($mpoints+$lpoints)." + ".$product_points,
    '#suffix' =>'<br>',
  
  );



 

/*
if($product_spon_cat[$i]!='' && $product_sponsored[$i]=='Y'){
  $selected_category=$product_spon_cat[$i];
  
  
  if(isset($sub_category[$selected_category])){
  foreach($sub_category[$selected_category] as $skey=>$sval){
    $options[$sval]=$sval;
  }
}

}else{
  $options='';
}

*/



  $form['plist']['product_spon_sub'.$i] = array(
    '#type' => 'select', 
      '#default_value' => (isset($product_spon_sub[$i]) ? $product_spon_sub[$i] : ''),
      '#options' => $options,  
      '#required' => TRUE, 
      '#attributes' => array('id'=>'product_spon_sub'.$i),
      '#validated' => TRUE,
      '#prefix' =>'&nbsp;',
    '#suffix' =>'<br>',
  
  );

  $form['plist']['product_keywords'.$i] = array(
    '#type' => 'markup', 
      '#markup' => $keywords[$i],
      '#prefix' =>'<div id="pkblock">&nbsp;',
    '#suffix' =>'</div></td><td id="prod_col5">',
  
  );

  


  if(isset($product_spon_slot[$i])){
    $product_spon_slot[$i]=$product_spon_slot[$i];

  }
if($product_spon_slot[$i]==0){
  $product_spon_slot[$i]='';
}


  $form['plist']['sponsored_slot'.$i] = array(
    '#type' => 'textfield',
		'#arg' => $i,
    '#attributes' => array('id'=>'sponsored_slot'),
  	//	'#options' => array('CJ'=>t('Commission Junction (CJ)'),'LS'=>t('Linkshare (LS)'),),
      /*'#title' =>t('DFF Name'),*/
      '#default_value' => (isset($product_spon_slot[$i]) ? $product_spon_slot[$i] : ''),
      //'#options' => $category, 
      //'#options' => array(t('0'), t('1'), t('2')),     
			//'#default_value' => $product_spon_slot[$i],
      '#prefix' =>'<div id="sp_slot">',
			'#suffix' =>'</div></td><td id="sixth_col">',    
  
  );


  if(isset($product_spon_expires[$i])){
    $product_spon_expires[$i]=$product_spon_expires[$i];

  }

if($product_spon_expires[$i]=='1000-01-01'){
  $product_spon_expires[$i]='';
}


  $form['plist']['sponsored_expiry'.$i] = array(
    '#type' => 'textfield', 
    '#attributes' => array('id'=>'sponsored_expiry'),
    '#default_value' => $product_spon_expires[$i],
    '#size' => 35, 
    '#maxlength' => 50,
    '#prefix' => '',
   /* '#title' => t('FTP Password'),*/ 
    '#suffix' =>'</td></tr>',
  
  );




/*
if($vendor_points!=0){
      // $category_operator_arr= explode("#",$category_operator_id[$i]);
    
  
       $form['plist']['vendor_points'.$i] = array(
        '#type' => 'markup',
        '#prefix' => '<div id="plvendorflag"><br>', 
       /* '#title' => t('FTP Password'),*/ 
  /*      '#markup' => "Vendor ".ProductListForm::truncate_decimals($vendor_points,2).'&nbsp;'.$vendor_display_flag,
        
        '#suffix' =>'</div></td></tr>',
  
    );

}else{

  
        // $category_operator_arr= explode("#",$category_operator_id[$i]);
      
    
         $form['plist']['vendor_points'.$i] = array(
          '#type' => 'markup',
          '#prefix' => '<div id="plvendorflag"><br>', 
         /* '#title' => t('FTP Password'),*/ 
    /*      '#markup' => 'Vendor 0.0'.'&nbsp;'.$vendor_display_flag,
          
          '#suffix' =>'</div></td></tr>',
    
      );

}  
   */ 
       
  $markup_id = 'box'.$i; 
  
  
  $i++;
  $col_num++;
  }
  $vendor_points=0;
  $product_points=0;
  $manufacturer_points=0;
  $distributor_points=0;
  $vendor_flag_points=0;
  $imgurl='';

    $form['plist']['total_values'] = array(
        '#type' => 'hidden',
          '#default_value' => $i,
  
   //       '#suffix' => '</td></tr></table></div>',
            
       ); 

  $form['plist']['scroll_area_end'] = array(
      '#type' => 'markup',
      '#markup' => '</table></div></td></tr>',
     );
  
  $form['plist']['box_extra'] = array(
      '#type' => 'markup',
      '#markup' => '<tr><td class = "job_status" colspan = "4"><div id="box_new">&nbsp;</div></td></tr>',  
    );  
    
  

    if($sk1!='null' && $from_which_page==''){
    
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Search result for '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 
  }else if($sk1!='null' && $from_which_page=='prank'){
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Product sale selection for product rank ID '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($sk1!='null' && $from_which_page=='vendor'){
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Product sale selection for vendor rank ID '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($sk1!='null' && $from_which_page=='dist'){
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Product sale selection for distributor rank ID '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($sk1!='null' && $from_which_page=='man'){
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Product sale selection for manufacturer rank ID '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($sk1!='null' && $from_which_page=='cat'){
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">Product sale listing for category ID '.urldecode($sk1),
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($operation_status=='y' && $operation_name=='update'){
    if($operated_rows==1){
      $mstext=' selection';
    }else{
      $mstext=' selections';
    }

    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">&nbsp;'.$operated_rows. $mstext.' processed successfully.',
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else if($operation_status=='y' && $operation_name=='delete'){
    if($operated_rows==1){
      $mstext=' selection';
    }else{
      $mstext=' selections';
    }

    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">&nbsp;'.$operated_rows. $mstext.' deleted successfully.',
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }else{
    $form['plist']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="box">',
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    ); 


  }
    
    $form['plist']['general_msg'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td colspan = "7" id="general_msg1"></td></tr>',
      '#markup' => '',
    );
   
    
    $form['plist']['dir_page'] = array(
      '#type' => 'hidden',
      '#value' => $page,
    );     



    $form['plist']['td1'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td colspan = "7" id = "btn_td"><table><tbody><tr class="tblborder"><td class="tblborder">',
      '#markup' => '',
    );    
    

if($first_letter=='O-' || $first_letter=='A-' || $first_letter=='M-'){   
  $form['plist']['update_sel'] = array(
        '#type' => 'button',
      //  '#attributes' => array(
      //    'class' => array('update-ps-class1'),
      //    ),
         '#ajax' => array(
        'callback' => '::update_product_rank_entry',
        'wrapper' => 'productlist_form_content',
        'name' => 'submit3',
      ),
          '#value' => t('Update Selection'),      
       ); 
       
  }else{
    $form['plist']['update_sel'] = array(
      '#type' => 'button',
      '#disabled' => true,
      '#value' => t('Update Selection'),
       
        
     ); 
     

  } 
     
  
    $form['plist']['td9'] = array(
      '#type' => 'markup',
      '#prefix' => '</td><td class="tblborder">',
      '#markup' => '',
    );  
    
    if($first_letter=='A-' || $first_letter=='M-'){
    $form['plist']['del_sel'] = array(
      '#type' => 'button',
       '#ajax' => array(
      'callback' => '::delete_product_rank_entry',
      'wrapper' => 'productlist_form_content',
      'name' => 'submit4',
    ),
        '#value' => t('Delete Selection'),
     ); 
    }else{

      $form['plist']['del_sel'] = array(
        '#type' => 'button',
        '#disabled' => true,
        '#value' => t('Delete Selection'),
       ); 

    }
  
     $form['plist']['td6'] = array(
      '#type' => 'markup',
      '#suffix' => '</td><td class="tblborder">',
      '#markup' => '',
    );   
    
    $form['plist']['search_sel'] = array(
      '#type' => 'button',
       '#ajax' => array(
      'callback' => '::filter_product_sale',
      'wrapper' => 'productlist_form_content',
      //'name' => 'submit4',
    ),
        '#value' => t('Search 1 Selection'),
     ); 

     $form['plist']['td7'] = array(
      '#type' => 'markup',
      '#suffix' => '</td>',
      '#markup' => '',
    );   
  
  
  
  
    
    
  $form['plist']['rank_sel'] = array(
        '#type' => 'markup',
        '#markup' => '', 
  
         '#suffix' => '</tr></table></td></tr></table></div>',
      //      '#suffix' => '</td></tr>',
       ); 
  
  
  

  
  
  
       $form['plist']['#action'] = 'listdir';
       //$form['#attached']['library'][] = 'core/drupal.ajax';
  
     $form['plist']['#attached']['library'][] = 'product_listing/product_listing';
     //  $form['collection']['#attached']['library'][] = 'dff_importer/custom_ajax';
     //$form['collection']['#attached']['library'][] = 'core/drupal.dialog.ajax';
       
  
     $content = $form;
     $themes=array('content' => $content);
     
    // return new Response(render($themes['content']));
     
    //return $themes['content'];
    //} 
  //	else {
  //	  	drupal_access_denied();
  //	}
  
  return $form;
  }
  
  
  
  /**
  * {@inheritdoc}
  */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $form_state->clearErrors();
  /*
  for($k=0;$k<$values['total_values'];$k++)
  {
  // If validation errors, add inline errors
  if ($errors = $form_state->getErrors()) {
  // Add error to fields using Symfony Accessor
  $accessor = PropertyAccess::createPropertyAccessor();
  foreach ($errors as $field => $error) {
      if ($accessor->getValue($form, $field)) {
          $accessor->setValue($form, $field.'[#prefix]', '<div class="form-group error">');
          $accessor->setValue($form, $field.'[#suffix]', '<div class="input-error-desc">' .$error. '</div></div>');
      }
  }
  $form_state->clearErrors();
  }
  
  
  
  }
  */
  
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  /*
  // If validation errors, add inline errors
  if ($errors = $form_state->getErrors()) {
    // Add error to fields using Symfony Accessor
    $accessor = PropertyAccess::createPropertyAccessor();
    foreach ($errors as $field => $error) {
        if ($accessor->getValue($form, $field)) {
            $accessor->setValue($form, $field.'[#prefix]', '<div class="form-group error">');
            $accessor->setValue($form, $field.'[#suffix]', '<div class="input-error-desc">' .$error. '</div></div>');
        }
    }
  }
  */
  
   }
   
   public function filter_product_sale(array &$form, FormStateInterface $form_state){
    $values = $form_state->getValues();
    
    $p=1;
    for($k=0;$k<$values['total_values'];$k++)
    {
      if($values['hid'.$k]!="")
      { 
        if($p==1){
        //$source=$values['file_format'.$k];
        $name = trim($values['product_name'.$k]);
        }
  
  $p++;
      }
  
    }
  
    if($name==''){
      //$source='null';
      $name = 'null';
    }
    $base_path=base_path();
    $response = new AjaxResponse();
    $url=$base_path."admin/product/productlist/asc/name/".$name; /* The URL that will be loaded into window.location. This should be a full URL. */
    $response->addCommand(new RedirectCommand($url));
  
    //$response = new RedirectResponse($url->toString());
      //  $response->send();
  
    return $response;
   }
   
  function update_product_rank_entry(array &$form, FormStateInterface $form_state) {
 
    $current_date=date("Y-m-d");

    $result_for_updation = \Drupal::database()->select('process_control', 'pc')
  ->fields('pc', array('process_control_id','process_proc_max','process_php_time_max','process_php_mem_max'))
  ->condition('process_name','prodsale', '=')
  ->execute()->fetchObject();

  $process_proc_max = $result_for_updation->process_proc_max; 

  $process_php_time_max = $result_for_updation->process_php_time_max; 
  $process_php_mem_max = $result_for_updation->process_php_mem_max; 



  
  
  if($process_php_time_max!=0){
    ini_set('max_execution_time', $process_php_time_max);
    
  }
  
  if($process_php_mem_max!=' '){
  
    //ini_set('upload_max_filesize', $process_php_mem_max);
    $var_arr = preg_split('#(?<=\d)(?=[a-z])#i',$process_php_mem_max);
    $new_val=$var_arr[0];
    $new_val_ext=$var_arr[1];
    ini_set('memory_limit', $new_val.$new_val_ext);
  
  }


  $checked_id=array();
  $exploded=array();
  $selected_chkbox='';

    $values = $form_state->getValues();

    $page = $values['dir_page'];

    $response_arr=array();
    $arr=array();
    $ajax_response1 = new AjaxResponse();
    $affected=0;
    for($k=0;$k<$values['total_values'];$k++)
    {
    if($values['hid'.$k]!="")
      {	

      /*  
        product_sponsored
        product_spon_cat
        product_spon_sub
        sponsored_slot
        sponsored_expiry


       


        $pl_result = \Drupal::database()->select('product_sale', 'pl')
        ->fields('pl', array('product_mfr_rank_id','product_dist_rank_id','product_vend_rank_id','product_prod_rank_id','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires'))
        ->condition('product_sale_id',$values['hid'.$k], '=')
        ->execute()->fetchObject();

*/


      //  PRODUCTPOINTS  from product rank table + MANUFACTURERPOINTS  from manufcturer rank table+ DISTRIBUTORPOINTS from distributor rank table + VENDORPOINTS from vendor rank table

/*
      $pl_check_any_spon_prod_present = \Drupal::database()->select('product_sale', 'pl')
      ->fields('pl', array('product_sale_id','product_mfr_rank_id','product_dist_rank_id','product_vend_rank_id','product_prod_rank_id','product_points','product_rank','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires'))
      ->condition('product_sponsored',trim($values['product_sponsored'.$k]), '=')
      ->condition('product_spon_cat',trim($values['product_spon_cat'.$k]), '=')
      ->condition('product_spon_sub',trim($values['product_spon_sub'.$k]), '=')
      ->condition('product_spon_slot',trim($values['sponsored_slot'.$k]), '=')
      ->condition('product_spon_expires',trim($values['product_spon_expires'.$k]), '=')
      ->execute()->fetchObject();
      $product_sale_id = $pl_check_any_spon_prod_present->product_sale_id;
      $product_sponsored = $pl_check_any_spon_prod_present->product_sponsored;
        $product_spon_cat = $pl_check_any_spon_prod_present->product_spon_cat;
        $product_spon_sub = $pl_check_any_spon_prod_present->product_spon_sub;
        $product_spon_slot = $pl_check_any_spon_prod_present->product_spon_slot;
        $product_spon_expires = $pl_check_any_spon_prod_present->product_spon_expires;

*/



        $pl_result = \Drupal::database()->select('product_sale', 'pl')
        ->fields('pl', array('product_mfr_rank_id','product_dist_over_rank_id','product_vend_rank_id','product_prod_rank_id','product_points','product_rank'))
        ->condition('product_sale_id',$values['hid'.$k], '=')
        ->execute()->fetchObject();
        
        
        $product_mfr_rank_id = $pl_result->product_mfr_rank_id;
        $product_dist_rank_id = $pl_result->product_dist_over_rank_id;
        $product_vend_rank_id = $pl_result->product_vend_rank_id;
        $product_prod_rank_id = $pl_result->product_prod_rank_id;


        //$selected_product_sponsored = $pl_result->product_sponsored;
        //$selected_product_spon_cat = $pl_result->product_spon_cat;
        //$selected_product_spon_sub = $pl_result->product_spon_sub;
        //$selected_product_spon_slot = $pl_result->product_spon_slot;
        //$selected_product_spon_expires = $pl_result->product_spon_expires;



        //$product_sponsored=$values['product_sponsored'.$k];
        //$product_spon_cat=$values['product_spon_cat'.$k];
        //$product_spon_sub=$values['product_spon_sub'.$k];
        //$sponsored_slot = $values['sponsored_slot'.$k];
        //$sponsored_expiry = $values['sponsored_expiry'.$k];
        



        


        $prod_points = \Drupal::database()->select('product_rank', 'pr')
        ->fields('pr', array('product_points'))
        ->condition('product_rank_id',$product_prod_rank_id, '=')
        ->execute()->fetchObject();

        $product_points = $prod_points->product_points;



        $man_points = \Drupal::database()->select('manufacturer_rank', 'mr')
        ->fields('mr', array('manufacturer_points'))
        ->condition('manufacturer_rank_id',$product_mfr_rank_id, '=')
        ->execute()->fetchObject();

        $manufacturer_points = $man_points->manufacturer_points;


        $dist_points = \Drupal::database()->select('distributor_rank', 'dr')
        ->fields('dr', array('distributor_points'))
        ->condition('distributor_rank_id',$product_dist_rank_id, '=')
        ->execute()->fetchObject();

        $distributor_points = $dist_points->distributor_points;


        $vend_points = \Drupal::database()->select('vendor_rank', 'vr')
        ->fields('vr', array('vendor_points'))
        ->condition('vendor_rank_id',$product_vend_rank_id, '=')
        ->execute()->fetchObject();

        $vendor_points = $vend_points->vendor_points;



        $total_product_points = $product_points+$manufacturer_points+$distributor_points+$vendor_points; 

        $product_rank = $total_product_points/21*5;

        $update_date = date("Y-m-d h:i:s");

    $date_arr = explode(":",$update_date);
    $date_last = end($date_arr);

    $rand_str1 = substr($date_last, 1); 


/*
    $find_product_sale_id[$skey] = $snode->product_sale_id;  
    $find_product_sponsored[$skey]=$snode->product_sponsored;
    $find_product_spon_cat[$skey]=$snode->product_spon_cat;
    $find_product_spon_sub[$skey]=$snode->product_spon_sub;
    $find_product_spon_slot[$skey]=$snode->product_spon_slot;
    $find_product_spon_expires[$skey]=$snode->product_spon_expires;
*/


    if($affected<$process_proc_max){

      if(trim($values['product_name'.$k])!="" && !is_numeric($values['product_name'.$k])){ 

      $checked_id[]=$values['hid'.$k];
    $selected_chkbox.=$k.",";


      if(trim($values['sponsored_slot'.$k])==''){
        $values['sponsored_slot'.$k]=0;
      }
      if(trim($values['sponsored_expiry'.$k])==''){
        $values['sponsored_expiry'.$k]='1000-01-01';
      }



      $pl_check_any_spon_prod_present = \Drupal::database()->select('product_sale', 'pl')
      ->fields('pl', array('product_sale_id','product_price','product_standard_shipping_cost','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires'))
      //->condition('product_sponsored',trim($values['product_sponsored'.$k]), '=')
      //->condition('product_spon_cat',trim($values['product_spon_cat'.$k]), '=')
      //->condition('product_spon_sub',trim($values['product_spon_sub'.$k]), '=')
      //->condition('product_spon_slot',trim($values['sponsored_slot'.$k]), '=')
      //->condition('product_spon_expires',trim($values['product_spon_expires'.$k]), '=')
      ->condition('product_sale_id',$values['hid'.$k], '=')
      ->execute()->fetchObject();
      $selected_product_sale_id = $pl_check_any_spon_prod_present->product_sale_id;
      $selected_product_sponsored = $pl_check_any_spon_prod_present->product_sponsored;
        $selected_product_spon_cat = $pl_check_any_spon_prod_present->product_spon_cat;
        $selected_product_spon_sub = $pl_check_any_spon_prod_present->product_spon_sub;
        $selected_product_spon_slot = $pl_check_any_spon_prod_present->product_spon_slot;
        $selected_product_spon_expires = $pl_check_any_spon_prod_present->product_spon_expires;

        $selected_product_price = $pl_check_any_spon_prod_present->product_price;
        $selected_product_standard_shipping_cost = $pl_check_any_spon_prod_present->product_standard_shipping_cost;


//Query to find all sponsored products 
 /*$all_sponsored_results = \Drupal::database()->select('product_sale', 'pl')
 ->fields('pl', array('product_sale_id','product_sponsored','product_spon_cat','product_spon_sub','product_spon_slot','product_spon_expires'))
 ->condition('product_sponsored','Y', '=') 
 ->condition('product_spon_cat',trim($values['product_spon_cat'.$k]), '=')
 ->condition('product_spon_sub',trim($values['product_spon_sub'.$k]), '=') 
 ->condition('product_spon_slot',trim($values['sponsored_slot'.$k]), '=')           
 ->execute()->fetchObject();
*/

$newcat = trim($values['product_spon_cat'.$k]);
$newsub = trim($values['product_spon_sub'.$k]);
$newslot = trim($values['sponsored_slot'.$k]);
$newexpiry = trim($values['sponsored_expiry'.$k]);

$newprice = trim($values['price'.$k]);
$newshipping = trim($values['standard_shipping_cost'.$k]);

$countnode =  \Drupal::database()->select('product_sale', 'pl')
->fields('pl', array('product_sale_id'))
->condition('product_sponsored','Y', '=')
->condition('product_spon_cat',trim($values['product_spon_cat'.$k]), '=')
->condition('product_spon_sub',trim($values['product_spon_sub'.$k]), '=') 
->condition('product_spon_slot',trim($values['sponsored_slot'.$k]), '=') 
->countQuery()
->execute()
->fetchField();
  
/*
 $countnode = \Drupal::database()->query("SELECT COUNT(product_sale_id) AS count FROM {product_sale} WHERE product_sponsored = :product_sponsored,product_spon_cat = :product_spon_cat, product_spon_sub = :product_spon_sub, product_spon_slot = :product_spon_slot ", array(':product_sponsored' => 'Y',':product_spon_cat' => $newcat,':product_spon_sub' => $newsub,':product_spon_slot' =>$newslot ))->fetchField();
 */
     /*$find_product_sale_id=array();  
     $find_product_sponsored=array();
     $find_product_spon_cat=array();
     $find_product_spon_sub=array();
     $find_product_spon_slot=array();
     $find_product_spon_expires=array();
     */
    //foreach($all_sponsored_results as $skey=>$snode){  
      /*   $find_product_sale_id=$all_sponsored_results->product_sale_id;  
         $find_product_sponsored=$all_sponsored_results->product_sponsored;
         $find_product_spon_cat=$all_sponsored_results->product_spon_cat;
         $find_product_spon_sub=$all_sponsored_results->product_spon_sub;
         $find_product_spon_slot=$all_sponsored_results->product_spon_slot;
         $find_product_spon_expires=$all_sponsored_results->product_spon_expires;    
   //}
*/


   //End query
$current_date=date('Y-m-d'); 

      if($countnode==0){

        if(trim($values['product_sponsored'.$k])=='Y'){
if(trim($values['sponsored_slot'.$k])!=0 && trim($values['sponsored_expiry'.$k])>=$current_date){
        //if(trim($values['product_sponsored'.$k]))
        $nid = \Drupal::database()->update('product_sale')
         ->fields(array(
          'product_price' => trim($values['price'.$k]),
          'product_points'=>(float)$total_product_points,
          'product_rank'=>(float)$product_rank,
          'product_standard_shipping_cost' => trim($values['standard_shipping_cost'.$k]),
          'product_sponsored'=>trim($values['product_sponsored'.$k]),
          'product_spon_cat'=>trim($values['product_spon_cat'.$k]),
          'product_spon_sub'=>trim($values['product_spon_sub'.$k]),
          'product_spon_slot' => trim($values['sponsored_slot'.$k]),
          'product_spon_expires' => trim($values['sponsored_expiry'.$k]),
           'product_randomizer'=>$rand_str1,
           'product_update'=>$update_date,
         ))
         ->condition('product_sale_id',$values['hid'.$k], '=')
            ->execute();
         }elseif(trim($values['sponsored_expiry'.$k])!='1000-01-01' && trim($values['sponsored_expiry'.$k])<$current_date){
          $border_css=['border' => '1px solid red'];
          $response_arr[]=$values['hid'.$k];

          $visible_1=['display' => 'block'];
          $visible_2=['display' => 'none'];
        $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));

          //$arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Expiry date should not be less than todays date.'));
  


         }elseif(trim($values['sponsored_expiry'.$k])=='1000-01-01' && trim($values['sponsored_slot'.$k])!=0){
          $border_css=['border' => '1px solid red'];
          $response_arr[]=$values['hid'.$k];

          $visible_1=['display' => 'block'];
          $visible_2=['display' => 'none'];
        $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));

          //$arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Expiry date should not be less than todays date.'));
   
          
         }else{
          $border_css=['border' => '1px solid red'];
          $arr=$ajax_response1->addCommand(new CssCommand('#pl_content_table td input[type="text"].product_name'.$k, $border_css));

          $response_arr[]=$values['hid'.$k];

          $visible_1=['display' => 'block'];
          $visible_2=['display' => 'none'];
        $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));

          //$arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Invalid values, correct or uncheck selection to continue.'));
         }


        }else{
          $nid = \Drupal::database()->update('product_sale')
          ->fields(array(
           'product_price' => trim($values['price'.$k]),
           'product_points'=>(float)$total_product_points,
           'product_rank'=>(float)$product_rank,
           'product_standard_shipping_cost' => trim($values['standard_shipping_cost'.$k]),
           'product_sponsored'=>'N',
           'product_spon_cat'=>'',
           'product_spon_sub'=>'',
           'product_spon_slot' =>0,
           'product_spon_expires' =>'1000-01-01',
            'product_randomizer'=>$rand_str1,
            'product_update'=>$update_date,
          ))
          ->condition('product_sale_id',$values['hid'.$k], '=')
             ->execute();


        }

         }else if($countnode==1 && trim($values['product_sponsored'.$k])=='N'){

 //if(trim($values['product_sponsored'.$k]))
 $nid = \Drupal::database()->update('product_sale')
 ->fields(array(
  'product_price' => trim($values['price'.$k]),
  'product_points'=>(float)$total_product_points,
  'product_rank'=>(float)$product_rank,
  'product_standard_shipping_cost' => trim($values['standard_shipping_cost'.$k]),
  'product_sponsored'=>'N',
  'product_spon_cat'=>'',
  'product_spon_sub'=>'',
  'product_spon_slot' =>0,
  'product_spon_expires' => '1000-01-01',
   'product_randomizer'=>$rand_str1,
   'product_update'=>$update_date,
 ))
 ->condition('product_sale_id',$values['hid'.$k], '=')
    ->execute();


         }else if($countnode==1 && trim($values['product_sponsored'.$k])=='Y'){

          /*$newcat = trim($values['product_spon_cat'.$k]);
$newsub = trim($values['product_spon_sub'.$k]);
$newslot = trim($values['sponsored_slot'.$k]);
$newexpiry = trim($values['sponsored_expiry'.$k]);

          $selected_product_sale_id = $pl_check_any_spon_prod_present->product_sale_id;
          $selected_product_sponsored = $pl_check_any_spon_prod_present->product_sponsored;
            $selected_product_spon_cat = $pl_check_any_spon_prod_present->product_spon_cat;
            $selected_product_spon_sub = $pl_check_any_spon_prod_present->product_spon_sub;
            $selected_product_spon_slot = $pl_check_any_spon_prod_present->product_spon_slot;
            $selected_product_spon_expires = $pl_check_any_spon_prod_present->product_spon_expires;

            $selected_product_price = $pl_check_any_spon_prod_present->product_price;
            $selected_product_standard_shipping_cost = $pl_check_any_spon_prod_present->product_standard_shipping_cost;

            */

          if($selected_product_spon_cat==$newcat && $selected_product_spon_sub==$newsub && $selected_product_spon_slot==$newslot && $selected_product_spon_expires==$newexpiry && ($selected_product_price!=$newprice || $selected_product_standard_shipping_cost!=$newshipping)){

            $nid = \Drupal::database()->update('product_sale')
            ->fields(array(
             'product_price' => trim($values['price'.$k]),
             'product_points'=>(float)$total_product_points,
             'product_rank'=>(float)$product_rank,
             'product_standard_shipping_cost' => trim($values['standard_shipping_cost'.$k]),
              'product_randomizer'=>$rand_str1,
              'product_update'=>$update_date,
            ))
            ->condition('product_sale_id',$values['hid'.$k], '=')
               ->execute();


          }else if($selected_product_spon_cat==$newcat && $selected_product_spon_sub==$newsub && $selected_product_spon_slot==$newslot && $selected_product_spon_expires!=$newexpiry && $newexpiry>$current_date){
            
            
            $nid = \Drupal::database()->update('product_sale')
            ->fields(array(
              'product_spon_expires' => trim($values['sponsored_expiry'.$k]),
              'product_randomizer'=>$rand_str1,
              'product_update'=>$update_date,
            ))
            ->condition('product_sale_id',$values['hid'.$k], '=')
               ->execute();

            
            
          }else{
            $border_css=['border' => '1px solid red'];
            $visible_1=['display' => 'block'];
            $visible_2=['display' => 'none'];
          $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
            $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
            $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));

            //product_sponsored2
            //sponsored_slot1
            //sponsored_expiry1

            $arr=$ajax_response1->addCommand(new CssCommand('#pl_content_table td input[type="text"].sponsored_expiry'.$k, $border_css));


            //sponsored_expiry0

          $response_arr[]=$values['hid'.$k];

          if(trim($values['sponsored_expiry'.$k])<$current_date){
            $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Expiry date should not be less than todays date.'));

          }else{
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Slot not available, already taken by Rowid '. $values['eid'.$k].', try a different placement.'));
          }

          //$arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Invalid values, correct or uncheck selection to continue.'));

          }

          /*$nid = \Drupal::database()->update('product_sale')
          ->fields(array(
           'product_price' => trim($values['price'.$k]),
           'product_points'=>(float)$total_product_points,
           'product_rank'=>(float)$product_rank,
           'product_standard_shipping_cost' => trim($values['standard_shipping_cost'.$k]),
           'product_sponsored'=>trim($values['product_sponsored'.$k]),
           'product_spon_cat'=>trim($values['product_spon_cat'.$k]),
           'product_spon_sub'=>trim($values['product_spon_sub'.$k]),
           'product_spon_slot' => trim($values['sponsored_slot'.$k]),
           'product_spon_expires' => trim($values['sponsored_expiry'.$k]),
            'product_randomizer'=>$rand_str1,
            'product_update'=>$update_date,
          ))
          ->condition('product_sale_id',$values['hid'.$k], '=')
             ->execute();
         */
         
                  }else{
                    $visible_1=['display' => 'block'];
                    $visible_2=['display' => 'none'];
                  $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
                    $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
                    $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));
                    $border_css=['border' => '1px solid red'];
                    $arr=$ajax_response1->addCommand(new CssCommand('#pl_content_table td input[type="text"].product_sponsored'.$k, $border_css));

          $response_arr[]=$values['hid'.$k];
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Slot not available, already taken by Rowid '. $values['eid'.$k].', try a different placement.'));

          
         }

        }else{

          $border_css=['border' => '1px solid red'];
          $arr=$ajax_response1->addCommand(new CssCommand('#pl_content_table td input[type="text"].product_name'.$k, $border_css));

          $response_arr[]=$values['hid'.$k];

          $visible_1=['display' => 'block'];
          $visible_2=['display' => 'none'];
        $arr=$ajax_response1->addCommand(new CssCommand('#search_box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box', $visible_2));
          $arr=$ajax_response1->addCommand(new CssCommand('#box_new', $visible_1));

          //$arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
          $arr=$ajax_response1->addCommand(new HtmlCommand('#box_new', 'Invalid values, correct or uncheck selection to continue.'));


        }

         }
         if($nid){
          // drupal_
          $affected++;
          //$find_product_sale_id=array();  
          //$find_product_sponsored=array();
          //$find_product_spon_cat=array();
          //$find_product_spon_sub=array();
          //$find_product_spon_slot=array();
          //$find_product_spon_expires=array();
        }
  
    
  
    //}
    /*else if(trim($values['dist_name'.$k])==$chkresult->distributor_name){
  
      $response_arr[]=$values['hid'.$k];
      $border_css=['border' => '1px solid red'];
      $arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
     // $arr=$ajax_response1->addCommand(new CssCommand('#edit-ftpdir'.$k, $border_css));
  
      $visible=['display' => 'block'];
      $visible1=['display' => 'none'];
      
      $arr=$ajax_response1->addCommand(new CssCommand('.man_error', $visible));
      $arr=$ajax_response1->addCommand(new CssCommand('.custom_error', $visible1));
      
  
    }else{
      $response_arr[]=$values['hid'.$k];
      $border_css=['border' => '1px solid red'];
      $arr=$ajax_response1->addCommand(new CssCommand('#man_table td input[type="text"].dist_name'.$k, $border_css));
     // $arr=$ajax_response1->addCommand(new CssCommand('#edit-ftpdir'.$k, $border_css));
  
      $visible=['display' => 'block'];
      $visible1=['display' => 'none'];
      
      $arr=$ajax_response1->addCommand(new CssCommand('.man_error', $visible1));
      $arr=$ajax_response1->addCommand(new CssCommand('.custom_error', $visible));
  
    }   
  */
  
  
    }
     
    }

    $exploded=explode(",",$selected_chkbox);
$last_id=$exploded[0];
   
    if(count($response_arr)>0){
      $border_css=['border' => '1px solid red'];
    
      $str=count($response_arr);
      
      
      return $arr;
      }else{
  
   
    //$element = $form['form_content'];
    //$element['#markup'] = 'Selected lists are added succesfully. List will be refreshed now.<br><script>window.location.reload()</script>';
  
    
    //$renderer = \Drupal::service('renderer');
    
  
          $response = new AjaxResponse();
         // $response->addCommand(new HtmlCommand('#general_msg2','success'));
          //$response->addCommand(new InvokeCommand('#general_msg2','val',['success']));
        // $response->addCommand(new ReplaceCommand('#productlist_form_content', 
        // $renderer->render($form['plist'])));
        // $response->addCommand(new HtmlCommand('#box','<font color="green">Selected Rows Updated successfully.</font>'));
         
         $response->addCommand(new HtmlCommand('#general_msg1','<script>update_sale('.$last_id.','.$page.','.$affected.')</script>'));
         
         

        //return $form_state->setRebuild(TRUE);
         return $response;
    
     //$form_state['rebuild'] = TRUE;
     
      }
      //fclose($tfp1);
      //fclose($tfp2);
  
  }



  // Get the value from example select field and fill
// the textbox with the selected text.
function myAjaxCallback(array &$form, FormStateInterface $form_state) {



  //$arguments = $form_state->getValue(['file_format']['#arg']);
    $element= $form_state->getTriggeringElement()["#arg"];
  
  $selectedValue = $form_state->getValue('product_sponsored'.$element);
  //$selectedText = $form['product_sponsored'.$element]['#options'][$selectedValue];
  
  
   $renderer = \Drupal::service('renderer');


   $keys = array();
   $newArray = array();
 
   $base_path=base_path();
    // $table_path = \Drupal::service('file_system')->realpath("private://lists");
     $filename="lists/Categories.txt";
 
     if (($handle = fopen($filename, 'r')) !== FALSE) {
       $i = 0;
       while (($lineArray = fgetcsv($handle, 4000, "|", '"')) !== FALSE) {
         for ($j = 0; $j < count($lineArray); $j++) {
           $arr[$i][$j] = $lineArray[$j];
         }
         $i++;
       }
       fclose($handle);
     }
    
   
   $tmp = array();
   $category=array();
   foreach($arr as $arg)
   {
       $tmp[$arg[0]][] = $arg[1];
       
   }
   
   $output = array();
   $sub_category=array();
   
   foreach($tmp as $type => $labels)
   {
       $output[] = array(
           'category' => $type,
           'sub_category' => $labels
       );
       if($type!='All Departments' && $type!='No Override' && $type!='REJECT' && $type!='' && $type!='Vendor List'){
       $category[$type]=$type;
       $sub_category[$type]=$labels;
       }
   }
  
  
   $ajax_response1 = new AjaxResponse();

   //$ajax_response1->addCommand(new AlertCommand($element));

   if($selectedValue=='Y'){

    $renderedField = '';
  foreach ($category as $key => $value) {
    $renderedField .= "<option value='".$value."'>".$value."</option>";
  }

  $renderedField2 = '';
  foreach ($sub_category[$category['Computer & Office']] as $key1 => $value1) {
    $renderedField2 .= "<option value='".$value1."'>".$value1."</option>";
  }
  $renderedField2 .= "<option value='None'>None</option>";

  //$form['plist']['sponsored_slot'.$element]['#options']=array('1','2','0');
 // $slot_options_set=array('1', '2', '0');

  //$arr = array('1' => 'Nice way', '2' => 'Good way');
   // $form['plist']['sponsored_slot']['#options'] = array('1');
    //$form_state->setRebuild(TRUE);
    //$value_field1 = $form_state->getValue('sponsored_slot');

    //$form_state->setValue('sponsored_slot',1); 



  $ajax_response1->addCommand(new HtmlCommand('#edit-product-spon-cat'.$element,$renderedField));

  $ajax_response1->addCommand(new HtmlCommand('#product_spon_sub'.$element,$renderedField2));

  //$ajax_response1->addCommand(new InvokeCommand('#sponsored_slot', 'value', 1));


  //$ajax_response1->addCommand(new HtmlCommand('#sponsored_slot'.$element,1));

  return $ajax_response1;
}else{

//  $slot_options_reset=array(t('0'), t('1'), t('2'));

  $ajax_response1->addCommand(new HtmlCommand('#edit-product-spon-cat'.$element,''));
  $ajax_response1->addCommand(new HtmlCommand('#product_spon_sub'.$element,''));

  $ajax_response1->addCommand(new HtmlCommand('#sponsored_slot'.$element,''));
  return $ajax_response1;

}



  }

        /**
   * Ajax callback to change options for second field.
   */

public function changeOptionsAjax(array &$form, FormStateInterface $form_state) {

  $element1= $form_state->getTriggeringElement()["#arg"];
  $selectedValue = $form_state->getValue('product_spon_cat'.$element1);

  $keys = array();
  $newArray = array();

  $base_path=base_path();
   // $table_path = \Drupal::service('file_system')->realpath("private://lists");
    $filename="lists/Categories.txt";

    if (($handle = fopen($filename, 'r')) !== FALSE) {
      $i = 0;
      while (($lineArray = fgetcsv($handle, 4000, "|", '"')) !== FALSE) {
        for ($j = 0; $j < count($lineArray); $j++) {
          $arr[$i][$j] = $lineArray[$j];
        }
        $i++;
      }
      fclose($handle);
    }
   
  
  $tmp = array();
  $category=array();
  foreach($arr as $arg)
  {
      $tmp[$arg[0]][] = $arg[1];
      
  }
  
  $output = array();
  $sub_category=array();
  
  foreach($tmp as $type => $labels)
  {
      $output[] = array(
          'category' => $type,
          'sub_category' => $labels
      );
      if($type!='All Departments' && $type!='No Override' && $type!='' && $type!='Vendor List'){
      $category[$type]=$type;
      $sub_category[$type]=$labels;
      }
  }

 
  $ajax_response1 = new AjaxResponse();


 


  

  $renderedField = '';
  foreach ($sub_category[$selectedValue] as $key => $value) {

   // $ajax_response1->addCommand(new AlertCommand($value));
    //return $ajax_response1;

    $renderedField .= "<option value='".$value."'>".$value."</option>";
  }
  $renderedField .= "<option value='None'>None</option>";
  $ajax_response1->addCommand(new HtmlCommand('#product_spon_sub'.$element1,$renderedField));

    return $ajax_response1;
  


    
  }
  
  function delete_product_rank_entry(array &$form, FormStateInterface $form_state) {

  $result_for_updation = \Drupal::database()->select('process_control', 'pc')
  ->fields('pc', array('process_control_id','process_proc_max','process_php_time_max','process_php_mem_max'))
  ->condition('process_name','prodsale', '=')
  ->execute()->fetchObject();

  $process_proc_max = $result_for_updation->process_proc_max; 

  $process_php_time_max = $result_for_updation->process_php_time_max; 
  $process_php_mem_max = $result_for_updation->process_php_mem_max; 
  
  
  if($process_php_time_max!=0){
    ini_set('max_execution_time', $process_php_time_max);
    
  }
  
  if($process_php_mem_max!=' '){
  
    //ini_set('upload_max_filesize', $process_php_mem_max);
    $var_arr = preg_split('#(?<=\d)(?=[a-z])#i',$process_php_mem_max);
    $new_val=$var_arr[0];
    $new_val_ext=$var_arr[1];
    ini_set('memory_limit', $new_val.$new_val_ext);
  
  }

    $values = $form_state->getValues();
    $page = $values['dir_page'];
    $affected=0;
  
   for($k=0;$k<$values['total_values'];$k++)
    {
       if($values['hid'.$k]!="")
       {	
        if($affected<$process_proc_max){

        if(db_delete('product_sale')
                  ->condition('product_sale_id', $values['hid'.$k],'=')
                  ->execute()){
                    $affected++;

                  }
        }         
       
      }
     
    }
    //$element = $form['form_content'];

    //Start Code for updating product category count 
  $results = \Drupal::database()->select('category', 'c')
  ->fields('c', array('category_id'))            
  ->execute()->fetchAll();
  
    foreach($results as $key=>$node){
      $cat_id = $node->category_id;
      //$new_results = \Drupal::database()->query('SELECT count(product_sale_id) as pcatcount from {pasna01_product_sale} where product_cat_id='.$cat_id)->fetchField();
  
      $countnode = \Drupal::database()->query("SELECT COUNT(product_sale_id) AS count FROM {product_sale} WHERE product_cat_id = :product_cat_id", array(':product_cat_id' => $cat_id))->fetchField();
      
      \Drupal::database()->update('category')
      ->fields(array(
        'category_cat_sub_count' => $countnode,
      ))
      ->condition('category_id',$cat_id, '=')
      ->execute();
      
      }
  //End Code for updating product category count 
  
     
    //$renderer = \Drupal::service('renderer');
      $response = new AjaxResponse();
      //$response->addCommand(new ReplaceCommand('#productlist_form_content', 
      //$renderer->render($form['plist'])));
      //$response->addCommand(new HtmlCommand('#box','<font color="green">Selected Rows Deleted Successfully.</font>'));
      $response->addCommand(new HtmlCommand('#general_msg1','<script>delete_product('.$page.','.$affected.')</script>'));
        
      
      //$form_state->setRebuild(TRUE);
      return $response;
      
 
    
   //return $element;
  
  }

  public function truncate_number( $number, $precision = 2) {
    // Zero causes issues, and no need to truncate
    if ( 0 == (int)$number ) {
        return $number;
    }
    // Are we negative?
    $negative = $number / abs($number);
    // Cast the number to a positive to solve rounding
    $number = abs($number);
    // Calculate precision number for dividing / multiplying
    $precision = pow(10, $precision);
    // Run the math, re-applying the negative value to ensure returns correctly negative / positive
    return floor( $number * $precision ) / $precision * $negative;
}
  
public function truncate_decimals($number, $decimals=2)
{
  $factor = pow(10,$decimals); 
  $val = intval($number*$factor)/$factor;
  return round($val,1);
}
  
  }
  