<?php
  require_once("../../../../../../../../scripts/config.php");
  require_once("../../../../../../../../class/miki_page.php");
  
  $pages = Miki_page::get_all_pages("name", false, "asc");
  
  foreach($pages as $p){
    $temp[] = $p->name;
  }
  
  echo implode("&&", $temp);
?>