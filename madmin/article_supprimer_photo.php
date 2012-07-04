<?php
  require_once("include/headers.php");
  
  // si pas d'id d'article spécifié ou pas de nom d'image, on retourne à l'index
  if (!isset($_GET['aid']) || !is_numeric($_GET['aid']) || !isset($_GET['pic'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $article = new Miki_shop_article($_GET['aid']);     
    $article->delete_picture($_GET['pic']);
    
    $_SESSION['message_result_success'] = "La photo a été supprimée avec succès";
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La photo a été supprimée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=1452&id=$article->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $_SESSION['message_result_error'] = "Une erreur est survenue lors de la suppression de la photo : <br />" .$e->getMessage();
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors de la suppression de la photo : <br />" .$e->getMessage());
    // puis redirige vers la page précédente
    $referer = "index.php?pid=1452&id=$article->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    
  }
?>