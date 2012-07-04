<?php
  /**
   * Affiche les photos d'un album photo
   */

  if (!isset($_REQUEST['aid']) || !is_numeric($_REQUEST['aid'])){
    $site_url = Miki_configuration::get('site_url');
    miki_redirect($site_url);
  }

  // récupération de la page qu'on veut afficher
  if (isset($_GET['p']) && is_numeric($_GET['p']))
	  $p = $_GET['p'];
  else
	  $p = 1;
  
	if(isset($_REQUEST['aid']) && is_numeric($_REQUEST['aid']))
	{	
  		$aid = $_REQUEST['aid'];
		try{
		$album = new Miki_album($aid);
		}
		catch(Exception $e){
		$site_url = Miki_configuration::get('site_url');
		miki_redirect($site_url);
		}		
	}
	else{
    $site_url = Miki_configuration::get('site_url');
		miki_redirect($site_url);
  }
	  
  // définit le nombre max d'éléments à afficher par page
  $max_elements = 24;
  $max_per_line = 6;
  
  $width = $album->thumb_width + 10;
  $height = $album->thumb_height + 10;
  
  $pics = $album->get_pictures($max_elements, $p);
  $nb_pics = $album->get_nb_pictures();
  
  $title = $album->title[$_SESSION['lang']];
  $description = $album->description[$_SESSION['lang']];

	?>



	<div style="padding:30px 15px 30px 15px">
	<style type="text/css">
	  
	  .photo_content{
			margin: 0 auto;
			border: solid 1px #A3A4A4;
			padding: 3px;
	  }
	  
	</style>

	<h1><?php echo $title; ?></h1>

	<h4><?php echo $description; ?></h4>

	<table style="margin:5px">

	  <?php
		$x = $max_per_line;
		foreach($pics as $pic){
		  $size = getimagesize(URL_BASE .$pic->folder ."/thumb/" .$pic->filename);
		  $thumb_width = $size[0] ."px";
		  $thumb_height = $size[1] ."px";
		  
		  // définit le texte qui sera écrit sous la grande image
		  $pic_text = "";
		  
		  if ($pic->place != "")
			$pic_text .= "Lieu : " .stripslashes($pic->place);
		  if ($pic->title[$_SESSION['lang']] != ""){
			if ($pic_text != "")
			  $pic_text .= "<br />";
			
			$pic_text .= stripslashes($pic->title[$_SESSION['lang']]);
		  }
		  if ($pic->description[$_SESSION['lang']] != ""){
			if ($pic_text != "")
			  $pic_text .= "<br />";
			
			$pic_text .= "Description : " .stripslashes($pic->description[$_SESSION['lang']]);
		  }
		  
		  /*if ($album->description[$_SESSION['lang']] != ""){
			$pic_text .= stripslashes($album->description[$_SESSION['lang']]);
		  }*/
					
		  $x--;
		  
		  if ($x == ($max_per_line - 1)){
			echo "<tr>
					<td style='width:" .$width ."px;text-align:center'>
					  <div class='photo_content' style='width:$thumb_width;height:$thumb_height'>
						<a href='" .URL_BASE ."$pic->folder/$pic->filename' title=\"$pic_text\" rel='milkbox:miki_album'>
						  <img src='" .URL_BASE ."$pic->folder/thumb/$pic->filename' alt=\"" .$pic->title[$_SESSION['lang']] ."\" style='border:0;' />
						</a>
					  </div>
					</td>";
		  }
		  elseif ($x > 0){
			echo "<td style='width:" .$width ."px;text-align:center'>
					<div class='photo_content' style='width:$thumb_width;height:$thumb_height'>
					  <a href='" .URL_BASE ."$pic->folder/$pic->filename' title=\"$pic_text\" rel='milkbox:miki_album'>
						<img src='" .URL_BASE ."$pic->folder/thumb/$pic->filename' alt=\"" .$pic->title[$_SESSION['lang']] ."\" style='border:0' />
					  </a>
					</div>
				  </td>";
		  }
		  elseif ($x == 0){
			echo "<td style='width:" .$width ."px;text-align:center'>
					<div class='photo_content' style='width:$thumb_width;height:$thumb_height'>
					  <a href='" .URL_BASE ."$pic->folder/$pic->filename' title=\"$pic_text\" rel='milkbox:miki_album'>
						<img src='" .URL_BASE ."$pic->folder/thumb/$pic->filename' alt=\"" .$pic->title[$_SESSION['lang']] ."\" style='border:0' />
					  </a>
					</div>
				  </td>
				</tr>
				<tr><td colspan='$max_per_line'>&nbsp;</td></tr>";
			$x = $max_per_line;
		  }
		}
		
		while ($x > 0 && $x != $max_per_line){
		  if ($x == 1)
			echo "<td style='width:" .$width ."px'>&nbsp;</td></tr>";
		  else
			echo "<td style='width:" .$width ."px'>&nbsp;</td>";
			
		  $x--;
		}
	  ?>
		
	</table>
	  
	<?php
	  $nb_pages = (int)($nb_pics / $max_elements);
	  $reste = ($nb_pics % $max_elements);
	  if ($reste != 0)
		$nb_pages++;
	  
	  if ($nb_pages > 1){
		$code = "<div style='text-align:right;padding:0 20px;'>";
		
		if ($p != 1)
		  $code .= "<a href='[miki_page='photos' params='aid=$aid&&p=1']' title='première page'><<</a>&nbsp;&nbsp;<a href='[miki_page='photos' params='aid=$aid&&p=" .($p-1) ."']' title='page précédente'f><</a>&nbsp;&nbsp;";
		
		if ($nb_pages <= 12){
		  for ($x=1; $x<=$nb_pages; $x++){
			if ($x == $p)
			  $code .= "<span style='font-weight:bold'>$x</span> | ";
			else
			  $code .= "<a href='[miki_page='photos' params='aid=$aid&&p=$x']'>$x</a> | ";             
		  }
		}
		elseif ($p == $nb_pages){
		  for ($x=($p-12); $x<=$p; $x++){
			if ($x == $p)
			  $code .= "<span style='font-weight:bold'>$x</span> | ";
			else
			  $code .= "<a href='[miki_page='photos' params='aid=$aid&&p=$x']'>$x</a> | ";             
		  }
		}
		elseif ($p == 1){
		echo $p;
		  for ($x=$p; $x<=($p+12); $x++){
			if ($x == $p)
			  $code .= "<span style='font-weight:bold'>$x</span> | ";
			else
			  $code .= "<a href='[miki_page='photos' params='aid=$aid&&p=$x']'>$x</a> | ";             
		  }
		}
		elseif ($p != 1){
		  $start = $p - 6;
		  if ($start < 1)
		  $start = 1;
		  $stop = $start + 12;
		  
		  if ($stop > $nb_pages)
			$stop = $nb_pages;
			
		  for ($x=$start; $x<=$stop; $x++){
			if ($x == $p)
			  $code .= "<span style='font-weight:bold'>$x</span> | ";
			else
			  $code .= "<a href='[miki_page='photos' params='aid=$aid&&p=$x']'>$x</a> | ";             
		  }
		}
		
		$code = substr($code, 0, strlen($code)-3);
		
		if ($p != $nb_pages)
		  $code .= "&nbsp;&nbsp;<a href='[miki_page='photos' params='aid=$aid&&p=" .($p+1) ."']' title='page suivante'>></a>&nbsp;&nbsp;<a href='[miki_page='photos' params='aid=$aid&&p=$nb_pages']' title='dernière page'>>></a>";
		
		$code .= "</div>";
		
		echo $code;
	  }
	?>    
	  
	  <a href="[miki_page='galerie_photos']">Retour aux albums photos</a>
	</div>