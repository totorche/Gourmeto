<?php
  require_once ("shop_shipping_modules.php");
  require_once ("country_fr.php");
  
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

  
  /**
   * Récupert les pages enfants d'une page donnée
   * 
   * @param Miki_page $p La page dont on veut récupérer les pages enfants
   * @param string $pos La position de la page dont on veut récupérer les pages enfants
   * @param int $depth La pronfondeur de la page dont on veut récupérer les pages enfants (pour indentation)
   * 
   * @return mixed Un tableau contenant les pages enfants (position + page)
   */     
  function get_children($p, $pos, $depth = 0){
    // récupert la variable de la page en cours d'édition
    global $page;
    
    // récupert les pages enfants de la page donnée
    $children = $p->get_children("position");
    
    // le tableau dans lequel seront stockées les pages enfants avant d'être retournées
    $temp = array();
    
    // variable utilisée pour l'indentation des pages
    $white = "";
    
    // définit l'indentation
    for($x=0; $x<$depth; $x++){
      $white .= "&nbsp;&nbsp;";
    }
    
    // parcourt chaque page enfant trouvée
    foreach($children as $child){
      // si l'enfant en cours n'est pas la page en cours d'édition, on le conserve
      $temp[] = array($white ."$pos.$child->position" => $child);
      $temp = array_merge($temp, get_children($child, "$pos.$child->position", $depth + 1));
    }
    
    // retourne les résultats trouvés
    return $temp;
  }

  
  // récupert le shop
  $shops = Miki_shop::get_all_shops();
  if (sizeof($shops) == 0){
    $shop = new Miki_shop();
    $shop->id_person = 'null';
    $shop->save();
  }
  else
    $shop = array_shift($shops);
    
  // récupert tous les transporteurs activés
  $tranporters = Miki_shop_transporter::search("", true);
?>

<script type="text/javascript" src="scripts/checkform.js"></script>
<script type="text/javascript" src="scripts/SimpleTabs.js"></script>
<link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />

