<?php
  require_once("include/headers.php");
  
  // si pas d'id d'article spécifié ou pas de nom d'image, on retourne à l'index
  if (!isset($_GET['eid']) || !is_numeric($_GET['eid']) || !isset($_GET['pic'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $element = new Miki_shop_article_option($_GET['eid']);     
    $element->delete_picture($_GET['pic']);
    
    $_SESSION['message_result_success'] = "La photo a été supprimée avec succès";
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La photo a été supprimée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=283&id=$element->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $_SESSION['message_result_error'] = "Une erreur est survenue lors de la suppression de la photo : <br />" .$e->getMessage();
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors de la suppression de la photo : <br />" .$e->getMessage());
    // puis redirige vers la page précédente
    $referer = "index.php?pid=283&id=$element->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    
  }
?>