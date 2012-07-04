<?php 
  require_once("include/headers.php");
  
  if (!test_right(12))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $sh = new Miki_stylesheet($_POST['id']); 
    $sh->name = $_POST['name'];
    $sh->content = stripslashes($_POST['content']);
    $sh->update();
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La feuille de style a été mise à jour avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=11";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=13";
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>