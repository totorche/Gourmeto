<?php 
  require_once("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  $ids = explode(";",$_GET['id']);
  
  foreach($ids as $id){
    if (!is_numeric($id))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  $ids = implode(",", $ids);
  
  // supprime la relation entre les articles et cet attribut
  $sql = sprintf("DELETE FROM miki_shop_article_attribute_s_miki_shop_article WHERE miki_shop_article_attribute_id IN (%s)",
    mysql_real_escape_string($ids));
  $result = mysql_query($sql);
  
  $sql = sprintf("DELETE FROM miki_shop_article_attribute WHERE id IN (%s)",
    mysql_real_escape_string($ids));
  $result = mysql_query($sql);
  if (!$result){
    $referer = "index.php?pid=153";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Erreur lors de la suppression des attributs sélectionnés : ") ."<br />$sql<br />" .mysql_error();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }

  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Les attributs sélectionnés ont été supprimés avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=153";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>