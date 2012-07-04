<?php
  require_once ("include/headers.php");
  
  if (!isset($_POST['pos']) || !isset($_POST['aid']) ||!is_numeric($_POST['aid'])) {
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $ordre = explode("|", $_POST['pos']);
  
  foreach($ordre as $o){
    $temp = explode("=", $o);
    $pic = new Miki_album_picture($temp[0]);
    $pic->position = $temp[1];
    $pic->save();
  }
  
  echo "1";
?>