<?php

if (!test_right(31) && !test_right(32) && !test_right(33))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

?>

<div id="arianne">
  <a href="index.php?pid=114"><?php echo _("Newsletter"); ?></a> > <?php echo _("Importations CSV"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Importation CSV"); ?></h1>
  
  <div>
    <p>
      Cet utilitaire vous permet d'importer un fichier CSV (fichier contenant sur chaque ligne un enregistrement dont les valeurs sont séparées par des points-virgule) contenant une liste des adresses e-mail à ajouter aux abonnés.
      <br /><br />
      Vous pouvez ajouter le nom et le prénom correspondant à chaque adresse e-mail afin de pouvoir, par la suite, personnaliser vos newsletter.
      <br /><br />
      <span style="font-weight:bold">Le format doit être le suivant pour chaque enregistrement : <span style="text-decoration:underline">prénom;nom;email</span></span>
    </p>
    
    <form id="formulaire" action="index.php?pid=118" method="post" name="formulaire" enctype="multipart/form-data" style="margin-top:40px">
      <input type='hidden' name='MAX_FILE_SIZE' value='5000000' />
      
      Le fichier à importer : <input type="file" name="csv_file" />
      
      <br /><br />
      
      <?php 
        echo _("Veuillez choisir le groupe d'abonnés auquel vous désirez envoyer le newsletter : ");
        
        $groups = Miki_newsletter_group::get_all_groups();
        
        echo "<select name='id_group'>";
                
        foreach($groups as $group){
          echo "  <option value='$group->id'>$group->name</option>\n";
        }
        
        echo "</select>";
      ?>
      
      <div style="margin-top:20px">
        <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=160'" />
        &nbsp;&nbsp;
        <input type="submit" value="<?php echo _("Envoyer"); ?>" />
      </div>
      
    </form>
  <div>
</div>