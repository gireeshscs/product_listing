<?php
/**
 * @file
 * Contains \Drupal\product_listing\Form\CategoryForm.
 */
namespace Drupal\product_listing\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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


class CategoryForm extends FormBase {

/**
* {@inheritdoc}
*/
public function getFormId() {
    return 'category_form';
}
  

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $sort=NULL, $colname=NULL) {

    $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    $user_name = $account->get('name')->value; 
  
    //$user_name_arr=explode("-",$user_name);
  
    $first_letter=substr($user_name, 0, 2);
    $last_letter=substr($user_name, -1);



$path = \Drupal::request()->getpathInfo();
$arg  = explode('/',$path);
//$colname = end($arg);
//$sort = prev($arg);

$colname = $arg[5];
  $sort = $arg[4];
  //$sk1 = $arg[6];

$form_state->setCached(FALSE);

$base_path=base_path();

//$host = \Drupal::request()->getHost()."/alphasite/admin/ranking/manufacture/";
$field_name1='';
$field_name2='';
$field_name3='';
$active_lnk='';
$inactive_lnk='';
$inactive_op_lnk='';
$active_op_lnk='';
$inactive_date_lnk='';
$active_date_lnk='';

if($sort=='asc' && $colname=='name'){
  $field_name1='category_cat';
  $field_name2='category_sub';
  //$field_name3='category_vend_sub';
  $active_lnk='sortnameasc';
  $inactive_lnk='';
}
if($sort=='desc' && $colname=='name'){
  $field_name1='category_cat';
  $field_name2='category_sub';
  //$field_name3='category_vend_sub';
  $inactive_lnk='sortnameasc';
  $active_lnk='';
}


if($sort=='asc' && $colname=='date'){
  $field_name1='category_update';
  $field_name2='category_update';
  //$field_name3='category_update';
  $active_date_lnk='sortdateasc';
  $inactive_lnk='';
}
if($sort=='desc' && $colname=='date'){
  $field_name1='category_update';
  $field_name2='category_update';
  //$field_name3='category_update';
  $inactive_date_lnk='sortdateasc';
  $active_lnk='';
}

if($sort=='asc' && $colname=='operator'){
  $field_name1='category_operator_id';
  $field_name2='category_operator_id';
  //$field_name3='category_operator_id';
  $active_op_lnk='sortopasc';
//  $inactive_lnk='';
}
if($sort=='desc' && $colname=='operator'){
  $field_name1='category_operator_id';
  $field_name2='category_operator_id';
  //$field_name3='category_operator_id';
  $inactive_op_lnk='sortopasc';
//  $active_lnk='';
}



$form['#attributes'] = array('name' => 'category_form','id' =>'category_form',
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
   
   $form['keyword'] = [
    '#type' => 'markup',
    '#markup' => '<a href="'.$base_path.'/admin/product/productkeyword/asc/name/null" class="btn btn-secondary btn-sm active" id="other_btn" role="button" aria-pressed="true">Product Keyword Listing</a>&nbsp;',
    '#weight' => '-4',
 ];
  
	  $form['ftpcon'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="'.$base_path.'/admin/product/category/asc/name" class="btn btn-secondary btn-sm active" id="collection_btn" role="button" aria-pressed="true">Product Category Listing</a>&nbsp;',
	    '#weight' => '-4',
	 ];

	 $form['combine_csv_link'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="'.$base_path.'/admin/product/productrank/asc/name/null" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="other_btn">Product Rank Listing</a>&nbsp;',
	    '#weight' => '-3',
	 ];
 
	 $form['csvindb'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="'.$base_path.'/admin/product/productlist/asc/name/null" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="other_btn">Product Sale Listing</a>',
	    '#weight' => '-2',
	 ];
 
	  $form['end'] = [
	    '#type' => 'markup',
	    '#markup' => '</div>',
	    '#weight' => '-1',
	 ];
	 
/*
 $form['category']['tbl_start'] = array(
    '#type' => 'markup',
    '#markup' => t('<div id = "form_content" style="padding-top:10px;"><table width = "100%" border = "0" id = "category_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Category Listing</h3></td></tr>
<tr id="header_row"><th id = "f_child">Select<br><input type="checkbox" id="sel" name="main_sel" onclick="checkAllCat(document.category_form.total_values.value)"></th><th id = "s_child">Name <span id="'.$active_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/asc/name" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/desc/name"  id="'.$inactive_lnk.'">v</a></span><br/>Category<br>Override<br/></th><th id = "t_child">Updated <span id="'.$active_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/asc/date" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/desc/date"  id="'.$inactive_date_lnk.'">v</a></span></br>SubCategory</br>Override</th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/asc/operator" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/category/desc/operator"  id="'.$inactive_op_lnk.'">v</a></span><br>&nbsp;<br>Source</div></th></tr>'),
  ); 
*/

$countnode = \Drupal::database()->query("SELECT COUNT(product_sale_id) AS psale_count FROM {product_sale}")->fetchField();



$pageno = \Drupal::request()->query->get('page');

$operated_rows = \Drupal::request()->query->get('rows');
$operation_status = \Drupal::request()->query->get('status');
$operation_name = \Drupal::request()->query->get('op');


$total_results = \Drupal::database()->select('category', 'c')
->fields('c', array('category_id'))
->execute()->fetchAll();

$tot_num_results = count($total_results);


$pmoderesult = \Drupal::database()->select('process_control', 'pc')
->fields('pc', array('process_control_id','process_read_max','process_page_max','process_page_row_max'))
->condition('process_name','prodcat', '=')
->execute()->fetchObject();

$process_read_max = $pmoderesult->process_read_max; 
$process_page_row_max = $pmoderesult->process_page_row_max; 
$process_page_max = $pmoderesult->process_page_max; 

$total_collections=$process_page_max*$process_page_row_max;

$perPage = $process_page_row_max;
$page = (isset($pageno)) ? (int)$pageno : 1;
$startAt = $perPage * ($page - 1);

$nxt=$page+1;


/*
if (isset($pageno)) {
 $pageno = $pageno;
} else {
 $pageno = 1;
}
*/

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

$lastPage = ceil($tot_num_results / $no_of_records_per_page);


if($page==$lastPage){
  $nxt=$page;
}


  $form['category']['tbl_start'] = array(
    '#type' => 'markup',
    '#markup' => t('<div id = "form_content" style="padding-top:10px;"><table width = "100%" border = "0" id = "category_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Category Listing ('.$countnode.')</h3></td></tr>
<tr id="header_row"><th id = "cat_f_child"><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page </span><div id="">&nbsp;<a href="'.$base_path.'/admin/product/category/'.$sort.'/'.$colname.'?page='.$prev.'" class="man_page_num_left"><</a><input type="hidden" id="admin_page" size="5px" value="category"><input type="hidden" id="existing_man_page_num" size="5px" value='.$page.'><input type="hidden" id="last_man_page" size="5px" value='.$lastPage.'><input type="hidden" id="sort_col1" size="5px" value='.$sort.'><input type="hidden" id="sort_col2" size="5px" value='.$colname.'><input type="number" id="man_page_num" size="5px" value='.$page.'><a href="'.$base_path.'/admin/product/category/'.$sort.'/'.$colname.'?page='.$nxt.'"" class="man_page_num_right">></a></div>&nbsp;&nbsp;&nbsp;&nbsp;Select</th><th id = "form_s_child">Category <span id="'.$active_lnk.'"><a href="'.$base_path.'/admin/product/category/asc/name" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="'.$base_path.'/admin/product/category/desc/name"  id="'.$inactive_lnk.'">v</a></span></th><th id = "cat_t_child1"><span>SubCategory</span></th><th id="cat_t_child2">Product Count</th><th id="cat_t_child3">Product Limit</th><th id = "cat_t_child">Updated <span id="'.$active_date_lnk.'"><a href="'.$base_path.'/admin/product/category/asc/date" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="'.$base_path.'/admin/product/category/desc/date"  id="'.$inactive_date_lnk.'">v</a></span></th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="'.$base_path.'/admin/product/category/asc/operator" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="'.$base_path.'/admin/product/category/desc/operator"  id="'.$inactive_op_lnk.'">v</a></span></div></th></tr>'),
  ); 


  $keys = array();
  $newArray = array();
  $base_path = base_path();
  // Function to convert CSV into associative array
  //function csvToArray($file, $delimiter)
  //{

  //  $table_path = \Drupal::service('file_system')->realpath("private://lists");
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
 // $newsub_category=array();
  
  foreach($tmp as $type => $labels)
  {
      $output[] = array(
          'category' => $type,
          'sub_category' => $labels
      );
      if($type!='All Departments' && $type!='REJECT' && $type!=''){
      $category[$type]=$type;
      $sub_category[$type]=$labels;
      //$newsub_category[$labels]=$labels;
      }
  }

/*
  $total_results = \Drupal::database()->select('category', 'c')
  ->fields('c', array('category_id'))
  ->execute()->fetchAll();

  $tot_num_results = count($total_results);
*/

  $total_pages = ceil($tot_num_results / $no_of_records_per_page);


        if($tot_num_results>$total_collections){
          $tot_num_results=$total_collections;
        }
        if(is_array($total_results)){
          $tot_cnt =   $tot_num_results;
         
          }else{
          $tot_cnt=0;
          
          }


 //Use Database API to retrieve the appropriate contact(s) 
      $results = \Drupal::database()->select('category', 'c')
        ->fields('c', array('category_id','category_cat','category_sub','category_cat_sub_count','category_cat_sub_limit','category_update','category_operator_id'))
        //->orderBy('category_vendor_name','category_vendor_cat','category_vendor_sub', 'ASC')
        ->orderBy($field_name1,$sort)
        ->orderBy($field_name2,$sort)
        //->orderBy($field_name3,$sort)
        ->range($offset, $no_of_records_per_page)               
        ->execute()->fetchAll();
    //  return $query; 

//$records = $results->fetchAll();
$num_results = count($results);


    
        $form_state->setCached(FALSE);
        $i = 0;
        
        if($pageno==1){
          $col_num=1;
        }else{
          $col_num=$pageno*$no_of_records_per_page-$no_of_records_per_page+1;
        }

 $form['category']['scroll_area_start'] = array(
    '#type' => 'markup',
    '#markup' => '<tr><td colspan = "4"><div id = "cat_scroll_area"><table id = "scroll_table">',
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


$category_id=array();
$category_cat=array();
$category_sub=array();
$category_cat_sub_count=array();
$category_update=array();
$category_operator_id=array();
//$category_operator_id=array();
$category_cat_sub_limit=array();



 foreach($results as $key=>$node){
$category_id[$key] = $node->category_id;
$category_cat[$key]=$node->category_cat;
$category_sub[$key]=$node->category_sub;
$category_cat_sub_count[$key]=$node->category_cat_sub_count;
$category_update[$key]=$node->category_update;
$category_operator_id[$key]=$node->category_operator_id;
//$category_operator_id[$key]=$node->category_operator_id;
$category_cat_sub_limit[$key]=$node->category_cat_sub_limit;
//$dff_collection_status[$key]=$node->dff_collection_status;

}

for($k=0;$k<$trows;$k++){



//foreach($results as $node){
 $form['category']['tbl_row'.$i] = array(
    '#type' => 'markup',
    '#markup' => '<tr><td id = "cat_scroll_td">',
  );
   
     $form['category']['id['.$i.']'] = array(
     '#type' => 'checkbox',  
     '#prefix' => '&nbsp;',
     "#default_value" => 0,
     '#attributes' => array('onclick' => 'if(document.forms["category_form"].hid'.$i.'.value==""){ document.forms["category_form"].hid'.$i.'.value="'.$category_id[$i].'"; }else{ document.forms["category_form"].hid'.$i.'.value="'.$i.'"; }','class'=>array('chk')),
     );
            $form['category']['hid'.$i] = array(
     '#type' => 'hidden',  
     "#default_value" => '', 
     );

     $form['category']['eid'.$i] = array(
     '#type' => 'hidden',  
     "#default_value" => $category_id[$i], 
     );
     
      $form['category']['ch'.$i] = array(
    '#type' => 'markup',
    '#markup' => ''.$col_num.'<br>('.$category_id[$i].')</td><td id="cat_prodcol2">',
    
  );
  

    //$sub_options = array();

    $form['category']['category_cat'.$i] = array(
      '#type' => 'markup', 
     /* '#title' => t('FTP Password'),*/ 
      '#markup' => $category_cat[$i],
      '#prefix' => '',
      '#suffix' =>'</td><td id="cat_prodcol3">',

  );


  $form['category']['category_sub'.$i] = array(
    '#type' => 'markup', 
   /* '#title' => t('FTP Password'),*/ 
    '#markup' => $category_sub[$i],
    '#prefix' => '',
    '#suffix' =>'</td><td id="cat_prodcol4">&nbsp;',

);


$form['category']['product_count'.$i] = array(
  '#type' => 'markup', 
 /* '#title' => t('FTP Password'),*/ 
  '#markup' => $category_cat_sub_count[$i],
  '#prefix' => '',
  '#suffix' =>'</td><td id="cat_prodcol5">',

);



$form['category']['product_limit'.$i] = array(
  '#type' => 'number', 
 /* '#title' => t('FTP Password'),*/ 
  //'#value' => $category_cat_sub_limit[$i],
  '#default_value' => $category_cat_sub_limit[$i], 
  '#size' => 5, 
  '#attributes' => array('id'=>'plimit'),
  '#maxlength' => 255, 
  '#prefix' => '',
  '#suffix' =>'</td><td id="cat_prodcol6">&nbsp;',

);


     $form['category']['category_update'.$i] = array(
         '#type' => 'markup', 
        /* '#title' => t('FTP Password'),*/ 
         '#markup' => $category_update[$i],
         '#prefix' => '',
         '#suffix' =>'</td><td id="cat_prodcol7">&nbsp;',

     );


     $form['category']['category_operator_id'.$i] = array(
      '#type' => 'markup', 
     /* '#title' => t('FTP Password'),*/ 
      '#markup' => $category_operator_id[$i],
      '#prefix' => '',
      '#suffix' =>'</td></tr>',

  );
 
     
$markup_id = 'box'.$i; 


$i++;
$col_num++;
}
$form['category']['scroll_area_end'] = array(
    '#type' => 'markup',
    '#markup' => '</table></div></td></tr>',
   );

   $form['category']['dir_page'] = array(
    '#type' => 'hidden',
    '#value' => $page,
  );     

  $form['category']['box_extra'] = array(
    '#type' => 'markup',
    '#markup' => '<tr><td class = "job_status" colspan = "4"><div id="box_new">&nbsp;</div></td></tr>',  
  );  

  if($operation_status=='y' && $operation_name=='update'){
    
    if($operated_rows==1){
      $mstext=' selection';
    }else{
      $mstext=' selections';
    }
  
    $form['category']['box'] = array(
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
  
    $form['category']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">&nbsp;'.$operated_rows. $mstext.' deleted successfully.',
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    );   
  }else if($operation_status=='y' && $operation_name=='cntupdate'){
  
  
    $form['category']['box'] = array(
      '#type' => 'markup',
      '#prefix' => '<tr><td class="job_status" colspan="4"><div id="search_box">&nbsp;Product category count updated successfully.',
      '#suffix' => '</div></td></tr>',
      '#markup' => '',
    );   
  }else{


$form['category']['box'] = array(
    '#type' => 'markup',
    '#prefix' => '<tr><td class="job_status" colspan="4"><div id="box">',
    '#suffix' => '</div></td></tr>',
    '#markup' => '',
  );    
}  
  $form['category']['general_msg'] = array(
    '#type' => 'markup',
    '#prefix' => '<tr><td colspan = "7" id="general_msg1"></td></tr>',
    '#markup' => '',
  );

  
  $form['category']['td1'] = array(
    '#type' => 'markup',
    '#prefix' => '<tr><td colspan = "7" id = "btn_td"><table><tbody><tr class="tblborder"><td class="tblborder">',
    '#markup' => '',
  );    

  if($first_letter=='O-' || $first_letter=='A-'){

  $form['category']['update_sel'] = array(
    '#type' => 'button',
    //'#attributes' => array(
    //  'class' => array('update-cat-class1'),
    //  ),
    //'#name' => 'submit4',
     '#ajax' => array(
    'callback' => '::update_category',
    'wrapper' => 'form_content',
    
  ),
      '#value' => t('Update Selection'),
     
      
   ); 

  }else{
    $form['category']['update_sel'] = array(
      '#type' => 'button',
      '#disabled' => true,
      '#value' => t('Update Selection'),
     ); 


  }
  
   
    $form['category']['td3'] = array(
    '#type' => 'markup',
    '#prefix' => '</td><td class="tblborder">',
    '#markup' => '',
  );    


  $form['category']['td9'] = array(
    '#type' => 'markup',
    '#prefix' => '<td class="tblborder">',
    '#markup' => '',
  );  
  

  if($first_letter=='A-'){
  $form['category']['del_sel'] = array(
    '#type' => 'button',
    //'#name' => 'submit4',
     '#ajax' => array(
    'callback' => '::delete_category',
    'wrapper' => 'form_content',
    
  ),
      '#value' => t('Delete Selection'),
     
      
   ); 
  }else{

    $form['category']['del_sel'] = array(
      '#type' => 'button',
      '#disabled' => true,
      '#value' => t('Delete Selection'),
     ); 
  }


  $form['category']['total_values'] = array(
    '#type' => 'hidden',
      '#default_value' => $i,

//       '#suffix' => '</td></tr></table></div>',
        
   ); 


   $form['category']['td6'] = array(
    '#type' => 'markup',
    '#suffix' => '</td>',
    '#markup' => '',
  );    




  $form['category']['td7'] = array(
    '#type' => 'markup',
    '#prefix' => '<td class="tblborder">',
    '#markup' => '',
  );  


  $form['category']['rank_sel'] = array(
    '#type' => 'button',
    '#value' => t('Product Sale 1 Selection'),
    '#attributes' => array('id' => 'from_cat_to_sale'),
    //'#submit' => array('::filter_product_sale_entry'),
    //'#name' => 'submit7',
    //  '#ajax' => array(
  //  'callback' => '::product_rank',
  //  'wrapper' => 'form_content',
    
//  ),
    //  '#value' => t('Product Sale Selection'),
    //  '#limit_validation_errors' => array(
    //    array(''), // Validate $form_state['values']['title'].
    //  ),

     '#suffix' => '</td><td class="tblborder">',
  //      '#suffix' => '</td></tr>',
   ); 

   

  
$form['category']['count_update_sel'] = array(
      '#type' => 'button',
      //'#name' => 'submit7',
      '#ajax' => array(
      'callback' => '::product_count_update',
      'wrapper' => 'form_content',    
    ),
        '#value' => t('Product Count Update'),
       '#suffix' => '</td></tr></table></td></tr></table></div>',
    //      '#suffix' => '</td></tr>',
     ); 







     $form['category']['#action'] = 'listdir';
     //$form['#attached']['library'][] = 'core/drupal.ajax';

   $form['category']['#attached']['library'][] = 'product_listing/product_listing';
   //  $form['collection']['#attached']['library'][] = 'dff_importer/custom_ajax';
   //$form['collection']['#attached']['library'][] = 'core/drupal.dialog.ajax';
     

	 $content = $form;
	 $themes=array('content' => $content);
return $form;
  }

  /**
 * {@inheritdoc}
 */
public function validateForm(array &$form, FormStateInterface $form_state) {


}

/**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  //  $form_state->setRebuild(TRUE);
/*
  $values = $form_state->getValues();
  $hid_arr='';
      for($k=0;$k<$values['total_values'];$k++)
        {
          if($values['hid'.$k]!="")
       {	
  
        
         $hid_arr.=$values['hid'.$k].',';
  
       }
      }
      
  $path = '/admin/product/productlist/asc/name/';
  // query string
  $path_param = [
   'manid' => $hid_arr,
  ];
  
  $url = Url::fromRoute('product_listing.productlist', ['sort' => 'asc','colname'=>'name'], ['query' => ['page'=>'cat','token' => $hid_arr]]);
  
  $form_state->setRedirectUrl($url);

*/


   }


   public function filter_product_sale_entry(array &$form, FormStateInterface $form_state){

    $form_state->clearErrors();

    $values = $form_state->getValues();
    $hid_arr='';
        for($k=0;$k<$values['total_values'];$k++)
          {
            if($values['hid'.$k]!="")
        {	

          
          $hid_arr.=$values['hid'.$k].',';

        }
        }
        
    $path = '/admin/product/productlist/asc/name/';
    // query string
    $path_param = [
    'manid' => $hid_arr,
    ];

    $url = Url::fromRoute('product_listing.productlist', ['sort' => 'asc','colname'=>'name'], ['query' => ['page' => 'cat','token' => $hid_arr]]);

    $form_state->setRedirectUrl($url);
  }
   
public function update_category(array &$form, FormStateInterface $form_state){
  $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
  $user_name = $account->get('name')->value;

$result_for_updation = \Drupal::database()->select('process_control', 'pc')
->fields('pc', array('process_control_id','process_proc_max','process_php_time_max','process_php_mem_max'))
->condition('process_name','prodcat', '=')
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

//$nidupdate='';
$checked_id=array();
  $exploded=array();
  $selected_chkbox='';


$values = $form_state->getValues();

$page = $values['dir_page'];

$response_arr=array();
$arr=array();
$ajax_response1 = new AjaxResponse();
//$ajax_response=array();



$msg = "";

$max=1;

//for($k=0;$k<$values['total_values'];$k++)
//$process_proc_max
$affected=0;
for($k=0;$k<$values['total_values'];$k++)
{
 
  if($values['hid'.$k]!="")
  {	
    
     if($affected<$process_proc_max){

      $checked_id[]=$values['hid'.$k];
      $selected_chkbox.=$k.",";


      $nidupdate = \Drupal::database()->update('category')
    ->fields(array(
      'category_cat_sub_limit' => trim($values['product_limit'.$k]),
      //'product_keyword_phrase' => trim($values['keyword_phrase'.$k]),
     // 'product_keyword_cat' => trim($values['keyword_cat'.$k]),
     // 'product_keyword_sub'=>trim($values['keyword_sub'.$k]),
      'category_operator_id' => $user_name,
    ))
    ->condition('category_id',$values['hid'.$k], '=')
    ->execute();

     }
     if($nidupdate){
      $affected++;
    }else{
      $response_arr[]=$values['hid'.$k];
    }   
}
/*
if(!$nidupdate){
// $border_css=['border' => '1px solid red'];
$response_arr[]=$values['hid'.$k];
$border_css=['border' => '1px solid red'];
$arr=$ajax_response1->addCommand(new CssCommand('#edit-ftpsuffix'.$k, $border_css));
$arr=$ajax_response1->addCommand(new CssCommand('#edit-ftpdir'.$k, $border_css));
$visible=['display' => 'block'];
$arr=$ajax_response1->addCommand(new CssCommand('.custom_error', $visible));
}
*/

}
$exploded=explode(",",$selected_chkbox);
$last_id=$exploded[0];


if(count($response_arr)>0){


return $arr;
}else{

  $response = new AjaxResponse();
  // $response->addCommand(new HtmlCommand('#general_msg2','success'));
   //$response->addCommand(new InvokeCommand('#general_msg2','val',['success']));
 // $response->addCommand(new ReplaceCommand('#productlist_form_content', 
 // $renderer->render($form['plist'])));
  //$response->addCommand(new HtmlCommand('#box','<font color="green">Selected Rows Updated successfully.</font>'));
  
  $response->addCommand(new HtmlCommand('#general_msg1','<script>update_cat('.$last_id.','.$page.','.$affected.')</script>'));

//$ajax_response = new AjaxResponse();
//$element = $form['form_content'];
//\Drupal::messenger()->addMessage($this->t('Form Submitted Successfully'), 'status', TRUE); 
  //$renderer = \Drupal::service('renderer');
  //$response = new AjaxResponse();
  //$response->addCommand(new ReplaceCommand('#form_content',$renderer->render($form['category'])));
  //$response->addCommand(new HtmlCommand('#form_content','<script>window.location.reload()</script>'));
  //return $form_state->setRebuild(TRUE);
  return $response;
  
}
//return $element;
}   



function product_count_update(array &$form, FormStateInterface $form_state) {

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
    
    //$renderer = \Drupal::service('renderer');
    $response = new AjaxResponse();
    //$response->addCommand(new ReplaceCommand('#form_content', 
    //$renderer->render($form['category'])));
   // $response->addCommand(new HtmlCommand('#form_content','<script>window.location.reload()</script>'));

    //$response->addCommand(new HtmlCommand('#box','<font color="green">Product category count updated successfully.</font>'));
    $response->addCommand(new HtmlCommand('#general_msg1','<script>cat_count_update()</script>'));


    //$form_state->setRebuild(TRUE);
    return $response;
    

}

//Function for deleting category
function delete_category(array &$form, FormStateInterface $form_state) {
  $account = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
  $user_name = $account->get('name')->value;

  $result_for_updation = \Drupal::database()->select('process_control', 'pc')
  ->fields('pc', array('process_control_id','process_proc_max','process_php_time_max','process_php_mem_max'))
  ->condition('process_name','prodcat', '=')
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
  $response_arr=array();
  $arr=array();
  $ajax_response1 = new AjaxResponse();


  $values = $form_state->getValues();
  $page = $values['dir_page'];

      $affected=0;

 for($k=0;$k<$values['total_values'];$k++)
  {
     if($values['hid'.$k]!="")
     {
      
      if($affected<$process_proc_max){


            $parent_deleted = \Drupal::database()->delete('category')
                      ->condition('category_id', $values['hid'.$k],'=')
                      ->execute();   
                      
                      $parent_prod_deleted = \Drupal::database()->delete('product_sale')
                      ->condition('product_cat_id', $values['hid'.$k],'=')
                      ->execute();  
                      
                      //$parent_prod_deleted = \Drupal::database()->delete('product_sale')
                      //->condition('category_id', $values['hid'.$k],'=')
                      //->execute();  

      }

if($parent_deleted){
  $affected++;
}else{
 $response_arr[]=$values['hid'.$k];

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
    //$response->addCommand(new ReplaceCommand('#form_content', 
    //$renderer->render($form['category'])));
    //$response->addCommand(new HtmlCommand('#form_content','<script>window.location.reload()</script>'));

    //$response->addCommand(new HtmlCommand('#box','<font color="green">Selected Rows Deleted Successfully.</font>'));
      $response->addCommand(new HtmlCommand('#general_msg1','<script>delete_cat('.$page.','.$affected.')</script>'));

    //$form_state->setRebuild(TRUE);
    return $response;
    

	 
 //return $element;

}





}
