<?php

/************************************************************************************************
 *
 * Ce script permet de comptabiliser l'affichage d'une newsletter.
 * 
 * L'affichage n'est comptabilisé que si le lecteur a affiché les images.  
 * 
 * Une fois la comptabilisation effectuée, on affiche une image transparente de 1px sur 1px  
 *
 ************************************************************************************************/     

if ((!isset($_GET['nid']) || !is_numeric($_GET['nid'])) || 
    (!isset($_GET['pid']) || !is_numeric($_GET['pid'])))
  exit();

require_once("config.php");
require_once("../class/miki_newsletter.php");

// compte l'ouverture de la newsletter
try{
  $nid = $_GET['nid'];
  $pid = $_GET['pid'];
  $n = new Miki_newsletter($nid);
  $n->set_opened($pid);
}
catch(Exception $e){}

// créé et renvoie une image de 1px sur 1px
header ("Content-type: image/gif");
$image = imagecreate(1,1);
$blanc = imagecolorallocate($image, 255, 255, 255);
imagecolortransparent($image, $blanc); // On rend le fond transparent
imagegif($image);

?>
