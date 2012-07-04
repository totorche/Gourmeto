<?php
 
  require_once("include/headers.php");
  
  if (!test_right(15))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['pid']) || !is_numeric($_GET['pid']) || !isset($_GET['m'])){
    echo "<script type='text/javascript'>document.location='" .$_SERVER["HTTP_REFERER"] ."';</script>";
  }
  
  // nettoie l'url du referer
  $referer = $_SERVER["HTTP_REFERER"];
  
  try{
    $page = new Miki_page($_GET['pid']);
    
    // déplace la page
    if ($_GET['m'] == "up")
      $page->move_up();
    elseif ($_GET['m'] == "down")
      $page->move_down();
    elseif ($_GET['m'] == "to" && isset($_POST['pos']) && is_numeric($_POST['pos']))
      $page->move_to($_POST['pos']);
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La page a été déplacée avec succès");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors du déplacement de la page");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>