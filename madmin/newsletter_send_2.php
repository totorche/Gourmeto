<?php
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id_newsletter']) || !is_numeric($_POST['id_newsletter'])){
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){  
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else{
      $id_newsletter = $_GET['id'];
    }
  }
  else{
    $id_newsletter = $_POST['id_newsletter'];
  }
  
  // récupert le groupes d'abonnés à qui on doit envoyer la newsletter
  if (isset($_POST['id_group']))
    $id_group = $_POST['id_group'];
  else
    $id_group = "";
  
  // vérifie si on doit initialiser la newsletter
  if (isset($_POST['init']) && $_POST['init'] == 1)
    $init = 1;
  else
    $init = "";
?>


<script type="text/javascript" src="scripts/progressBar/progressBar.js"></script>
<link type="text/css" rel="stylesheet" href="scripts/progressBar/progressBar.css" />

<script type="text/javascript">
  
  // pour savoir si on doit initialiser la newsletter
  var init = '<?php echo $init; ?>';
  var id_group = '<?php echo $id_group; ?>';
  var id_newsletter = '<?php echo $id_newsletter; ?>';

  
  /*****************************************************************************************************
   *
   * Cette fonction gère les erreurs survenues pendant l'envoi de la newsletter
   * 
   *    - action = 1 : supprime la prochaine adresse e-mail de la liste d'envoi
   *    - action = 2 : ajoute les adresses e-mail ayant causé une erreur à la liste d'envoi
   *    - action = 3 : supprime les adresses e-mail ayant causé une erreur
   *    
   *    - resend = true : renvoie la newsletter
   *    - resend = false : ne renvoie pas la newsletter
   *    
   *    - goBack = true : retourne à la liste des newsletters
   *    - goBack = false : reste sur la page actuelle                  
   *
   *****************************************************************************************************/   
  function traite_error(action, resend, goBack){
    var val_return = "";
    
    var req = new Request({url:'newsletter_traite_error.php', 
      async: false,
  		onSuccess: function(txt){
  		  // supprime les espaces et sauts de ligne
        var regExpBeginning = /^\s+/;
        var regExpEnd       = /\s+$/;
        txt = txt.replace(regExpBeginning, "").replace(regExpEnd, "");
  		
  			if (txt.substr(0,2) == "OK"){
          val_return = true;
        }
        else{
          val_return = false;
        }
      },
      onFailure: function(xhr){
        val_return = false;
      }
    });
    req.send("id_newsletter=" + id_newsletter + "&action=" + action);
    
    // si on doit renvoyer de la newsletter
    if (resend){
      send();
    }
    
    // si on doit retourner à la liste des newsletters
    if (goBack){
      document.location = 'index.php?pid=114';
    }
    
    return val_return;
  }
  
  // récupert les adresses ayant été supprimées dans le cadre d'une erreur d'envoi
  function get_errors(){
    var val_return = "";
    
    var req = new Request({url:'newsletter_get_errors.php', 
      async: false,
  		onSuccess: function(txt){
  		  // supprime les espaces et sauts de ligne
        var regExpBeginning = /^\s+/;
        var regExpEnd       = /\s+$/;
        txt = txt.replace(regExpBeginning, "").replace(regExpEnd, "");
  		
  			if (txt == "0"){
          val_return = false;
        }
        else{
  			  // récupert puis affiche les e-mails concernés par une erreur
  			  var tab = txt.split("&&");
  			  var nb_errors = tab[0];
  			  var emails = tab.slice(1,tab.length);
  			  
  			  var text = "";
  			  for (var x = 0; x < emails.length; x++){
            text += "&nbsp;&nbsp;-&nbsp;" + emails[x] + "<br />\n";
          }
          
          $('emails_errors').set('html',text);
  			  val_return = true;
        }
      },
      onFailure: function(xhr){
        val_return = true;
      }
    });
    req.send();
    
    return val_return;
  }
  
  // s'occupe de l'envoi de la newsletter et de la mise à jour de la progressBar
  function send(){
    var val_return = true;
    
    var req = new Request({url:'newsletter_send.php', 
  		onSuccess: function(txt){
  		  // supprime les espaces et sauts de ligne
        var regExpBeginning = /^\s+/;
        var regExpEnd       = /\s+$/;
        txt = txt.replace(regExpBeginning, "").replace(regExpEnd, "");
  		
  		  // si on a initialisé la newsletter, lors des prochains appels on ne l'initialisera plus
        if (init == 1)
          init = "";
          
  			if (txt.substr(0,4) == "OK-1"){
  			  // envoi non-terminé, on rappel la fonction d'envoi
  			  var percent = txt.split("-");
  			  percent = percent[2];
  			  pb.set(percent);
          send.delay(5000);
        }
        else if (txt.substr(0,4) == "OK-2"){
          // envoi terminé, on affiche un message
          pb.set(100);
          
          $('waiting').setStyle('display','none');
          
          // s'il y a eu des erreur lors de l'envoi
          if (get_errors()){
            $('msg_termine_error').setStyle('display','block');
            $('msg_termine_error').highlight('#00FF00');
          }
          // si aucune erreur
          else{
            $('msg_termine').setStyle('display','block');
            $('msg_termine').highlight('#00FF00');
          }
        }
        else{
          // supprime la prochaine adresse e-mail de la liste d'envoi puis continue l'envoi de la newsletter
          if (!traite_error(1, true, false)){
            
            // s'il y a eu une erreur pendant la suppression de l'adresse, on arrête là
            $('waiting').setStyle('display','none');
            $('msg_error').setStyle('display','block');
            $('msg_error').highlight('#FF0000');
            
            // ajoute les adresses e-mail ayant causé une erreur à la liste d'envoi
            // et considère l'envoie de la newsletter comme non-terminé
            traite_error(2, false, false);
            
            val_return = false;
          }
        }
      },
      onFailure: function(xhr){
        // supprime la prochaine adresse e-mail de la liste d'envoi puis continue l'envoi de la newsletter
        if (!traite_error(1, true, false)){
          
          // s'il y a eu une erreur pendant la suppression de l'adresse, on arrête là
          $('waiting').setStyle('display','none');
          $('msg_error').setStyle('display','block');
          $('msg_error').highlight('#FF0000');
          
          // ajoute les adresses e-mail ayant causé une erreur à la liste d'envoi
          // et considère l'envoie de la newsletter comme non-terminé
          traite_error(2, false, false);
          
          val_return = false;
        }
      }
    });
    req.send("id_newsletter=" + id_newsletter + "&id_group=" + id_group + "&init=" + init);
  }
  
  window.addEvent('domready', function(){
  	// progressbar
    pb = new dwProgressBar({
  		container: $('progressBar'),
  		startPercentage: 0,
  		speed:1000,
  		boxID: 'box',
  		percentageID: 'perc',
  		displayID: 'display',
  		displayText: true
  	});
  	
  	send();
  });

