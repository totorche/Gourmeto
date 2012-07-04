<?php

// inclusion du script de connexion à la base de données
require_once("../../scripts/config.php");
include("../../class/miki_user.php");

//récupert le login et le mot de passe
$name = $_POST["login"];
$password = sha1($_POST["password"]);

$result = Miki_user::test_user($name, $password);
if ($result !== -1){
  session_start();
  $user = new Miki_user($result);
  $_SESSION['miki_admin_user_id'] = $result;
  
  // pour l'authentification des plugins MCImageManager et MCFileManager de TinyMCE
  $_SESSION['isLoggedIn'] = true;
  
  if (in_array  ('curl', get_loaded_extensions())){
    try{
      $postdata='url=http://' .$_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
      $ch1 = curl_init("http://www.fbw-one.com/acces_miki.php");
      curl_setopt ($ch1, CURLOPT_VERBOSE, 2);
      curl_setopt ($ch1, CURLOPT_ENCODING, 0);
      curl_setopt ($ch1, CURLOPT_USERAGENT, 'Mozilla/5.0');
      curl_setopt ($ch1, CURLOPT_POSTFIELDS, $postdata);
      curl_setopt ($ch1, CURLOPT_POST, 1);
      curl_setopt ($ch1, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt ($ch1, CURLOPT_FAILONERROR, 1);
      curl_setopt ($ch1, CURLOPT_HEADER, 1);
      curl_setopt ($ch1, CURLINFO_HEADER_OUT, 1);
      curl_setopt ($ch1, CURLOPT_CONNECTTIMEOUT, 30);
      $r=curl_exec ($ch1);
      $ch1_info=curl_getinfo($ch1);
      if (curl_errno($ch1)) return false;
      else curl_close($ch1);
    }
    catch(Exception $e){}
  }
  
  exit($user->default_page);
}
else
  exit('-1');

?>