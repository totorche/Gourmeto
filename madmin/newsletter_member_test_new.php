<?php 
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['email'])){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Veuillez entrer une adresse e-mail";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['groups'])){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "Veuillez choisir un groupe d'abonnés";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // teste la validité de l'adresse e-mail donnée
  if (!Miki_newsletter::test_email($_POST['email'])){
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = "L'adresse e-mail n'est pas valide";
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // pour savoir si le membre a été sauvé ou pas
  $saved = false;
  
  // sauve le membre
  try{
    $member = new Miki_newsletter_member();
    
    // teste si le prénom ou le nom est donné
    if (isset($_POST['firstname']) && $_POST['firstname'] != ""){
      $member->firstname = $_POST['firstname'];
    }
    else{
      $member->firstname = "";
    }
    
    // ajoute les données
    if (isset($_POST['lastname']) && $_POST['lastname'] != ""){
      $member->lastname = $_POST['lastname'];
    }
    else{
      $member->lastname = "";
    }
    
    $member->email = $_POST['email'];
    
    // sauve le membre
    $member->save();
    
    $saved = true;
    
    $groups = $_POST['groups'];
    
    // ajoute le membre aux groupes spécifiés
    foreach($groups as $group){
      $member->add_to_group($group);
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'inscription a été effectuée avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=119";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=1191";
    // sauve les élément postés
    $_SESSION['saved_member'] = $member;
    
    // si la membre a déjà été sauvé, on le supprime
    if ($saved){
      $member->delete();
    }
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>