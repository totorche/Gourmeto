<?php
  
  /**
   * Création d'image miniature
   * 
   * Si une erreur survient, une exception est levée
   *      
   * @param string $source Image initiale (chemin relatif par rapport à la racine du site web)
   * @param string $dest Image miniature (chemin relatif par rapport à la racine du site web)
   * @param int $new_w Largeur maximale de la miniature
   * @param int $new_h Hauteur maximale de la miniature   
   * @param boolean $replace Si la miniature doit remplacer l'image originale (l'image originale sera donc supprimée). False par défaut
   * @param boolean $to_grey Si true, on passe l'image en noir et blanc       
   *  
   * @return boolean   
   */   
  function createthumb($source, $dest, $new_w, $new_h, $replace = false, $to_grey = false){
  	$system=explode('.',$source);
  	if (preg_match('/jpg|jpeg/i', $system[sizeof($system)-1])){
      $src_img = imagecreatefromjpeg($source);
  		if (!$src_img)
        throw new Exception(_("Erreur pendant le redimensionnement de l'image"));
  	}
  	else if (preg_match('/png/i', $system[sizeof($system)-1])){
      $src_img = @imagecreatefrompng($source);
      if (!$src_img)
        throw new Exception(_("Erreur pendant le redimensionnement de l'image"));
  	}
  	else if (preg_match('/gif/i',$system[sizeof($system)-1])){
      $src_img = @imagecreatefromgif($source);
      if (!$src_img)
        throw new Exception(_("Erreur pendant le redimensionnement de l'image"));
  	}
  	else{
      throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));
    }

  	$old_x = imageSX($src_img);
    $old_y = imageSY($src_img);  
    
    if ($old_x < $new_w)
      $new_w = $old_x;
    if ($old_y < $new_h)
      $new_h = $old_y;
      
    if ($new_h > $new_w)
      $new_w = $new_h;
    elseif ($new_w > $new_h)
      $new_h = $new_w;
    
    if ($old_x > $old_y) {
    	$thumb_w = $new_w;
    	$thumb_h = $old_y * ($new_h / $old_x);
    }
    if ($old_x < $old_y) {
    	$thumb_w = $old_x * ($new_w / $old_y);
    	$thumb_h = $new_h;
    }
    if ($old_x == $old_y) {
    	$thumb_w = $new_w;
    	$thumb_h = $new_h;
    }
    
    // si on doit passer en noir/blanc
    if ($to_grey){
      $size = getimagesize($source);
      imagecopymergegray($src_img,$src_img,0,0,0,0,$size[0],$size[1],0);
    }
      
    $dst_img = ImageCreateTrueColor($thumb_w, $thumb_h);
    imagealphablending($dst_img, false);
    imagesavealpha($dst_img, true);
    if (!$dst_img)
      throw new Exception(_("Erreur pendant le redimensionnement de l'image"));
  	if (!imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y))
      throw new Exception(_("Erreur pendant le redimensionnement de l'image"));
  	
  	if (preg_match("/png/",$system[sizeof($system)-1]))
    {
      if (!imagepng($dst_img,$dest,4))
        throw new Exception(_("Erreur pendant le redimensionnement de l'image")); 
    } elseif (preg_match("/jpg|jpeg/",$system[sizeof($system)-1])) {
    	if (!imagejpeg($dst_img,$dest,100))
        throw new Exception(_("Erreur pendant le redimensionnement de l'image")); 
    } elseif (preg_match("/gif/",$system[sizeof($system)-1])) {
    	if (!imagegif($dst_img,$dest))
        throw new Exception(_("Erreur pendant le redimensionnement de l'image")); 
    }    
    imagedestroy($dst_img); 
    imagedestroy($src_img);
    
    // remplace l'image originale par l'image redimensionnée si demandé (paramètre $replace)
    if ($replace){
      copy($dest, $source);
      unlink($dest);
    }
    
    return true;
  }
  
  
  
  /**
   * Remplace les caractères non autorisés pour un nom de fichier
   * 
   * @param string $name chaîne à traiter
   * @return string chaîe traitée         
   */   
  function decode($name){
    $replace_source = array(" ","°","*","!","#","%","&","(",")","+",",","/",":","<",">","=","?","@","[","]","^","{","}","|","~","...","…",".","'","\"","À","Á","Â","Ã","Ä","Å","à","á","â","ã","ä","å","Ò","Ó","Ô","Õ","Ö","Ø","ò","ó","ô","õ","ö","ø","È","É","Ê","Ë","è","é","ê","ë","Ç","ç","Ì","Í","Î","Ï","ì","í","î","ï","Ù","Ú","Û","Ü","ù","ú","û","ü","ÿ","Ñ","ñ");
    $replace_dest   = array("-","" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,"" ,""   ,"" ,"" ,"" ,""  ,"a","a","a","a","a","a","a","a","a","a","a","a","o","o","o","o","o","o","o","o","o","o","o","o","e","e","e","e","e","e","e","e","c","c","i","i","i","i","i","i","i","i","u","u","u","u","u","u","u","u","y","n","n");
  
    $temp = mb_strtolower(str_replace($replace_source, $replace_dest, $name), 'UTF-8');
    $temp = preg_replace("@-{2,}@i","-",$temp);
  
    return $temp;
  }
  
  
  
  /**
   * Retourne la taille x et y du logo en fonction d'une taille maximale
   * 
   * @param string $path Chemin relatif de l'image (par rapport à la racine du site web)
   * @param int $max_height Hauteur maximale
   * @param int $max_width Largeur maximale
   * @return mixed Un tableau contenant en indice '0' la nouvelle largeur calculée et en indice '1' la nouvelle longueur calculée   
   */   
  function get_image_size($path, $max_height, $max_width){
    $size = getimagesize($path);
    $old_w = $size[0];
    $old_h = $size[1];
    
    $new_w = $max_width;
    $new_h = $max_height;
    
    // si on ne doit rien réduire, on rend tel quel
    if ($old_h <= $new_h && $old_w <= $new_w){
      $return[0] = $old_w;
      $return[1] = $old_h;
      return $return;
    }
    
    if ($old_w < $new_w)
      $new_w = $old_w;
    if ($old_h < $new_h)
      $new_h = $old_h;
    
    if ($new_w < $old_w) {
    	$thumb_w = $new_w;
    	$thumb_h = $old_h / $old_w * $new_w;
    	
    	if ($thumb_h > $new_h){
        $thumb_w = $old_w / $old_h * $new_h;
    	  $thumb_h = $new_h;
      }
    }
    elseif ($new_h < $old_h){
    	$thumb_w = $old_w / $old_h * $new_h;
    	$thumb_h = $new_h;
    	
    	if ($thumb_w > $new_w){
        $thumb_w = $new_w;
    	  $thumb_h = $old_h / $old_w * $new_w;
      }
    }
    elseif ($new_w == $new_h){
    	$thumb_w = $new_w;
    	$thumb_h = $new_h;
    }
    
    $return[0] = $thumb_w;
    $return[1] = $thumb_h;
    return $return;
  }
  
  /**
   * Cette fonction teste si une image donnée est valide
   *
   * @param string $path Le chemin de l'image
   *       
   * @return boolean
   */              
  function is_valide_picture($path){
    $system=explode('.',$path);
    if (preg_match('/jpg|jpeg/i', $system[sizeof($system)-1])){
      $src_img = imagecreatefromjpeg($path);
  	}
  	else if (preg_match('/png/i', $system[sizeof($system)-1])){
      $src_img = @imagecreatefrompng($path);
  	}
  	else if (preg_match('/gif/i',$system[sizeof($system)-1])){
      $src_img = @imagecreatefromgif($path);
  	}
  	else{
  	  return false;
    }
    
    if (!$src_img)
      return false;
    
    return true;
  }
  
?>