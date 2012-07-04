<?php

// si on doit traiter une inscription
if (isset($_POST['email'])){

  require_once("../include/headers.php");

  // test la validité d'une adresse e-mail
  function test_email($email){
    $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // caractères autorisés avant l'arobase
    $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // caractères autorisés après l'arobase (nom de domaine)
                                   
    $regex = '/^' . $atom . '+' .   // Une ou plusieurs fois les caractères autorisés avant l'arobase
    '(\.' . $atom . '+)*' .         // Suivis par zéro point ou plus
                                    // séparés par des caractères autorisés avant l'arobase
    '@' .                           // Suivis d'un arobase
    '(' . $domain . '{1,63}\.)+' .  // Suivis par 1 à 63 caractères autorisés pour le nom de domaine
                                    // séparés par des points
    $domain . '{2,63}$/i';          // Suivi de 2 à 63 caractères autorisés pour le nom de domaine
    
    // test de l'adresse e-mail
    if (preg_match($regex, $email)) {
        return true;
    } else {
        return false;
    }
  }

  $erreur = false;
  $email = "";
  
  // vérifie que l'adresse e-mail ne soie pas vide
  if ($_POST['email'] == ""){
    $erreur = "Aucune adresse e-mail n'a été fournie";
  }
  // vérifie que l'adresse e-mail soie valide
  elseif (!test_email($_POST['email'])){
  	$erreur = "L'adresse e-mail n'est pas valide";
  }
  // essaie d'ajouter l'adresse e-mail à la liste des abonnés
  else{
    try{
      $email = $_POST['email'];
      
      if (Miki_newsletter_member::email_exists($email)){
        $member = new Miki_newsletter_member();
        $member->load_from_email($email);
      }
      else{
        $member = new Miki_newsletter_member();
        $member->email = $email;
      }
      
      $member->save();
      $member->add_to_group(1);
    }
    catch(Exception $e){
      $erreur = $e->getMessage();
    }
  }
  
  // si aucune erreur
  if ($erreur === false){
    echo "<div>
            <div id='result'>1</div>
            <div id='msg'>Vous avez été inscrit avec succès</div>
          </div>";          
  }
  else{
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>$erreur</div>
          </div>";
  }
}
// sinon on affiche le formulaire
else{
?>

  <script type="text/javascript">
    $(document).ready(function() {    
      
      /* attach a submit handler to the form */
      $("#form-newsletter").submit(function(event) {
        
        $('[placeholder]').defaultValue();
        
        /* stop form from submitting normally */
        event.preventDefault(); 
            
        /* get some values from elements on the page: */
        var form = $(this);
        var params = form.serialize();
        var url = form.attr('action');
        
        // masque le bouton et affiche le gif animé pour patienter
        form.find(':submit').css("display","none");
        $("<img src='pictures/loader_black.gif' style='margin-left: 5px;' alt='<?php echo _("Chargement..."); ?>' />").insertAfter(form.find(':submit'));
    
        /* Send the data using post and put the results in a div */
        $.post(url, params,
          function( data ) {
            var result = $(data).find('#result');
            var msg = $(data).find('#msg');
            if (result.html() == '0'){
              // réaffiche le bouton
              form.find(':submit').next("img").remove();
              form.find(':submit').css("display","inline");
              
              $("#form-newsletter-results-error p").empty().append(msg.html());
              $("#form-newsletter-results-error").css('display', 'block').delay(5000).hide("slow");
            }
            else{
              // réaffiche le bouton
              form.find(':submit').next("img").remove();
              form.find(':submit').css("display","inline");
                
              $("#form-newsletter-results-success p").empty().append(msg.html());
              $("#form-newsletter-results-success").css('display', 'block').delay(5000).hide("slow");
            }
          }
        );
      });
    });
  </script>
  
  <div class="miki-box-newsletter" style="width: 100%; overflow: hidden;">
    
    <h3>RECEVEZ NOTRE NEWSLETTER</h3>
    
    <div id="form-newsletter-results-success" class="box_result_success" style="display: none;"><p></p></div>
    <div id="form-newsletter-results-error" class="box_result_error" style="display: none;"><p></p></div>
    
    <form action="box/newsletter.php" method="post" name="form-newsletter" id="form-newsletter" enctype="application/x-www-form-urlencoded">
      <input type="text" name="email" placeholder="Entrez votre adresse e-mail" />
      <input type="submit" value="Envoyer" class="button1" />
      <!--<input type="image" src="pictures/bouton-envoyer.gif" value="submit" style="border:0;vertical-align:middle" />-->
    </form>
  
  </div>
<?php
}
?>