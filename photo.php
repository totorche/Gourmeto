<style type="text/css">
  
  .image_content{
    text-align: center;
  }
  
  .image{}
  
  .image_infos{}
  
  .image_title{}
  
  .image_place{
    margin-top: 20px;
  }
  
  .image_description{
    margin-top: 20px;
  }
  
</style>

<?php
  /**
   * Affiche une photo
   */
   
  include_once("scripts/functions_pictures.php");
   
  // récupert la photo
  if (isset($_REQUEST['eid']) && is_numeric($_REQUEST['eid'])){
    try{
      $element = new Miki_album_picture($_REQUEST['eid']);
    }
    catch(Exception $e){
      miki_redirect($_SESSION['url_back']);
    }
  }
  else{
    miki_redirect($_SESSION['url_back']);
  }
  
  // définit la largeur et la hauteur de l'image
  $max_width = 400;
  $max_height = 600;
  
  $title = addcslashes($element->title, '"');
  $file = $element->folder .$element->filename;
  
  // teste l'image
  if(!is_valide_picture($file)){
    echo _("La photo demandée n'a pas été trouvée ou contient des erreurs.");
  }
  elseif ($element->state == 0){
    echo _("La photo demandée n'a pas été publiée.");
  }
  else{
    $size = get_image_size($file, $max_height, $max_width);
    $width = $size[0] ."px";
    $height = $size[1] ."px";
    
    echo "<div class='image_content'>
            <div class='image'>
              <a href='$file' title=\"$title\" target='_blank'><img src='$file' alt=\"$title\" style='width: $width; height: $height;'></a>
            </div>
            <div class='image_infos'>
              <h2 class='image_title'>$element->title</h2>
              <p class='image_place'>$element->place</p>
              <p class='image_description'>$element->description</p>
            </div>
          </div>";
  }
?>