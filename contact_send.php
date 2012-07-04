<?php
  require_once('include/headers.php');
  require_once("set_links.php");
  require_once('recaptchalib.php');
  
  
  // récupert les information dans une personne (ne sera pas sauvée dans la BDD)
  $person = new Miki_person();
  $person->firstname = stripslashes($_POST['firstname']);
  $person->lastname = stripslashes($_POST['lastname']);
  $person->address = stripslashes($_POST['address']);
  $person->npa = $_POST['npa'];
  $person->city = stripslashes($_POST['city']);
  $person->country = stripslashes($_POST['country']);
  $person->email1 = $_POST['email'];
  $person->tel1 = $_POST['tel'];
  
  $message = nl2br(stripslashes($_POST['message']));
  
  $_SESSION['person'] = $person;
  $_SESSION['message'] = $message;
  
  /*if (!isset($_POST["recaptcha_response_field"])){
    $_SESSION['message_error'] = _("Le code de contrôle a été mal recopié !");
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."#';</script>";
    exit();
  }
  
  $privatekey = "6LeOdwsAAAAAANeVvKQuHFfVfhx4Cwc-dqHsgtST";
  $resp = recaptcha_check_answer ($privatekey,
                                  $_SERVER["REMOTE_ADDR"],
                                  $_POST["recaptcha_challenge_field"],
                                  $_POST["recaptcha_response_field"]);
  
  if (!$resp->is_valid){
     echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Le code de contrôle a été mal recopié") ."</div>
          </div>";
    exit();
  }
  else{*/
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    if (!$person->email1 == ""){
      // création du mail
      $mail = new miki_email('contact', 'fr');
      
      $mail->From     = $person->email1;
      $mail->FromName = $person->lastname ." " .$person->firstname;
      
      // prépare les variables nécessaires à la création de l'e-mail
      $vars_array['firstname'] = $person->firstname;
      $vars_array['lastname'] = $person->lastname;
      $vars_array['address'] = $person->address;
      $vars_array['npa'] = $person->npa;
      $vars_array['city'] = $person->city;
      $vars_array['country'] = $person->country;
      $vars_array['email'] = $person->email1;
      $vars_array['tel1'] = $person->tel1;
      $vars_array['message'] = $message;
      $vars_array['sitename'] = $sitename;
      
      // initialise le contenu de l'e-mail
      $mail->init($vars_array);
      
      $mail->AddAddress($email_answer);
      
      if(!$mail->Send()){
        echo "<div>
                <div id='result'>0</div>
                <div id='msg'>" ._("Une erreur est survenue lors de l'envoi du message :") ."<br />" .$mail->ErrorInfo ."</div>
              </div>";
        exit();
      }
      
      echo "<div>
              <div id='result'>1</div>
              <div id='msg'>" ._("Votre message nous est parvenu avec succès.") ."<br />" ._("Nous allons traiter votre message dans les plus brefs délais.") ."</div>
            </div>";
      exit();
    }
    
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Certaines informations obligatoires sont manquantes") ."</div>
          </div>";
    exit();
  /*}*/
?>