<?php 
  require_once("include/headers.php");
  
  if (!test_right(10))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!test_right(1))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve les utilisateurs/groupes
  try{
    $groups = Miki_group::get_all_groups(false);
    $actions = Miki_action::get_all_actions();
    
    foreach($groups as $group){
      $group->remove_actions();
      foreach($actions as $action){
        if (isset($_POST[$action->id .'_' .$group->id]))
          $group->add_action($action);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les modifications a été apportées avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=74";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=74";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>