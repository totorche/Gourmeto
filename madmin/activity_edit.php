<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("functions_pictures.php");
  
    if (!test_right(60)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // si pas d'id spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
    
    try{
      $activity = new Miki_activity($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  
  <link rel="stylesheet" type="text/css" href="../scripts/milkbox/milkbox.css" />
  <script type="text/javascript" src="../scripts/milkbox/mootools-1.2.3.1-assets.js"></script>
  <script type="text/javascript" src="../scripts/milkbox/milkbox.js"></script>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_edit_activity'),{
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
    
    var nb_pictures = 1;
    
    // ajouter un logo supplémentaire
    function add_logo(){
      nb_pictures++;
      
      var br = new Element('br');
      br.inject($('ajouter_image'), 'before');
      
      var el = new Element('input', {type: 'file', name: 'picture' + nb_pictures});
      el.inject($('ajouter_image'), 'before');
      
      $('nb_pictures').set('value',nb_pictures);
    }
    
  </script>
  
  <style type="text/css">
    
    #form_edit_activity td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_edit_activity input[type=text]{
      width: 250px;
    }
    
    #form_edit_activity textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=181"><?php echo _("Liste activités"); ?></a> > Modifier une activité
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une activité"); ?></h1>  

  <form id="form_edit_activity" action="activity_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $activity->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur l'activité</td>
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
                      <td><input type='text' id='title_$l->code' name='title[$l->code]' value='" .$activity->title[$l->code] ."' /></td>
                    </tr>
                    <tr>
                      <td>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' id='description_$l->code'>" .$activity->description[$l->code] ."</textarea></td>
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
        <td>Localité <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required" name="city" value="<?php echo $activity->city ?>" /></td>
      </tr>
      <tr>
        <td>Région</td>
        <td><input type="text" class="" name="region" value="<?php echo $activity->region ?>" /></td>
      </tr>
      <tr>
        <td>Pays <span style="color:#ff0000">*</span></td>
        <td>
          <select name="country">
            <?php
              foreach($country_list as $key=>$el){
                echo "<option value=\"$el\"";
                if ($activity->country == $el) echo " selected='selected'"; 
                echo ">$el</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Site Internet <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required requiredLink" name="web" value="<?php echo $activity->web ?>" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Images</td>
        <td>
          <a name="pictures"></a>
          <?php
            // affiche les inputs pour les images
            for($x=1; $x<=sizeof($activity->pictures); $x++){
              $size = get_image_size("../pictures/activities/thumb/" .$activity->pictures[$x - 1], 30, 30);
              $random = rand(1,1000);
              echo "<div style='margin-bottom:10px'>
                      <input type='file' name='picture$x' style='float:left' />&nbsp;
                      <a href='../pictures/activities/" .$activity->pictures[$x - 1] ."' title='Agrandir' rel='milkbox:activity'>
                        <img src='../pictures/activities/thumb/" .$activity->pictures[$x - 1] ."?rand=$random' alt=\"image de l'activité\" style='border:0;float:left;margin-left:5px;width:" .$size[0] ."px;height:" .$size[1] ."px;vertical-align:top' />
                      </a>
                      <div style='float:left;margin-left:5px'>
                        (Laissez vide pour conserver les images)<br />
                        <a href='activity_delete_picture.php?id=$activity->id&pic=" .$activity->pictures[$x - 1] ."' title='Supprimer cette photo'>Supprimer cette photo</a>
                      </div>
                      <div style='clear:both;height:1px'><img src='" .URL_BASE ."pictures/pixel.gif' /></div>
                    </div>";
            }
            
            if (sizeof($activity->pictures) == 0){
              echo "<input type='file' name='picture$x' />";
            }
          ?>
          <span id="ajouter_image">
            <a href="#logos" onclick="add_logo();" title="Ajouter une image" style="margin:0 5px">
              <img src="pictures/add.png" alt="Ajouter une image" style="border:0;vertical-align:middle" />
            <a>
            <a href="#logos" onclick="add_logo();" title="Ajouter une image">Ajouter une image</a>
          </span>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=181'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
      
  </form>
</div>