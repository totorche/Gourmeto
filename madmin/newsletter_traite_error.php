<?php 
  /******************************************************************************************************
   *
   * Cette page gère les erreurs survenues lors de l'envoi d'une newsletter
   * 
   * Différentes actions peuvent être effectuées selon la valeur de la variable "action" :
   *    
   *    - action = 1 : supprime la prochaine adresse e-mail de la liste d'envoi
   *    - action = 2 : ajoute les adresses e-mail ayant causé une erreur à la liste d'envoi
   *    - action = 3 : supprime les adresses e-mail ayant causé une erreur
   *
   *****************************************************************************************************/
   
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    exit("ERR-Vous n'avez pas les permissions nécessaires pour effectuer cette action.");
  }
  
  if (!isset($_POST['id_newsletter']) || !is_numeric($_POST['id_newsletter'])){
    exit("ERR-Aucune newsletter n'a été passée en paramètre.");
  }
  
  if (!isset($_POST['action']) || !is_numeric($_POST['action'])){
    exit("ERR-Aucune action n'a été passée en paramètre.");
  }
  
  // récupert la newsletter
  try{
    $newsletter = new Miki_newsletter($_POST['id_newsletter']);
  }catch(Exception $e){
    exit("ERR-La newsletter n'a pas été trouvée");
  }
  
  // récupert l'action a effectuer
  $action = $_POST['action'];
  
  // supprime la prochaine adresse e-mail de la liste d'envoi 
  if ($action == 1){
    try{
      $list = $newsletter->get_emails();
      $address = array_shift($list);
      $newsletter->remove_email($address['id']);
      
      // récupert les erreurs déjà trouvées s'il y en a
      if (isset($_SESSION['newsletter_errors']) && is_array($_SESSION['newsletter_errors'])){
        $newsletter_errors = $_SESSION['newsletter_errors'];
      }
      else{
        $newsletter_errors = array();
      }
      
      // ajoute l'adresse en erreur à la liste
      $newsletter_errors[] = $address['email'];
      
      // puis stock la liste dans la session
      $_SESSION['newsletter_errors'] = $newsletter_errors;
      
      exit("OK-" .$address['email']);
    }catch(Exception $e){
      exit("ERR-" .$e->getMessage());
    }
  }
  // ajoute les adresses e-mail ayant causé une erreur à la liste d'envoi
  // et considère l'envoie de la newsletter comme non-terminé
  elseif ($action == 2){
    if (isset($_SESSION['newsletter_errors']) && is_array($_SESSION['newsletter_errors'])){
      foreach($_SESSION['newsletter_errors'] as $email){
        try{
          $member = new Miki_newsletter_member();
          $member->load_from_email($email);
          $newsletter->add_to_send_list($member->id);
        }
        catch(Exception $e){
          exit("ERR-" .$e->getMessage());
        }
      }
    }
    $newsletter->state = 1;
    $newsletter->update();
    exit("OK");
  }
  // supprime les adresses e-mail ayant causé une erreur et considère l'envoie de la newsletter comme terminé
  elseif ($action == 3){
    if (isset($_SESSION['newsletter_errors'])){
      unset($_SESSION['newsletter_errors']);
    }
    
    $newsletter->state = 2;
    $newsletter->update();
    exit("OK");
  }
?>