<?php
  require_once("include/headers.php");
  
  if (!test_right(64)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
    
  // si pas d'id spécifié ou pas de nom d'image, on retourne à la page précédente
  if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['file'])){
    $site_url = Miki_configuration::get('site_url');
    header("location: " .$_SESSION['url_back']);
    exit();
  }
  
  try{
    $event = new Miki_event($_GET['id']);
    $event->delete_file($_GET['file']);
    
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le fichier a été supprimé avec succès");
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }catch(Exception $e){
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Une erreur est survenue lors de la suppression du fichier :") ."<br />" .$e->getMessage();
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
?>