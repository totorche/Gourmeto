<?php
  require_once("include/headers.php");
  
  // si pas d'id d'article spécifié ou pas de nom d'image, on retourne à l'index
  if (!isset($_GET['aid']) || !is_numeric($_GET['aid']) || !isset($_GET['pid']) || !is_numeric($_GET['pid'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  try{
    $album = new Miki_album($_GET['aid']);
    $album->delete_picture($_GET['pid']);
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La photo a été supprimée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=135&id=$album->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors de la suppression de la photo : <br />" .$e->getMessage());
    // puis redirige vers la page précédente
    $referer = "index.php?pid=135&id=$album->id";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>