<?php 
  require_once("include/headers.php");
  
  if (!test_right(32)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve la page
  try{
    $newsletter = new Miki_newsletter($_GET['id']);
    $newsletter->name .= " - copie";
    $newsletter->state = 0;
    unset($newsletter->id);
    $newsletter->save();

    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La newsletter a été créée avec succès");
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=114";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=114";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>