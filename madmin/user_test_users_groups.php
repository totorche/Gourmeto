<?php 
  require_once("include/headers.php");
  
  if (!test_right(9))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve les utilisateurs/groupes
  try{
    $groups = Miki_group::get_all_groups(false);
    $users = Miki_user::get_all_users();
    foreach($users as $user){
      $user->remove_groups();
      foreach($groups as $group){
        if (isset($_POST[$user->id .'_' .$group->id]))
          $user->add_group($group);
      }
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Les modifications a été apportées avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=64";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=64";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>