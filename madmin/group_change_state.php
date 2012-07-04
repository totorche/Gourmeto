<?php 
  require_once("include/headers.php");
  
  if (!test_right(10))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }
  
  // si l'action par défaut a été donnée
  if ($_GET['a'] == '1' || $_GET['a'] == '0')
    $action = $_GET['a'];
  else
    $action = "";
  echo $_GET['id'] ."<br />";
  $pid = explode(";",$_GET['id']);
  
  foreach($pid as $p){
    if (!is_numeric($p))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  }

  // url du referer
  $referer = $_SESSION['url_back'];
  
  try{
    foreach($pid as $p){
      $group = new Miki_group($p); 
      $group->change_state($action);
    }
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'état des groupes sélectionnés a été modifié avec succès");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des groupes sélectionnés");
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>