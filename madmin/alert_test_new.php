<?php 
  require_once("include/headers.php");
  
  if (!isset($_POST['alert'])){
    exit("0");
  }
  
  // sauve l'alerte
  try{
    $alert = new Miki_alert();
    $alert->id_person = 0;
    $alert->sentence = stripslashes(strip_tags($_POST['alert']));
    $alert->save();
    
    exit("1");
  }catch(Exception $e){
    exit("0");
  }
?>