<?php

// si pas d'id donné, retour
if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

if (!test_right(80)){
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

$id = $_GET['id'];
$menudujour = "";

try{
  $menudujour = new Miki_menudujour($id);
}catch(Exception $e){
  // si aucun enregistrement n'a été trouvé -> retour
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
  exit();
}

$plats = $menudujour->get_plats();
$prices = $menudujour->get_prices();

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// pour la date du menu du jour
$date = $menudujour->date;

if ($date == "0000-00-00 00:00:00")
  $date = "";

if ($date != ""){
  $date = date("d/m/Y", strtotime($date));
}

?>

<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript" src="scripts/mootools_more.js"></script>

<script type="text/javascript" src="scripts/calendar/js/calendar-eightysix-v1.1.js"></script>
<link type="text/css" media="screen" href="scripts/calendar/css/calendar-eightysix-v1.1-default.css" rel="stylesheet" />

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
                                        
    MooTools.lang.set('fr-FR', 'Date', {
      months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
      days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
      dateOrder: ['date', 'mois', 'année', '/']
    });
    
    MooTools.lang.setLanguage("fr-FR");
    
    new CalendarEightysix('date', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if ($date != "") echo ", 'defaultDate': '$date'"; ?> });
  });
  
  // ajouter un menu supplémentaire
  function add_menu(){
    var el = $("menu_exemple").getElement('table').clone();
    el.getElements('input').set('disabled', false);
    el.getElements('select').set('disabled', false);
    el.inject($("add_menu"), "before");
  }
  
  // ajouter un prix supplémentaire
  function add_price(){
    var el = $("price_exemple").getElement('table').clone();
    el.getElements('input').set('disabled', false);
    el.getElements('select').set('disabled', false);
    el.inject($("add_price"), "before");
  }

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=301"><?php echo _("Menus du jour"); ?></a> > <?php echo _("Modifier un menu du jour"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un menu du jour"); ?></h1>
  <form id="formulaire" action="menudujour_test_edit.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    
    <table id="main_table_form">
      <tr>
        <td class="form_text"><?php echo _("Nom : "); ?> <span style="color:#ff0000">*</span></td>
        <td class="form_box"><input type="text" name="name" style="width:200px" class="required" value="<?php echo $menudujour->name; ?>"></td>
      </tr>
      <tr>
        <td><?php echo _("Date : "); ?> <span style="color:#ff0000">*</span></td> 
        <td><input id="date" type="text" class="required" name="date" maxlength="25" style="width: 200px;" /></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="vertical-align: top;"><?php echo _("Plats : "); ?></td> 
        <td>
          <div id="menu_exemple" style="display:none;">
            <table style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #cccccc;">
              <tr>
                <td>
                  <?php echo _("Type de plat : "); ?>
                </td>
                <td>
                  <select name="menu_type[]" disabled="disabled" style="margin-right: 10px;">
                    <option value="1"><?php echo _("Hors d'oeuvre"); ?></option>
                    <option value="2"><?php echo _("Entrée"); ?></option>
                    <option value="3"><?php echo _("Plat principal"); ?></option>
                    <option value="4"><?php echo _("Dessert"); ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo _("Nom : "); ?>
                </td>
                <td>
                  <input type="text" name="menu_name[]" disabled="disabled" value="" style="width: 300px;" />
                </td>
              </tr>
            </table>
          </div>
          
          <?php
            foreach($plats as $plat){
          ?>
          
              <table style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #cccccc;">
                <tr>
                  <td>
                    <?php echo _("Type de plat : "); ?>
                  </td>
                  <td>
                    <select name="menu_type[]" style="margin-right: 10px;">
                      <option value="1" <?php echo $plat['type'] == 1 ? "selected='selected'" : ""; ?>><?php echo _("Hors d'oeuvre"); ?></option>
                      <option value="2" <?php echo $plat['type'] == 2 ? "selected='selected'" : ""; ?>><?php echo _("Entrée"); ?></option>
                      <option value="3" <?php echo $plat['type'] == 3 ? "selected='selected'" : ""; ?>><?php echo _("Plat principal"); ?></option>
                      <option value="4" <?php echo $plat['type'] == 4 ? "selected='selected'" : ""; ?>><?php echo _("Dessert"); ?></option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo _("Nom : "); ?>
                  </td>
                  <td>
                    <input type="text" name="menu_name[]" value="<?php echo $plat['name']; ?>" style="width: 300px;" />
                  </td>
                </tr>
              </table>
              
          <?php } ?>
          
          <a href='javascript:add_menu();' title='<?php echo _("Ajouter un menu"); ?>' id='add_menu'><?php echo _("Ajouter un menu"); ?></a>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="vertical-align: top;"><?php echo _("Prix : "); ?></td> 
        <td>
          <div id="price_exemple" style="display:none;">
            <table style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #cccccc;">
              <tr>
                <td>
                  <?php echo _("Prix : "); ?>
                </td>
                <td>
                  <input type="text" name="menu_price[]" disabled="disabled" class="currency" value="" style="width: 100px;" /> CHF
                </td>
              </tr>
              <tr>
                <td>
                  <?php echo _("Description : "); ?>
                </td>
                <td>
                  <input type="text" name="menu_price_description[]" disabled="disabled" value="" style="width: 300px;" />
                </td>
              </tr>
            </table>
          </div>
          
          <?php
            foreach($prices as $price){
          ?>
          
              <table style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #cccccc;">
                <tr>
                  <td>
                    <?php echo _("Prix : "); ?>
                  </td>
                  <td>
                    <input type="text" name="menu_price[]" class="currency" value="<?php echo $price['price']; ?>" style="width: 100px;" /> CHF
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo _("Description : "); ?>
                  </td>
                  <td>
                    <input type="text" name="menu_price_description[]" value="<?php echo $price['description']; ?>" style="width: 300px;" />
                  </td>
                </tr>
              </table>
              
          <?php } ?>
          
          <a href='javascript:add_price();' title='<?php echo _("Ajouter un prix"); ?>' id='add_price'><?php echo _("Ajouter un prix"); ?></a>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=121'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
    </table>
  </form>
</div>