<?php
  require_once("include/headers.php");
  
  if (isset($_POST['type']) && isset($_POST['firstname']) && isset($_POST['lastname']) && 
      isset($_POST['address']) && isset($_POST['npa']) && isset($_POST['city']) && 
      isset($_POST['country']) && isset($_POST['tel']) && isset($_POST['email']) && 
      isset($_POST['password1']) && isset($_POST['password2'])){
    
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
      $person = new Miki_person();
      $person->type = stripslashes($_POST['type']);
      $person->firstname = stripslashes($_POST['firstname']);
      $person->lastname = stripslashes($_POST['lastname']);
      $person->address = stripslashes($_POST['address']);
      $person->npa = stripslashes($_POST['npa']);
      $person->city = stripslashes($_POST['city']);
      $person->country = stripslashes($_POST['country']);
      $person->tel1 = stripslashes($_POST['tel']);
      $person->email1 = stripslashes($_POST['email']);
      $person->language = 'fr';
      $person->company_id = 'NULL';
      $person->save();
      
      $account = new Miki_account();
      $account->person_id = $person->id;
      $account->username = $person->email1;
      $account->state = 1;
      $account->set_password($_POST['password1']);
      $account->save();
      
      $_SESSION['miki_user_id'] = $person->id;
      
      $person->send_inscription($_POST['password1'], false);
			
      // OK
      echo "<div>
              <div id='result'>1</div>
              <div id='msg'>" ._("Votre compte a été créé avec succès") ."</div>
            </div>";
    }catch(Exception $e){
      // erreur
      echo "<div>
              <div id='result'>0</div>
              <div id='msg'>" .$e->getMessage() ."</div>
            </div>";
    }
  }
  else{
    // erreur
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Il manque des informations") ."</div>
          </div>";
  }
?>