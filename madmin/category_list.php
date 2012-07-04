<?php

if (!test_right(7) && !test_right(15) && !test_right(23))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

// récupert les pages devant être déployées
$deploy = array();
if (isset($_COOKIE['deploy_category'])){
  $deploy = explode(";",$_COOKIE['deploy_category']);
  $_SESSION['deploy_category'] = $_COOKIE['deploy_category'];
}
elseif(isset($_SESSION['deploy_category'])){
  $deploy = explode(";",$_SESSION['deploy_category']);
  $_COOKIE['deploy_category'] = $_SESSION['deploy_category'];
}  

// pour savoir si la catégorie doit être affichée (déploiement)
function print_category($category, $deploy){
  $temp = $category;
  while(isset($temp->parent_id)){
    if (!in_array($temp->parent_id, $deploy))
      return false;
    $temp = new Miki_category($temp->parent_id);
  }
  return true;
}

?>

<script type="text/javascript">

// colorie une ligne d'un tableau
function colorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.addClass('mouseOver');
  });
}

// remet une ligne dans sa couleur normale
function uncolorLine(lineId){
  var td = $(lineId).getElements('td');
  td.each(function(item){
    item.removeClass('mouseOver');
  });
}

// coche ou décoche tous les éléments de la liste
function check_element(item){
  $$('.check_element').each(function(el){
    el.checked = item.checked;
  });
}

function action_send(){
  var ids = "";
  // récupert les pages cochées
  $$('.check_element').each(function(el){
    if (el.checked)
      ids += ";" + el.get('page_id');
  });
  
  // si aucune page n'est cochée
  if (ids == "")
    return false;
  
  // enlève le 1er ';'
  ids = ids.substring(1);
  
  // effectue l'opération demandée
  if ($('action').value == "delete"){
    if (confirm("<?php echo _('Etes-vous sûr de vouloir supprimer les éléments sélectionnés ?'); ?>"))
      document.location = 'category_delete.php?id=' + ids;
  }
}

// récupert les catégories devant être déployées
var deploy = Cookie.read('deploy_category');
Cookie.dispose("deploy_category");
if (deploy != null)
  deploy = deploy.split(";");
else
  deploy = new Array();

// ajoute une catégorie pour qu'elle soit déployée
function add_deploy(id){
  deploy.include(id);
  Cookie.write('deploy_category',deploy.join(';'));
  document.location.reload();
}

// enlève une catégorie au déploiement
function remove_deploy(id){
  var temp = new Array();
  if (deploy != null){
    deploy.each(function(el){
      if (el != id)
      temp.push(el);
    });
  }
  deploy = temp;
  Cookie.write('deploy_category',deploy.join(';'));
  document.location.reload();
}

</script>

<div id="arianne">
  <a href="index.php?pid=1"><?php echo _("Contenu"); ?></a> > <?php echo _("Catégories"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Catégories actuelles"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(7)){ ?>
      <a href="index.php?pid=82" title="<?php echo _("Ajouter une catégorie"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une catégorie"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une catégorie"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:5%">&nbsp;</td>
      <td style="width:40%"><?php echo _("Nom"); ?></td>
      <td style="width:55%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      $categories = Miki_category::get_all_categories("position");
      $n = 0;
      
      if (sizeof($categories) == 0)
        echo "<tr><td colspan='3'>" ._("Aucune catégorie n'est présente dans la base de données") ."</td></tr>";
        
      foreach($categories as $category){
        // test si on doit afficher la page (déploiement)
        if (print_category($category, $deploy)){
          // détecte la class
          if ($n === 1)
            $class = "line1";
          else
            $class = "line2";
          
          $n = ($n+1)%2;
          echo "
            <tr id='$category->id' class='pages' onmouseover=\"colorLine('$category->id');\" onmouseout=\"uncolorLine('$category->id');\">
              <td class='$class' style='width:5%;height:2em;text-align:left'>";
              
              // pour le déploiement
              if ($category->has_children()){
                if (!in_array($category->id, $deploy))
                  echo "<a href='#' onclick='javascript:add_deploy($category->id);'><img src='pictures/arrow-r.gif' border='0' /></a>";
                else
                  echo "<a href='#' onclick='javascript:remove_deploy($category->id);'><img src='pictures/arrow-d.gif' border='0' /></a>";
              }
              
        echo "</td>
              <td class='$class' style='width:22%;height:2em'>";
                if (test_right(26))
                  echo "<a href='index.php?pid=83&id=$category->id' title='" ._('Editer') ."'>$category->name</a>";
                else
                  echo $category->name; 
                echo "</td>
              <td class='$class' style='width:15%;height:2em;height:20px;text-align:right;padding-right:10px'>";
                if (test_right(26))
                  echo "<span style='margin-right:10px'><a href='index.php?pid=83&id=$category->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
                if (test_right(27))
                  echo "<span style='margin-right:10px'><a class='delete' href='category_delete.php?id=$category->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
                echo "<span><input type='checkbox' class='check_element' page_id='$category->id' /></span>
              </td>
            </tr>";
        }
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(25)){ ?>
        <a href="index.php?pid=82" title="<?php echo _("Ajouter une catégorie"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une catégorie"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une catégorie"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php
        if (test_right(27)){ 
          echo _('Objets sélectionnés : '); 
      ?>
      <select name="action" id="action">
        <option value="delete"><?php echo _('Supprimer'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
      <?php } ?>
    </div>
    <div style="clear:both"></div>
</div>