</script>


<div id="arianne">
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=114"><?php echo _("Newsletters"); ?></a> > <?php echo _("Envoi d'une newsletter 2/2"); ?>
</div>

<div id="first_contener">
  <h1><?php echo _("Envoi de la newsletter en cours"); ?></h1>
  
  <!-- message lors d'une réussite complète de l'envoi de la newsletter -->
  <div id="msg_termine" style='display:none;text-align:center;margin:50px 0 20px 0'>
    L'envoi de la newsletter est maintenant terminé !
    <br /><br />
    <a href='index.php?pid=114' title='Retourner aux newsletters'>Retourner aux newsletters</a>
  </div>
  
  <!-- message avec des erreurs partielles lors de l'envoi de la newsletter -->
  <div id="msg_termine_error" style='display:none;text-align:center;margin:50px 0 20px 0'>
    L'envoi de la newsletter est maintenant terminé !
    <br /><br /><br />
    La newsletter n'a cependant pas pu être envoyée aux adresses suivantes :
    <br /><br />
    
    <div id="emails_errors"></div>
    
    <br /><br />
    <a href="javascript:send();" title="Réessayer d'envoyer la newsletter">Réessayer d'envoyer la newsletter</a>
    <br /><br />
    <a href="javascript:traite_error(3, false, true);" title="Retourner aux newsletters">Considérer l'envoi de la newsletter comme terminé et retourner aux newsletters</a>
    <br /><br />
    <a href="javascript:traite_error(2, false, true);" title="Retourner aux newsletters">Continuer l'envoi de la newsletter plus tard et retourner aux newsletters</a>
  </div>
  
  <!-- message avec une erreur fatale lors de l'envoi de la newsletter -->
  <div id="msg_error" style='display:none;text-align:center;margin:50px 0 20px 0;color:#FF0000'>
    Une erreur est survenue pendant l'envoi de la newsletter.
    <br /><br />
    L'envoi de la newsletter n'a pas été terminé !
    <br /><br />
    
    <a href="javascript:send();" title="Réessayer d'envoyer la newsletter">Réessayer d'envoyer la newsletter</a>
    <br /><br />
    <a href="index.php?pid=114" title="Retourner aux newsletters">Retourner aux newsletters</a>
  </div>
  
  <div id="waiting" style='text-align:center;margin:50px 0 20px 0'>
    L'envoi de la newsletter est en cours.
    <br /><br />
    Veuillez patienter svp
    <br /><br />
    <img src="pictures/wait.gif" alt="..." />
  </div>
  
  <div id="progressBar" style="text-align:center"></div>
  
</div>