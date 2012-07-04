<?php

if (!test_right(40) && !test_right(41)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}
  
// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

if (isset($_SESSION['saved_album'])){ 
  $album = $_SESSION['saved_album'];
  $album->description = preg_replace("/<br( \/)?\>/i", "", $album->description);  
  unset($_SESSION['saved_album']);
}

if (isset($_GET['id'])){ 
  $album = new Miki_album($_GET['id']);
  $album->description = preg_replace("/<br( \/)?\>/i", "", $album->description);  
}
  
?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
                                        
    tabs = new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
  });
  
  
  // copie le contenu de la langue en cours dans les autres langues
  function copy_content(lang){
    // récupert les éléments de la langue actuelle
    var title = $('title_' + lang).get('value');
    var description = $('description_' + lang).get('value');
    
    // parcourt tous les titres
    $$('input.lang_content').each(function(el){
      // si la langue n'est pas la langue actuelle, on la met à jour
      if (el.id != "title_" + lang){
        // met à jour les éléments
        el.set('value', title)
      }
    });
    
    // parcourt toutes les descriptions
    $$('textarea.lang_content').each(function(el){
      // si la langue n'est pas la langue actuelle, on la met à jour
      if (el.id != "description_" + lang){
        // met à jour les éléments
        el.set('value', description)
      }
    });
  }
  
</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=131"><?php echo _("Albums photo"); ?></a> > <?php echo _("Ajouter/modifier un album photo - étape 1"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter/modifier un album photo"); ?></h1>
    
  <form id="formulaire" action="album_test_new_step1.php" method="post" style="width:100%;margin-top:20px" name="formulaire" enctype="multipart/form-data">
    
    <?php 
    if (isset($album))
      echo "<input type='hidden' name='id' value='$album->id' />";
    ?>
    
    <table style="width:100%;margin: 0 auto">
      <tr>
        <td style="width:150px"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="name" class="required" style="width:400px" maxlength="45" value="<?php if (isset($album)) echo $album->name; else echo ""; ?>" /></td>
      </tr>
      <tr>
        <td colspan="2">
        
          <div id="tab_content" style="margin:20px 0">
            <!-- onglets "titre et description" dans toutes les langues -->
            <?php
              $langs = Miki_language::get_all_languages();
              foreach($langs as $lang){
                echo "<span class='tab_selector'><span style='float:left;margin:0 5px'>" ._("Configuration") ."</span><img src='pictures/flags/$lang->picture' alt='$lang->name' title='$lang->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
                echo "<div id='content_$lang->code' class='lang_content' lang='$lang->code'>";
                ?>      
                        <table>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                          </tr>
                          <tr>
                            <td><?php echo _("Langue : "); ?></td>
                            <td><?php echo $lang->name; ?></td>
                          </tr>
                          <tr>
                            <td colspan="2">&nbsp;</td>
                          </tr>
                          <tr>
                            <td><?php echo _("Titre : "); ?> <span style="color:#ff0000">*</span></td>
                            <td><input type="text" name="title_<?php echo $lang->code; ?>" id="title_<?php echo $lang->code; ?>" class="required lang_content" style="width:400px" value="<?php if (isset($album)) echo $album->title[$lang->code]; ?>" /></td>
                          </tr>
                          <tr>
                            <td style="vertical-align:top"><?php echo _("Description : "); ?> <span style="color:#ff0000">*</span></td>
                            <td><textarea name="description_<?php echo $lang->code; ?>" id="description_<?php echo $lang->code; ?>" class="required lang_content" style="width:400px;height:150px"><?php if (isset($album)) echo $album->description[$lang->code]; ?></textarea></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                            <td>
                              <a href="#" onclick='javascript:copy_content("<?php echo $lang->code; ?>");' title="Copier ce contenu dans toutes les langues">Copier ce contenu dans toutes les langues</a>
                            </td>
                          </tr>
                        </table>
                      </div>
                 
            <?php
              }
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td style="width:150px"><?php echo _("Hauteur des vignettes : "); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="thumb_height" class="required numeric" style="width:100px" value="<?php if (isset($album)) echo $album->thumb_height; else echo "100"; ?>" /> px</td>
      </tr>
      <tr>
        <td><?php echo _("Largeur des vignettes : "); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="thumb_width" class="required numeric" style="width:100px" value="<?php if (isset($album)) echo $album->thumb_width; else echo "100"; ?>" /> px</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="width:150px"><?php echo _("Hauteur des images : "); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="picture_height" class="required numeric" style="width:100px" value="<?php if (isset($album)) echo $album->picture_height; else echo "1000"; ?>" /> px</td>
      </tr>
      <tr>
        <td><?php echo _("Largeur des images : "); ?> <span style="color:#ff0000">*</span></td>
        
        <td><input type="text" name="picture_width" class="required numeric" style="width:100px" value="<?php if (isset($album)) echo $album->picture_width; else echo "1000"; ?>" /> px</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo _("Etat : "); ?></td>
        <td>
          <select name="state">
            <option value="1" <?php if (isset($album) && $album->state == 1) echo "selected='selected'"; ?>>publié</option>
            <option value="0" <?php if (isset($album) && $album->state == 0) echo "selected='selected'"; ?>>non publié</option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=131'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr>
    </table>
  </form>
    
</div>