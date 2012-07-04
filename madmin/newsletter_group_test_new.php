<?php 
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['name'])){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Veuillez entrer un nom";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve le membre
  try{
    // crée puis sauve le membre
    $group = new Miki_newsletter_group();
    $group->name = $_POST['name'];
    $group->save();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le groupe a été ajouté avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=1193";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=1194";
    
    // stock le groupe dans la session pour conserver les données
    $_SESSION['saved_group'] = $group;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>