<script type="text/javascript">  
  
  window.addEvent('domready', function() {
    myCheckForm = new checkForm($('shop_form'),{
                                  useAjax: false
                                });
     
    // configure les onglets                     
    tabs = new SimpleTabs($('tab_content'),{
      selector:'.tab_selector',
      onSelect: function(toggle, container, index) {
  			toggle.addClass('tab-selected');
  			container.setStyle('display', '');
  			
  			<?php
  			  // parcourt tous les transporteurs
          foreach($tranporters as $transporter){
            // récupert le type de frais de port du transporteur pour le shop en cours
            $type_frais_de_port = $transporter->get_shipping_method($shop->id);
            if ($type_frais_de_port === false)
              $type_frais_de_port = 1;
            ?>
            // affiche le premier type de frais de port
            change_type_frais_port(<?php echo $transporter->id; ?>, <?php echo $type_frais_de_port; ?>);
            <?php
          }
        ?>
  		}
    });
                                    
    // formate les valeurs entrées pour les frais de port d'après le poid des articles
    $('shop_form').addEvent('submit', function(){
    
      //if (type_frais_port == 1 || type_frais_port == 2){
        $$(".shipping_zone_class").each(function(el){
          var poid = el.getElements('.poid');
          var prix = el.getElements('.prix');
          var valeur = "";
          for(x=0; x<poid.length; x++){
            pd = isNaN(parseFloat(poid[x].get('value'))) ? "0" : parseFloat(poid[x].get('value'));
            px = isNaN(parseFloat(prix[x].get('value'))) ? "0" : parseFloat(prix[x].get('value'));
            if (pd != '0' ||px != '0')
              valeur += pd + ":" + px + ",";
          }
          el.getElements('input[type=hidden]').set('value', valeur.substring(0, valeur.length - 1));
        });
      //}
    });
  });
  
  // lorsque l'on change le type de frais de port
  function change_type_frais_port(id_transporter, type){
    
    // vérifie que le type de frais de port que l'on veut afficher existe bien dans la page
    //if (!$('frais_port_' + type))
    //  return false;
      
    // masque toutes les section de frais de port
    $$('.transporter_' + id_transporter + ' .type_frais_port').setStyle('display','none');
    
    // et désactive leurs inputs
    $$('.transporter_' + id_transporter + ' .input_shipping_table').each(function(el){
      el.disabled = true;
    });
    
    $$('.transporter_' + id_transporter + ' .select_country').each(function(el){
      el.disabled = true;
    });
    
    $$('.transporter_' + id_transporter + ' .input_shipping_method_name').each(function(el){
      el.disabled = true;
    });
    
    // affiche la section de frais de port désirée
    $('frais_port_transporter_' + id_transporter + '_type_' + type).setStyle('display','block');
    
    // et active ses inputs
    $$('#frais_port_transporter_' + id_transporter + '_type_' + type + ' .shipping_zone_class .input_shipping_table').each(function(el){
      el.disabled = false;
    });
    
    $$('#frais_port_transporter_' + id_transporter + '_type_' + type + ' .shipping_zone_class .select_country').each(function(el){
      el.disabled = false;
    });
    
    $$('#frais_port_transporter_' + id_transporter + '_type_' + type + ' .input_shipping_method_name').each(function(el){
      el.disabled = false;
    });
  }
  
  var no_shipping_zone = 1;
  
  

  /************************************************************************************
   *
   * Pour les frais de port en fonction du poids total des articles de la commande
   * 
   ************************************************************************************/
  
  // ajoute une zone d'envoi
  function add_shipping_zone_1(id_transporter){
    var cloned = new Element('div');
    cloned.set('style', $('ref_shipping_zone_transporter_' + id_transporter + '_type_1').get('style'));
    cloned.set('html', $('ref_shipping_zone_transporter_' + id_transporter + '_type_1').get('html'));
    
    no_shipping_zone++;
    cloned.set('id', 'shipping_zone_transporter_' + id_transporter + '_type_1_' + no_shipping_zone);
    cloned.setStyle('display','block');
    cloned.addClass('shipping_zone_class');
    
    el = new Element('div');
    var content = '<a href="#frais_port_transporter_' + id_transporter + '_type_1" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_1(this);" title="Supprimer cette zone d\'envoi">Supprimer cette zone d\'envoi</a>';
    el.set('html', content);
    el.inject(cloned);
    
    cloned.inject($('zone_envoi_transporter_' + id_transporter + '_type_1'),'bottom');
    cloned.getElement('select').selectedIndex = 0;
    cloned.getElement('input').value = '';
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
    
    change_type_frais_port(id_transporter, 1);
  }
  
  // supprime une zone d'envoi
  function delete_shipping_zone_1(el){
    $(el).getParent('div').getParent('div').dispose();
  }
  
  // ajoute un prix d'envoi
  function add_shipping_price_1(el, id_transporter){
    var div = $(el).getParent('div').getPrevious('div');

    var cloned = new Element('div');
    cloned.set('style', $('ref_shipping_price_transporter_' + id_transporter + '_type_1').get('style'));
    cloned.set('html', $('ref_shipping_price_transporter_' + id_transporter + '_type_1').get('html'));
    
    cloned.inject(div, 'after');
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
  }
  
  // supprime un prix d'envoi
  function remove_shipping_price_1(el){
    $(el).getParent('div').dispose();
  }
  
  
  
  /************************************************************************************
   *
   * Pour les frais de port en fonction du montant total des articles de la commande
   * 
   ************************************************************************************/
  
  // ajoute une zone d'envoi
  function add_shipping_zone_2(id_transporter){
    var cloned = $('ref_shipping_zone_transporter_' + id_transporter + '_type_2').clone();
    
    no_shipping_zone++;
    cloned.set('id', 'shipping_zone_transporter_' + id_transporter + '_type_2_' + no_shipping_zone);
    cloned.setStyle('display','block');
    cloned.addClass('shipping_zone_class');
    
    el = new Element('div');
    var content = '<a href="#frais_port_transporter_' + id_transporter + '_type_2" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_2(this);" title="Supprimer cette zone d\'envoi">Supprimer cette zone d\'envoi</a>';
    el.set('html', content);
    el.inject(cloned);
    
    cloned.inject($('zone_envoi_transporter_' + id_transporter + '_type_2'),'bottom');
    cloned.getElement('select').selectedIndex = 0;
    cloned.getElement('input').value = '';
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
    
    change_type_frais_port(id_transporter, 2);
  }
  
  // supprime une zone d'envoi
  function delete_shipping_zone_2(el){
    $(el).getParent('div').getParent('div').dispose();
  }
  
  // ajoute un prix d'envoi
  function add_shipping_price_2(el, id_transporter){
    var div = el.getParent('div').getPrevious('div');
    var cloned = $('ref_shipping_price_transporter_' + id_transporter + '_type_2').clone();
    cloned.inject(div, 'after');
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
  }
  
  // supprime un prix d'envoi
  function remove_shipping_price_2(el){
    el.getParent('div').dispose();
  }
  
  
  
  /************************************************************************************
   *
   * Pour les frais de port en fonction du pays
   * 
   ************************************************************************************/  
  
  // ajoute une zone d'envoi
  function add_shipping_zone_3(id_transporter){
    var cloned = $('ref_shipping_zone_transporter_' + id_transporter + '_type_3').clone();
    
    no_shipping_zone++;
    cloned.set('id', 'shipping_zone_transporter_' + id_transporter + '_type_3_' + no_shipping_zone);
    cloned.setStyle('display','block');
    cloned.addClass('shipping_zone_class');
    
    el = new Element('div');
    var content = '<a href="#frais_port_transporter_' + id_transporter + '_type_3" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_3(this);" title="Supprimer cette zone d\'envoi">Supprimer cette zone d\'envoi</a>';
    el.set('html', content);
    el.inject(cloned);
    
    cloned.inject($('zone_envoi_transporter_' + id_transporter + '_type_3'),'bottom');
    cloned.getElement('select').selectedIndex = 0;
    cloned.getElement('input').value = '';
    
    // met à jour la validation du formulaire
    myCheckForm.majFormValidation();
    
    change_type_frais_port(id_transporter, 3);
  }
  
  // supprime une zone d'envoi
  function delete_shipping_zone_3(el){
    el.getParent('div').getParent('div').dispose();
  }
  
