<?php
  
  require_once('include/headers.php');
  
  if (isset($_POST['cat_id']) && is_numeric($_POST['cat_id'])){  
    try{
      $category = new Miki_shop_article_category($_POST['cat_id']);
      
      $langs = Miki_language::get_all_languages();
      foreach($langs as $lang){
        echo $lang->code ."&&" .htmlspecialchars_decode($category->name[$lang->code], ENT_NOQUOTES) ."%%";
      }
    }catch(Exception $e){
      echo "-1";
    }
  }
  else{
    echo "-1";
  }
?>