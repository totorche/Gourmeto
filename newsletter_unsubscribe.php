<?php 
  require_once("include/headers.php");
?>

<div style='text-align:center'>

<?php 
  if (!isset($_GET['m']) || !isset($_GET['u'])){
    $email = Miki_configuration::get('email_answer');
    echo "Des informations sont manquantes ou erronnées.<br /><br />Si le problème devait persister, veuillez nous écrire à l'adresse suivante : <a href='mailto:$email' title='Nous écrire'>$email</a>.";
  }
  else{
    try{
      // récupert le membre d'après l'id donné
      $member = new Miki_newsletter_member(base64_decode($_GET['u']));
      
      // vérifie que l'adresse e-mail du membre corresponde bien à l'adresse e-mail donnée 
      if ($member->email != base64_decode($_GET['m'])){
        echo "L'adresse e-mail ne correspond pas au compte utilisateur donné";
      }
      // si tout est ok...
      else{
        // si un groupe est donné on vérifie que le membre appartienne bien au groupe donné
        if (isset($_GET['g']) && $member->is_in_group(base64_decode($_GET['g']))){
          // et, si c'est le cas, on l'en enlève
          $member->remove_from_group(base64_decode($_GET['g']));
          
          // si le membre n'est plus inscrit à aucun groupe, on le supprime
          $groupes_restant = $member->get_groups();
          if (sizeof($groupes_restant) == 0){
            $member->delete();
          }
        }
        // sinon on supprime directement le membre
        else{
          $member->delete();
        }
        $sitename = Miki_configuration::get('sitename');
        echo "Vous avez été désinscrit de le newsletter de $sitename avec succès.";
      }
    }
    catch(Exception $e){
      $email = Miki_configuration::get('email_answer');
      echo "Une erreur est survenue.<br /><br />Si le problème devait persister, veuillez nous écrire à l'adresse suivante : <a href='mailto:$email' title='Nous écrire'>$email</a>.";
    }
  }
?>

</div>