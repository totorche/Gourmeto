<?php
  require_once ("include/headers.php");
  
  if (!isset($_POST['pos'])) {
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $ordre = explode("|", $_POST['pos']);
  foreach($ordre as $o){
    $temp = explode("=", $o);
    if ($temp[0] != "" && is_numeric($temp[0])){
      $place = new Miki_place($temp[0]);
      $place->position = $temp[1];
      $place->update();
    }
  }
  
  echo "1";
?>