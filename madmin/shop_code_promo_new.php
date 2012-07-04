<head>
  <?php
    require_once ('shop_article_state.php');
  
    if (!test_right(47)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
      
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
  ?>
  
  <script type="text/javascript" src="scripts/mootools_more.js"></script>
  
  <script type="text/javascript" src="scripts/calendar/js/calendar-eightysix-v1.1.js"></script>
  <link type="text/css" media="screen" href="scripts/calendar/css/calendar-eightysix-v1.1-default.css" rel="stylesheet" />
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
    
      MooTools.lang.set('fr-FR', 'Date', {
        months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        dateOrder: ['date', 'mois', 'année', '/']
      });
      
      MooTools.lang.setLanguage("fr-FR");
      
      new CalendarEightysix('date_start', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true });
      new CalendarEightysix('date_stop', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true });
      
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_code_promo'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=156"><?php echo _("Codes de promotion"); ?></a> > Ajouter un code de promotion
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un code de promotion"); ?></h1>  

  <form id="form_new_code_promo" action="shop_code_promo_test_new.php" method="post" name="form_new_code_promo" enctype="multipart/form-data">
    <table>
      <tr>
        <td style="vertical-align:top">Code <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required" name="code" maxlength="45" style="width:250px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Type de code <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="type">
            <option value="1">Rabais en CHF</option>
            <option value="2">Rabais en pourcent</option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top">Rabais <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required currency" name="discount" style="width:100px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Date de début <span style="color:#ff0000">*</span></td> 
        <td><input id="date_start" type="text" class="required" name="date_start" maxlength="25" style="width:150px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Date de fin <span style="color:#ff0000">*</span></td> 
        <td><input id="date_stop" type="text" class="required" name="date_stop" maxlength="25" style="width:150px" /></td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=156'" />
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