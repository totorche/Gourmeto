<head>
  <?php
    // stock l'url actuel pour un retour après opérations
    $_SESSION['url_back'] = $_SERVER["REQUEST_URI"];
  
    if (!test_right(71)){
      echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
      exit();
    }
    
    // récupert toutes les personnes (pour pouvoir choisir à qui on affecte la rédaction)
    $persons = Miki_person::get_all_persons(false, "lastname", "asc");
  ?>
  
  <script type="text/javascript">
  
    window.addEvent('domready', function() {
      <?php require_once("scripts/checkform.js"); ?>
      
      var myCheckForm = new checkForm($('form_new_redaction'),{
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
      
      $('nb_pictures').set('value', nb_pictures);
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
    
    #form_new_redaction td{
      padding: 2px 15px 2px 0;
      vertical-align: top;
      min-width: 100px;
    }
    
    #form_new_redaction input[type=text]{
      width: 250px;
    }
    
    #form_new_redaction textarea{
      width: 400px;
      height: 100px;
    }
    
  </style>
</head>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <a href="index.php?pid=221"><?php echo _("Liste rédactions"); ?></a> > Ajout d'une rédaction
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Ajout d'une rédaction"); ?></h1>  

  <form id="form_new_redaction" action="redaction_test_new.php" method="post" name="formulaire" enctype="multipart/form-data">
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
                if (isset($_SESSION['saved_redaction']) && $_SESSION['saved_redaction']->id_person == $p->id) echo " selected='selected'"; 
                echo ">$p->lastname $p->firstname</option>";
              }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td><?php echo _("Titre"); ?> <span style="color:#ff0000">*</span></td>
        <td><input type="text" class="required" name="title" value="<?php echo ((isset($_SESSION['saved_redaction'])) ? $_SESSION['saved_redaction']->title : "") ?>" /></td>
      </tr>
      <tr>
        <td><?php echo _("Nombre de mots"); ?> <span style="color:#ff0000">*</span></td>
        <td>
          <select name="nb_words" class="required">
            <?php
              for($x=50; $x<=1000; $x+=50){
                echo "<option value='$x'";
                if (isset($_SESSION['saved_redaction']) && $_SESSION['saved_redaction']->nb_words == $x) echo " selected='selected'";
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
          <input type="text" name="keywords[]" style="margin-top: 5px" value="<?php echo (isset($_SESSION['saved_redaction']) && is_array($_SESSION['saved_redaction']->keywords) && isset($_SESSION['saved_redaction']->keywords[0])) ? $_SESSION['saved_redaction']->keywords[0] : ""; ?>" /><br />
          <input type="text" name="keywords[]" style="margin-top: 5px" value="<?php echo (isset($_SESSION['saved_redaction']) && is_array($_SESSION['saved_redaction']->keywords) && isset($_SESSION['saved_redaction']->keywords[1])) ? $_SESSION['saved_redaction']->keywords[1] : ""; ?>" /><br />
          <input type="text" name="keywords[]" style="margin-top: 5px" value="<?php echo (isset($_SESSION['saved_redaction']) && is_array($_SESSION['saved_redaction']->keywords) && isset($_SESSION['saved_redaction']->keywords[2])) ? $_SESSION['saved_redaction']->keywords[2] : ""; ?>" /><br />
          <input type="text" name="keywords[]" style="margin-top: 5px" value="<?php echo (isset($_SESSION['saved_redaction']) && is_array($_SESSION['saved_redaction']->keywords) && isset($_SESSION['saved_redaction']->keywords[3])) ? $_SESSION['saved_redaction']->keywords[3] : ""; ?>" /><br />
          <div id="ajouter_keyword" style="margin-top:5px">
            <a href="#keywords" onclick="add_keyword();" title="<?php echo _("Ajouter un mot-clé"); ?>" style="margin:0 5px">
              <img src="pictures/add.png" alt="<?php echo _("Ajouter un mot-clé"); ?>" style="border:0;vertical-align:middle" />
            <a>
            <a href="#keywords" onclick="add_keyword();" title="<?php echo _("Ajouter un mot-clé"); ?>"><?php echo _("Ajouter un mot-clé"); ?></a>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td style="vertical-align:top"><?php echo _("Images"); ?></td>
        <td>
          <a name="logos"></a>
          <input type="file" name="picture1" />
          <span id="ajouter_image">
            <a href="#logos" onclick="add_logo();" title="<?php echo _("Ajouter une image"); ?>" style="margin:0 5px">
              <img src="pictures/add.png" alt="<?php echo _("Ajouter une image"); ?>" style="border:0;vertical-align:middle" />
            <a>
            <a href="#logos" onclick="add_logo();" title="<?php echo _("Ajouter une image"); ?>"><?php echo _("Ajouter une image"); ?></a>
          </span>
        </td>
      </tr>
      <tr>
        <td colspan="2">&nbsp;</td>
      </tr>
      <tr>
        <td><?php echo _("Commentaires"); ?></td>
        <td><textarea class="" name="comment"><?php echo ((isset($_SESSION['saved_redaction'])) ? $_SESSION['saved_redaction']->comment : "") ?></textarea></td>
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