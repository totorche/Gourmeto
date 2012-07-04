<?php

// vérifie si on tourne sous Windows ou pas
if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win32') !== false || 
    strpos($_SERVER['SERVER_SOFTWARE'], 'Win64') !== false)
  $is_windows = true;
else
  $is_windows = false;

if ($is_windows)
  ini_set("include_path", ini_get("include_path") .";../scripts");
else
  ini_set("include_path", ini_get("include_path") .":../scripts");



require_once("config.php");
require_once("../class/miki_language.php");
require_once("../class/miki_album.php");
require_once("../class/miki_album_picture.php");

$error = "";

// teste les données du formulaire
if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])){
	$error = 'Invalid Upload';
}

try{
  $url_base = $_POST['url_base'];
  $album = new Miki_album($_POST['album_id']);
  $pic = new Miki_album_picture();
  $pic->state = 1;
  $pic->folder = $album->folder;
  $pic->id_album = $album->id;
  $pic->position = $album->get_nb_pictures();
  
  $system = explode('.',$_FILES['Filedata']['name']);
  $ext = $system[sizeof($system)-1];
  $filename = substr($_FILES['Filedata']['name'], 0, strlen($_FILES['Filedata']['name']) - strlen($ext) - 1);
  $filename = $album->title[Miki_language::get_main_code()] .'-' .$filename; //.$album->get_nb_pictures();
  
  $langs = Miki_language::get_all_languages();
  foreach($langs as $l){
    $pic->title[$l->code] = '';
    $pic->description[$l->code] = '';
  }
  
  $pic->save();
  $pic->upload_picture($_FILES['Filedata'], $filename, $url_base .$pic->folder, $album->picture_width, $album->picture_height, $album->thumb_width, $album->thumb_height);
  
  // si la photo existe à double, on supprime le doublon (mais on garde le fichier)
  if ($pic->has_double()){
    $pic->delete(false);
  }
}catch(Exception $e){
  // supprime l'image de la base de données (pas du serveur car elle n'existe pas encore)
  $pic->delete(false);
  $error = $e->getMessage();
}

if ($error){
	$return = array(
		'status' => '0',
		'error' => $error
	);
}
else{
	$return = array(
		'status' => '1',
		'name' => $_FILES['Filedata']['name']
	);
	
  $pic_path = '../' .$pic->folder .'/' .$pic->filename;
 
	// Our processing, we get a hash value from the file
	//$return['hash'] = md5_file($_FILES['Filedata']['tmp_name']);
	$return['hash'] = md5_file($pic_path);
 
	// ... and if available, we get image data
	$info = @getimagesize($pic_path);
 
	if ($info) {
		$return['width'] = $info[0];
		$return['height'] = $info[1];
		$return['mime'] = $info['mime'];
	}
}

if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml') {
	// header('Content-type: text/xml');

	// Really dirty, use DOM and CDATA section!
	echo '<response>';
	foreach ($return as $key => $value) {
		echo "<$key><![CDATA[$value]]></$key>";
	}
	echo '</response>';
} else {
	// header('Content-type: application/json');

	echo json_encode($return);
}

?>