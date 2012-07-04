  <?php
    require_once ('shop_article_attribute_type.php');
  
    if (!test_right(51) && !test_right(52) && !test_right(53))
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_attribut'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
    // affiche ou masque les champs de valeurs de l'attribut    
    function change_type(val){
      if (val == 2){
        $('liste_deroulante').setStyle('display','block');
      }
      else{
        $('liste_deroulante').setStyle('display','none');
      }
    }
    
    // ajoute une valeur pour la liste déroulante à l'attribut en cours de création
    function ajouter_valeur_liste_deroulante(){
      var newDiv = new Element('div', {
        'html': 'Nom : ',
        'styles': {
          'margin-bottom': '10px'
        }
      });
      var newInput = new Element('input', {
        'type': 'text',
        'name': 'valeur[]',
        'styles': {
          'width': '400px'
        }
      });
      
      newInput.inject(newDiv);
      
      newDiv.inject($('liste_deroulante'));
    }    
    
    
    
  </script>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=153"><?php echo _("Liste attributs"); ?></a> > Ajouter un attribut
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un attribut"); ?></h1> 
  
  <?php
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
  ?> 

  <form id="form_new_attribut" action="article_attribute_test_new.php" method="post" name="form_new_attribut" enctype="multipart/form-data" style="width:580px">
    <table style="width:100%">
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur l'attribut</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>        
      <tr>
        <td style="vertical-align:top">Nom <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required" name="name" style="width:400px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Type <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="type" onchange="javascript:change_type(this.value);">
            <?php
              foreach($shop_article_attribute_type as $key=>$value){
                echo "<option value=\"$key\""; 
                  if ($key == 1) echo " selected='selected'"; 
                echo ">$value</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          <div id="liste_deroulante" style="display:none">
            <p style="font-weight:bold">Gérer les valeurs de l'attribut</p>
            
            <div style="float:right;margin-left:30px">
              <a href="#" onclick="javascript:ajouter_valeur_liste_deroulante();">Ajouter une valeur</a>
            </div>
            
            <div style="margin-bottom:10px">
              Nom : <input type="text" name="valeur[]" style="width:400px" />
            </div>
          </div>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=153'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
  </form>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>