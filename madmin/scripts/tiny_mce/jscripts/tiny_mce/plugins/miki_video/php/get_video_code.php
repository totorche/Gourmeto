<?php
  require_once("../../../../../../../../scripts/config.php");
  require_once("../../../../../../../../class/miki_configuration.php");
  require_once("../../../../../../../../class/miki_language.php");
  require_once("../../../../../../../../class/miki_video.php");

  if (!isset($_REQUEST['vid']) || !is_numeric($_REQUEST['vid']) || 
      !isset($_REQUEST['vtype']) || !is_numeric($_REQUEST['vtype']) ||
      !isset($_REQUEST['h']) || !is_numeric($_REQUEST['h']) || 
      !isset($_REQUEST['w']) || !is_numeric($_REQUEST['w'])){
    exit();
  }
  
  $vid = (int)$_REQUEST['vid'];
  $type = (int)$_REQUEST['vtype'];
  $height = (int)$_REQUEST['h'];
  $width = (int)$_REQUEST['w'];
  $styles = (isset($_REQUEST['styles'])) ? $_REQUEST['styles'] : "";
  $target = (isset($_REQUEST['target'])) ? $_REQUEST['target'] : "";

  if ($type != 1 && $type != 2){
    exit();
  }
  
  try{
    $video = new Miki_video($vid);
    $code = "";

    // vidéo complète
    if ($type == 1){
      ob_start();
      $video->print_video($width, $height, $styles); 
      $code = ob_get_contents();
      ob_end_clean();
    }
    // image avec lien sur la vidéo
    elseif ($type == 2){
      ob_start();
      $video->print_thumb($width, $height, "miki_video_$vid", $styles, $target);
      $code = ob_get_contents();
      ob_end_clean();
    }
    
    echo $code;
  }
  catch(Exception $e){
    exit();
  }
?>