<?php 
  require_once("include/headers.php");
  
  if (!isset($_POST['id'])){
    exit("0");
  }
  
  $ids = explode(";",$_POST['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id)){
      exit("0");
    }
  }
  
  try{
    foreach($ids as $id){
      $alert = new Miki_alert($id);
      $alert->delete();
    }
    
    exit("1");
  }catch(Exception $e){
    exit("0");
  }
?>