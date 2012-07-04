<?php
  require_once("include/headers.php");
  
  // vérifie si on utilise Ajax ou non
  $ajax = (isset($_POST['ajax']) && $_POST['ajax'] == 1);
  
  // recherche si la personne est connectée
  if (isset($_SESSION['miki_user_id']))
    $person = new Miki_person($_SESSION['miki_user_id']);
  else{
    // erreur
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Il manque des informations") ."</div>
          </div>";
  }
  
  if (isset($_POST['type']) && isset($_POST['firstname']) && isset($_POST['lastname']) && 
      isset($_POST['address']) && isset($_POST['npa']) && isset($_POST['city']) && 
      isset($_POST['country']) && isset($_POST['tel']) && isset($_POST['email'])){
    
    // si les mots de passe ne concordent pas
    if ($_POST['password1'] != $_POST['password2']){
      // erreur
      echo "<div>
              <div id='result'>0</div>
              <div id='msg'>" ._("Les mots de passe ne concordent pas") ."</div>
            </div>";
      exit();
    }
    
    try{
      $person->type = stripslashes($_POST['type']);
      $person->firstname = stripslashes($_POST['firstname']);
      $person->lastname = stripslashes($_POST['lastname']);
      $person->address = stripslashes($_POST['address']);
      $person->npa = stripslashes($_POST['npa']);
      $person->city = stripslashes($_POST['city']);
      $person->country = stripslashes($_POST['country']);
      $person->tel1 = stripslashes($_POST['tel']);
      $person->email1 = stripslashes($_POST['email']);
      $person->update();
      
      $account = new Miki_account();
      $account->load_from_person($person->id);
      $account->username = $person->email1;
      
      if ($_POST['password1'] != "")
        $account->set_password($_POST['password1']);
        
      $account->update();
      
      //$person->send_inscription($_POST['password1'], false);
      
      // OK
      send_result(true, _("Votre compte a été modifié avec succès"), $ajax);
    }catch(Exception $e){
      // erreur
      send_result(false, $e->getMessage(), $ajax);
    }
  }
  else{
    // erreur
    send_result(false, _("Il manque des informations"), $ajax);
  }
?>