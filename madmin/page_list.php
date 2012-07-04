<?php

if (!test_right(7) && !test_right(15) && !test_right(23))
  echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";

// stock l'url actuel pour un retour après opérations
$_SESSION['url_back'] = $_SERVER["REQUEST_URI"];

$deploy_temp = "";
// récupert les pages devant être déployées
if (isset($_COOKIE['deploy'])){
  $deploy_temp = explode(";", $_COOKIE['deploy']);
  $_SESSION['deploy'] = $_COOKIE['deploy'];
}
elseif(isset($_SESSION['deploy'])){
  $deploy_temp = explode(";",$_SESSION['deploy']);
  $_COOKIE['deploy'] = $_SESSION['deploy'];
} 
$deploy = $deploy_temp;
if (!is_array($deploy))
  $deploy = array($deploy); 

// pour savoir si la page doit être affichée (déploiement)
function print_page($page, $deploy){
  $temp = $page;
  while(isset($temp->parent_id)){
    if (!in_array($temp->parent_id, $deploy))
      return false;
    $temp = new Miki_page($temp->parent_id);
  }
  return true;
}

// affiche la page passée en paramètre
function go_print($page, $size){
  
  global $deploy;
  global $n;
  
  // test si on doit afficher la page (déploiement)
  if (print_page($page, $deploy)){
  
    // détecte la class
    if ($n === 1)
      $class = "line1";
    else
      $class = "line2";
     
    // recherche la position exacte de la page
    $position = $page->position;
    $temp = $page;
    while(isset($temp->parent_id)){
      $temp = new Miki_page($temp->parent_id);
      $position = $temp->position .'.' .$position;
    }
    
    // vérifie si la page possède un album photo
    try{
      $album = new Miki_album();
      $album->load_by_name($page->name, false);
      
      if ($album->get_nb_pictures() > 0){
        // prend toutes les photos de la page en cours
        $pictures[$page->id] = $album->get_pictures();
      }
    }
    catch(Exception $e){
      // l'album n'existe pas
      $album = false;
    }
    
    $n = ($n+1)%2;
    $gabarit = new Miki_template($page->template_id);
    $user = new Miki_user($page->user_creation);
    echo "
      <tr id='$page->id' class='pages' onmouseover=\"colorLine('$page->id');\" onmouseout=\"uncolorLine('$page->id');\">
        <td class='$class' style='width:5%;height:2em;text-align:left'>";
        
        // pour le déploiement
        if ($page->has_children()){
          if (!in_array($page->id, $deploy))
            echo "<a href='#' onclick='javascript:add_deploy($page->id);'><img src='pictures/arrow-r.gif' border='0' /></a>";
          else
            echo "<a href='#' onclick='javascript:remove_deploy($page->id);'><img src='pictures/arrow-d.gif' border='0' /></a>";
        }
        
  echo "</td>
        <td class='$class' style='width:5%;height:2em;text-align:left'>$position</td>
        <td class='$class' style='width:22%;height:2em'>";
          if (test_right(15))
            echo "<a href='index.php?pid=4&id=$page->id' title='" ._('Editer') ."'>$page->name</a>";
          else
            echo $page->name; 
          echo "</td>
        <td class='$class' style='height:2em'>$gabarit->name</td>
        <!--<td class='$class' style='height:2em'>$user->name</td>-->
        <td class='$class' style='height:2em;text-align:center'>";
        if($page->state == 2) 
          echo "<img src='pictures/true.gif' border='0' alt='" ._('Active') ."' />"; 
        elseif(test_right(15) && $page->state == 1)
          echo "<a href='page_change_state.php?id=$page->id' title='" ._('Définir comme inactive') ."'><img src='pictures/true.gif' border='0' alt='" ._('Définir comme inactive') ."' /></a>";
        elseif(test_right(15))
          echo "<a href='page_change_state.php?id=$page->id' title='" ._('Définir comme active') ."'><img src='pictures/false.gif' border='0' title='" ._('Définir comme active') ."' /></a>";
  echo "</td>
        <td class='$class' style='height:2em;text-align:center'>";
        if(test_right(15) && $page->state == 2) 
          echo "<img src='pictures/true.gif' border='0' alt='" ._('Page par défaut') ."' />"; 
        elseif(test_right(15))
          echo "<a href='page_set_default.php?id=$page->id' title='" ._('Définir comme page par défaut') ."'><img src='pictures/false.gif' border='0' alt='" ._('Définir comme page par défaut') ."' /></a>"; 
  echo "</td>
        <td class='$class' style='height:2em;text-align:center'>";
        if(test_right(15) && $page->analytics == 1)
          echo "<a href='page_analytics.php?id=$page->id&a=0' title='" ._('Désactiver le code Google Analytics') ."'><img src='pictures/true.gif' border='0' alt='" ._('Désactiver le code Google Analytics') ."' /></a>";
        elseif(test_right(15))
          echo "<a href='page_analytics.php?id=$page->id&a=1' title='" ._('Activer le code Google Analytics') ."'><img src='pictures/false.gif' border='0' alt='" ._('Activer le code Google Analytics') ."' /></a>";
  echo "</td>
        <td class='$class' style='height:2em;height:20px;text-align:center'>";
        if(test_right(15) && ($page->state == 2 || $page->state == 1)){ 
          echo "<a href='page_move.php?pid=$page->id&m=up' title='" ._('Déplacer vers le haut') ."'><img src='pictures/arrow-u.gif' border='0' alt='" ._('Déplacer vers le haut') ."' /></a>&nbsp;
                <a href='page_move.php?pid=$page->id&m=down' title='" ._('Déplacer vers le bas') ."'><img src='pictures/arrow-d.gif' border='0' alt='" ._('Déplacer vers le bas') ."' /></a>&nbsp;
                <select onchange='move_to(this.value, $page->id);'>";
                for ($x=1; $x <= $size; $x++){
                  
                  $position = $x;
                  $temp = $page;
                  while(isset($temp->parent_id)){
                    $temp = new Miki_page($temp->parent_id);
                    $position = $temp->position .'.' .$position;
                  }
                  
                  echo "<option value='$x'"; if ($x == $page->position) echo "selected='selected'"; echo ">$position</option>";
                }
          echo "</select>";        
        } 
  echo "</td>
        <td class='$class' style='height:2em;height:20px;text-align:right;padding-right:10px'>";
        
          if ($album && test_right(41)){
            echo "<span style='margin-right:10px'><a href='index.php?pid=135&id=$album->id' title='" ._('Album photos') ."'><img src='pictures/photos.gif' border='0' alt='" ._('Album photos') ."' /></a></span>";
          }
        
    echo "<span style='margin-right:10px'><a href='index.php?pid=2&id=$page->id' title='" ._('Voir') ."'><img src='pictures/view.gif' border='0' alt='" ._('Voir') ."' /></a></span>";
          if (test_right(15))
            echo "<span style='margin-right:10px'><a href='index.php?pid=4&id=$page->id' title='" ._('Editer') ."'><img src='pictures/edit.gif' border='0' alt='" ._('Editer') ."' /></a></span>";
          if (test_right(23))
            echo "<span style='margin-right:10px'><a class='delete' href='page_delete.php?id=$page->id' title='" ._('Supprimer') ."'><img src='pictures/delete.gif' border='0' alt='" ._('Supprimer') ."' /></a></span>";
          
          echo "<span><input type='checkbox' class='check_element' page_id='$page->id' /></span>
        </td>
      </tr>";
      
      
    // récupert les enfants de la page
    $children = $page->get_children("position", true, "asc");
    
    // s'il y a des enfants, on les affiche
    foreach($children as $child){
      go_print($child, sizeof($children));
    }
  }
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
      document.location = 'page_delete.php?id=' + ids;
  }
  else if ($('action').value == "active")
    document.location = 'page_change_state.php?a=1&id=' + ids;
  else if ($('action').value == "inactive")
    document.location = 'page_change_state.php?a=0&id=' + ids;
}

