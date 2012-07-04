<?php require_once("include/payement_type.php"); ?>

<head>
  <?php
    /**
     * Affiche un événement
     */
     
    // récupert la bonne adresse
    if (isset($_REQUEST['eid']) && is_numeric($_REQUEST['eid'])){
      try{
        $element = new Miki_event($_REQUEST['eid']);
      }
      catch(Exception $e){
        miki_redirect($_SESSION['url_back']);
      }
    }
    else{
      miki_redirect($_SESSION['url_back']);
    }
    
    require_once("config/event_category.php");
    
    // définit si on doit affocher les informations de la société du membre
    $print_company_infos = false; 
    
    // définit la taille des images  
    $image_width = 300; // en pixels
    $image_height = 200; // en pixels
    
    // récupert la configuration des événements concernant les inscriptions
    if ($element->online_subscription == 1)
      $event_subscription = Miki_configuration::get("event_subscription");
    else
      $event_subscription = 0;
    
    // récupert les images de l'événement
    $pictures = $element->get_pictures($_SESSION['lang']);
    
    // récupert les documents
    if (isset($element->files[$_SESSION['lang']])){
      $files = $element->files[$_SESSION['lang']];
      $tab_temp = array();
      foreach($files as $index => $f){
        if (!$element->is_picture($f))
          $tab_temp[$f] = $element->files_name[$_SESSION['lang']][$index];
      }
      $files = $tab_temp;
    }
    else
      $files = array();
  
    // ainsi que les autres informations
    $title = $element->title[$_SESSION['lang']];
    $description = $element->description[$_SESSION['lang']];
    $date_start = date("d/m/Y H:i", strtotime($element->date_start));
    $date_stop = (date("d/m/Y H:i", strtotime($element->date_stop)));
    $date_start_rfc = date("Y-m-d\TH:i", strtotime($element->date_start));
    $date_stop_rfc = date("Y-m-d\TH:i", strtotime($element->date_stop));
    $date_start_text = "<time itemprop='startDate' datetime='$date_start_rfc'>$date_start</time>";
    $date_stop_text = "<time itemprop='endDate' datetime='$date_stop_rfc'>$date_stop</time>";
    
    $address = "<div itemprop='location' itemscope itemtype='http://schema.org/PostalAddress'>";
    
    if ($element->place != "")
      $address .= "<div>$element->place</div>";
    
    if ($element->address != "")
      $address .= "<div itemprop='streetAddress'>$element->address</div>";
    
    if ($element->city != "")
      $address .= "<div itemprop='addressLocality'>$element->city</div>";
    
    if ($element->region != "" && $element->country != "")
      $address .= "<div><span itemprop='addressRegion'>$element->region</span>, <span itemprop='addressCountry'>$element->country</span></div>";
    elseif ($element->country != "")
      $address .= "<div itemprop='addressCountry'>$element->country</div>";
    
    $link = $element->get_url_simple($_SESSION['lang']);
    
    if ($element->entrance_type == 0)
      $entrance =  _('Libre');
    else
      $entrance = $element->entrance_price .' ' .$element->entrance_currency;
    
    // affecte le titre à la page (modifie le titre du gabarit)
    $title_h1 = $title;
  ?>
  
  <link rel="stylesheet" href="css/events.css" />
  
  <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
  <script type="text/javascript" src="scripts/gmap3.min.js"></script>
  
  <link rel="stylesheet" href="css/jquery-ui/tabs.css" />
    
  <link rel="stylesheet" href="scripts/slider-evolution/themes/carbono/jquery.slider.css" />
  <script type="text/javascript" src="scripts/slider-evolution/jquery.slider.min.js"></script>
  
  <link rel="stylesheet" href="css/iconize.css" />
  
  <style type="text/css">
    
    table.element tr td:first-child{
      padding-right: 20px;
      font-weight: bold;
      min-width: 170px;
    }
    
    table.element td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
    }
    
    div.event_files a.icon{
      padding: 5px 0 5px 20px;
      background-position: center left;
    }
    
    .map_disclaimer{
      font-size: 0.7em;
      margin-bottom: 0;
    }
    
  </style>
