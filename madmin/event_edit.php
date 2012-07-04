<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("../config/event_category.php");
    
    if (!test_right(63)){
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
      $event = new Miki_event($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  
    // pour la date de début
    $date_start = $event->date_start;
    
    if ($date_start == "0000-00-00 00:00:00")
      $date_start = "";
    
    if ($date_start != ""){
      $date_start    = explode(" ", $date_start);
      $time_start    = explode(":", $date_start[1]);
      $date_start    = explode("-", $date_start[0]);
      $year_start    = $date_start[0];
      $month_start   = $date_start[1];
      $day_start     = $date_start[2];
      $heure_start   = $time_start[0];
      $minutes_start = $time_start[1];
      
      if (substr($month_start, 0, 1) == '0')
        $month_start = mb_substr($month_start, 1, mb_strlen($month_start));
      if (substr($day_start, 0, 1) == '0')
        $day_start = mb_substr($day_start, 1, mb_strlen($day_start));
    }
    else{
      $year_start    = "";
      $month_start   = "";
      $day_start     = "";
      $heure_start   = "";
      $minutes_start = "";
    }
    
    // pour la date de fin
    $date_stop = $event->date_stop;
    
    if ($date_stop == "0000-00-00 00:00:00")
      $date_stop = "";
    
    if ($date_stop != ""){
      $date_stop    = explode(" ", $date_stop);
      $time_stop    = explode(":", $date_stop[1]);
      $date_stop    = explode("-", $date_stop[0]);
      $year_stop    = $date_stop[0];
      $month_stop   = $date_stop[1];
      $day_stop     = $date_stop[2];
      $heure_stop   = $time_stop[0];
      $minutes_stop = $time_stop[1];
      
      if (substr($month_stop, 0, 1) == '0')
        $month_stop = mb_substr($month_stop, 1, mb_strlen($month_stop));
      if (substr($day_stop, 0, 1) == '0')
        $day_stop = mb_substr($day_stop, 1, mb_strlen($day_stop));
    }
    else{
      $year_stop    = "";
      $month_stop   = "";
      $day_stop     = "";
      $heure_stop   = "";
      $minutes_stop = "";
    }
    
    // récupert le nombre de fichiers
    $nb_pictures = 0;
    $langs = Miki_language::get_all_languages();
    foreach($langs as $lang){
      if (isset($event->files[$lang->code]) && is_array($event->files[$lang->code])){
        $nb_pictures += sizeof($event->files[$lang->code]);
      }
      else{
        $nb_pictures += 1;
      }
    }
  ?>
  
  <script type="text/javascript" src="scripts/SimpleTabs.js"></script>
  <link rel="stylesheet" type="text/css" href="scripts/SimpleTabs.css" />
  
  <script type="text/javascript" src="scripts/mootools_more.js"></script>
  
  <script type="text/javascript" src="scripts/calendar/js/calendar-eightysix-v1.1.js"></script>
  <link type="text/css" media="screen" href="scripts/calendar/css/calendar-eightysix-v1.1-default.css" rel="stylesheet" />
  
  <link rel="stylesheet" type="text/css" href="../scripts/milkbox/milkbox.css" />
  <script type="text/javascript" src="../scripts/milkbox/mootools-1.2.3.1-assets.js"></script>
  <script type="text/javascript" src="../scripts/milkbox/milkbox.js"></script>
  
  <link rel="stylesheet" type="text/css" href="../css/iconize.css" />
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_edit_event'),{
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
      
      new CalendarEightysix('date_debut', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($event) && $date_start != "") echo ", 'defaultDate': '$day_start/$month_start/$year_start'"; ?> });
      new CalendarEightysix('date_fin', { 'startMonday': true, 'format': '%d/%m/%Y', 'slideTransition': Fx.Transitions.Back.easeOut, 'draggable': true <?php if (isset($event) && $date_stop != "") echo ", 'defaultDate': '$day_stop/$month_stop/$year_stop'"; ?> });
      
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
    
    var nb_pictures = <?php echo $nb_pictures; ?>;
    
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
    
    #form_edit_event td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_edit_event input[type=text]{
      width: 250px;
    }
    
    #form_edit_event textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=191"><?php echo _("Liste événements"); ?></a> > Modification d'un événement
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modification d'un événement"); ?></h1>  

  <form id="form_edit_event" action="event_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $event->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="<?php echo $nb_pictures; ?>" />
    
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
                      <td><input type='text' id='title_$l->code' name='title[$l->code]' value=\"" .((isset($event)) ? $event->title[$l->code] : "") ."\" class='required' /></td>
                    </tr>
                    <tr>
                      <td>" ._("Description : ") ."</td>
                      <td><textarea name='description[$l->code]' id='description_$l->code' class='required'>" .((isset($event)) ? $event->description[$l->code] : "") ."</textarea></td>
                    </tr>
                    <tr>
                      <td colspan='2'><a href='#' onclick='javascript:copy_content(\"$l->code\");'>" ._("Copier le contenu de cette langue dans les autres langues") ."</a></td>
                    </tr>
                    <tr>
                      <td style='vertical-align:top'>Fichiers</td>
                      <td>
                        <a name='pictures'></a>";
                  
                  
                  // affiche les inputs pour les fichiers de la langue en cours
                  if (isset($event->files[$l->code]) && sizeof($event->files[$l->code]) > 0){
                    for($y=1; $y<=sizeof($event->files[$l->code]); $y++){
                        
                      $file = "../pictures/events/" .$event->files[$l->code][$y - 1];
        
                      $filename = (is_array($event->files_name[$l->code]) && isset($event->files_name[$l->code][$y - 1])) ? $event->files_name[$l->code][$y - 1] : "";
                      
                      echo "<div style='margin-bottom:10px;width:100%;overflow:hidden'>
                              <div style='float:left'>
                                <input type='hidden' name='picture_lang$x' value='$l->code' />
                                <input type='text' name='picture_name$x' value=\"$filename\" /><br />
                                <input type='file' name='picture$x' />
                              </div>
                              <div style='float:left;margin-left:5px'>
                                <a href='$file' target='_blank' title='" ._("Télécharger le fichier") ."' class='icon'>" .$event->files[$l->code][$y - 1] ."</a>&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;
                                <a href='event_delete_file.php?id=$event->id&file=" .$event->files[$l->code][$y - 1] ."' title='" ._("Supprimer ce fichier") ."'>" ._("Supprimer ce fichier") ."</a><br />
                                (" ._("Laissez vide pour conserver les fichiers") .")
                              </div>
                            </div>";
                            
                      $x++;
                    }
                  }
                  // si aucun fichier pour la langue en cours, on affiche les inputs vides
                  else{
                    echo "<input type='hidden' name='picture_lang$x' value='$l->code' />
                          <input type='text' name='picture_name$x' value='' /><br />
                          <input type='file' name='picture$x' />";
                          
                    $x++;
                  }
                  
                  // puis on affiche les liens pour ajouter un nouveau fichier
                  echo "<span id='ajouter_image_$l->code'>
                          <a href='#logos' onclick=\"add_logo('$l->code');\" title='Ajouter un fichier' style='margin:0 5px'>
                            <img src='pictures/add.png' alt='Ajouter un fichier' style='border:0;vertical-align:middle' />
                          <a>
                          <a href='#logos' onclick=\"add_logo('$l->code');\" title='Ajouter un fichier'>Ajouter un fichier</a>
                        </span>";
                  
                echo "</td>
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
        <td>Visibilité <span style="color:#ff0000">*</span></td> 
        <td>
          <select name="type">
            <option value="0" <?php if (isset($event) && $event->type == 0) echo " selected='selected'"; ?>>Publique</option>
            <option value="1" <?php if (isset($event) && $event->type == 1) echo " selected='selected'"; ?>>Privé</option>
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
                if (isset($event) && $event->category == $key) echo " selected='selected'"; 
                echo ">$cat</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Tags</td>
        <td><input type="text" class="" name="tags" value="<?php echo ((isset($event)) ? $event->tags : "") ?>" /></td>
      </tr>
      <tr>
        <td>Organisateur <span style="color:#ff0000">*</span></td>
          <td><input type="text" class="required" name="organizer" value="<?php echo ((isset($event)) ? $event->organizer : Miki_configuration::get('sitename')) ?>" /></td>
      </tr>
      <tr>
        <td>Date de début <span style="color:#ff0000">*</span></td> 
        <td><input id="date_debut" type="text" class="required" name="date_start" maxlength="25" style="width:150px" value="<?php if ($day_start != "") echo "$day_start/$month_start/$year_start"; ?>" /></td>
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
              
              echo "<option value='$heure'";
              if ($heure == $heure_start)
                echo " selected='selected'";
              echo ">$heure</option>\n";
            }
            ?>
          </select>
          &nbsp;h&nbsp;
          <select name="minutes_start">
            <option value='00' <?php echo ($minutes_start == '00') ? "selected='selected'" : ""; ?>>00</option>
            <option value='10' <?php echo ($minutes_start == '10') ? "selected='selected'" : ""; ?>>10</option>
            <option value='20' <?php echo ($minutes_start == '20') ? "selected='selected'" : ""; ?>>20</option>
            <option value='30' <?php echo ($minutes_start == '30') ? "selected='selected'" : ""; ?>>30</option>
            <option value='40' <?php echo ($minutes_start == '40') ? "selected='selected'" : ""; ?>>40</option>
            <option value='50' <?php echo ($minutes_start == '50') ? "selected='selected'" : ""; ?>>50</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Date de fin <span style="color:#ff0000">*</span></td> 
        <td><input id="date_fin" type="text" class="required" name="date_stop" maxlength="25" style="width:150px" value="<?php if ($day_start != "") echo "$day_stop/$month_stop/$year_stop"; ?>" /></td>
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
              
              echo "<option value='$heure'";
              if ($heure == $heure_stop)
                echo " selected='selected'";
              echo ">$heure</option>\n";
            }
            ?>
          </select>
          &nbsp;h&nbsp;
          <select name="minutes_stop">
            <option value='00' <?php echo ($minutes_stop == '00') ? "selected='selected'" : ""; ?>>00</option>
            <option value='10' <?php echo ($minutes_stop == '10') ? "selected='selected'" : ""; ?>>10</option>
            <option value='20' <?php echo ($minutes_stop == '20') ? "selected='selected'" : ""; ?>>20</option>
            <option value='30' <?php echo ($minutes_stop == '30') ? "selected='selected'" : ""; ?>>30</option>
            <option value='40' <?php echo ($minutes_stop == '40') ? "selected='selected'" : ""; ?>>40</option>
            <option value='50' <?php echo ($minutes_stop == '50') ? "selected='selected'" : ""; ?>>50</option>
          </select>
        </td>
      </tr>
      <tr>
        <td>Lieu de l'événement (bâtiment, locaux, etc.)</td>
        <td><input type="text" class="" name="place" value="<?php echo ((isset($event)) ? $event->place : "") ?>" /></td>
      </tr>
      <tr>
        <td>Adresse</td>
        <td><input type="text" class="" name="address" value="<?php echo ((isset($event)) ? $event->address : "") ?>" /></td>
      </tr>
      <tr>
        <td>Code postal</td>
        <td><input type="text" class="numeric" name="npa" value="<?php echo ((isset($event)) ? $event->npa : "") ?>" /></td>
      </tr>
      <tr>
        <td>Localité</td>
        <td><input type="text" class="" name="city" value="<?php echo ((isset($event)) ? $event->city : "") ?>" /></td>
      </tr>
      <tr>
        <td>Région</td>
        <td><input type="text" class="" name="region" value="<?php echo ((isset($event)) ? $event->region : "") ?>" /></td>
      </tr>
      <tr>
        <td>Pays</td>
        <td>
          <select name="country">
            <?php
              foreach($country_list as $key=>$el){
                echo "<option value=\"$el\"";
                if (isset($event) && $event->country == $el) echo " selected='selected'"; 
                elseif (!isset($event) && $el == "Suisse") echo " selected='selected'";
                echo ">$el</option>"; 
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Nb max. de participants <span style="color:#ff0000">*</span></td>
        <td><input type="text" name="max_participants" class="required numeric" value="<?php echo ((isset($event)) ? $event->max_participants : "0") ?>" /> (0 = infini)</td>
      </tr>
      <tr>
        <td>Nb accompagnants autorisé</td>
        <td><input type="text" class="numeric" name="accompanist" value="<?php echo ((isset($event)) ? $event->accompanist : "") ?>" /></td>
      </tr>
      <tr>
        <td>Activer les inscriptions online</td>
        <td><input type="checkbox" name="online_subscription" value="1" onclick="javascript:change_online_subscription(this);" <?php echo (isset($event) && $event->online_subscription == 1) ? "checked='checked'" : ""; ?> /></td>
      </tr>
      <tr style="display: <?php echo (isset($event) && $event->online_subscription == 1) ? "table-row" : "none"; ?>;">
        <td>M'informer lors de chaque inscription</td>
        <td>
          <input type="checkbox" name="subscription_information" value="1" onclick="javascript:change_subscription_information(this);" <?php echo (isset($event) && $event->subscription_information == 1) ? "checked='checked'" : ""; ?> />
          <span style="display: <?php echo (isset($event) && $event->subscription_information == 1) ? "inline" : "none"; ?>";>
            &nbsp;&nbsp;
            A cette adresse e-mail : 
            <input type="text" class="email" name="subscription_information_email" value="<?php echo (isset($event)) ? $event->subscription_information_email : ""; ?>" />
          </span>
        </td>
      </tr>
      <tr>
        <td>Type d'entrée <span style="color:#ff0000">*</span></td> 
        <td>
          <select id="select_entrance_type" name="entrance_type" onchange="javascript:change_entrance();">
            <option value="0" <?php if (isset($event) && $event->entrance_type == 0) echo " selected='selected'"; ?>>Gratuit</option>
            <option value="1" <?php if (isset($event) && $event->entrance_type == 1) echo " selected='selected'"; ?>>Payant</option>
          </select>
          
          <div id="manage_entrance" style="margin-top:10px;padding:5px;border:solid 1px #EEEEEE">
            <table>
              <tr>
                <td>Prix de l'entrée</td>
                <td><input type="text" class="currency" name="entrance_price" value="<?php echo ((isset($event)) ? $event->entrance_price : "") ?>" /></td>
              </tr>
              <tr>
                <td>Monnaie</td>
                <td>
                  <select name="entrance_currency">
                    <option value="CHF" <?php if (isset($event) && $event->entrance_currency == "CHF") echo " selected='selected'"; ?>>Francs suisses (CHF)</option>
                    <option value="EUR" <?php if (isset($event) && $event->entrance_currency == "€") echo " selected='selected'"; ?>>Euro (€)</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Paiement online obligatoire</td>
                <td>
                  <select name="payement_online">
                    <option value="0" <?php if (isset($event) && $event->payement_online == 0) echo " selected='selected'"; ?>>Pas de paiement online</option>
                    <option value="1" <?php if (isset($event) && $event->payement_online == 1) echo " selected='selected'"; ?>>Paiement online facultatif</option>
                    <option value="2" <?php if (isset($event) && $event->payement_online == 2) echo " selected='selected'"; ?>>Paiement online obligatoire </option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>Commentaire sur le prix d'entrée</td>
                <td><textarea name="entrance_text"><?php echo ((isset($event)) ? $event->entrance_text : "") ?></textarea></td>
              </tr>
            </table>
          </div>
          
        </td>
      </tr>
      <tr>
        <td>Web</td>
        <td><input type="text" class="requiredLink" name="web" value="<?php echo ((isset($event)) ? $event->web : "") ?>" /></td>
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