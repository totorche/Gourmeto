<?php
  require_once("../../../../../../../../scripts/config.php");
  require_once("../../../../../../../../class/miki_video.php");
  require_once("../../../../../../../../class/miki_language.php");
  
  $videos = Miki_video::get_all_videos(false, "position", "ASC");
  $language = Miki_language::get_main_code();
  
  $return = "";
  foreach($videos as $v){
    //$temp[] = $p->name;
    $return .= "<option value='$v->id'>" .$v->title[$language] ."</option>\n";
  }
  
  //echo implode("&&", $temp);
  echo $return;
?>