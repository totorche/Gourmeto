<head>
  <?php
    require_once ('shop_article_state.php');
  
    if (!test_right(51) && !test_right(52) && !test_right(53)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // si pas d'id d'article spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
      
    try{
      $article = new Miki_shop_article($id);
    }
    catch(Exception $e){
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
      
      new CalendarEightysix('date_debut', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true });
      new CalendarEightysix('date_fin', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true });
      
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_deal'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Ajouter un deal
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un deal pour l'article '") .$article->get_name('fr') ."'"; ?></h1>  

  <form id="form_new_deal" action="article_deal_test_new.php" method="post" name="form_new_deal" enctype="multipart/form-data">
    <input type="hidden" name="id_article" value="<?php echo $article->id; ?>" />
    
    <table>
      <tr>
        <td style="vertical-align:top"><?php echo _("Début du deal"); ?> <span style="color:#ff0000">*</span></td> 
        <td>
          <input id="date_debut" type="text" class="required" name="date_debut" maxlength="25" style="width:100px" />
          <select name="heure_debut" style="margin-left: 20px;">
            <?php
              for ($x = 0; $x < 24; $x++){
                if (strlen($x) == 1)
                  $temp = '0' .$x;
                else
                  $temp = $x;
                echo "<option value='$temp'>$temp</option>\r\n";
              }
            ?>
          </select> <?php echo _("heures"); ?>
          <select name="minute_debut" style="margin-left: 5px;">
            <?php
              for ($x = 0; $x < 60; $x++){
                if (strlen($x) == 1)
                  $temp = '0' .$x;
                else
                  $temp = $x;
                echo "<option value='$temp'>$temp</option>\r\n";
              }
            ?>
          </select> <?php echo _("minutes"); ?>
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Fin du deal"); ?> <span style="color:#ff0000">*</span></td> 
        <td>
          <input id="date_fin" type="text" class="required" name="date_fin" maxlength="25" style="width:100px" />
          <select name="heure_fin" style="margin-left: 20px;">
            <?php
              for ($x = 0; $x < 24; $x++){
                if (strlen($x) == 1)
                  $temp = '0' .$x;
                else
                  $temp = $x;
                echo "<option value='$temp'>$temp</option>\r\n";
              }
            ?>
          </select> heures
          <select name="minute_fin" style="margin-left: 5px;">
            <?php
              for ($x = 0; $x < 60; $x++){
                if (strlen($x) == 1)
                  $temp = '0' .$x;
                else
                  $temp = $x;
                echo "<option value='$temp'>$temp</option>\r\n";
              }
            ?>
          </select> minutes
        </td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Prix"); ?> <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required currency" name="price" style="width:100px" /> CHF</td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Quantité disponible"); ?> <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required numeric" name="quantity" style="width:100px" /></td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=261&id=<?php echo $article->id; ?>'" />
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