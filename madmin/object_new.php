<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("../config/object_category.php");
  
    if (!test_right(55)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_object'),{
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
    
    #form_new_object td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_new_object input[type=text]{
      width: 250px;
    }
    
    #form_new_object textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=171"><?php echo _("Liste objets"); ?></a> > Ajout d'un objet
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un objet"); ?></h1>  

  <form id="form_new_object" action="object_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur l'objet</td>
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
                echo "<span class='tab_selector'>" ._("Contenu") ."<img src='pictures/flags/$l->picture' alt='$l->name' title='$l->name' style='margin:4px;vertical-align:middle;border:0' /></span>";
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
                      <td><input type='text' id='title_$l->code' name='title[$l->code]' value=\"" .((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->title[$l->code] : "") ."\" /></td>
                    </tr>
                    <tr>
                      <td>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' id='description_$l->code'>" .((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->description[$l->code] : "") ."</textarea></td>
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
            <option value="1" <?php if (isset($_SESSION['saved_object']) && $_SESSION['saved_object']->state == 1) echo " selected='selected'"; ?>>Oui</option>
            <option value="0" <?php if (isset($_SESSION['saved_object']) && $_SESSION['saved_object']->state == 0) echo " selected='selected'"; ?>>Non</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Catégorie <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="category">
            <?php
              foreach($object_category as $key=>$cat){
                echo "<option value=\"$cat\"";
                if (isset($_SESSION['saved_object']) && $_SESSION['saved_object']->category == $cat) echo " selected='selected'"; 
                echo ">$cat</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Adresse</td>
        <td><input type="text" class="" name="address" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->address : "") ?>" /></td>
      </tr>
      <tr>
        <td>Code postal</td>
        <td><input type="text" class="numeric" name="npa" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->npa : "") ?>" /></td>
      </tr>
      <tr>
        <td>Localité</td>
        <td><input type="text" class="" name="city" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->city : "") ?>" /></td>
      </tr>
      <tr>
        <td>Région</td>
        <td><input type="text" class="" name="region" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->region : "") ?>" /></td>
      </tr>
      <tr>
        <td>Pays <span style="color:#ff0000">*</span></td>
        <td>
          <select name="country">
            <?php
              foreach($country_list as $key=>$el){
                echo "<option value=\"$el\"";
                if (isset($_SESSION['saved_object']) && $_SESSION['saved_object']->country == $el) echo " selected='selected'"; 
                elseif (!isset($_SESSION['saved_object']) && $el == "Suisse") echo " selected='selected'";
                echo ">$el</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Téléphone</td>
        <td><input type="text" name="tel" class="phone" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->tel : "") ?>" /></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><input type="text" class="email" name="email" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->email : "") ?>" /></td>
      </tr>
      <tr>
        <td>Email pour la réservation</td>
        <td><input type="text" class=" email" name="email_booking" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->email_booking : "") ?>" /></td>
      </tr>
      <tr>
        <td>Site Internet</td>
        <td><input type="text" class="requiredLink" name="web" value="<?php echo ((isset($_SESSION['saved_object'])) ? $_SESSION['saved_object']->web : "") ?>" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Images</td>
        <td>
          <a name="pictures"></a>
          <input type="file" name="picture1" />
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=171'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
      
  </form>
</div>