<?php 
  require_once("include/headers.php");
  
  if (!test_right(10))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    $group = new Miki_group($_POST['id']); 
    $group->name = $_POST['name'];
    $group->state = $_POST['active'] == 1 ? 1:0; 
    $group->update();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le groupe a été modifié avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=71";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=73";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>