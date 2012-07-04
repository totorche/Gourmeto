<?php

// d�marre la session
require_once("../include/headers.php");

// v�rifie si on tourne sous Windows ou pas
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
    strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
  $is_windows = true;
else
  $is_windows = false;

if ($is_windows)
  ini_set("include_path", "..;/usr/local/lib/php;../include;../include/mail");
else
  ini_set("include_path", "..:/usr/local/lib/php:../include:../include/mail");



// inclusion du script de connexion � la base de donn�es
require_once("config.php");

include("../class/miki_account.php");

//r�cupert le login et le mot de passe
$login = $_POST["username"];
$password = $_POST["password"];

$result = Miki_account::test_user($login, $password);

if ($result != -1){
  $account = new Miki_account($result);

  // teste si le compte est activ�
  if ($account->state == 0)
    exit('-2');
  
  $_SESSION['miki_user_id'] = $account->person_id;
  $account->visit();
  
  // si on doit m�moriser la connexion du membre dans un cookie (valable 1 ann�e)
  if (isset($_POST['memorize']) && $_POST['memorize'] == 1){
    setcookie('miki_user_id', $account->person_id, (time() + (60 * 60 * 24 * 365)), '/');
  }
  
  exit("1");
}
else
  exit('-3');

?>