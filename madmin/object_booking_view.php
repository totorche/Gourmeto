<head>
  <?php
    if (!test_right(58)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."-1';</script>";
      exit();
    }
    
    // si pas d'id de contact spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."-2';</script>";
      exit();
    }
      
    try{
      $booking = new Miki_object_booking($_GET['id']);
      
      // récupert l'objet réservé
      $object = new Miki_object($booking->id_object);
      
      // récupert la personne ayant passé la réservation
      $person = new Miki_person($booking->id_person);
        
      $title = $object->title[Miki_language::get_main_code()];
      
      $date_booking = date("d/m/Y", strtotime($booking->date_booking));
      $date_start = date("d/m/Y", strtotime($booking->date_start));
      $date_stop = date("d/m/Y", strtotime($booking->date_stop));
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <style type="text/css">
    #details td{
      padding: 5px 10px 5px 0;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=174"><?php echo _("Réservations d'objets"); ?></a> > Détails d'une réservation
</div>

<div id="first_contener">
  <h1><?php echo _("Détails d'une réservation"); ?></h1>
  
  <table id="details" cellspacing="0" cellpadding="0" style="border:0">
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Date de la réservation"); ?> :</td>
      <td><?php echo $date_booking; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold">Objet réservé :</td>
      <td><a href="index.php?pid=173&id=<?php echo $object->id; ?>" title="Voir l'objet"><?php echo $title; ?></a></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Genre"); ?> :</td>
      <td><?php echo $person->type; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Prénom"); ?> :</td>
      <td><?php echo $person->firstname; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Nom"); ?> :</td>
      <td><?php echo $person->lastname; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Adresse"); ?> :</td>
      <td><?php echo $person->address; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Code postal"); ?> :</td>
      <td><?php echo $person->npa; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Localité"); ?> :</td>
      <td><?php echo $person->city; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Pays"); ?> :</td>
      <td><?php echo $person->country; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Adresse e-mail"); ?> :</td>
      <td><?php echo $person->email1; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Téléphone"); ?> :</td>
      <td><?php echo $person->tel1; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Langue"); ?> :</td>
      <td><?php echo $person->language; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Nb d'adultes"); ?> :</td>
      <td><?php echo $booking->nb_adults; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Nb d'enfants"); ?> :</td>
      <td><?php echo $booking->nb_children; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Réservé depuis le"); ?> :</td>
      <td><?php echo $date_start; ?></td>
    </tr>
    <tr>
      <td style="vertical-align:top;font-weight:bold"><?php echo _("Jusqu'à"); ?> :</td>
      <td><?php echo $date_stop; ?></td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">
        <input type="button" value="Retour" onclick="document.location='index.php?pid=174';" />
      </td>
    </tr>
  </table>
    
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>