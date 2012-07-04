<style type="text/css">
  
  table{
    margin: 1mm;
    width: 295mm;
    border-collapse: collapse;
  }
  
  td{
    padding: 1mm;
    font-size: 8pt;
  }
  
  td.title{
    font-weight: bold;
    border: solid 2px #000000
  }
  
  td.element{
    border: solid 1px #000000
  }
  
</style>

<script src="scripts/mootools.js" type="text/javascript"></script>

<script type="text/javascript"> 


window.addEvent('domready', function() {
  alert('<?php echo _("Ce document doit être imprimé au format \'Paysage\'"); ?>');
  printThis();
});


function printThis(){ 
  var usertype=navigator.userAgent.toLowerCase(); 
  if (window.print) { 
    setTimeout('window.print();',200); 
  } 
  else if (usertype.indexOf("mac") != -1) { 
    alert("Press 'Cmd+p' on your keyboard to print article."); 
  } 
  else { 
    alert("Press 'Ctrl+p' on your keyboard to print article.") 
  } 
}

</script>

<?php
  require_once("include/headers.php");
  
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

  require_once("../config/country_fr.php");

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
  
  $participants = $event->get_participants();
  $title = $event->title[Miki_language::get_main_code()];
  
  $semaine[1] = _("LU");
  $semaine[2] = _("MA");
  $semaine[3] = _("ME");
  $semaine[4] = _("JE");
  $semaine[5] = _("VE");
  $semaine[6] = _("SA");
  $semaine[7] = _("DI");
  
  $jour_semaine = $semaine[date("N", strtotime($event->date_start))];
  
  $date = explode(" ",$event->date_start);
  $time = explode(":", $date[1]);
  $date = explode("-",$date[0]);
  $jour = $date[2];
  $mois = $date[1];
  $annee = $date[0];
  $heure = $time[0];
  $minutes = $time[1];
  
  $date_start = "$jour_semaine $jour/$mois/$annee " ._("dès") ." " .$heure ."h" .$minutes;
?>

<html>
  <head>
    <link href="style.css" type="text/css" rel="stylesheet">
  </head>
  <body>
    
    <table>
      <tr>
        <td colspan="4" style="font-weight:bold"><?php echo $title; ?></td>
        <td colspan="4" style="font-weight:bold"><?php echo $date_start; ?></td>
      </tr>
      <tr>
        <td colspan="8">&nbsp;</td>
      </tr>
      <tr>
        <td class="title" style="width:8mm">Type</td>
        <td class="title" style="width:40mm">Nom</td>
        <td class="title" style="width:40mm">Prénom</td>
        <td class="title" style="width:65mm">Adresse</td>
        <td class="title" style="width:8mm">Npa</td>
        <td class="title" style="width:40mm">Localité</td>
        <td class="title" style="width:30mm">Tél</td>
        <td class="title" style="width:50mm">E-mail</td>
        <td class="title" style="width:14mm">Nb</td>
      </tr>
      
    
    <?php
      foreach($participants as $p){
        echo "<tr>
                <td class='element'>" .$p[1]->type ."</td>
                <td class='element'>" .$p[1]->lastname ."</td>
                <td class='element'>" .$p[1]->firstname ."</td>
                <td class='element'>" .$p[1]->address ."</td>
                <td class='element'>" .$p[1]->npa ."</td>
                <td class='element'>" .$p[1]->city ."</td>
                <td class='element'>" .$p[1]->tel1 ."</td>
                <td class='element'>" .$p[1]->email1 ."</td>
                <td class='element'>" .$p[0] ."</td>
              </tr>";
      }
    ?>

    </table>
  </body>
</html>