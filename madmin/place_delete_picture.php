<?php
  require_once("include/headers.php");
  
  if (!test_right(69)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
    
  // si pas d'id spécifié ou pas de nom d'image, on retourne à la page précédente
  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['pic'])){
    $site_url = Miki_configuration::get('site_url');
    header("location: " .$_SESSION['url_back']);
    exit();
  }
  
  try{
    $place = new Miki_place($_GET['id']);
    $place->delete_picture($_GET['pic']);
    
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La photo a été supprimée avec succès");
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }catch(Exception $e){
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Une erreur est survenue lors de la suppression de l'image :") ."<br />" .$e->getMessage();
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
?>