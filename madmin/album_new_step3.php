<?php

if (!test_right(40) && !test_right(41)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

if (!isset($_SESSION['miki_album']) && !isset($_GET['id'])){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

if (isset($_SESSION['miki_album'])){
  $album = $_SESSION['miki_album'];
  $pics = $album->get_pictures();
}
elseif (isset($_GET['id'])){
  $album = new Miki_album($_GET['id']);
  $_SESSION['miki_album'] = $album;
  $pics = $album->get_pictures();
}

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/tiny_mce/jscripts/tiny_mce/tiny_mce.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
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
  
  // lorsque l'utilisateur veut afficher toutes les langues ou qu'une seule
  function change_type_languages(value){
    if (value == 0){
      $$('.autre_langue').each(function(el){
        el.setStyle('display', 'none');
      });
    }
    else if (value == 1){
      $$('.autre_langue').each(function(el){
        el.setStyle('display', 'block');
      });
    }
  }
  
</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=131"><?php echo _("Albums photo"); ?></a> > <?php echo _("Ajouter/modifier un album photo"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajouter/modifier un album photo"); ?></h1>
  
  <form id="formulaire" action="album_test_new_step3.php" method="post" style="width:100%;margin-top:20px" name="formulaire" enctype="multipart/form-data">
    <table style="width:100%;margin: 0 auto">
      <tr>
        <td colspan="2">
          Titre et description identiques pour toutes les langues :
          <select name="same_lang" id="same_lang" onchange="change_type_languages(this.value);">
            <option value="0" selected="selected">oui</option>
            <option value="1">non</option>
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
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="border-top:dashed #cccccc 1px">&nbsp;</td>
      </tr>
      

      <?php
        foreach($pics as $p){
          echo "
          <tr>
            <td style='text-align:center;vertical-align:top;width:" .($album->thumb_width + 30) ."px'>
              <img src='" .URL_BASE .$p->folder .'/thumb/' .$p->filename ."' alt='" .$p->filename ."' />
            </td>
            <td>
              <div>
                <div style='width:100px;float:left;margin-right:10px'>Couverture : </div>
                <div style='float:left'>
                  Définir cette photo comme étant la couverture de l'album 
                  <input type='radio' name='cover_picture' value=\"$p->id\" style='border:0'" .(($album->cover_picture == $p->id) ? " checked='checked'" : "") ."/>
                </div>
                <div style='clear:left;height:1px'><img src='pictures/pixel.gif' alt='' /></div>
              </div>
              <div style='margin-top:10px'>
                <div style='width:100px;float:left;margin-right:10px'>Lieu : </div>
                <div style='float:left'>
                  <input type='text' name='place_$p->id' style='width:350px' value=\"" .$p->place ."\">
                </div>
                <div style='clear:left;height:1px'><img src='pictures/pixel.gif' alt='' /></div>
              </div>";
              
              $langs = Miki_language::get_all_languages();
              foreach($langs as $l){
                echo "
                  <div style='margin:20px 0 5px 0" .(($l->code != Miki_language::get_main_code()) ? ";display:none' class='autre_langue'" : "'") ."'>
                    <img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin-right:10px;vertical-align:middle;border:0' />
                    Contenu pour la langue suivante : $l->name
                  </div>
                  <div" .(($l->code != Miki_language::get_main_code()) ? " style='display:none' class='autre_langue'" : "") .">
                    <div style='width:100px;float:left;margin-right:10px'>Titre : </div>
                    <div style='float:left'>
                      <input type='text' name='title_" .$p->id ."_" .$l->code ."' class='picture_title_" .$l->code ."' style='width:350px' value=\"" .$p->title[$l->code] ."\">
                    </div>
                    <div style='clear:left;height:1px'><img src='pictures/pixel.gif' alt='' /></div>
                  </div>
                  <div" .(($l->code != Miki_language::get_main_code()) ? " style='display:none' class='autre_langue'" : "") .">
                    <div style='width:100px;float:left;margin-right:10px'>Description : </div>
                    <div style='float:left'>
                      <textarea name='description_" .$p->id ."_" .$l->code ."' class='picture_description_" .$l->code ."' style='width:350px;height:100px'>" .$p->description[$l->code] ."</textarea>
                    </div>
                    <div style='clear:left;height:1px'><img src='pictures/pixel.gif' alt='' /></div>
                  </div>";
              }
              
      echo "</td>
          </tr>
          <tr>
            <td colspan='2'>&nbsp;</td>
          </tr>
          <tr>
            <td colspan='2' style='border-top:dashed #cccccc 1px'>&nbsp;</td>
          </tr>";
        }
      ?>
    
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