</head>

<script type="text/javascript">

  $(document).ready(function() {
    
    // pour l'affichage de la carte Google Map
    $('.event_map').gmap3(
      { action: 'addMarker',
        address: "<?php echo addcslashes("$element->address $element->npa $element->city $element->country", '"'); ?>",
        infowindow:{
          options:{
            content: "<?php echo $title; ?>"
          }
        },
        map:{
          center: true,
          zoom: 15
        }
      }
    );
    
    // pour l'affichage des images et du slide si plusieurs images
    $(".event_pictures").slideshow({
      width      : <?php echo $image_width; ?>,
      height     : <?php echo $image_height; ?>,
      transition : 'square',
      slideshow  : false,
      navigation : true,
      control    : false
    });
    
    // met en place les onglets
    var tabs = $( "#tabs" ).tabs();
    
    $("#form_event_subscription").validate({
      rules: {
        lastname: "required",
        firstname: "required",
        address: "required",
        npa: {
          required: true,
          digits: true
        },
        city: "required",
        tel: "required",
        email: {
          required: true,
          email: true
        }
      },
      submitHandler: function(form) {
        form_event_subscription_send();
      }
    });
  });
  
  // pour l'envoi du formulaire d'inscription
  function form_event_subscription_send(){
    
    // récupert le moyen de paiement
    var payement_type = $('select[name=payement_type]').val();
  
    var form = $("#form_event_subscription");
    var params = form.serialize();
    var url = form.attr('action');
    
    // masque le bouton et affiche le gif animé pour patienter
    form.find(':submit').css("display","none");
    $("<img src='pictures/loader.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));

    /* Send the data using post and put the results in a div */
    $.post(url, params,
      function(data){
        var result = $(data).find('#result');
        var msg = $(data).find('#msg');
        
        // si succès
        if (result.html() == '1'){
          // si paiement via Paypal ou carte de crédit, on redirige sur Paypal
          if (payement_type == 'payement_paypal'){
            document.location = "<?php echo SITE_URL; ?>/paypal_redirect.php";
          }else{
            // réaffiche le bouton
            form.find(':submit').next("img").remove();
            form.find(':submit').css("display","inline");
            
            // affiche le message d'erreur
            $("#form_event_subscription_results_success p").empty().append(msg.html());
            $("#form_event_subscription_results_success").css('display', 'block').delay(10000).hide("slow");
          }
        }
        // si erreur
        else{
          // réaffiche le bouton
          form.find(':submit').next("img").remove();
          form.find(':submit').css("display","inline");
          
          // affiche le message d'erreur
          $("#form_event_subscription_results_error p").empty().append(msg.html());
          $("#form_event_subscription_results_error").css('display', 'block').delay(10000).hide("slow");
        }
      }
    );
  }
  
</script>


<?php
echo "<div class='event' itemscope itemtype='http://schema.org/PostalAddress'>";
      
      // affiche les images s'il y en a 
      if (is_array($pictures)){
        echo "<div class='event_pictures_container'>
                <div class='event_pictures'>";
                  foreach($pictures as $pic){
                    echo "<div><img src='timthumb.php?src=" .urlencode($element->file_path .$pic) ."&w=$image_width&h=$image_height' alt=\"$title\" /></div>";
                  }
        echo "  </div>
              </div>";
      }
?>
        
        <!-- Si des images sont affichées, on donne une marge gauche aux onglets -->
        <div id="tabs" style="width: <?php echo (is_array($pictures)) ? (904 - 20 - $image_width) ."px;'" : "100%"; ?>"">
          
          <!-- Les onglets -->
          <ul>
            <li><a href="#tab_event_text"><img src='pictures/information.png' alt='' /><?php echo _("Informations"); ?></a></li>
            <li><a href="#tab_event_description"><img src='pictures/text.png' alt='' /><?php echo _("Description"); ?></a></li>
            <?php
              // affiche l'onglet des participants si autorisé
              if (Miki_configuration::get('event_view_subscriptions') == 1)
                echo "<li><a href='#tab_event_participants'><img src='pictures/people.png' alt='' />&nbsp;" ._("Participants") ."</a></li>";
              
              // affiche les téléchargements s'il y en a  
              if (sizeof($files) > 0)
                echo "<li><a href='#tab_event_files'><img src='pictures/clip.png' alt='' />" ._("Documents") ."</a></li>";
                
              // si les inscriptions sont ouvertes
              if ($event_subscription > 0)
                echo "<li><a href='#tab_event_subscription'><img src='pictures/checkbox.png' alt='' />" ._("Inscription") ."</a></li>";
            ?>
          </ul>
          
          <!-- Contenu de l'onglet "Informations" -->
          <div id="tab_event_text" class="event_text">
            <p><?php echo _("Informations concernant l'événement") ." $title"; ?></p>
            <table class="element">
              <tr>
                <td><?php echo _("Catégorie"); ?> : </td>
                <td><?php echo $categories[$element->category]; ?></td>
              </tr>
              <tr>
                <td><?php echo _("Début"); ?> : </td>
                <td><?php echo $date_start_text; ?></td>
              </tr>
              <tr>
                <td><?php echo _("Fin"); ?> : </td>
                <td><?php echo $date_stop_text; ?></td>
              </tr>
              <tr>
                <td style="vertical-align: top;"><?php echo _("Lieu"); ?> : </td>
                <td><?php echo $address; ?></td> 
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td><?php echo _("Participants"); ?> : </td>
                <td><?php echo $element->get_nb_participants() ." " ._("inscrits"); ?></td>
              </tr>
              <tr>
                <td><?php echo _("Places disponibles"); ?> : </td>
                <td>
                  <?php 
                    if ($element->max_participants == 0)
                      echo _("illimité");
                    else
                      echo ($element->max_participants - $element->get_nb_participants()) ." " ._("places restantes"); ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td style="vertical-align: top;"><?php echo _("Site Internet"); ?> : </td>
                <td colspan="3">
                  <?php if ($element->web != "") echo "<a href='$element->web' title='$element->web' target='_blank' itemprop='url'>$element->web</a>"; ?>
                </td>
              </tr>
              <tr>
                <td style="font-weight:bold;vertical-align:top"><?php echo _("Entrée"); ?> : </td>
                <td colspan="3">
                  <div style="float:left"><?php echo $entrance; ?></div>
                  <div style="margin-left:100px;font-style:italic"><?php echo $element->entrance_text; ?></div>
                </td>
              </tr>
              <tr>
                <td style="vertical-align:top;font-weight:bold"><?php echo _("Organisateur"); ?> : </td>
                <td style="vertical-align:top;padding-right:0">
                  <?php
                    // récupert l'organisateur
                    if (is_numeric($element->organizer)){
                      try{
                        $organizer = new Miki_person($element->organizer);
                        
                        // si on doit affocher les informations de la société du membre
                        if ($print_company_infos){
                          $company_organizer = new Miki_company($organizer->company_id);
                          
                          if ($company_organizer->logo != ""){
                            $size = get_image_size(URL_BASE ."pictures/logo_companies/$company_organizer->logo", 100, 100);
                            echo "<a href='[miki_page='membre_details_admin' params='pid=$organizer->id']' title='Voir le profil de $organizer->firstname $organizer->lastname'>
                                    <img src='" .URL_BASE ."pictures/logo_companies/$company_organizer->logo' style='width:" .$size[0] ."px;height:" .$size[1] ."px;float:left;margin-right:10px;border:0' />
                                  </a>";
                          }
                        }
                        
                        echo "<div style='float:left'>
                                <a href='[miki_page='membre_details_admin' params='pid=$organizer->id']' title='Voir le profil de $organizer->firstname $organizer->lastname'>
                                  $organizer->firstname $organizer->lastname
                                </a>
                                <br />
                                $company_organizer->name
                              </div>";
                      }
                      catch(Exception $e){
                        echo $element->organizer;
                      }
                    }
                    else
                      echo $element->organizer;
                  ?>
                </td>
              </tr>
              <tr>
                <td colspan="2">&nbsp;</td>
              </tr>
              <tr>
                <td style="vertical-align:top;font-weight:bold"><?php echo _("Posté par"); ?> : </td>
                <td style="vertical-align:top;padding-right:0">
                  <?php
                    if (is_numeric($element->poster)){
                      try{
                        $poster = new Miki_person($element->poster);
                        
                        // si on doit affocher les informations de la société du membre
                        if ($print_company_infos){
                          $company_poster = new Miki_company($poster->company_id);
                        }
                        
                        echo "<div style='float:left'>
                                <a href='[miki_page='membre_details_admin' params='pid=$poster->id']' title='Voir le profil de $poster->firstname $poster->lastname'>
                                  $poster->firstname $poster->lastname
                                </a>";
                                
                                // si on doit affocher les informations de la société du membre
                                if ($print_company_infos){
                                  echo "<br />$company_poster->name";
                                }
                                
                        echo "</div>";
                      }
                      catch(Exception $e){
                        echo SITENAME;
                      }
                    }
                    else{
                      echo SITENAME;
                    }
                  ?>
                </td>
              </tr>
            </table>
          </div>
          
          <!-- Contenu de l'onglet "Description" -->
          <div id="tab_event_description" class="event_description" itemprop="description">
              <p><?php echo $description; ?></p>
          </div>
          
          <?php
            // affiche l'onglet des participants si autorisé
            if (Miki_configuration::get('event_view_subscriptions') == 1){
              echo "<div id='tab_event_participants' class='event_participants' itemprop='attendees'>";
                      
                // récupert tous les participants à l'événement
                $participants = $element->get_participants();

                if (sizeof($participants) == 0){
                  echo "<p>" ._("Aucune inscription pour le moment") ."</p>";
                }
                else{
                  echo "<ul>";
                  
                  // parcourt tous les participants
                  foreach($participants as $participant){
                    // récupert la personne représentant le participant
                    $p = $participant[1];
                    
                    // vérifie si une société est liée à la personne en cours
                    if ($p->company_id != "" && $p->company_id != "NULL")
                      $company = new Miki_company($p->company_id);
                    else
                      $company = false;
                    
                    // affiche les informations du participant
                    echo "<li itemscope itemtype='http://schema.org/Person'>
                            <!--<a href=\"[miki_page='membre_details_admin' params='pid=$p->id']\" title=\"Voir le profil de $p->firstname $p->lastname\">-->
                              <span itemprop='givenName'>$p->firstname</span> <span itemprop='familyName'>$p->lastname</span>, <span itemscope itemtype='http://schema.org/PostalAddress'><span itemprop='addressLocality'>$p->city</span></span>
                            <!--</a>-->";

                            // si une société est liée à l'event
                            if ($company){
                              echo "<div itemprop='worksFor' itemscope itemtype='http://schema.org/Organization'>";
                              if ($company->logo != ""){
                                $size = get_image_size("pictures/logo_companies/$company->logo", 100, 100);
                                echo "<!--<a href=\"[miki_page='membre_details_admin' params='pid=$p->id']\" title=\"Voir le profil de $p->firstname $p->lastname\">-->
                                        <img itemprop='image' src='" .SITE_URL ."/pictures/logo_companies/$company->logo' border='0' style='width:" .$size[0] ."px;height:" .$size[1] ."px;float:left;margin-right:10px' />
                                      <!--</a>-->";
                              }
                              
                              echo "<div><span itemprop='name'>$company->name</span> - <span itemprop='address' itemscope itemtype='http://schema.org/PostalAddress'><span itemprop='addressLocality'>$company->city</span>, <span itemprop='addressCountry'>$company->country</span></span></div>";
                              echo "</div>";
                            }
                        
                    echo "</li>";
                  }
                  
                  echo "</ul>";
                }
    
              echo "</div>";
            }
            
            // affiche l'onglet des documents s'il y en a
            if (sizeof($files) > 0){
              echo "<div id='tab_event_files' class='event_files'>
                      <p style='margin-bottom:10px'>" ._("Les fichiers suivants sont disponibles en téléchargement") ." : </p>
                      <ul>";
                      foreach($files as $file => $name){
                        echo "<li><a class='icon' href='pictures/events/$file' title=\"$name\" target='_blank'>$name</a></li>";
                      }
              echo "  </ul>
                    </div>";
            }
          ?>
          
          <?php
          // Affiche l'onglet "Inscription" si les inscriptions sont ouvertes
          if ($event_subscription > 0){
          ?>
          
            <div id="tab_event_subscription" class="event_subscription">
              <p><?php echo _("Inscription à l'événement") ." " .$title; ?></p>
              
              <?php
              
              // si les inscription ne sont ouvertes qu'aux membres et que la personne n'est pas connectée
              if ($event_subscription == 1 && !$miki_person){
                echo sprintf(_("Seuls les membres de %s peuvent s'inscrire."), SITENAME);
              }
              // si la personne est déjà inscrite
              elseif($miki_person && $element->is_subscribed($miki_person->id)){
                echo _("Vous êtes déjà inscrit à cet événement.");
              }
              elseif($element->max_participants > 0 && $element->max_participants - $element->get_nb_participants() <= 0){
                echo _("Il ne reste plus de place pour cet événement.");
              }
              // sinon, si les inscriptions sont ouvertes à tout le monde ou que le personne est connectée, on affiche le formulaire
              else{
                ?>
                <form id="form_event_subscription" action="event_subscription.php" method="post" name="formulaire" enctype="application/x-www-form-urlencoded">
                  
                  <input type="hidden" name="eid" value="<?php echo $element->id; ?>" />
                  
                  <div id="form_event_subscription_results_success" class="box_result_success" style="display: none;"><p></p></div>
                  <div id="form_event_subscription_results_error" class="box_result_error" style="display: none;"><p></p></div>
                  
                  <table class="element">
                    <tr>
                      <td><?php echo _("Places disponibles"); ?> : </td>
                      <td>
                        <?php 
                          if ($element->max_participants == 0)
                            echo _("illimité");
                          else
                            echo ($element->max_participants - $element->get_nb_participants()) ." " ._("places restantes"); ?>
                      </td>
                    </tr>
                    <tr>
                      <td style="vertical-align: top;"><?php echo _("Entrée"); ?> : </td>
                      <td colspan="3">
                        <div style="float:left"><?php echo $entrance; ?></div>
                        <div style="margin-left:100px; font-style:italic"><?php echo $element->entrance_text; ?></div>
                      </td>
                    </tr>
                    <?php
                    if ($element->accompanist > 0){
                    ?>
                    <tr>
                      <td><?php echo _("Accompagnants"); ?> : </td>
                      <td><?php echo $element->accompanist ." accompagnants " ._("sont autorisés pour chaque inscription"); ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    
                    <tr>
                      <td colspan='2'>&nbsp;</td>
                    </tr>
                    
                    <?php
                    // si le paiement s'effectue online et que l'inscription est payante, on affiche les moyens de paiement
                    if (Miki_configuration::get("event_online_payement") == 1 && $element->entrance_type == 1){
                      echo "<tr>
                              <td>" ._("Moyens de paiement") ." : <span style='color:#ff0000'>*</span></td>
                              <td>
                                <select name='payement_type' style='width:90%'>";
                                  foreach($miki_payement_type as $index => $payement_type){
                                    if (Miki_configuration::get($index) == 1){
                                      if ((in_array($index, array("payement_facture_avant", "payement_facture_apres", "payement_bank")) && $element->payement_online != 2) ||
                                          (in_array($index, array("payement_paypal")) && $element->payement_online != 0))
                                      echo "<option value='$index'>$payement_type</option>";
                                    }
                                  }
                      echo "    </select>
                              </td>
                            </tr>";
                    }
                    
                    // si tout le monde peut s'inscrire et qu'on est pas connecté, on affiche les champs pour les informations personnelles
                    if($event_subscription == 2 && !$miki_person){
                    ?>
                    
                      <tr>
                        <td><?php echo _("Nom :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="lastname" value="" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Prénom :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="firstname" value="" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Adresse :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="address" value="" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Code postal :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="npa" value="" style="width:100px" maxlength="6" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Localité :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="city" value="" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Pays :"); ?> <span style="color:#ff0000">*</span></td>
                        <td>
                          <select name="country" style="width:90%">
                            <?php
                              foreach($country_list as $key=>$c){
                                echo "<option value=\"$c\"" .(($c == "Suisse") ? "selected='selected'" : "") .">$c</option>"; 
                              }
                            ?>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td><?php echo _("Téléphone :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" name="tel" value="" /></td>
                      </tr>
                      <tr>
                        <td><?php echo _("Email :"); ?> <span style="color:#ff0000">*</span></td>
                        <td><input type="text" id="email" name="email" value="" /></td>
                      </tr>
                      <tr>
                        <td colspan="2" style="font-style: italic;"><?php echo _("Les champs munis d'une <span style='color:#ff0000'>*</span> sont obligatoires"); ?></td>
                      </tr>
                    
                    <?php
                    }
                    // sinon, si la personne est connectée
                    elseif($event_subscription == 2 && $miki_person){
                      echo "<tr>
                              <td colspan='2'>&nbsp;</td>
                            </tr>
                            <tr>
                              <td colspan='2' style='font-weight: normal;'>" 
                                ._("Vous serez inscrit en tant que") 
                                ."<br /><br />$miki_person->firstname $miki_person->lastname<br />$miki_person->address<br />$miki_person->npa $miki_person->city
                                  <br /><br />" ._("Si vous n'êtes pas cette personne ou que vous désirez inscrire quelqu'un d'autre, veuillez vous déconnecter de votre compte en") ." <a href='scripts/deconnection.php?goto=" .htmlentities(urlencode("http://" .$_SERVER["SERVER_NAME"] .$_SERVER["REQUEST_URI"])) ."' title='" ._("Se déconnecter") ."'>" ._("cliquant ici") ."</a>.
                              </td>
                            </tr>";
                    }
                  ?>
                  </table>
                  
                  <?php
                    // si tout le monde peut s'inscrire ou si seuls les membres peuvent s'inscrirent et que la personne est connectée, on affiche le bouton d'inscription
                    if ($event_subscription == 2 || ($event_subscription == 1 && $miki_person)){
                  ?>
                      <div style="text-align: right">
                        <input border="0" class="button_big1" type="submit" value="<?php echo _("Je m'inscris"); ?>" />
                      </div>
                  <?php
                    }
                  ?>
                  
                </form>
            <?php
              }
            ?>  
            </div>
          
          <?php
          }
          ?>
          
        </div>

        <div class='event_map'></div>
        
        <p class="map_disclaimer"><?php echo sprintf(_("La carte ci-dessus est basée sur les données renseignées par l'ogranisateur de l'événement. %s décline toute responsabilité en cas d'une potentielle erreur."), SITENAME); ?></p>
      </div>