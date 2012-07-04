<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("../config/event_category.php");
  
    if (!test_right(62)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    if (isset($_SESSION['saved_event'])){
      
      // pour la date de début
      $date_start = $_SESSION['saved_event']->date_start;
      
      if ($date_start == "0000-00-00 00:00:00")
        $date_start = "";
      
      if ($date_start != ""){
        $date_start  = explode(" ", $date_start);
      	$time_start    = explode(":", $date_start[1]);
        $date_start  = explode("-", $date_start[0]);
        $year_start  = $date_start[0];
        $month_start = $date_start[1];
        $day_start   = $date_start[2];
      	$heure_start   = $time_start[0];
      	$minutes_start = $time_start[1];
        
        if (substr($month_start, 0, 1) == '0')
          $month_start = mb_substr($month_start, 1, mb_strlen($month_start));
        if (substr($day_start, 0, 1) == '0')
          $day_start = mb_substr($day_start, 1, mb_strlen($day_start));
      }
      
      // pour la date de fin
      $date_stop = $_SESSION['saved_event']->date_stop;
      
      if ($date_stop == "0000-00-00 00:00:00")
        $date_stop = "";
      
      if ($date_stop != ""){
        $date_stop  = explode(" ", $date_stop);
      	$time_stop    = explode(":", $date_stop[1]);
        $date_stop  = explode("-", $date_stop[0]);
        $year_stop  = $date_stop[0];
        $month_stop = $date_stop[1];
        $day_stop   = $date_stop[2];
      	$heure_stop   = $time_stop[0];
      	$minutes_stop = $time_stop[1];
        
        if (substr($month_stop, 0, 1) == '0')
          $month_stop = mb_substr($month_stop, 1, mb_strlen($month_stop));
        if (substr($day_stop, 0, 1) == '0')
          $day_stop = mb_substr($day_stop, 1, mb_strlen($day_stop));
      }
    }
    else{
      $date_start = "";
      $date_stop = "";
    }
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  
  <script type="text/javascript" src="scripts/mootools_more.js"></script>
  
  <script type="text/javascript" src="scripts/calendar/js/calendar-eightysix-v1.1.js"></script>
  <link type="text/css" media="screen" href="scripts/calendar/css/calendar-eightysix-v1.1-default.css" rel="stylesheet" />
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_event'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
                                      
      tabs = new SimpleTabs($('tab_content'),{selector:'.tab_selector'});
      
      MooTools.lang.set('fr-FR', 'Date', {
        months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        dateOrder: ['date', 'mois', 'année', '/']
      });
      
      MooTools.lang.setLanguage("fr-FR");
      
      new CalendarEightysix('date_debut', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($_SESSION['saved_event']) && $date_start != "") echo ", 'defaultDate': '$day_start/$month_start/$year_start'"; ?> });
      new CalendarEightysix('date_fin', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($_SESSION['saved_event']) && $date_stop != "") echo ", 'defaultDate': '$day_stop/$month_stop/$year_stop'"; ?> });
      
      // affiche ou masque les options de gestion des entrées
      change_entrance();
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
    
    var nb_pictures = <?php echo Miki_language::get_nb_languages(); ?>;
    
    // ajouter un logo supplémentaire
    function add_logo(lang){
      nb_pictures++;
      
      var br = new Element('br');
      br.inject($('ajouter_image_' + lang), 'before');
      
      var el = new Element('input', {type: 'hidden', name: 'picture_lang' + nb_pictures, value: lang});
      el.inject($('ajouter_image_' + lang), 'before');
      
      var el = new Element('input', {type: 'text', name: 'picture_name' + nb_pictures});
      el.inject($('ajouter_image_' + lang), 'before');
      
      var br = new Element('br');
      br.inject($('ajouter_image_' + lang), 'before');
      
      var el2 = new Element('input', {type: 'file', name: 'picture' + nb_pictures});
      el2.inject($('ajouter_image_' + lang), 'before');
      
      var br = new Element('br');
      br.inject($('ajouter_image_' + lang), 'before');
      
      var br = new Element('br');
      br.inject($('ajouter_image_' + lang), 'before');
      
      $('nb_pictures').set('value',nb_pictures);
    }
    
    function change_entrance(){
      var val = $('select_entrance_type').value;
      if (val == 0){
        $('manage_entrance').setStyle('display', 'none');
      }
      else if (val == 1){
        $('manage_entrance').setStyle('display', 'block');
      }
    }
    
    function change_online_subscription(element){
      element = $(element);
      if (element.checked){
        element.getParent('tr').getNext('tr').setStyle('display','table-row');
      }
      else{
        element.getParent('tr').getNext('tr').setStyle('display','none');
      }
    }
    
    function change_subscription_information(element){
      element = $(element);
      if (element.checked){
        element.getNext('span').setStyle('display','inline');
      }
      else{
        element.getNext('span').setStyle('display','none');
      }
    }
    
  </script>
  
  <style type="text/css">
    
    #form_new_event td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_new_event input[type=text]{
      width: 250px;
    }
    
    #form_new_event textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=191"><?php echo _("Liste événements"); ?></a> > Ajout d'un événement
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un événement"); ?></h1>  

  <form id="form_new_event" action="event_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur l'événement</td>
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
              
              $x = 1;
              
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
                      <td><input type='text' id='title_$l->code' name='title[$l->code]' value='" .((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->title[$l->code] : "") ."' class='required' /></td>
                    </tr>
                    <tr>
                      <td>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' id='description_$l->code' class='required'>" .((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->description[$l->code] : "") ."</textarea></td>
                    </tr>
                    <tr>
                      <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
                    </tr>
                    <tr>
                      <td style='vertical-align:top'>Fichiers</td>
                      <td>
                        <a name='pictures'></a>
                        <input type='hidden' name='picture_lang$x' value='$l->code' />
                        <input type='text' name='picture_name$x' value='' /><br />
                        <input type='file' name='picture$x' />
                        <span id='ajouter_image_$l->code'>
                          <a href='#logos' onclick=\"add_logo('$l->code');\" title='Ajouter un fichier' style='margin:0 5px'>
                            <img src='pictures/add.png' alt='Ajouter un fichier' style='border:0;vertical-align:middle' />
                          <a>
                          <a href='#logos' onclick=\"add_logo('$l->code');\" title='Ajouter un fichier'>Ajouter un fichier</a>
                        </span>
                      </td>
                    </tr>
                  </table>
                </div>";
                
                $x++;
              }
            ?>
          </div>
      
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td>Visibilité <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="type">
            <option value="0" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->type == 0) echo " selected='selected'"; ?>>Publique</option>
            <option value="1" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->type == 1) echo " selected='selected'"; ?>>Privé</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Catégorie <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="category">
            <?php
              foreach($categories as $key=>$cat){
                echo "<option value=\"$key\"";
                if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->category == $key) echo " selected='selected'"; 
                echo ">$cat</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Tags</td>
        <td><input type="text" class="" name="tags" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->tags : "") ?>" /></td>
      </tr>
      <tr>
        <td>Organisateur <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="organizer" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->organizer : Miki_configuration::get('sitename')) ?>" /></td>
      </tr>
      <tr>
        <td>Date de début <span style="color:#ff0000">*</span></td> 
        <td><input id="date_debut" type="text" class="required" name="date_start" maxlength="25" style="width:150px" /></td>
      </tr>
      <tr>
        <td>Heure de début</td> 
        <td>
          <select name="heure_start">
            <?php 
            for ($x=0; $x<=23; $x++){
              $heure = $x;
              if (mb_strlen($heure) == 1)
                $heure = '0' .$heure;
              
              echo "<option value='$heure'>$heure</option>\n";
            }
            ?>
          </select>
          &nbsp;h&nbsp;
          <select name="minutes_start">
            <option value='00'>00</option>
            <option value='10'>10</option>
            <option value='20'>20</option>
            <option value='30'>30</option>
            <option value='40'>40</option>
            <option value='50'>50</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Date de fin <span style="color:#ff0000">*</span></td> 
        <td><input id="date_fin" type="text" class="required" name="date_stop" maxlength="25" style="width:150px" /></td>
      </tr>
      <tr>
        <td>Heure de fin</td> 
        <td>
          <select name="heure_stop">
            <?php 
            for ($x=0; $x<=23; $x++){
              $heure = $x;
              if (mb_strlen($heure) == 1)
                $heure = '0' .$heure;
              
              echo "<option value='$heure'>$heure</option>\n";
            }
            ?>
          </select>
          &nbsp;h&nbsp;
          <select name="minutes_stop">
            <option value='00'>00</option>
            <option value='10'>10</option>
            <option value='20'>20</option>
            <option value='30'>30</option>
            <option value='40'>40</option>
            <option value='50'>50</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Lieu de l'événement (bâtiment, locaux, etc.)</td>
        <td><input type="text" class="" name="place" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->place : "") ?>" /></td>
      </tr>
      <tr>
        <td>Adresse</td>
        <td><input type="text" class="" name="address" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->address : "") ?>" /></td>
      </tr>
      <tr>
        <td>Code postal</td>
        <td><input type="text" class="numeric" name="npa" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->npa : "") ?>" /></td>
      </tr>
      <tr>
        <td>Localité</td>
        <td><input type="text" class="" name="city" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->city : "") ?>" /></td>
      </tr>
      <tr>
        <td>Région</td>
        <td><input type="text" class="" name="region" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->region : "") ?>" /></td>
      </tr>
      <tr>
        <td>Pays</td>
        <td>
          <select name="country">
            <?php
              foreach($country_list as $key=>$el){
                echo "<option value=\"$el\"";
                if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->country == $el) echo " selected='selected'"; 
                elseif (!isset($_SESSION['saved_event']) && $el == "Suisse") echo " selected='selected'";
                echo ">$el</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Nb max. de participants <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="max_participants" class="required numeric" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->max_participants : "0") ?>" /> (0 = infini)</td>
      </tr>
      <tr>
        <td>Nb accompagnants autorisé</td>
        <td><input type="text" class="numeric" name="accompanist" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->accompanist : "") ?>" /></td>
      </tr>
      <tr>
        <td>Activer les inscriptions online</td>
        <td><input type="checkbox" name="online_subscription" value="1" onclick="javascript:change_online_subscription(this);" <?php echo (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->online_subscription == 1) ? "checked='checked'" : ""; ?> /></td>
      </tr>
      <tr style="display: <?php echo (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->online_subscription == 1) ? "table-row" : "none"; ?>;">
        <td>M'informer lors de chaque inscription</td>
        <td>
          <input type="checkbox" name="subscription_information" value="1" onclick="javascript:change_subscription_information(this);" <?php echo (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->subscription_information == 1) ? "checked='checked'" : ""; ?> />
          <span style="display: <?php echo (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->subscription_information == 1) ? "inline" : "none"; ?>";>
            &nbsp;&nbsp;
            A cette adresse e-mail : 
            <input type="text" class="email" name="subscription_information_email" value="<?php echo (isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->subscription_information_email : ""; ?>" />
          </span>
        </td>
      </tr>
      <tr>
        <td>Type d'entrée <span style="color:#ff0000">*</span></td> 
        <td>
          <select id="select_entrance_type" name="entrance_type" onchange="javascript:change_entrance();">
            <option value="0" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->entrance_type == 0) echo " selected='selected'"; ?>>Gratuit</option>
            <option value="1" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->entrance_type == 1) echo " selected='selected'"; ?>>Payant</option>
          </select>
          
          <div id="manage_entrance" style="margin-top:10px;padding:5px;border:solid 1px #EEEEEE">
            <table>
              <tr>
                <td>Prix de l'entrée</td>
                <td><input type="text" class="currency" name="entrance_price" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->entrance_price : "") ?>" /></td>
              </tr>
              <tr>
                <td>Monnaie</td>
                <td>
                  <select name="entrance_currency">
                    <option value="CHF" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->entrance_currency == "CHF") echo " selected='selected'"; ?>>Francs suisses (CHF)</option>
                    <option value="EUR" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->entrance_currency == "€") echo " selected='selected'"; ?>>Euro (€)</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Paiement online obligatoire</td>
                <td>
                  <select name="payement_online">
                    <option value="0" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->payement_online == 0) echo " selected='selected'"; ?>>Pas de paiement online</option>
                    <option value="1" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->payement_online == 1) echo " selected='selected'"; ?>>Paiement online facultatif</option>
                    <option value="2" <?php if (isset($_SESSION['saved_event']) && $_SESSION['saved_event']->payement_online == 2) echo " selected='selected'"; ?>>Paiement online obligatoire </option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Commentaire sur le prix d'entrée</td>
                <td><textarea name="entrance_text"><?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->entrance_text : "") ?></textarea></td>
              </tr>
            </table>
          </div>
          
        </td>
      </tr>
      <tr>
        <td>Web</td>
        <td><input type="text" class="requiredLink" name="web" value="<?php echo ((isset($_SESSION['saved_event'])) ? $_SESSION['saved_event']->web : "") ?>" /></td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=191'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
      
  </form>
</div>