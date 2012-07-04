<?php
  require_once ("include/headers.php");
  
  if (!test_right(51) && !test_right(52) && !test_right(53)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  function decode($name){
    $replace_source = array(" ","°","*","!","#","%","&","(",")","+",",","/",":","<",">","=","?","@","[","]","^","{","}","|","~","...","…",".","'","\"","À","Á","Â","Ã","Ä","Å","à","á","â","ã","ä","å","Ò","Ó","Ô","Õ","Ö","Ø","ò","ó","ô","õ","ö","ø","È","É","Ê","Ë","è","é","ê","ë","Ç","ç","Ì","Í","Î","Ï","ì","í","î","ï","Ù","Ú","Û","Ü","ù","ú","û","ü","ÿ","Ñ","ñ");
    $replace_dest   = array("-","" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,""   ,"" ,"" ,"" ,""  ,"a","a","a","a","a","a","a","a","a","a","a","a","o","o","o","o","o","o","o","o","o","o","o","o","e","e","e","e","e","e","e","e","c","c","i","i","i","i","i","i","i","i","u","u","u","u","u","u","u","u","y","n","n");
    
    $temp = mb_strtolower(str_replace($replace_source, $replace_dest, $name), 'UTF-8');
    $temp = preg_replace("@-{2,}@i","-",$temp);
    
    return $temp;
  }
  
  $erreur = "";
  
  // teste que tous les champs obligatoires soient remplis
  if (!isset($_POST['name']) || $_POST['name'] == "")
    $erreur .= "<br />- Nom";
  if (!isset($_POST['type']) || $_POST['type'] == "")
    $erreur .= "<br />- Type";
    
  if ($erreur != ""){
    $erreur = "Les champs suivants sont obligatoires : " .$erreur;
    
    $referer = "index.php?pid=155";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $erreur;
    
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  $id = $_POST['id'];
  $nom = stripslashes($_POST['name']);
  $code = decode(stripslashes($_POST['name']));
  $type = $_POST['type'];
  
  // définit les valeurs de l'attribut
  
  // si c'est un champ texte
  if ($type == 0){
    $valeurs = "";
  }
  // si c'est un champ "oui/non"
  elseif ($type == 1){
    $valeurs = "Oui&&Non";
  }
  // si c'est une liste déroulante
  elseif ($type == 2){
    $tab_valeurs = $_POST['valeur'];
    $valeurs = array();
    foreach($tab_valeurs as $valeur){
      if ($valeur != ""){
        $valeurs[] = $valeur;
      }
    }
    $valeurs = implode("&&", $valeurs);
  }
  
  $sql = sprintf("UPDATE miki_shop_article_attribute SET name = '%s', code = '%s', type = %d, value = '%s' WHERE id = %d",
    mysql_real_escape_string($nom),
    mysql_real_escape_string($code),
    mysql_real_escape_string($type),
    mysql_real_escape_string($valeurs),
    mysql_real_escape_string($id));
  $result = mysql_query($sql);
  if (!$result){
    $referer = "index.php?pid=155";
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = _("Erreur lors de la modification de l'attribut dans la base de données : ") ."<br />$sql<br />" .mysql_error();
    // puis redirige vers la page précédente
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
    exit();
  }
  
  // ajoute le message à la session
  $_SESSION['success'] = 1;
  $_SESSION['msg'] = _("L'attribut a été modifié avec succès");
  // puis redirige vers la page précédente
  $referer = "index.php?pid=153";
  echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
?>