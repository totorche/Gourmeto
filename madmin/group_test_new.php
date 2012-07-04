<?php 
  require_once("include/headers.php");
  
  if (!test_right(2))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    $group = new Miki_group(); 
    $group->name = $_POST['name'];
    $group->state = $_POST['active'] == 1 ? 1:0;
    $group->save();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le groupe a été ajouté avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=71";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=72";
    // sauve les élément postés
    $_SESSION['saved_name']       = $_POST['name'];
    $_SESSION['saved_active']     = $_POST['active'] == 1 ? true : false;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>