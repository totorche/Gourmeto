<?php

if (!test_right(40) && !test_right(41) && !test_right(42)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

if (isset($_GET['id'])){
  $album = new Miki_album($_GET['id']);
}
else{
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

if (isset($_GET['page']) && is_numeric($_GET['page']))
  $page = $_GET['page'];
else
  $page = 1;

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<style type="text/css">

  #main_table td{
    vertical-align: middle;
  }

</style>

<link rel="stylesheet" type="text/css" href="../scripts/milkbox/milkbox.css" />
<script type="text/javascript" src="../scripts/milkbox/mootools-1.2.3.1-assets.js"></script>
<script type="text/javascript" src="../scripts/milkbox/milkbox.js"></script>

<script type="text/javascript" src="scripts/mootools_more.js"></script>

<script type="text/javascript">
  
  var sort = '';
  var busy = false;
  
  window.addEvent('domready', function() {
    // Initialise la liste triable
    sort = new Sortables('.sortable', {
             //constrain: true,
          	 clone: false,
          	 opacity: 0.5,
          	 revert: true,
          	 onComplete: function(){
               sort.detach();
               busy = true;
               
               // on récupert leur ordre
                var ordre = sort.serialize(function(element, index){
                            return element.id + '=' + index;
                          }).join('|');
                var req = new Request({url:'album_repos.php', 
              		onSuccess: function(txt){
              		  busy = false;
                  }
                });
                req.send("pos=" + ordre + "&aid=<?php echo $album->id; ?>");
             }
           });
           
           
    sort.detach();
    
    Array.each($$('.enable_sortable'), function(el){
      el.addEvent('mousedown', function(){
        if (!busy){
          sort.attach();
        }
      });
      
      el.addEvent('mouseup', function(){
        //sort.detach();
      });
    });
  });
  
</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=131"><?php echo _("Albums photo"); ?></a> > <?php echo _("Les photos"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Albums photos") ." '$album->name'"; ?></h1>
  
  <?php
    // définit le nombre max d'éléments à afficher par page
    $max_elements = 20000;
    $max_per_line = 8;
    
    $width = $album->thumb_width + 10;
    $height = $album->thumb_height + 30;
    
    $pics = $album->get_pictures($max_elements, $page);
    $nb_pics = $album->get_nb_pictures();
  ?>
  
  <div style="width:<?php echo (($width + 10) * $max_per_line); ?>px;padding:10px; overflow: hidden; margin-bottom:10px">
    <div style="margin-bottom:10px; float: left;">
      <a href="index.php?pid=131" title="<?php echo _("Retour aux albums photos"); ?>"><?php echo _("Retour aux albums photos"); ?></a>
    </div>
    
    <?php if (test_right(41)){ ?>
      <div style="float: right;">
        <a href="index.php?pid=134&id=<?php echo $album->id; ?>" title="<?php echo _("Modifier les metas"); ?>"><?php echo _("Modifier les metas"); ?></a>
      </div>
    <?php } ?>
  </div>
    
  <div style="margin-bottom:10px">
    <a href="index.php?pid=133&id=<?php echo $album->id; ?>" title="<?php echo _("Ajouter des photos"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter des photos"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter des photos"); ?></a>
  </div>
    
  
  <ul id="main_table" style="width:<?php echo (($width + 10) * $max_per_line); ?>px;padding:10px; overflow: hidden" class='sortable'>

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
          
          $pic_text .= "Titre : " .stripslashes($pic->title[$_SESSION['lang']]);
        }
        if ($pic->description[$_SESSION['lang']] != ""){
          if ($pic_text != "")
            $pic_text .= "<br />";
          
          $pic_text .= "Description : " .stripslashes($pic->description[$_SESSION['lang']]);
        }
          
        echo "<li style='width:" .$width ."px;height:" .$height ."px;text-align:center; margin: 5px; float: left;' id='$pic->id'>
                <div style='margin:0 auto;border:solid 1px #cccccc;padding:5px;width:$thumb_width;height:$thumb_height'>
                  <a href='" .URL_BASE ."$pic->folder/$pic->filename' title=\"$pic_text\" rel='milkbox:miki_album'>
                    <img src='" .URL_BASE ."$pic->folder/thumb/$pic->filename' alt=\"" .$pic->title[Miki_language::get_main_code()] ."\" style='border:0'>
                  </a>
                </div>
                <a href='album_delete_picture.php?aid=$album->id&pid=$pic->id' class='delete' title='Supprimer cette photo'>Supprimer</a>&nbsp;&nbsp;<span style='cursor: pointer' class='enable_sortable'><img src='pictures/move.gif' style='vertical-align: middle' alt='+' /></span>
              </li>";
      }
    ?>
      
  </ul>
    
  <?php
    $nb_pages = (int)($nb_pics / $max_elements);
    $reste = ($nb_pics % $max_elements);
    if ($reste != 0)
      $nb_pages++;
    
    if ($nb_pages > 1){
      $code = "<div style='height:30px;text-align:right;margin-top:10px;padding-right:10px;width:" .($width * $max_per_line - 10) ."px'>";
      
      if ($page != 1)
        $code .= "<a href='index.php?pid=135&id=$album->id&page=1' title='première page'><<</a>&nbsp;&nbsp;<a href='index.php?pid=135&id=$album->id&page=" .($page-1) ."' title='page précédente'f><</a>&nbsp;&nbsp;";
      
      if ($nb_pages <= 12){
        for ($x=1; $x<=$nb_pages; $x++){
          if ($x == $page)
            $code .= "<span style='font-weight:bold'>$x</span> | ";
          else
            $code .= "<a href='index.php?pid=135&id=$album->id&page=" .($x) ."'>$x</a> | ";             
        }
      }
      elseif ($page == $nb_pages){
        for ($x=($page-12); $x<=$page; $x++){
          if ($x == $page)
            $code .= "<span style='font-weight:bold'>$x</span> | ";
          else
            $code .= "<a href='index.php?pid=135&id=$album->id&page=" .($x) ."'>$x</a> | ";             
        }
      }
      elseif ($page == 1){
        for ($x=$page; $x<=($page+12); $x++){
          if ($x == $page)
            $code .= "<span style='font-weight:bold'>$x</span> | ";
          else
            $code .= "<a href='index.php?pid=135&id=$album->id&page=" .($x) ."'>$x</a> | ";             
        }
      }
      elseif ($page != 1){
        $start = $page - 6;
        if ($start < 1)
        $start = 1;
        $stop = $start + 12;
        
        if ($stop > $nb_pages)
          $stop = $nb_pages;
          
        for ($x=$start; $x<=$stop; $x++){
          if ($x == $page)
            $code .= "<span style='font-weight:bold'>$x</span> | ";
          else
            $code .= "<a href='index.php?pid=135&id=$album->id&page=" .($x) ."'>$x</a> | ";             
        }
      }
      
      $code = mb_substr($code, 0, mb_strlen($code)-3);
      
      if ($page != $nb_pages)
        $code .= "&nbsp;&nbsp;<a href='index.php?pid=135&id=$album->id&page=" .($page+1) ."' title='page suivante'>></a>&nbsp;&nbsp;<a href='index.php?pid=135&id=$album->id&page=" .($nb_pages) ."' title='dernière page'>>></a>";
      
      $code .= "</div>";
      
      echo $code;
    }
  ?>
  
  <div style="margin-top:10px">
    <a href="index.php?pid=133&id=<?php echo $album->id; ?>" title="<?php echo _("Ajouter des photos"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter des photos"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter des photos"); ?></a>
  </div>
  
  <div style="margin-top:10px">
    <a href="index.php?pid=131" title="<?php echo _("Retour aux albums photos"); ?>"><?php echo _("Retour aux albums photos"); ?></a>
  </div>
    
</div>