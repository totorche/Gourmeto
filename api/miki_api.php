<?php
  /**
   * API de Miki
   * 
   * Cette page doit être appelée pour tout appel à l'API Miki.
   * Cette page peut être appelée soit via la méthode POST soit via la méthode GET   
   * 
   * Tous les paramètres transmis doivent commencer par "mapi_"    
   *    
   * Les paramètres suivants sont obligatoires :
   *  
   *   mapi_username : Nom de l'utilisateur qui accède à l'API
   *   mapi_apikey : Clé API de l'utilisateur    
   *   mapi_callback : Fonction à appeler
   */
  
  /**
   * Retourne le résultat d'une opération ayant été exécutée avec succès
   * 
   * @param mixed $success_object Le résultat de l'opération (optionnel)
   * 
   * @return mixed Le résultat de l'opération encodé en JSON
   */      
  function mapi_success($success_object = ""){
    $success = array();
    $success['success'] = true;
    $success['result'] = $success_object;
    echo json_encode($success);
  }
  
  /**
   * Retourne le résultat d'une opération ayant échoué
   * 
   * @param mixed $error_msg Le message d'erreur
   * 
   * @return mixed Le résultat de l'opération encodé en JSON
   */      
  function mapi_error($error_msg){
    $success = array();
    $success['success'] = false;
    $success['error'] = $error_msg;
    echo json_encode($success);
  }
   
  // démarre la session
  session_start();
  
  // vérifie si on tourne sous Windows ou pas
  if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
      strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
    $is_windows = true;
  else
    $is_windows = false;
  
  if ($is_windows)
    ini_set("include_path", ".;..;/usr/local/lib/php;../include;../include/mail;../class;../scripts");
  else
    ini_set("include_path", ".:..:/usr/local/lib/php:../include:../include/mail:../class:../scripts");
    
  // inclusion du script de connexion à la base de données
  require_once("scripts/config.php");

  require_once("../class/miki_user.php");

  /**
   * Tableau contenant les fonctions disponibles avec comme index le nom de la fonction 
   * et comme valeur le fichier dans lequel se trouve la fonction
   */    
  $miki_rest_functions = array();
  $miki_rest_functions["get_news"] = "news.php";
  $miki_rest_functions["add_article"] = "shop_article.php";
  
  // récupert les paramètres passés à la page
  $mapi_param = array();
  foreach($_REQUEST as $key => $value) {
  	if(preg_match("/mapi_(.*)/", $key, $matches)) {
      if ($matches[1] == "username")
  		  $mapi_username = $value;
  		elseif ($matches[1] == "apikey")
  		  $mapi_apikey = $value;
  		elseif ($matches[1] == "callback")
  		  $mapi_callback = $value;
  		else
        $mapi_param[$matches[1]] = $value;
  	}
  }
  
  // vérifie si les paramètres obligatoires ont été fournis
  if (!isset($mapi_username) || 
      !isset($mapi_apikey) || 
      !isset($mapi_callback)){
    
    exit();
  }
  
  try{
    // récupert l'utilisateur avec lequel on tente de se connecter
    $user = new Miki_user();
    $user->load_from_name($mapi_username);

    // compare la clé API de l'utilisateur avec celle donnée
    if ($user->apikey != $mapi_apikey)
      exit();
    
    // vérifie si la fonctione demandée est dans les fonction de l'API Miki
    if (isset($miki_rest_functions[$mapi_callback])){
      
      // vérifie que le fichier contenant la fonction à appeler existe bien
      if (!file_exists($miki_rest_functions[$mapi_callback]))
        exit();
      
      // inclut le fichier contenant la fonction à appeler
      require_once($miki_rest_functions[$mapi_callback]);
      
      // vérifie que la fonction demandée existe bien
      if (!is_callable($mapi_callback))
        exit();
      
      // exécute la fonction demandée et retourne le résultat
      header("{$_SERVER['SERVER_PROTOCOL']} 200 OK");
      echo call_user_func($mapi_callback, $mapi_param);
    }else{
      // page 404
      header("{$_SERVER['SERVER_PROTOCOL']} 404 Not Found");
      exit();
    }
  }
  catch(Exception $e){
    exit();
  }
?>