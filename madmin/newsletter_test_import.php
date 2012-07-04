<?php
  require_once("include/headers.php");
  
  header('Content-type: text/html; charset=UTF-8'); 
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }

  if (!isset($_FILES['csv_file'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id_group']) || !is_numeric($_POST['id_group'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
?>

<div id="arianne">
  <?php echo _("Newsletter"); ?> > <?php echo _("Importations CSV"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Importation CSV"); ?></h1>
    
  <div>
    <p>
      <?php 
        $result = Miki_newsletter::import_csv($_FILES['csv_file'], $_POST['id_group']);
        if ($result === true){
          echo "L'importation s'est terminée avec succès !";
        }
        else{
          echo "L'importation s'est terminée avec des erreurs : <br /><br />$result";
        }
      ?>
    </p>
    
    <div style="margin-top:20px">
      <input type="button" value="<?php echo _("Retour aux newsletters"); ?>" onclick="javascript:document.location='index.php?pid=114'" />
    </div>
    
  <div>
</div>