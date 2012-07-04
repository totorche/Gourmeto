<head>
  <?php
    
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    require_once("../config/country_fr.php");
    require_once("functions_pictures.php");
  
    if (!test_right(72)){
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
      $redaction = new Miki_redaction($id);
    }
    catch(Exception $e){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // récupert toutes les personnes (pour pouvoir choisir à qui on affecte la rédaction)
    $persons = Miki_person::get_all_persons(false, "lastname", "asc");
  ?>
  
  <link rel="stylesheet" type="text/css" href="../scripts/milkbox/milkbox.css" />
  <script type="text/javascript" src="../scripts/milkbox/mootools-1.2.3.1-assets.js"></script>
  <script type="text/javascript" src="../scripts/milkbox/milkbox.js"></script>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_edit_redaction'),{
                                        useAjax: false,
                                        errorPlace: 'bottom'
                                      });
    });
    
    var nb_pictures = 1;
    
    // ajouter une image supplémentaire
    function add_logo(){
      nb_pictures++;
      
      var br = new Element('br');
      br.inject($('ajouter_image'), 'before');
      
      var el = new Element('input', {type: 'file', name: 'picture' + nb_pictures, styles: {'margin-top': '5px'}});
      el.inject($('ajouter_image'), 'before');
      
      $('nb_pictures').set('value',nb_pictures);
    }
    
    // ajouter un mot-clé supplémentaire
    function add_keyword(){
      var el = new Element('input', {type: 'text', name: 'keywords[]', styles: {'margin-top': '5px'}});
      el.inject($('ajouter_keyword'), 'before');
      
      var el_delete = new Element('a', {
        href: '#keywords', 
        title: '<?php echo _("Supprimer ce mot-clé"); ?>', 
        html: '<?php echo _("Supprimer ce mot-clé"); ?>', 
        styles: {
          margin: '0 0 0 3px'
        },
        events: {
          click: function(){
            delete_keyword(this);
          }
        }
      });
      el_delete.inject($('ajouter_keyword'), 'before');
      
      var br = new Element('br');
      br.inject($('ajouter_keyword'), 'before');
    }
    
    function delete_keyword(el){
      //alert(el);
      var previous = el.getPrevious("input[type=text]");
      previous.destroy();
      var next = el.getNext("br");
      next.destroy();
      el.destroy();
    }
    
  </script>
  
  <style type="text/css">
    
    #form_edit_redaction td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_edit_redaction input[type=text]{
      width: 250px;
    }
    
    #form_edit_redaction textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=221"><?php echo _("Liste rédactions"); ?></a> > Modifier une rédaction
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Modifier une rédaction"); ?></h1>  

  <form id="form_edit_redaction" action="redaction_test_edit.php" method="post" name="formulaire" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $redaction->id; ?>" />
    <input type="hidden" name="nb_pictures" id="nb_pictures" value="1" />
    
    <table>
      <tr>
        <td colspan="2" style="font-weight:bold">Information sur la rédaction</td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo _("Client"); ?> <span style="color:#ff0000">*</span></td>
        <td>
          <select name="id_person" class="required">
            <?php
              foreach($persons as $p){
                echo "<option value=\"$p->id\"";
                if ($redaction->id_person == $p->id) echo " selected='selected'"; 
                echo ">$p->lastname $p->firstname</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><?php echo _("Titre"); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required" name="title" value="<?php echo $redaction->title; ?>" /></td>
      </tr>
      <tr>
        <td><?php echo _("Nombre de mots"); ?> <span style="color:#ff0000">*</span></td>
        <td>
          <select name="nb_words" class="required">
            <?php
              for($x=50; $x<=1000; $x+=50){
                echo "<option value='$x'";
                if (isset($_SESSION['saved_redaction']) && $_SESSION['saved_redaction']->nb_words == $p->$x) echo " selected='selected'";
                echo ">$x</option>";              
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      
      
      <tr>
        <td style="vertical-align:top"><?php echo _("Mots-clés"); ?></td>
        <td>
          <a name="keywords"></a>
          <?php
            // affiche les inputs pour les images
            foreach($redaction->keywords as $keyword){
              echo "<input type='text' name='keywords[]' value=\"$keyword\" style='margin-top: 5px;' />
                    <a href='#keywords' title='" ._("Supprimer ce mot-clé") ."' onclick='javascript:delete_keyword(this);'>" ._("Supprimer ce mot-clé") ."</a>
                    <br />";
            }
          ?>
          <div id="ajouter_keyword" style="margin-top:5px">
            <a href="#logos" onclick="add_keyword();" title="<?php echo _("Ajouter un mot-clé"); ?>" style="margin:0 5px;">
              <img src="pictures/add.png" alt="<?php echo _("Ajouter un mot-clé"); ?>" style="border:0;vertical-align:middle" />
            <a>
            <a href="#logos" onclick="add_keyword();" title="<?php echo _("Ajouter un mot-clé"); ?>"><?php echo _("Ajouter un mot-clé"); ?></a>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      
      
      <tr>
        <td style="vertical-align:top"><?php echo _("Images"); ?></td>
        <td>
          <a name="pictures"></a>
          <?php
            // affiche les inputs pour les images
            for($x=1; $x<=sizeof($redaction->pictures); $x++){
              $size = get_image_size("../pictures/redactions/thumb/" .$redaction->pictures[$x - 1], 30, 30);
              $random = rand(1,1000);
              echo "<div>
                      <input type='file' name='picture$x' style='float:left' />&nbsp;
                      <a href='../pictures/redactions/" .$redaction->pictures[$x - 1] ."' title='" ._("Agrandir") ."' rel='milkbox:redaction'>
                        <img src='../pictures/redactions/thumb/" .$redaction->pictures[$x - 1] ."?rand=$random' alt=\"" ._("image de la rédaction") ."\" style='border:0;float:left;margin-left:5px;width:" .$size[0] ."px;height:" .$size[1] ."px;vertical-align:top' />
                      </a>
                      <div style='float:left;margin-left:5px'>
                        (" ._("Laissez vide pour conserver les images") .")<br />
                        <a href='redaction_delete_picture.php?id=$redaction->id&pic=" .$redaction->pictures[$x - 1] ."' title='" ._("Supprimer cette photo") ."'>" ._("Supprimer cette photo") ."</a>
                      </div>
                      <div style='clear:both;height:1px'><img src='" .URL_BASE ."pictures/pixel.gif' /></div>
                    </div>";
            }
            
            if (sizeof($redaction->pictures) == 0){
              echo "<input type='file' name='picture$x' />";
            }
          ?>
          <span id="ajouter_image">
            <a href="#logos" onclick="add_logo();" title="Ajouter une image" style="margin:0 5px">
              <img src="pictures/add.png" alt="Ajouter une image" style="border:0;vertical-align:middle" />
            <a>
            <a href="#logos" onclick="add_logo();" title="Ajouter une image">Ajouter une image</a>
          </span>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo _("Commentaires"); ?></td>
        <td><textarea class="" name="comment"><?php echo $redaction->comment; ?></textarea></td>
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
          <input type="button" value="<?php echo _("Annuler"); ?>" onclick="javascript:document.location='index.php?pid=221'" />
          &nbsp;&nbsp;
          <input type="submit" value="<?php echo _("Envoyer"); ?>" />
        </td>
      </tr> 
    </table>
      
  </form>
</div>