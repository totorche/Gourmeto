<?php 
  require_once("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // vérifie que l'id de l'article a été donné
  if (!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."1';</script>";
    exit();
  }
  
  // vérifie que les id des sets d'options ont été donnés
  if (!isset($_REQUEST['sid'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."2';</script>";
    exit();
  }
  
  // vérifie si l'action (add, remove) a été donnée
  if (!isset($_REQUEST['action']) || !in_array($_REQUEST['action'], array('add', 'remove'))){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."3';</script>";
    exit();
  }
  
  $action = $_REQUEST['action'];
  $option_set_ids = explode(";",$_REQUEST['sid']);

  foreach($option_set_ids as $option_set_id){
    if (!is_numeric($option_set_id)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."4';</script>";
      exit();
    }
  }
  
  // url du referer
  $referer = "index.php?pid=1452&id=" .$_REQUEST['aid'];
  
  try{
    // récupert l'article
    $article = new Miki_shop_article($_REQUEST['aid']);
    
    // parcourt tous les sets donnés
    foreach($option_set_ids as $option_set_id){
      // ajoute ou supprime le set en cours (si on le supprime, on enlève également de l'article toutes les options liées au set)
      if ($action == 'add')
        $article->add_set($option_set_id);
      elseif ($action == 'remove'){
        $article->remove_set($option_set_id);
        $article->remove_all_options($option_set_id);
      }
    }
  }catch(Exception $e){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Une erreur est survenue lors la modification des sets sélectionnés : " .$e->getMessage());
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("Les sets sélectionnés ont été modifiés avec succès");
  // puis redirige vers la page précédente
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>