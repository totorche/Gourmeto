<?php
  
  require_once('include/headers.php');
  
  if (isset($_POST['cat_id']) && is_numeric($_POST['cat_id'])){  
    try{
      $category = new Miki_shop_article_category($_POST['cat_id']);
      $categories = $category->get_children();

      foreach($categories as $c){
        echo "$c->id&&" .htmlspecialchars_decode($c->name['fr'], ENT_NOQUOTES) ."%%";
      }
    }catch(Exception $e){
      echo "-1";
    }
  }
  else{
    echo "-1";
  }
?>