</script>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > Modifier mon shop
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier mon shop"); ?></h1>  
  
  <form id="shop_form" action="shop_test_edit.php" method="post" name="shop_form" enctype="application/x-www-form-urlencoded">
  
    <div id="tab_content">
      
      <!-- Onglet Général -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Général"); ?></span>
    
      <table>      
        <tr>
          <td>Nom du shop : </td>
          <td><input type="text" name="name" class="required" style="width:385px" value="<?php echo $shop->name; ?>" /></td>
        </tr>
        <tr>
          <td style="vertical-align:top">Description : </td>
          <td><textarea name="description" class="required" style="width:385px;height:150px"><?php echo $shop->description; ?></textarea></td>
        </tr>
      </table>
      
      <!-- Gestion des frais de port -->
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Frais de port"); ?></span>
      
      <div>
        
        <table style="margin-bottom: 40px;">      
          <tr>
            <td style="vertical-align: top;">Transporteur par défaut : </td>
            <td style="vertical-align: top;">
              <select name="default_transporter">
                <?php
                  $default_transporter = Miki_configuration::get('default_shop_transporter');
                  foreach($tranporters as $transporter){
                    echo "<option value=\"$transporter->id\"";
                    if ($transporter->id == $default_transporter) echo "selected='selected'";
                    echo ">$transporter->name</option>"; 
                  }
                ?>
              </select>
              <br />
              <span class="advice">Le transporteur par défaut sera utilisé pour estimer les frais de port avant que l'utilisateur n'ait encore choisi de transporteur pour sa commande.</span>
            </td>
          </tr>
        </table>
        
      
      <?php
        // et affiche la gestion des frais de port pour chaque transporteur
        foreach($tranporters as $transporter){
          $shipping_method_now = $transporter->get_shipping_method($shop->id);
          echo "<div style='margin-bottom: 20px; background: #F2F2F2; padding: 5px; border: solid 1px #CCCCCC;'><h2 style='background: #FFFFFF; color: #3b5998; padding: 10px; font-size: 1.2em; border: solid 1px #CCCCCC;'>$transporter->name</h2>"; 
      ?>
      
          <div class='transporter_<?php echo $transporter->id; ?>'>
            
            <div style="margin-bottom: 20px;">
              Gestion des frais de port :
              <select name="shipping_method_transporter_<?php echo $transporter->id; ?>" onchange="javascript:change_type_frais_port(<?php echo $transporter->id; ?>, this.value);">
                <?php
                  foreach($shop_shipping_module as $key=>$s){
                    echo "<option value=\"$key\"";
                    if ($shipping_method_now == $key || ($shipping_method_now == "" && $key == 1)) echo "selected='selected'";
                    echo ">$s</option>"; 
                  }
                ?>
              </select>
            </div>
            
            <!--********************************************************************************
            *
            * Pour les frais de port en fonction du poids total des articles de la commande
            * 
            *********************************************************************************-->
           
            <?php
            
            // récupert les données de la configuration de l'envoi et des frais d'envoi
            $shop_shipping = new Miki_shop_shipping_weight_price(0, $shop->id, $transporter->id);
            $shipping_method = "";
            $shipping_table = array();
            $shipping_country = array();
            $shipping_table_values = array();
            
            $x = 0;
            foreach($shop_shipping->configurations as $c){
              if ($c->title == 'shipping_method')
                $shipping_method = $c->value;
              elseif ($c->title == 'shipping_table'){
                $shipping_table[] = $c->value;
                
                $tab = explode(",", $c->value);
                foreach($tab as $t){
                  $shipping_table_values[$x][] = $t;
                } 
                
                $shipping_country[] = $c->country;
                $x++;
              }
            }
            
            if ($shipping_method != 'WEIGHT'){
              $shipping_table = array();
              $shipping_country = array();
              $shipping_table_values = array();
            }
            ?>
            
            <a name="frais_port_1"></a>
            <table id="frais_port_transporter_<?php echo $transporter->id; ?>_type_1" class="type_frais_port">
              <tr>
                <td colspan="2" id="zone_envoi_transporter_<?php echo $transporter->id; ?>_type_1" style="padding-bottom:20px">
                  <input type="hidden" name="shipping_method_name_<?php echo $transporter->id; ?>" class="input_shipping_method_name" value="WEIGHT" />
                  
                  <span style="font-weight:bold">Zone d'envoi :</span>
                  
                  <div style="margin:20px 0">
                    Sélectionnez ci-dessous les pays pour lesquels vous désirez définir des frais d'envoi spécifiques.
                    <br /><br />
                    Le première ligne "Pays pas défaut" est obligatoire et représente les frais d'envoi pour tous les pays qui n'auront pas été définis.
                  </div>
                  
                  <!-- définit une zone d'envoi invisible mais faisant office de référence pour l'ajout de zones supplémentaires -->
                  <div id="ref_shipping_zone_transporter_<?php echo $transporter->id; ?>_type_1" style="display:none;position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="<?php if (isset($shipping_table[0])) echo $shipping_table[0]; ?>" />
                    
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                        <option value="all" <?php if (isset($shipping_country[$x]) && $shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>"; 
                          }
                        ?>
                      </select>
                    </div>
                    <div>
                      <span style="padding-left:40px">Définir le coût d'envoi :</span>
                      <div style="margin:0 33px 5px 0;text-align:right">
                        de 0 kg&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                        <input type="text" class="poid currency" value="" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="" style="width:30px" /> CHF
                        <br />
                      </div>
                      <div id="ref_shipping_price_transporter_<?php echo $transporter->id; ?>_type_1" style="margin:0 10px 5px 0;text-align:right">
                        puis jusqu'à&nbsp;&nbsp;&nbsp;
                        <input type="text" class="poid currency" value="" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="" style="width:30px" /> CHF
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="remove_shipping_price_1(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                        <br />
                      </div>
                      <div style="text-align:right">
                        <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="javascript:add_shipping_price_1(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                      </div>
                    </div>
                  </div>
                  
                  <!-- positionne la 1ère zone d'envoi car obligatoire et référence pour l'ajout de zones supplémentaires -->
                  <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_1_1" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="" />
                    
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country" onchange="this.selectedIndex = 0;">
                        <option value="all" selected="selected">Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>"; 
                          }
                        ?>
                      </select>
                    </div>
                    <div>
                      <span style="padding-left:40px">Définir le coût d'envoi :</span>
                      <div style="margin:0 33px 5px 0;text-align:right">
                        de 0 kg&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                        <?php
                          // si des données ont déjà été insérées, on les récupert et on les affiche
                          if (isset($shipping_table_values[0][0]))
                            $val = explode(":",$shipping_table_values[0][0]);
                          else{
                            $val[0] = '';
                            $val[1] = '';
                          }
                        ?>
                        <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                        <br />
                      </div>
                      <?php
                      // si des données ont déjà été insérées, on les récupert et on les affiche
                      if (isset($shipping_table_values[0])){
                        for($y=1; $y < sizeof($shipping_table_values[0]); $y++){
                        ?>
                          <div style="margin:0 10px 5px 0;text-align:right">
                            puis jusqu'à&nbsp;&nbsp;&nbsp;
                            <?php
                              $val = explode(":",$shipping_table_values[0][$y]);
                            ?>
                            <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                            <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                            <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="remove_shipping_price_1(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                            <br />
                          </div>
                        <?php
                        }
                      }
                      ?>
                      <div style="text-align:right">
                        <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="add_shipping_price_1(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                      </div>
                    </div>
                  </div>
                  
                  <?php
                  // puis positionne les autres zones
                  for($x=1; $x<sizeof($shipping_table); $x++){
                  ?>
                    <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_1_<?php echo ($x+1); ?>" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                      <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="<?php echo $shipping_table[$x]; ?>" />
                      
                      <div>
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_1(this);" title="Supprimer cette zone d'envoi">Supprimer cette zone d'envoi</a>
                      </div>
                      <div style="float:left;margin-right:30px">
                        sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                        <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                          <option value="all" <?php if ($shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                          <?php
                            foreach($country_list as $key=>$c){
                              echo "<option value=\"$c\"";
                              if ($shipping_country[$x] == $c) echo "selected='selected'";
                              echo ">$c</option>"; 
                            }
                          ?>
                        </select>
                      </div>
                      <div>
                        <span style="padding-left:40px">Définir le coût d'envoi :</span>
                        <div style="margin:0 33px 5px 0;text-align:right">
                          de 0 kg&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                          <?php
                            $val = explode(":",$shipping_table_values[$x][0]);
                          ?>
                          <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                          <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                          <br />
                        </div>
                        <?php
                        for($y=1; $y < sizeof($shipping_table_values[$x]); $y++){
                        ?>
                          <div style="margin:0 10px 5px 0;text-align:right">
                            puis jusqu'à&nbsp;&nbsp;&nbsp;
                            <?php
                              $val = explode(":",$shipping_table_values[$x][$y]);
                            ?>
                            <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> kg&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                            <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                            <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="remove_shipping_price_1(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                            <br />
                          </div>
                        <?php
                        }
                        ?>
                        <div style="text-align:right">
                          <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                          <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="add_shipping_price_1(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                        </div>
                      </div>
                    </div>
                  <?php } ?>              
                  
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:right">
                  <img src="pictures/add.png" alt="Ajouter une zone d'envoi" style="border:0;vertical-align:middle" />&nbsp;
                  <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_1" onclick="javascript:add_shipping_zone_1(<?php echo $transporter->id; ?>)">Ajouter une zone d'envoi</a>
                </td>
              </tr>
            </table>
            
            
            <!--*********************************************************************************
            *
            * Pour les frais de port en fonction du montant total des articles de la commande
            * 
            **********************************************************************************-->
           
            <?php
              // récupert les données de la configuration de l'envoi et des frais d'envoi
              $shop_shipping = new Miki_shop_shipping_weight_price(0, $shop->id, $transporter->id);
              $shipping_method = "";
              $shipping_table = array();
              $shipping_country = array();
              $shipping_table_values = array();
              
              $x = 0;
              foreach($shop_shipping->configurations as $c){
                if ($c->title == 'shipping_method')
                  $shipping_method = $c->value;
                elseif ($c->title == 'shipping_table'){
                  $shipping_table[] = $c->value;
                  $tab = explode(",", $c->value);
                  foreach($tab as $t){
                    $shipping_table_values[$x][] = $t;
                  } 
                  $shipping_country[] = $c->country;
                  $x++;
                }
              }
              
              if ($shipping_method != 'PRICE'){
                $shipping_table = array();
                $shipping_country = array();
                $shipping_table_values = array();
              }
            ?>
            
            <a name="frais_port_transporter_<?php echo $transporter->id; ?>_type_2"></a>
            <table id="frais_port_transporter_<?php echo $transporter->id; ?>_type_2" class="type_frais_port">
              <tr>
                <td colspan="2" id="zone_envoi_transporter_<?php echo $transporter->id; ?>_type_2" style="padding-bottom:20px">
                  <input type="hidden" name="shipping_method_name_<?php echo $transporter->id; ?>" class="input_shipping_method_name" value="PRICE" />
                  
                  <span style="font-weight:bold">Zone d'envoi :</span>
                  
                  <div style="margin:20px 0">
                    Sélectionnez ci-dessous les pays pour lesquels vous désirez définir des frais d'envoi spécifiques.
                    <br /><br />
                    Le première ligne "Pays par défaut" est obligatoire et représente les frais d'envoi pour tous les pays qui n'auront pas été définis.
                  </div>
                  
                  <!-- définit une zone d'envoi invisible mais faisant office de référence pour l'ajout de zones supplémentaires -->
                  <div id="ref_shipping_zone_transporter_<?php echo $transporter->id; ?>_type_2" style="display:none;position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="<?php if (isset($shipping_table[0])) echo $shipping_table[0]; ?>" />
                    
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                        <option value="all" <?php if (isset($shipping_country[$x]) && $shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>"; 
                          }
                        ?>
                      </select>
                    </div>
                    <div>
                      <span style="padding-left:40px">Définir le coût d'envoi :</span>
                      <div style="margin:0 33px 5px 0;text-align:right">
                        de 0 CHF&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                        <input type="text" class="poid currency" value="" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="" style="width:30px" /> CHF
                        <br />
                      </div>
                      <div id="ref_shipping_price_transporter_<?php echo $transporter->id; ?>_type_2" style="margin:0 10px 5px 0;text-align:right">
                        puis jusqu'à&nbsp;&nbsp;&nbsp;
                        <input type="text" class="poid currency" value="" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="" style="width:30px" /> CHF
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="remove_shipping_price_2(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                        <br />
                      </div>
                      <div style="text-align:right">
                        <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="add_shipping_price_2(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                      </div>
                    </div>
                  </div>
                  
                  <!-- positionne la 1ère zone d'envoi car obligatoire et référence pour l'ajout de zones supplémentaires -->
                  <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_2_1" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="" />
                    
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country" onchange="this.selectedIndex = 0;">
                        <option value="all" selected="selected">Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>"; 
                          }
                        ?>
                      </select>
                    </div>
                    <div>
                      <span style="padding-left:40px">Définir le coût d'envoi :</span>
                      <div style="margin:0 33px 5px 0;text-align:right">
                        de 0 CHF&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                        <?php
                          if (isset($shipping_table_values[0][0]))
                            $val = explode(":",$shipping_table_values[0][0]);
                          else{
                            $val[0] = '';
                            $val[1] = '';
                          }
                        ?>
                        <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                        <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                        <br />
                      </div>
                      <?php
                      if (isset($shipping_table_values[0])){
                        for($y=1; $y < sizeof($shipping_table_values[0]); $y++){
                      ?>
                        <div style="margin:0 10px 5px 0;text-align:right">
                          puis jusqu'à&nbsp;&nbsp;&nbsp;
                          <?php
                            $val = explode(":",$shipping_table_values[0][$y]);
                          ?>
                          <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                          <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                          <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="remove_shipping_price_2(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                          <br />
                        </div>
                      <?php
                        }
                      }
                      ?>
                      <div style="text-align:right">
                        <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="add_shipping_price_2(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                      </div>
                    </div>
                  </div>
                  
                  <?php
                  // puis positionne les autres zones
                  for($x=1; $x<sizeof($shipping_table); $x++){
                  ?>
                    <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_2_<?php echo ($x+1); ?>" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                      <input type="hidden" name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" value="<?php echo $shipping_table[$x]; ?>" />
                      
                      <div>
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_2(this);" title="Supprimer cette zone d'envoi">Supprimer cette zone d'envoi</a>
                      </div>
                      <div style="float:left;margin-right:30px">
                        sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                        <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                          <option value="all" <?php if ($shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                          <?php
                            foreach($country_list as $key=>$c){
                              echo "<option value=\"$c\"";
                              if ($shipping_country[$x] == $c) echo "selected='selected'";
                              echo ">$c</option>"; 
                            }
                          ?>
                        </select>
                      </div>
                      <div>
                        <span style="padding-left:40px">Définir le coût d'envoi :</span>
                        <div style="margin:0 33px 5px 0;text-align:right">
                          de 0 CHF&nbsp;&nbsp;&nbsp;à&nbsp;&nbsp;&nbsp;
                          <?php
                            $val = explode(":",$shipping_table_values[$x][0]);
                          ?>
                          <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                          <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                          <br />
                        </div>
                        <?php
                        for($y=1; $y < sizeof($shipping_table_values[$x]); $y++){
                        ?>
                          <div style="margin:0 10px 5px 0;text-align:right">
                            puis jusqu'à&nbsp;&nbsp;&nbsp;
                            <?php
                              $val = explode(":",$shipping_table_values[$x][$y]);
                            ?>
                            <input type="text" class="poid currency" value="<?php echo $val[0]; ?>" style="width:30px" /> CHF&nbsp;&nbsp;&nbsp;=&nbsp;&nbsp;&nbsp;
                            <input type="text" class="prix currency" value="<?php echo $val[1]; ?>" style="width:30px" /> CHF
                            <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="remove_shipping_price_2(this)" title="Supprimer"><img src="pictures/false.gif" alt="supprimer" style="border:0;vertical-align:middle" /></a>
                            <br />
                          </div>
                        <?php
                        }
                        ?>
                        <div style="text-align:right">
                          <img src="pictures/add.png" alt="Ajouter une ligne" style="border:0;vertical-align:middle" />&nbsp;
                          <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="add_shipping_price_2(this, <?php echo $transporter->id; ?>);" title="Ajouter une ligne">Ajouter une ligne</a>
                        </div>
                      </div>
                    </div>
                  <?php } ?>              
                  
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:right">
                  <img src="pictures/add.png" alt="Ajouter une zone d'envoi" style="border:0;vertical-align:middle" />&nbsp;
                  <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_2" onclick="javascript:add_shipping_zone_2(<?php echo $transporter->id; ?>)">Ajouter une zone d'envoi</a>
                </td>
              </tr>
            </table>
            
            
            
            <!--*******************************************************************************
            *
            * Pour les frais de port en fonction du pays
            * 
            ********************************************************************************-->
            
            <?php
              // récupert les données de la configuration de l'envoi et des frais d'envoi
              $shop_shipping = new Miki_shop_shipping_fix(1, $shop->id, $transporter->id);
              $shipping_table = array();
              $shipping_country = array();
              
              foreach($shop_shipping->configurations as $c){
                if ($c->title == 'shipping_table'){
                  $shipping_table[] = $c->value;
                  $shipping_country[] = $c->country;
                }
              }
            ?>
            
            <a name="frais_port_transporter_<?php echo $transporter->id; ?>_type_3"></a>
            <table id="frais_port_transporter_<?php echo $transporter->id; ?>_type_3" class="type_frais_port">
              <tr>
                <td colspan="2" id="zone_envoi_transporter_<?php echo $transporter->id; ?>_type_3" style="padding-bottom:20px">
                  
                  <span style="font-weight:bold">Zone d'envoi :</span>
                  
                  <div style="margin:20px 0">
                    Sélectionnez ci-dessous les pays pour lesquels vous désirez définir des frais d'envoi spécifiques.
                    <br /><br />
                    Le première ligne "Pays pas défaut" est obligatoire et représente les frais d'envoi pour tous les pays qui n'auront pas été définis.
                  </div>
                  
                  <!-- définit une zone d'envoi invisible mais faisant office de référence pour l'ajout de zones supplémentaires -->
                  <div id="ref_shipping_zone_transporter_<?php echo $transporter->id; ?>_type_3" style="display:none;position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                        <option value="all" <?php if (isset($shipping_country[$x]) && $shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>";
                          }
                        ?>
                      </select>
                    </div>
                    <div style="padding-top:1.3em">
                      <span style="padding-left:40px">Définir le coût d'envoi : </span>
                      <input name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" type="text" class="currency" value="" style="width:30px" /> CHF
                    </div>
                  </div>
                  
                  <!-- positionne la 1ère zone d'envoi car obligatoire et référence pour l'ajout de zones supplémentaires -->
                  <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_3_1" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                    <div style="float:left;margin-right:30px">
                      sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                      <select name="country_<?php echo $transporter->id; ?>[]" class="select_country" onchange="this.selectedIndex = 0;">
                        <option value="all">Pays par défaut</option>
                        <?php
                          foreach($country_list as $key=>$c){
                            echo "<option value=\"$c\">$c</option>";  
                          }
                        ?>
                      </select>
                    </div>
                    <div style="padding-top:1.3em">
                      <span style="padding-left:40px">Définir le coût d'envoi : </span>
                      <input name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" type="text" class="currency" value="<?php if (isset($shipping_table[0])) echo $shipping_table[0]; ?>" style="width:30px" /> CHF
                    </div>
                  </div>
                  
                  <?php
                  // puis positionne les autres zones
                  for($x=1; $x<sizeof($shipping_table); $x++){
                  ?>
                    <div id="shipping_zone_transporter_<?php echo $transporter->id; ?>_type_3_<?php echo ($x+1); ?>" class="shipping_zone_class" style="position:relative;padding:20px 0 20px 0;border-bottom:solid 1px #cccccc">
                      <div>
                        <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_3" style="color:#336699;position:absolute;top:0;right:0" onclick="javascript:delete_shipping_zone_3(this);" title="Supprimer cette zone d'envoi">Supprimer cette zone d'envoi</a>
                      </div>
                      <div style="float:left;margin-right:30px">
                        sélectionner le pays pour lequel les coûts d'envoi sont valables : <br /> 
                        <select name="country_<?php echo $transporter->id; ?>[]" class="select_country">
                          <option value="all" <?php if ($shipping_country[$x] == 'all') echo "selected='selected'"; ?>>Pays par défaut</option>
                          <?php
                            foreach($country_list as $key=>$c){
                              echo "<option value=\"$c\"";
                              if ($shipping_country[$x] == $c) echo "selected='selected'";
                              echo ">$c</option>"; 
                            }
                          ?>
                        </select>
                      </div>
                      <div style="padding-top:1.3em">
                        <span style="padding-left:40px">Définir le coût d'envoi : </span>
                        <input name="shipping_table_<?php echo $transporter->id; ?>[]" class="input_shipping_table" type="text" class="currency" value="<?php echo $shipping_table[$x]; ?>" style="width:30px" /> CHF
                      </div>
                    </div>
                  <?php } ?>              
                  
                </td>
              </tr>
              <tr>
                <td colspan="2" style="text-align:right">
                  <img src="pictures/add.png" alt="Ajouter une zone d'envoi" style="border:0;vertical-align:middle" />&nbsp;
                  <a href="#frais_port_transporter_<?php echo $transporter->id; ?>_type_3" onclick="javascript:add_shipping_zone_3(<?php echo $transporter->id; ?>)">Ajouter une zone d'envoi</a>
                </td>
              </tr>
            </table>
            
            
            
            <!--********************************************************************************
            *
            * Pour la version sans frais de port
            * 
            *********************************************************************************-->
            
            <div id="frais_port_transporter_<?php echo $transporter->id; ?>_type_4" class="type_frais_port">Pas de frais de livraison</div>      
           
          </div>
      <?php
          echo "</div>";
        }
      ?>
      
      </div>
      
      
      <!-- Onglet DIVERS -->
      <?php
        $gestion_stock = Miki_configuration::get('miki_shop_gestion_stock');
        $page_conditions_vente = Miki_configuration::get('miki_shop_page_conditions_generales_de_vente');
        $service_client_tel = Miki_configuration::get('miki_shop_service_client_tel');
        $service_client_email = Miki_configuration::get('miki_shop_service_client_email');
        $service_client_horaire = Miki_configuration::get('miki_shop_service_client_horaire');
      ?>
      <span class='tab_selector' style='float:left;margin:0 5px'><?php echo _("Divers"); ?></span>
      
      <div style="width: 650px;">
        <table>
          <tr>
            <td>Gestion du stock : </td>
            <td>
              <select name="gestion_stock">
                <option value="1" <?php if ($gestion_stock == 1) echo "selected='selected'"; ?>><?php echo _("Oui"); ?></option>
                <option value="0" <?php if ($gestion_stock == 0) echo "selected='selected'"; ?>><?php echo _("Non"); ?></option>
              </select>
            </td>
          </tr>
          <tr>
            <td>Page des conditions générales de vente : </td>
            <td>
              <select name="page_conditions_vente">
                <option value="-1"><?php echo _("Aucun"); ?></option>
                <?php
                
                  // récupert toutes les pages
                  $elements = Miki_page::get_all_pages("position", false);
                  
                  // recherche les pages de premier niveau (pas de parent)
                  $temp = array();
                  foreach($elements as $el){
                    // si c'est une page de premier niveau (pas de parent) on la prend en compte
                    if (!$el->has_parent()){
                      $temp[] = $el;
                    }
                  }
                  $elements = $temp;
                  
                  
                  $parents = array();
                  // parcourt toutes les pages de premier niveau (pas de parent)
                  foreach ($elements as $el){
                    // créé l'élément représentant la page et sa position
                    $pages_list[] = array($el->position => $el);
                    // récupert de manière récursive les enfants de cette page
                    $pages_list = array_merge($pages_list, get_children($el, $el->position, 1));
                  }
                  
                  // parcourt toutes les pages trouvées
                  foreach($pages_list as $pages){
                    // récupert la page
                    $p = current($pages);
                    // sa position
                    $pos = key($pages);
                    
                    // puis l'ajoute au select
                    echo "<option value='$p->id' ";
                    if ($p->id == $page_conditions_vente) 
                      echo "selected='selected'"; 
                    echo ">$pos - $p->name</option>";
                  }
                ?>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" style="font-weight: bold;">Service clients</td>
          </tr>
          <tr>
            <td>Téléphone : </td>
            <td><input type="text" name="service_client_tel" value="<?php echo ($service_client_tel !== false) ? $service_client_tel : ""; ?>"  style="width: 300px;" /></td>
          </tr>
          <tr>
            <td>E-mail : </td>
            <td><input type="text" name="service_client_email" value="<?php echo ($service_client_email !== false) ? $service_client_email : ""; ?>"  style="width: 300px;" /></td>
          </tr>
          <tr>
            <td>Horaire d'ouverture du service client : </td>
            <td><input type="text" name="service_client_horaire" value="<?php echo ($service_client_horaire !== false) ? $service_client_horaire : ""; ?>"  style="width: 300px;" /></td>
          </tr>
        </table>
      </div>
    </div>
    
    <div style="margin-top:20px">
      <input type="button" value="Annuler" onclick="document.location='index.php?pid=142'" />
      &nbsp;&nbsp;
      <input type="submit" value="Envoyer" />
    </div>
  </form>

</div>