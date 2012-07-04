<?php 
  require_once("include/headers.php");
  
  if (!test_right(40) && !test_right(41)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }

  if (isset($_SESSION['miki_album'])){
    $album = $_SESSION['miki_album'];
  }
  else{
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }

  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
  // pour savoir si toutes les langues ont le même titre et la même description
  $same_lang = $_POST['same_lang'] == 0 ? true : false;
  
  // met à jour l'album
  if (isset($_POST['cover_picture'])){
    $album->cover_picture = $_POST['cover_picture'];
    $album->update();
  }
  
  // met toutes les photos de l'album dans un tableau dont les index correspondent à l'id de la photo contenue
  $pics_temp = $album->get_pictures();
  $pics = array();
  foreach($pics_temp as $pic){
    $pics[$pic->id] = $pic;
  }
  
  $erreur = "";
  
  // parcourt chaque valeur envoyées et les affecte à la bonne photo
  foreach($_POST as $key => $value){
    $tab = explode("_", $key);
    
    if ($tab[0] == "place"){
      try{
        $pic = $pics[$tab[1]];
        $pic->place = $value;
      }catch(Exception $e){
        $erreur .= "Image n° " .$tab[1] ." - lieu : " .$e->getMessage() ."<br />";
      }
    }
    elseif ($tab[0] == "title"){
      try{
        $pic = $pics[$tab[1]];
        
        if (!$same_lang || $tab[2] == Miki_language::get_main_code())
          $pic->title[$tab[2]] = $value;
        
        // affecte le même titre à toutes les langues si demandé
        if ($same_lang && $tab[2] == Miki_language::get_main_code()){
          $langs = Miki_language::get_all_languages();
          foreach($langs as $l){
            $pic->title[$l->code] = $pic->title[Miki_language::get_main_code()];
          }
        }
      }catch(Exception $e){
        $erreur .= "Image n° " .$tab[1] ." - titre : " .$e->getMessage() ."<br />";
      }
    }
    elseif ($tab[0] == "description"){
      try{
        $pic = $pics[$tab[1]];
        
        if (!$same_lang || $tab[2] == Miki_language::get_main_code())
          $pic->description[$tab[2]] = $value;
        
        // affecte la même description à toutes les langues si demandé
        if ($same_lang && $tab[2] == Miki_language::get_main_code()){
          $langs = Miki_language::get_all_languages();
          foreach($langs as $l){
            $pic->description[$l->code] = $pic->description[Miki_language::get_main_code()];
          }
        }
      }catch(Exception $e){
        $erreur .= "Image n° " .$tab[1] ." - description : " .$e->getMessage() ."<br />";
      }
    }
  }
  
  // met à jour toutes les photos
  foreach($pics as $pic){
    $pic->update();
  }
  
  if ($erreur == ""){
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("L'album a été créé/modifié avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=131";
    
    // détruit la variable de l'album en cours de création/modification
    unset($_SESSION['miki_album']);
    
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
  else{
    $referer = "index.php?pid=134";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
?>