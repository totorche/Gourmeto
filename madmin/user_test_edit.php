<?php 
  require_once("include/headers.php");
  
  if (!test_right(9))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['id']) || !is_numeric($_POST['id']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    $user = new Miki_user($_POST['id']); 
    $user->name = $_POST['name'];
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $user->email = $_POST['email'];
    $user->state = $_POST['active'] == 1 ? 1:0;
    $user->default_page = $_POST['default_page'];
    
    // si le password n'a pas vide, on le change
    if ($_POST['password'] != ""){
      if ($_POST['password'] !== $_POST['password2'])
        throw new Exception(_("Les mots de passe ne correspondent pas"));
      
      $user->password = sha1($_POST['password']);  
    }
      
    $user->update();
    
    // retire l'utilisateur de tous les groupes
    $user->remove_groups();
    // puis ajoute l'utilisateur aux groupes sélectionnés
    $groups = Miki_group::get_all_groups(false);
    foreach($groups as $group){
      if (isset($_POST[$group->name]) && $_POST[$group->name] == 1)
        $user->add_group($group);
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'utilisateur a été modifié avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=61";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=63";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>