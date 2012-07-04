<?php 
  require_once("include/headers.php");
  
  if (!test_right(1))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  if (!isset($_POST['name']) || !isset($_POST['password']) || !isset($_POST['password2']))
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  
  // sauve l'utilisateur
  try{
    if ($_POST['password'] !== $_POST['password2'])
      throw new Exception(_("Les mots de passe ne correspondent pas"));
      
    $user = new Miki_user(); 
    $user->name = $_POST['name'];
    $user->password = sha1($_POST['password']);
    $user->firstname = $_POST['firstname'];
    $user->lastname = $_POST['lastname'];
    $user->email = $_POST['email'];
    $user->state = $_POST['active'] == 1 ? 1:0;
    $user->default_page = $_POST['default_page'];
    $user->save();
    
    // affecte une clé API pour accéder à l'API Miki
    $user->apikey = md5($user->id .$_SERVER["SERVER_NAME"]);
    $user->update();
    
    // ajoute l'utilisateur aux groupes sélectionnés
    $groups = Miki_group::get_all_groups(false);
    foreach($groups as $group){
      if (isset($_POST[$group->name]) && $_POST[$group->name] == 1)
        $user->add_group($group);
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'utilisateur a été ajouté avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=61";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=62";
    // sauve les élément postés
    $_SESSION['saved_name']         = $_POST['name'];
    $_SESSION['saved_password']     = $_POST['password'];
    $_SESSION['saved_firstname']    = $_POST['firstname'];
    $_SESSION['saved_lastname']     = $_POST['lastname'];
    $_SESSION['saved_email']        = $_POST['email'];
    $_SESSION['saved_active']       = $_POST['active'] == 1 ? true : false;
    $_SESSION['saved_default_page'] = $_POST['default_page'];
    
    $saved_groups = "";
    $groups = Miki_group::get_all_groups(false);
    foreach($groups as $group){
      if (isset($_POST[$group->name]) && $_POST[$group->name] == 1)
        $saved_groups .= ";;$group->id";    
    }
    $_SESSION['saved_groups'] = $saved_groups;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>