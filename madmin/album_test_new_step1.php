<?php 
  require_once("include/headers.php");
  
  if (!test_right(40) && !test_right(41)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve l'actualité
  try{
    if (isset($_POST['id']) && is_numeric($_POST['id']))
      $album = new Miki_album($_POST['id']);
    else
      $album = new Miki_album();
    
    $album->name = $_POST['name'];
    $album->picture_height = $_POST['picture_height'];
    $album->picture_width = $_POST['picture_width'];
    $album->thumb_height = $_POST['thumb_height'];
    $album->thumb_width = $_POST['thumb_width'];
    $album->state = $_POST['state'];
    $album->user_creation = $_SESSION['miki_admin_user_id'];
    $languages = Miki_language::get_all_languages();
    
    foreach ($languages as $l){
      $album->title[$l->code] = $_POST['title_' .$l->code];
      $album->description[$l->code] = $_POST['description_' .$l->code];
    }
    
    $album->save();
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'album a été créé/modifié avec succès");
    $_SESSION['miki_album'] = $album;
    
    // puis redirige vers la page précédente
    $referer = "index.php?pid=133";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    if (isset($_POST['id']) && is_numeric($_POST['id']))
      $referer = "index.php?pid=132&id=" .$_POST['id'];
    else
      $referer = "index.php?pid=132";
    // sauve les élément postés
    $_SESSION['saved_album'] = $album;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    echo $_SESSION['msg'];
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>