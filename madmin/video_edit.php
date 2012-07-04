<head>
  <?php
    if (!test_right(72)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("../config/video_category.php");
    require_once("functions_pictures.php");
    
    // si pas d'id spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
    
    try{
      $video = new Miki_video($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  
  <link rel="stylesheet" type="text/css" href="../css/iconize.css" />
  
  <link rel="stylesheet" type="text/css" href="../scripts/milkbox/milkbox.css" />
  <script type="text/javascript" src="../scripts/milkbox/mootools-1.2.3.1-assets.js"></script>
  <script type="text/javascript" src="../scripts/milkbox/milkbox.js"></script>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_edit_video'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
                                      
      tabs = new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
    });
    
    // copie le contenu de la langue en cours dans les autres langues
    function copy_content(lang){
      // récupert les éléments de la langue actuelle
      var title = $('title_' + lang).value;
      var description = $('description_' + lang).value;
      
      // parcourt toutes les langues
      $$('.lang_content').each(function(el){
        // si la langue n'est pas la langue actuelle, on la met à jour
        if (el.id != "content_" + lang){
          var el_lang = el.get('lang');
          // met à jour les éléments
          $('title_' + el_lang).value = title;
          $('description_' + el_lang).value = description;
        }
      });
    }
    
  </script>
  
  <style type="text/css">
    
    #form_edit_video td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_edit_video input[type=text]{
      width: 250px;
    }
    
    #form_edit_video textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=231"><?php echo _("Liste vidéos"); ?></a> > Modifier une vidéo
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une vidéo"); ?></h1>

  <form id="form_edit_video" action="video_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $video->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur la vidéo</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2"><?php $video->print_video(250, 200); ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          
          <div id="tab_content">
            <!-- onglets "titre et description" dans toutes les langues -->
            <?php
              $languages = Miki_language::get_all_languages();
              
              foreach ($languages as $l){
                echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Contenu") ."</span><img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
                echo "
                <div id='content_$l->code' class='lang_content' lang='$l->code'>
                  <table>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>" ._("Langue : ") ."</td>
                      <td>$l->name</td>
                    </tr>
                    <tr>
                      <td colspan='2' style='text-align:center'>&nbsp;</td>
                    </tr>
                    <tr>
                      <td>" ._("Titre : ") ."</td>
                      <td><input type='text' id='title_$l->code' name='title[$l->code]' value='" .$video->title[$l->code] ."' /></td>
                    </tr>
                    <tr>
                      <td>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' id='description_$l->code'>" .$video->description[$l->code] ."</textarea></td>
                    </tr>
                    <tr>
                      <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
                    </tr>
                  </table>
                </div>";
              }
            ?>
          </div>
      
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>Publié <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="state">
            <option value="1" <?php if ($video->state == 1) echo " selected='selected'"; ?>>Oui</option>
            <option value="0" <?php if ($video->state == 0) echo " selected='selected'"; ?>>Non</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Type <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="type">
            <option value="youtube" <?php if ($video->type == 'youtube') echo " selected='selected'"; ?>>Youtube</option>
            <option value="vimeo" <?php if ($video->type == 'vimeo') echo " selected='selected'"; ?>>Vimeo</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Catégorie <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="category">
            <?php
              foreach($video_category as $key=>$cat){
                echo "<option value=\"$key\"";
                if ($video->category == $key) echo " selected='selected'"; 
                echo ">$cat</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top">Vidéo <span style="color:#ff0000">*</span></td>
        <td>
          <input type="text" name="video" class="required" value="<?php echo $video->video; ?>" />
          <br /><br />
          Entrez ici l'ID de la vidéo ou l'url de visionnage de la vidéo.
          <br /><br />
          Exemple pour l'url de la vidéo suivante sur Youtube : http://www.youtube.com/watch?v=Q2DJwMF2bG4
          <br />
          Soit vous pouvez mettre cet url complet, soit mettre l'ID qui ici est "Q2DJwMF2bG4".
          <br /><br />
          Exemple pour l'url de la vidéo suivante sur Vimeo : http://vimeo.com/5689083
          <br />
          Soit vous pouvez mettre cet url complet, soit mettre l'ID qui ici est "5689083".
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight:bold">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</td>
      </tr> 
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=231'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
      
  </form>
</div>