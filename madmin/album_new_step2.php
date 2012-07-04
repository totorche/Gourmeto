<?php

if (!test_right(40) && !test_right(41)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
if (isset($_GET['id']) && is_numeric($_GET['id'])){
  try{
    $album = new Miki_album($_GET['id']);
    $_SESSION['miki_album'] = $album;
  }
  catch(Exception $e){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
}
elseif (isset($_SESSION['miki_album'])){
  $album = $_SESSION['miki_album'];
}
else{
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
?>

<script type="text/javascript">
  <?php echo "var album_id = $album->id;"; ?>
  <?php echo "var url_base = '" .URL_BASE ."';"; ?>
</script>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/upload/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="scripts/upload/Swiff.Uploader.js"></script>
<script type="text/javascript" src="scripts/upload/FancyUpload2.js"></script>
<script type="text/javascript" src="scripts/upload/script.js"></script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=131"><?php echo _("Albums photo"); ?></a> > <?php echo _("Ajouter/modifier un album photo - étape 2"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter des photos à l'album") ." " .$album->title[Miki_language::get_main_code()]; ?></h1>
    
  <link rel="stylesheet" type="text/css" href="scripts/upload/style.css" />  
  
  <p style="margin-bottom:20px">Ajouter un ou plusieurs fichiers en cliquant sur "Parcourir" puis cliquez sur "Envoyer" pour débuter l'envoi<br /><br />
  <span style="text-decoration:underline">Important : </span><br />
  L'envoi des photos peut prendre beaucoup de temps selon la taille et le nombre de photos sélectionnées.<br />
  Ne recharger pas ou ne fermez pas cette page avant la fin du téléchargement.</p>
  
  <p style="margin-bottom:50px">
    L'album photo contient actuellement <span style="font-weight:bold"><?php echo $album->get_nb_pictures(); ?></span> photos
    &nbsp;&nbsp;&nbsp;
    <a href="index.php?pid=134" title="Continuer sans ajouter de nouvelles photos">Continuer sans ajouter de nouvelles photos</a>
  </p>
  
  
  <form action="album_picture_upload.php" method="post" enctype="multipart/form-data" id="form-demo">
    <input type="hidden" name="url_base" value="<?php echo URL_BASE; ?>" />
    
  	<fieldset id="demo-fallback">
  		<legend>File Upload</legend>
  		<label for="demo-photoupload">
  			Upload a Photo:
  			<input type="file" name="Filedata" />
  		</label>
  	</fieldset>
   
  	<div id="demo-status" class="hide">
  		<p>
  			<a href="#" id="demo-browse" class="button-select"></a>
  			<a href="#" id="demo-upload" class="button-start"></a>
  		</p>
  		<div style="clear:left;padding-top:20px">
  			<strong class="overall-title"></strong><br />
  			<img src="scripts/upload/pictures/bar.gif" class="progress overall-progress" />
  		</div>
  		<div style="float:left;margin-right:20px">
  			<strong class="current-title"></strong><br />
  			<img src="scripts/upload/pictures/bar.gif" class="progress current-progress" />
  		</div>
  		<div style="float:left">
  		  &nbsp;<br />
  		  <a href="#" id="demo-clear">Vider la liste</a>
  		</div>
  		<div style="clear:left" class="current-text"></div>
  	</div>
   
  	<ul id="demo-list"></ul>
   
  </form>
  
  
</div>