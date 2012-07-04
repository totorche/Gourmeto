<?php 
  require_once("include/headers.php");
  
  if (!test_right(4))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve la feuille de style
  try{
    $sh = new Miki_stylesheet(); 
    $sh->name = $_POST['name'];
    $sh->content = stripslashes($_POST['content']);
    $sh->save();
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("La feuille de style a été ajoutée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=11";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=12";
    // sauve les élément postés
    $_SESSION['saved_name'] = $_POST['name'];
    $_SESSION['saved_content'] = $_POST['content'];
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>