<?php
  /**
   * Inscrit une personne à un événement
   */
   
  require_once("include/headers.php");
   
  // récupert l'événement
  if (isset($_REQUEST['eid']) && is_numeric($_REQUEST['eid'])){
    try{
      $element = new Miki_event($_REQUEST['eid']);
    }
    catch(Exception $e){
      echo "<div>
              <div id='result'>0</div>
              <div id='msg'>" ._("L'événement demandé n'existe pas") ."</div>
            </div>";
      exit();
    }
  }
  else{
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Aucun événement n'a été défini") ."</div>
          </div>";
    exit();
  }
  
  // récupert le membre si la personne est connectée (false si pas connectée)
  $person = is_connected();
  
  // récupert la configuration des événements concernant les inscriptions
  $event_subscription = Miki_configuration::get("event_subscription");
  
  // si les inscriptions sont fermées, on abandonne
  if ($event_subscription == 0){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Les inscriptions sont fermées pour cet événement") ."</div>
          </div>";
    exit();
  }
  // si seuls les membres du site peuvent s'inscrire et que la personne n'est pas connectée, on abandonne
  elseif($event_subscription == 1 && !$person){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>";
              sprintf(_("Seuls les membres du site %s peuvent s'inscrire à cet événement"), SITENAME);
      echo "</div>
          </div>";
    exit();
  }
  
  // si la personne n'est pas connectée et que les informations personnelles n'ont pas été saisies, on abandonne
  if (!$person && 
      (!isset($_REQUEST['lastname'])  || empty($_REQUEST['lastname']) || 
       !isset($_REQUEST['firstname']) || empty($_REQUEST['firstname']) || 
       !isset($_REQUEST['address'])   || empty($_REQUEST['address']) || 
       !isset($_REQUEST['npa'])       || empty($_REQUEST['npa']) || 
       !isset($_REQUEST['city'])      || empty($_REQUEST['city']) || 
       !isset($_REQUEST['country'])   || empty($_REQUEST['country']) || 
       !isset($_REQUEST['tel'])       || empty($_REQUEST['tel']) || 
       !isset($_REQUEST['email'])     || empty($_REQUEST['email']))){
    
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Certaines informations obligatoires sont manquantes") ."</div>
          </div>";
    exit();
  }
  // si la personne n'est pas connectée, on crée une nouvelle personne
  elseif(!$person){
    try{
      $person = new Miki_person();
      $person->lastname   = $_REQUEST['lastname'];
      $person->firstname  = $_REQUEST['firstname'];
      $person->address    = $_REQUEST['address'];
      $person->npa        = $_REQUEST['npa'];
      $person->city       = $_REQUEST['city'];
      $person->country    = $_REQUEST['country'];
      $person->tel1       = $_REQUEST['tel'];
      $person->email1     = $_REQUEST['email'];
      $person->save();
    }
    catch(Exception $e){
      echo "<div>
              <div id='result'>0</div>
              <div id='msg'>" ._("Une erreur est survenue lors de l'inscription") ."</div>
            </div>";
      exit();
    }
  }
  
  // vérifie qu'il reste des places disponibles
  if ($element->max_participants > 0 && ($element->max_participants - $element->get_nb_participants()) <= 0){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Il ne reste plus de place pour cet événement") ."</div>
          </div>";
    exit();
  } 
      
  try{
    // inscrit la personne à l'événement
    $element->subscribe($person->id, 1, false);
  }
  catch(Exception $e){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Une erreur est survenue lors de l'inscription") ." : " .$e->getMessage() ."</div>
          </div>";
    exit();
  }
  
  // stock les informations de la personne et de l'événement dans la session
  // afin que ces éléments puissent être récupérés si le paiement s'effectue via Paypal 
  $_SESSION['paypal_person'] = $person;
  $_SESSION['paypal_event'] = $element;
  
  echo "<div>
          <div id='result'>1</div>
          <div id='msg'>" ._("Votre inscription a été effectuée avec succès") ."</div>
        </div>";
?>