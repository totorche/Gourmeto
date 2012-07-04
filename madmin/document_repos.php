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
      $el = new Miki_document($temp[0]);
      $el->position = $temp[1];
      $el->update();
    }
  }
  
  echo "1";
?>