<?php
  require_once("include/headers.php");
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  $newsletter_id = $_GET['id'];
?>


<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">


  // s'occupe de l'envoi de la newsletter et de la mise à jour de la progressBar
  function send(){
    var val_return = true;
    
    
  }


  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
                                        
    $('formulaire').addEvent('submit', function(event){
      var req = new Request({url:'newsletter_send.php', 
        url: this.get('action'),
    		onSuccess: function(txt){
    		  // supprime les espaces et sauts de ligne
          var regExpBeginning = /^\s+/;
          var regExpEnd       = /\s+$/;
          txt = txt.replace(regExpBeginning, "").replace(regExpEnd, "");
    		
          if (txt.substr(0,4) == "OK-2"){
            alert('<?php echo _("L\'email de test a été envoyé avec succès !"); ?>');
          }
          else{
            alert('<?php echo _("Une erreur est survenue pendant l\'envoi de l\'email de test."); ?>');
          }
        },
        onFailure: function(xhr){
          alert('<?php echo _("Une erreur est survenue pendant l\'envoi de l\'email de test."); ?>');
        }
      });
      req.send(this.toQueryString());
      return false;
    });
  });

</script>


<div id="arianne">
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=114"><?php echo _("Newsletters"); ?></a> > <?php echo _("Tester une newsletter"); ?>
</div>

<div id="first_contener">
  <h1><?php echo _("Test d'envoi de la newsletter"); ?></h1>
  
  <form id="formulaire" action="newsletter_send.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id_newsletter" value="<?php echo $newsletter_id; ?>" />
  
    <?php echo _("Entrez l'adresse e-mail de destination : "); ?><input type="text" name="email" class="required email" style="width:250px">
    <br /><br />
    
    <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=114'" />
    &nbsp;&nbsp;
    <input type="submit" value="<?php echo _("Envoyer"); ?>" />
  </form>
</div>