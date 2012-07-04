<?php
  require_once("../../../../../../../../scripts/config.php");
  require_once("../../../../../../../../class/miki_page.php");
  
  $pages = Miki_page::get_all_pages("name", false, "asc");
  
  $return = "";
  foreach($pages as $p){
    //$temp[] = $p->name;
    $return .= "<option value=\"$p->name\">$p->name</option>\n";
  }
  
  //echo implode("&&", $temp);
  echo $return;
?>