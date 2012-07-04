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
      $promo = new Miki_shop_promotion($id);
      $article = new Miki_shop_article($promo->id_article);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // pour la date de début
    $date_start = $promo->date_start;
    
    if ($date_start == "0000-00-00 00:00:00")
      $date_start = "";
    
    if ($date_start != ""){
      $date_start  = explode(" ", $date_start);
      $date_start  = explode("-", $date_start[0]);
      $year_start  = $date_start[0];
      $month_start = $date_start[1];
      $day_start   = $date_start[2];
      
      if (substr($month_start, 0, 1) == '0')
        $month_start = mb_substr($month_start, 1, mb_strlen($month_start));
      if (substr($day_start, 0, 1) == '0')
        $day_start = mb_substr($day_start, 1, mb_strlen($day_start));
    }
    
    // pour la date de fin
    $date_stop = $promo->date_stop;
    
    if ($date_stop == "0000-00-00 00:00:00")
      $date_stop = "";
    
    if ($date_stop != ""){
      $date_stop  = explode(" ", $date_stop);
      $date_stop  = explode("-", $date_stop[0]);
      $year_stop  = $date_stop[0];
      $month_stop = $date_stop[1];
      $day_stop   = $date_stop[2];
      
      if (substr($month_stop, 0, 1) == '0')
        $month_stop = mb_substr($month_stop, 1, mb_strlen($month_stop));
      if (substr($day_stop, 0, 1) == '0')
        $day_stop = mb_substr($day_stop, 1, mb_strlen($day_stop));
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
      
      new CalendarEightysix('date_debut', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($promo) && $date_start != "") echo ", 'defaultDate': '$day_start/$month_start/$year_start'"; ?> });
      new CalendarEightysix('date_fin', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($promo) && $date_stop != "") echo ", 'defaultDate': '$day_stop/$month_stop/$year_stop'"; ?> });
    
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_promotion'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Modifier une promotion
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'une promotion pour l'article '") .$article->get_name('fr') ."'"; ?></h1>  

  <form id="form_edit_promotion" action="article_promotion_test_edit.php" method="post" name="form_edit_promotion" enctype="multipart/form-data">
    <input type="hidden" name="id_promo" value="<?php echo $promo->id; ?>" />
    
    <table>
      <tr>
        <td style="vertical-align:top">Date de début <span style="color:#ff0000">*</span></td> 
        <td><input id="date_debut" type="text" class="required" name="date_debut" maxlength="25" style="width:150px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Date de fin <span style="color:#ff0000">*</span></td> 
        <td><input id="date_fin" type="text" class="required" name="date_fin" maxlength="25" style="width:150px" /></td>
      </tr>
      <tr>
        <td style="vertical-align:top">Prix <span style="color:#ff0000">*</span></td> 
        <td><input type="text" class="required currency" name="price" value="<?php echo number_format($promo->price,2,'.',"'"); ?>" style="width:200px" /> CHF</td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=150&id=<?php echo $article->id; ?>'" />
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