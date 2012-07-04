<head>
  <?php
    require_once("scripts/functions.php");
  
    if (!test_right(74)){
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
      $comment = new Miki_comment($id);
      
      $person = $comment->get_person();
      $date = date("d/m/Y à H:i", strtotime($comment->date));
      $text = preg_replace("/<br( \/)?\>/i", "", $comment->comment);
      
      $site_url = Miki_configuration::get('site_url') ."/";
      
      // vérifie le type d'objet lié au commentaire
      if ($comment->object_class == "Miki_page"){
          $source_type = _("Page");
          $object = new Miki_page($this->id_object);
          $link = $site_url .$object->get_url_simple();
          $title = $object->get_menu_name();
      }
      elseif ($comment->object_class == "Miki_news"){
          $source_type = _("Actualité");
          $object = new Miki_news($comment->id_object);
          $link = $site_url .$object->get_url_simple();
          $title = truncate_text($object->title, 50, true, true);
      }
      elseif ($comment->object_class == "Miki_shop_article"){
          $source_type = _("Produit");
          $object = new Miki_shop_article($comment->id_object);
          $link = $site_url .$object->get_url_simple();
          $title = truncate_text($object->name['fr'], 50, true, true);
      }
      elseif ($comment->object_class == "Miki_event"){
          $source_type = _("Evénement");
          $object = new Miki_event($comment->id_object);
          $link = $site_url .$object->get_url_simple();
          $title = truncate_text($object->title['fr'], 50, true, true);
      }
      elseif ($comment->object_class == "Miki_album"){
          $source_type = _("Album photo");
          $object = new Miki_album($comment->id_object);
          $link = $site_url .$object->get_url_simple();
          $title = truncate_text($object->name, 50, true, true);
      }
      elseif ($comment->object_class == "Miki_album_picture"){
          $source_type = _("Photo");
          return new Miki_album_picture($comment->id_object);
      }
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_edit_comment'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
  </script>
  
  <style type="text/css">
    
    #form_edit_comment td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_edit_comment input[type=text]{
      width: 250px;
    }
    
    #form_edit_comment textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=241"><?php echo _("Liste commentaires"); ?></a> > <?php echo _("Modifier un commentaire"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier un commentaire"); ?></h1>  

  <form id="form_edit_comment" action="comment_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $comment->id; ?>" />
    
    <table>
      <tr>
        <td style="font-weight:bold"><?php echo _("Posté le"); ?></td>
        <td><?php echo $date; ?></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="font-weight:bold"><?php echo _("Par"); ?></td>
        <td>
          <?php
            echo "$person->firstname $person->lastname";
            if ($person->email1 != "")
              echo "<br /><a href='mailto:$person->email1' title='" ._("Ecrire à cette personne") ."'>$person->email1</a>";
            if ($person->web != "")
              echo "<br /><a href='$person->web' target='_blank' title='" ._("Visiter le site web") ."'>$person->web</a>";
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="font-weight:bold"><?php echo _("En réponse à"); ?></td>
        <td>
          <?php 
            echo "$source_type<br />
                  <a href='$link' target='_blank' title='" ._("Voir cet objet") ."'>$title</a>"; 
          ?>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="font-weight:bold"><?php echo _("Etat"); ?> <span style="color:#ff0000">*</span></td>
        <td>
          <select name="state">
            <option value="1" <?php echo ($comment->state == 1) ? "selected='selected'" : ""; ?>><?php echo _("Approuvé"); ?></option>
            <option value="2" <?php echo ($comment->state == 2) ? "selected='selected'" : ""; ?>><?php echo _("En attente de validation"); ?></option>
            <option value="3" <?php echo ($comment->state == 3) ? "selected='selected'" : ""; ?>><?php echo _("Indésirable"); ?></option>
          </select>
        </td>
      </tr>
      <tr>
        <td style="font-weight:bold"><?php echo _("Commentaire"); ?> <span style="color:#ff0000">*</span></td>
        <td><textarea name="comment" style="width: 400px; height: 200px;"><?php echo $text; ?></textarea></td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" style="font-weight:bold">Les champs munis d'une <span style="color:#ff0000">*</span> sont obligatoires</td>
      </tr> 
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2" class="form_box">
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=241'" />
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