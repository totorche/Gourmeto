<?php
  /**
   * 1ère étape de l'ajout d'un article
   */     
?>

<head>
  <?php
    if (!test_right(52)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // si pas d'id d'article spécifié, on retourne à la page précédente
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    else
      $id = $_GET['id'];
      
    try{
      $article = new Miki_shop_article($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // test que l'on aie bien un shop de configuré
    $shops = Miki_shop::get_all_shops();
    if (sizeof($shops) == 0){
      $shop = false;
    }
    else
      $shop = array_shift($shops);
      
    if (!$shop){
      echo "<div>
              Vous n'avez aucune shop de configuré pour le moment.<br /><br />
              <input type='button' value='Créer un shop' onclick=\"document.location='index.php?pid=142'\" />
            </div>";
      exit();
    }
  ?>
  
  <script type="text/javascript">
    
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_article'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
    function change_type(el){
      if (el.value == 1){
        $("option_sets").setStyle('display', 'none');
        $$("#option_sets input[type=checkbox]").set('disabled', true);
      }
      else if (el.value == 2){
        $("option_sets").setStyle('display', 'block');
        $$("#option_sets input[type=checkbox]").set('disabled', false);
      }
    }
    
  </script>
  
  <style>
    
    #form_new_article table{
      width: 800px;
    }
    
    #form_new_article table td:first-child{
      width: 150px;
    }
    
    #option_sets{
      margin-top: 20px;    
      <?php echo $article->type == 1 ? "display: none;" : ""; ?>
      padding: 5px;
      background: #F2F2F2;
      border: solid 1px #CCCCCC;
    }
    
    .set{
      margin: 5px;
    }
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Shop"); ?></a> > <a href="index.php?pid=143"><?php echo _("Liste articles"); ?></a> > Ajouter un article 1/2
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'un article 1/2"); ?></h1>  

  <form id="form_new_article" action="index.php?pid=1452" method="post" name="form_new_article" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $article->id; ?>" />
    
    <table>
      <tr>
        <td style="vertical-align:top; width: 200px;"><?php echo _("Type de l'article"); ?></td> 
        <td>
          <select name="article_type" onchange="javascript: change_type(this);">
            <option name="<?php echo _("Article simple"); ?>" value="1" <?php echo $article->type == 1 ? "selected='selected'" : ""; ?>><?php echo _("Article simple"); ?></option>
            <option name="<?php echo _("Article configurable"); ?>" value="2" <?php echo $article->type == 2 ? "selected='selected'" : ""; ?> /><?php echo _("Article configurable"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div id="option_sets">
            <h2 style="background: #FFFFFF; color: #3b5998; padding: 10px; font-size: 1.2em; border: solid 1px #CCCCCC;"><?php echo _("Choisissez les sets d'options que vous voulez associer à cet article"); ?></h2>
            
            <?php
              $sets = Miki_shop_article_option_set::get_all_sets($shop->id);
              foreach($sets as $set){
                echo "<div class='set'><input type='checkbox' name='article_option_sets[]' id='set_$set->id' value='$set->id' " .($article->has_set($set->id) ? "checked='checked'" : "") ." /> <label for='set_$set->id'>$set->name&nbsp;&nbsp;&nbsp;(" .$set->get_nb_options() ." options)</label></div>";
              }
            ?>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=1452&id=<?php echo $id; ?>'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
  </form>
</div>

<?php
  // stock l'url actuel pour un retour après opérations
  $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
?>