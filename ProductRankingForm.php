<?php
/**
 * @file
 * Contains \Drupal\product_listing\Form\ProductRankingForm.
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

class ProductRankingForm extends FormBase {


/**
* {@inheritdoc}
*/
public function getFormId() {
  return 'prform';
}


/**
 * {@inheritdoc}
 */
public function buildForm(array $form, FormStateInterface $form_state,  $sort=NULL, $colname=NULL) {

  

//     $path = drupal_get_path('module', 'DFF_importer');
//	if (user_access('access by administrator')){

//	$form['#attributes'] = array('name' => 'ftp_con_form',
//	'onsubmit' => 'return valid()',
//	);

//$sort = \Drupal::request()->query->get('sort'); 
//$token_arr=array();

$path = \Drupal::request()->getpathInfo();
$arg  = explode('/',$path);
$colname = end($arg);
$sort = prev($arg);

//$token_arr[]=explode('?token=',$path);

$man_ids = explode('%2C',end($token_arr));

$query = \Drupal::request()->query->get('token');


$man_ids=explode(',',$query);



//echo $token[1];

$form_state->setCached(FALSE);

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



$form['#attributes'] = array('name' => 'prform','id' =>'prform',
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
  
	  $form['ftpcon'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="http://buyourusa.com/alphasite/admin/product/category/asc/name" class="btn btn-secondary btn-sm active" id="other_btn" role="button" aria-pressed="true">Product Category Listing</a>&nbsp;',
	    '#weight' => '-4',
	 ];

	 $form['combine_csv_link'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="http://buyourusa.com/alphasite/admin/product/productrank/asc/name" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="collection_btn">Product Rank Listing</a>&nbsp;',
	    '#weight' => '-3',
	 ];
 
	 $form['csvindb'] = [
	    '#type' => 'markup',
	    '#markup' => '<a href="#" role="button" aria-pressed="true" class="btn btn-secondary btn-sm active" id="other_btn">Product Sale Listing</a>',
	    '#weight' => '-2',
	 ];
 
	  $form['end'] = [
	    '#type' => 'markup',
	    '#markup' => '</div>',
	    '#weight' => '-1',
	 ];
	 

 $form['rank']['tbl_start'] = array(
    '#type' => 'markup',
    '#markup' => t('<div id = "form_content" style="padding-top:10px;"><table width = "100%" border = "0" id = "my_content_table" cellspacing = "1"><tr><td colspan = "8" align ="center"><h3>Product Rank Listing</h3></td></tr>
<tr id="header_row"><th id = "f_child">Select<br><input type="checkbox" id="sel" name="main_sel" onclick="checkAllCat(document.category_form.total_values.value)"></th><th id = "s_child">Name <span id="'.$active_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/asc/name" id="'.$active_lnk.'">^</a></span><span id="'.$inactive_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/desc/name"  id="'.$inactive_lnk.'">v</a></span><br/>Mfr. Name<br>Material%<br/></th><th id = "t_child">Updated <span id="'.$active_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/asc/date" id="'.$active_date_lnk.'">^</a></span><span id="'.$inactive_date_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/desc/date"  id="'.$inactive_date_lnk.'">v</a></span></br>Mfr. ID</br>Points</th><th id="fourth_child"><div id="forthchild">Operator<span id="'.$active_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/asc/operator" id="'.$active_op_lnk.'">^</a></span><span id="'.$inactive_op_lnk.'"><a href="http://buyourusa.com/alphasite/admin/product/productrank/desc/operator"  id="'.$inactive_op_lnk.'">v</a></span><br>&nbsp;<br></div></th></tr>'),
  ); 


  $keys = array();
  $newArray = array();
  

if($query!=""){
  $results = db_select('product_rank', 'p')
  ->fields('p', array('product_rank_id', 'manufacturer_rank_id', 'product_mfr_name', 'product_name','product_mfr_id','product_material','product_points','product_update','product_operator_id'))
  ->condition('manufacturer_rank_id',$man_ids, 'IN')

  ->execute()->fetchAll();

}else{
$results = db_select('product_rank', 'p')
->fields('p', array('product_rank_id', 'manufacturer_rank_id', 'product_mfr_name', 'product_name','product_mfr_id','product_material','product_points','product_update','product_operator_id'))            
->execute()->fetchAll();

}

    
$num_results = count($results);



$i = 0;
$col_num=1;
 $form['rank']['scroll_area_start'] = array(
    '#type' => 'markup',
    '#markup' => '<tr><td colspan = "4"><div id = "scroll_area"><table id = "scroll_table">',
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


$manufacturer_rank_id=array();
$product_rank_id=array();
$product_mfr_name=array();
$product_name=array();
$product_mfr_id=array();
$product_material=array();
$product_points=array();
$product_update=array();
$product_operator_id=array();
//$dff_collection_status=array();



 foreach($results as $key=>$node){
$product_rank_id[$key] = $node->product_rank_id;
$manufacturer_rank_id[$key]=$node->manufacturer_rank_id;
$product_mfr_name[$key]=$node->product_mfr_name;
$product_name[$key]=$node->product_name;
$product_mfr_id[$key]=$node->product_mfr_id;
$product_material[$key]=$node->product_material;
$product_points[$key]=$node->product_points;
$product_update[$key]=$node->product_update;
$product_operator_id[$key]=$node->product_operator_id;
//$dff_collection_status[$key]=$node->dff_collection_status;

}

for($k=0;$k<$trows;$k++){

//foreach($results as $node){
 $form['rank']['tbl_row'.$i] = array(
    '#type' => 'markup',
    '#markup' => '<tr><td id = "pr_scroll_td">',
  );
   
     $form['rank']['id['.$i.']'] = array(
     '#type' => 'checkbox',  
     "#default_value" => 0,
     '#attributes' => array('onclick' => 'if(document.forms["prform"].hid'.$i.'.value==""){ document.forms["prform"].hid'.$i.'.value="'.$product_rank_id[$i].'"; }else{ document.forms["prform"].hid'.$i.'.value="'.$i.'"; }'),
     );
            $form['rank']['hid'.$i] = array(
     '#type' => 'hidden',  
     "#default_value" => '', 
     );

     $form['rank']['eid'.$i] = array(
     '#type' => 'hidden',  
     "#default_value" => $product_rank_id[$i], 
     );
     
      $form['rank']['ch'.$i] = array(
    '#type' => 'markup',
    '#markup' => '</br>'.$col_num.'</td><td id="prodcol2">',
    
  );
  

     $form['rank']['product_name'.$i] = array(
      '#type' => 'markup',
      '#prefix' =>'',
			'#markup' => $product_name[$i],
			'#suffix' =>'<br><br><br>',         
      );
      

      $form['rank']['product_mfr_name'.$i] = array(
        '#type' => 'markup',
        '#prefix' =>'',
        '#markup' => $product_mfr_name[$i],
        '#suffix' =>'<br><br><br>',         
        );


     $form['rank']['product_material'.$i] = array(
      '#type' => 'textfield', 
      '#attributes' => array('id'=>'category_cat'.$i),
         '#default_value' => $product_material[$i], 
         '#size' => 32, 
         '#maxlength' => 255,     
      '#suffix' =>'</td><td id="prodcol3">',
  );



     $form['rank']['product_update'.$i] = array(
         '#type' => 'markup', 
        /* '#title' => t('FTP Password'),*/ 
         '#markup' => $product_update[$i],
         '#prefix' => '&nbsp;',
         '#suffix' =>'<br><br>',

     );


      $form['rank']['product_mfr_id'.$i] = array(
        '#type' => 'markup',
        '#prefix' => '<br>',
        '#suffix' => '<br>',
        '#markup' => $product_mfr_id[$i]."<br><br>",
    );

  

  $form['rank']['product_points'.$i] = array(
    '#type' => 'markup', 
    '#markup'=>$product_points[$i], 
      '#prefix' => '',
      '#suffix' =>'</td><td id="prodcol4">',

);



  //$form['category']['category_sub'.$i] = [
  //  '#type' => 'container',
  //  '#attributes' => ['id' => 'state-wrapper'],
  //  ];



    // $category_operator_arr= explode("#",$category_operator_id[$i]);
  

     $form['rank']['product_operator'.$i] = array(
      '#type' => 'markup', 
     /* '#title' => t('FTP Password'),*/ 
      '#markup' => $product_operator_id[$i],
      '#prefix' => '',
      '#suffix' =>'</td></tr>',

  );
  
     
$markup_id = 'box'.$i; 


$i++;
$col_num++;
}
$form['rank']['scroll_area_end'] = array(
    '#type' => 'markup',
    '#markup' => '</table></div></td></tr>',
   );


$form['rank']['box'] = array(
    '#type' => 'markup',
    '#prefix' => '<tr><td class="job_status" colspan="4"><div id="box">',
    '#suffix' => '</div></td></tr>',
    '#markup' => '',
  );    
  
  
  $form['rank']['td1'] = array(
    '#type' => 'markup',
    '#prefix' => '<tr><td colspan = "7" id = "btn_td"><table><tbody><tr class="tblborder"><td class="tblborder">',
    '#markup' => '',
  );    
  
   
    $form['rank']['td3'] = array(
    '#type' => 'markup',
    '#prefix' => '<td class="tblborder">',
    '#markup' => '',
  );    


  
$form['rank']['update_sel'] = array(
    	'#type' => 'button',
       '#ajax' => array(
      'callback' => '::update_product_rank_entry',
      'wrapper' => 'form_content',
      'name' => 'submit3',
    ),
        '#value' => t('Update Selection'),
       
        
     ); 
     
   
     $form['rank']['td6'] = array(
    '#type' => 'markup',
    '#suffix' => '</td>',
    '#markup' => '',
  );    


  $form['rank']['td9'] = array(
    '#type' => 'markup',
    '#prefix' => '<td class="tblborder">',
    '#markup' => '',
  );  
  
  $form['rank']['del_sel'] = array(
    '#type' => 'button',
     '#ajax' => array(
    'callback' => '::delete_product_rank_entry',
    'wrapper' => 'form_content',
    'name' => 'submit4',
  ),
      '#value' => t('Delete Selection'),
     
      
   ); 

   $form['rank']['td6'] = array(
    '#type' => 'markup',
    '#suffix' => '</td>',
    '#markup' => '',
  );    




  $form['rank']['td7'] = array(
    '#type' => 'markup',
    '#prefix' => '<td class="tblborder">',
    '#markup' => '',
  );  
  
  
$form['rank']['rank_sel'] = array(
    	'#type' => 'button',
        '#ajax' => array(
      'callback' => '::product_rank',
      'wrapper' => 'form_content',
      'name' => 'submit7',
    ),
        '#value' => t('Products Selection'),

       '#suffix' => '</td></tr></table></td></tr></table></div>',
    //      '#suffix' => '</td></tr>',
     ); 



$form['rank']['total_values'] = array(
    	'#type' => 'hidden',
        '#default_value' => $i,

 //       '#suffix' => '</td></tr></table></div>',
          
     ); 



     $form['rank']['#action'] = 'listdir';
     //$form['#attached']['library'][] = 'core/drupal.ajax';

   $form['rank']['#attached']['library'][] = 'product_listing/product_listing';
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

}

/**
 * {@inheritdoc}
 */
public function submitForm(array &$form, FormStateInterface $form_state) {

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


 }
 
 
function update_product_rank_entry(array &$form, FormStateInterface $form_state) {
  $values = $form_state->getValues();
  $response_arr=array();
  $arr=array();
  $ajax_response1 = new AjaxResponse();
  for($k=0;$k<$values['total_values'];$k++)
  {
	if($values['hid'.$k]!="")
    {	

   
      $nid = db_update('product_rank')
       ->fields(array(
         'product_material' => trim($values['product_material'.$k]),
         'product_update'=>date('Y-m-d h:i:s'),
       ))
       ->condition('product_rank_id',$values['hid'.$k], '=')
          ->execute();


  

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
 
  if(count($response_arr)>0){
    $border_css=['border' => '1px solid red'];
  
    $str=count($response_arr);
    
    return $arr;
    }else{

 
	$element = $form['form_content'];
	$element['#markup'] = 'Selected lists are added succesfully. List will be refreshed now.<br><script>window.location.reload()</script>';

	
	$renderer = \Drupal::service('renderer');


        $response = new AjaxResponse();
       $response->addCommand(new ReplaceCommand('#form_content', 
       $renderer->render($form['rank'])));
       $response->addCommand(new HtmlCommand('#form_content','<script>window.location.reload()</script>'));
      $form_state->setRebuild(TRUE);
       return $response;
	
	 //$form_state['rebuild'] = TRUE;
	 
    }

}

function delete_product_rank_entry(array &$form, FormStateInterface $form_state) {

  $values = $form_state->getValues();

 for($k=0;$k<$values['total_values'];$k++)
  {
     if($values['hid'.$k]!="")
     {	

      $man_deleted = db_delete('product_rank')
                ->condition('product_rank_id', $values['hid'.$k],'=')
                ->execute();
     
    }
   
  }
  $element = $form['form_content'];
 	
	$renderer = \Drupal::service('renderer');
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#form_content', 
    $renderer->render($form['rank'])));
    $response->addCommand(new HtmlCommand('#form_content','<script>window.location.reload()</script>'));
    $form_state->setRebuild(TRUE);
    return $response;
    
	
 //return $element;

}


}
