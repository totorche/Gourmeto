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
  
  if (isset($_GET['init']) && $_GET['init'] == 1)
    $init = 1;
  else
    $init = "";
?>


<script type="text/javascript" src="scripts/checkform.js"></script>

<script type="text/javascript">

  window.addEvent('domready', function() {
    var myCheckForm = new checkForm($('formulaire'),{
                                          useAjax: false,
                                          errorPlace: 'bottom',
                                          divErrorCss: {
                                            'margin':'5px 0 0 0px'
                                          }
                                        });
  });

</script>


<div id="arianne">
  <a href="#"><?php echo _("Newsletter"); ?></a> > <a href="index.php?pid=114"><?php echo _("Newsletters"); ?></a> > <?php echo _("Envoi d'une newsletter 1/2"); ?>
</div>

<div id="first_contener">
  <h1><?php echo _("Sélection du groupe de destinataires"); ?></h1>
  
  <form id="formulaire" action="index.php?pid=1198" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id_newsletter" value="<?php echo $_GET['id']; ?>" />
    <input type="hidden" name="init" value="<?php echo $init; ?>" />
    
    <?php 
      echo _("Veuillez choisir le groupe d'abonnés auquel vous désirez envoyer le newsletter : ");
      
      $groups = Miki_newsletter_group::get_all_groups();
      
      echo "<select name='id_group'>
              <option value=''>Tous</option>";
              
      foreach($groups as $group){
        echo "  <option value='$group->id'>$group->name</option>\n";
      }
      
      echo "</select>";
    ?>
    
    <br /><br />
    
    <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=1198'" />
    &nbsp;&nbsp;
    <input type="submit" value="<?php echo _("Envoyer"); ?>" />
  </form>
</div>