// récupert les pages devant être déployées
var deploy = Cookie.read('deploy');
Cookie.dispose("deploy");
if (deploy != null)
  deploy = deploy.split(";");
else
  deploy = new Array();

// ajoute une page pour qu'elle soit déployée
function add_deploy(id){
  deploy.include(id);
  Cookie.write('deploy',deploy.join(';'));
  document.location.reload();
}

// enlève une pafe au déploiement
function remove_deploy(id){
  var temp = new Array();
  if (deploy != null){
    deploy.each(function(el){
      if (el != id)
      temp.push(el);
    });
  }
  deploy = temp;
  Cookie.write('deploy',deploy.join(';'));
  document.location.reload();
}

// déplace la page à la position donnée
function move_to(pos, pid){ 
  // recherche les avatars
  var req = new Request({url:'page_move.php?pid=' + pid + '&m=to', 
		onSuccess: function(txt){
			document.location.reload();
    }
  });
  req.send("pos=" + pos);
}

</script>

<div id="arianne">
  <a href="#"><?php echo _("Contenu"); ?></a> > <?php echo _("Pages"); ?>
</div>

<?php 
  // affiche le message s'il y en a un
  print_message(); 
?>

<div id="first_contener">
  <h1><?php echo _("Pages actuelles"); ?></h1>
  
  <div style="margin-bottom:10px">
    <?php 
    if (test_right(7)){ ?>
      <a href="index.php?pid=3" title="<?php echo _("Ajouter une page"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une page"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une page"); ?></a>
    <?php } ?>
  </div>
  
  <table id="main_table">
    <tr class="headers">
      <td style="width:5%">&nbsp;</td>
      <td style="width:5%">&nbsp;</td>
      <td style="width:20%"><?php echo _("Pages"); ?></td>
      <td style="width:16%"><?php echo _("Gabarit"); ?></td>
      <td style="width:10%;text-align:center"><?php if(test_right(15)) echo _("Actif"); ?></td>
      <td style="width:8%;text-align:center"><?php if(test_right(15)) echo _("Défaut"); ?></td>
      <td style="width:8%;text-align:center"><?php if(test_right(15)) echo _("Analytics"); ?></td>
      <td style="width:8%;text-align:center"><?php if(test_right(15)) echo _("Déplacer"); ?></td>
      <td style="width:20%;text-align:right;padding-right:10px"><input type="checkbox" onclick="check_element(this)" /></td>
    </tr>
    
    <?php
      // récupert toutes les pages
      $pages = Miki_page::get_all_pages("position");
      
      $n = 0;
      
      if (sizeof($pages) == 0)
        echo "<tr><td colspan='9'>" ._("Aucune page n'est présente dans la base de données") ."</td></tr>";
      
      // recherche les pages de premier niveau (pas de parent)
      $temp = array();
      foreach($pages as $page){
        // si c'est une page de premier niveau (pas de parent) on la prend en compte
        if (!$page->has_parent()){
          $temp[] = $page;
        }
      }
      $pages = $temp;
      
      // affiche toutes les pages de premier niveau
      foreach($pages as $page){
        go_print($page, sizeof($pages));
      }
    ?>
      
    </table>
    
    <div style="margin-top:10px;float:left">
      <?php 
      if (test_right(7)){ ?>
        <a href="index.php?pid=3" title="<?php echo _("Ajouter une page"); ?>"><img src="pictures/newobject.gif" border="0" alt="<?php echo _("Ajouter une page"); ?>" style="vertical-align:middle" /><?php echo _("Ajouter une page"); ?></a>
      <?php } ?>
    </div>
    <div style="margin-top:10px;float:right">
      <?php echo _('Objets sélectionnés : '); ?>
      <select name="action" id="action">
        <?php 
          if (test_right(23)){ 
        ?>
        <option value="delete"><?php echo _('Supprimer'); ?></option>
        <?php } ?>
        <option value="active"><?php echo _('Activer'); ?></option>
        <option value="inactive"><?php echo _('Désactiver'); ?></option>
      </select>&nbsp;
      <input type="button" value="<?php echo _('Envoyer'); ?>" onclick="action_send()" />
    </div>
    <div style="clear:both"></div>
</div>