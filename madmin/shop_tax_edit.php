<?php
  require_once ("country_fr.php");
  
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

  
  // récupert le shop
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    $shop = new Miki_shop();
    $shop->id_person = 'null';
    $shop->save();
  }
  else
    $shop = array_shift($shops);
  
  //récupert les différentes taxes déjà configurées
  $taxes = $shop->get_taxes();
?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<script type="text/javascript">  
  
  window.addEvent('domready', function() {
    myCheckForm = new checkForm($('tax_form'),{
                                  useAjax: false
                                });
     
    // configure les onglets                     
    tabs = new SimpleTabs($('tab_content'),{
      selector:'.tab_selector',
      onSelect: function(toggle, container, index) {
  			toggle.addClass('tab-selected');
  			container.setStyle('display', '');
  		}
    });
  });
  
  
  var no_tax = 1;
  
  // ajoute une taxe
  function add_tax(index){
    var cloned = $('ref_tax_' + index).clone();
    cloned.getElement("select.tax_country").disabled = false;
    cloned.getElement("input.tax").disabled = false;
    
    no_tax++;
    cloned.set('id', 'tax_' + index + '_' + no_tax);
    cloned.setStyle('display','block');
    
    el = new Element('div');
    var content = '<a href="#tax_' + index + '_1" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_tax(this);" title="Supprimer cette zone d\'envoi">Supprimer cette zone d\'envoi</a>';
    el.set('html', content);
    el.inject(cloned);
    
    cloned.inject($('zone_tax_' + index).getElement("div.add_zone"),'before');
    cloned.getElement('select').selectedIndex = 0;
    cloned.getElement('input').value = '';
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
  }
  
  // supprime un prix d'envoi
  function delete_tax(el){
    el.getParent('div').getParent('div').dispose();
  } 
  
</script>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <?php echo _("Taxes"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier mon shop"); ?></h1>  
  
  <form id="add_tax_form" action="shop_tax_add.php" method="post" name="add_tax_form" enctype="application/x-www-form-urlencoded" style="margin-bottom: 40px; background: #F2F2F2; padding: 5px; border: solid 1px #CCCCCC;">
    <h2 style="background: #FFFFFF; color: #3b5998; padding: 10px; font-size: 1.2em; border: solid 1px #CCCCCC;"><?php echo _("Ajouter une taxe"); ?></h2>
    <?php echo _("Nom de la taxe"); ?> : <input type="text" name="tax_name" value="" />
    <input type="submit" value="Envoyer" />
  </form>
  
  <form id="tax_form" action="shop_tax_test_edit.php" method="post" name="tax_form" enctype="application/x-www-form-urlencoded">
  
    <div id="tab_content">
      
      <?php
        // Ajoute un onglet par taxe existante
        $x = 1;
        foreach($taxes as $tax_name=>$tax){
          echo "<span class='tab_selector' style='float:left; margin:0 5px;'>$tax_name</span>";
          ?>
          
          <div id="zone_tax_<?php echo $x; ?>">

            <div id="ref_tax_<?php echo $x; ?>" style="display: none; position: relative; padding: 20px 0 20px 0; border-bottom: solid 1px #cccccc;">
              <div>
                <a href="#tax_<?php echo $x; ?>_1" style="color: #336699; position: absolute; top: 0; right: 0;" onclick="javascript:delete_tax(this);" title="Supprimer cette zone d'envoi">Supprimer cette zone d'envoi</a>
              </div>
              <div style="float: left; margin-right: 30px;">
                Sélectionner le pays pour lequel la taxe est valable : <br /> 
                <select name="tax_country[<?php echo $tax_name; ?>][]" class="tax_country" disabled="disabled">
                  <option value="all" <?php if (key($tax) == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                  <?php
                    foreach($country_list as $key=>$c){
                      echo "<option value=\"$c\"";
                      if (key($tax) == $c) echo "selected='selected'";
                      echo ">$c</option>"; 
                    }
                  ?>
                </select>
              </div>
              <div>
                <div style="margin:0 0 5px 0;text-align:right">
                  Définir la taxe pour cette zone d'envoi :<br />
                  <input type="text" name="tax[<?php echo $tax_name; ?>][]" disabled="disabled" class="tax currency" value="118" style="width:30px" /> %
                </div>
              </div>
            </div>
            
            <div id="tax_<?php echo $x; ?>_1" style="position: relative; padding: 20px 0 20px 0; border-bottom: solid 1px #cccccc;">
              <div style="float: left; margin-right: 30px;">
                Sélectionner le pays pour lequel la taxe est valable : <br /> 
                <select name="tax_country[<?php echo $tax_name; ?>][]" onchange="this.selectedIndex = 0;">
                  <option value="all" <?php if (key($tax) == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                  <?php
                    foreach($country_list as $key=>$c){
                      echo "<option value=\"$c\"";
                      if (key($tax) == $c) echo "selected='selected'";
                      echo ">$c</option>"; 
                    }
                  ?>
                </select>
              </div>
              <div>
                <div style="margin:0 0 5px 0;text-align:right">
                  Définir la taxe pour cette zone d'envoi :<br />
                  <input type="text" name="tax[<?php echo $tax_name; ?>][]" class="currency" value="<?php echo current($tax); ?>" style="width:30px" /> %
                </div>
              </div>
            </div>
            
            <?php
            $y = 1;
            // puis positionne les autres zones
            foreach($tax as $tax_country => $tax_value){
              // prend seulement à partir de la 2ème configuration
              if ($y > 1){
            ?>
              <div id="tax_<?php echo $x; ?>_<?php echo ($y+1); ?>" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                <div>
                  <a href="#tax_<?php echo $x; ?>_1" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_tax(this);" title="Supprimer cette zone d'envoi">Supprimer cette zone d'envoi</a>
                </div>
                <div style="float: left; margin-right: 30px;">
                  Sélectionner le pays pour lequel la taxe est valable : <br /> 
                  <select name="tax_country[<?php echo $tax_name; ?>][]">
                    <option value="all" <?php if ($tax_country == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                    <?php
                      foreach($country_list as $key=>$c){
                        echo "<option value=\"$c\"";
                        if ($tax_country == $c) echo "selected='selected'";
                        echo ">$c</option>"; 
                      }
                    ?>
                  </select>
                </div>
                <div>
                  <div style="margin:0 0 5px 0;text-align:right">
                    Définir la taxe pour cette zone d'envoi :<br />
                    <input type="text" name="tax[<?php echo $tax_name; ?>][]" class="currency" value="<?php echo $tax_value; ?>" style="width: 30px;" /> %
                  </div>
                </div>
              </div>
              
            <?php 
              }
              $y++;
            } ?>
            
            <div style="margin-top: 20px; text-align: right;" class="add_zone">
              <img src="pictures/add.png" alt="Ajouter une zone d'envoi" style="border:0;vertical-align:middle" />&nbsp;
              <a href="#frais_port_3" onclick="javascript:add_tax(<?php echo $x; ?>)">Ajouter une zone d'envoi</a>
            </div>
            
          </div>
          
          <?php
          $x++;
        }
      ?>
    </div>
      
    
    <div style="margin-top:20px">
      <input type="button" value="Annuler" onclick="document.location='index.php?pid=142'" />
      &nbsp;&nbsp;
      <input type="submit" value="Envoyer" />
    </div>
  </form>

</div>