<?php
  $langues = Miki_language::get_all_languages();

  // récupert les valeur passées en GET
  $gets = $_GET;
  $get_req = "";
  foreach($gets as $key=>$val){
    if ($key != "p" && $key != "pn" && $key != "l")
      $get_req .= "&amp;" .urlencode($key) ."=" .urlencode($val);
  }
  
  $code = "";
  foreach($langues as $l){
    if ($l->code == $_SESSION['lang'])
      $code .= "<span style='color:#707070'>$l->code</span> | ";
    else
      $code .= "<span style='color:#a0a0a0'><a href='change_langue.php?p=$page->id&amp;l=$l->code" .$get_req ."' title=\"$l->name\" style='color:#a0a0a0'>$l->code</a></span> | ";
  }
  echo substr($code, 0, strlen($code) - 3);
?>