<?php
  require_once("include/headers.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
  <head>
  <meta http-equiv="content-Type" content="text/html; charset=utf-8" />    
  <title>Miki - Interface d'administration</title>
  <meta name="Description" lang="fr" content="" />
  <meta name="Keywords" lang="fr" content="" />
  <meta name="Reply-To" content="info@fbw-one.com" />
  <meta name="Category" content="" />
  <meta name="Robots" content="index, follow" />
  <meta name="Distribution" content="global" />
  <meta name="Revisit-After" content="15 days" />
  <meta name="Author" lang="fr" content="FBW-One sarl" />
  <meta name="Publisher" content="FBW-One sarl" />
  <meta name="Copyright" content="&reg;fbw-one sarl" />
  
  <link rel="stylesheet" type="text/css" href="style.css" />

  <!-- inclusion de mootools -->
  <script type="text/javascript" src="scripts/mootools.js"></script>
  
  <!-- pour le menu -->
  <script type="text/javascript" src="scripts/adminMenu.js"></script>
  
  <link rel="stylesheet" type="text/css" href="scripts/menu.css" />
  
  <script type="text/javascript">
    window.addEvent('domready', function() {
      $$(".delete").each(function(el){
        el.addEvent('click',function(){
          if (!confirm('<?php echo _("Etes-vous sûr de vouloir supprimer cet élément ?"); ?>'))
            return false;
        });
      });
    });
  </script>
  
  <?php 
    require_once("include/admin_pages.php");
  ?>
  
  <script type="text/javascript">
    // fonction testant les droits d'un utilisateur (pour la génération du menu)
    function test_right(action){
      actions = new Array();
      
      <?php
        // ajoute les actions possible pour l'utilisateur en cours
        if (!isset($_SESSION['miki_admin_user_id']))
          return false;
        $user = new Miki_user($_SESSION['miki_admin_user_id']);
        $actions = $user->get_actions();
        
        foreach($actions as $action){
          echo "actions.push($action->id);\n";
        }
      ?>
      
      return actions.contains(action);
    }
  
    window.addEvent('domready', function() { 
  
      /***************************************
      * Pour le menu   
      ***************************************/
      myMenu = new adminMenu($('menu'));

      if (test_right(7) || test_right(15) || test_right(23) || 
          test_right(8) || test_right(16) || test_right(24) || 
          test_right(37) || test_right(38) || test_right(39) || 
          test_right(40) || test_right(41) || test_right(42) ||
          test_right(55) || test_right(56) || test_right(57) || test_right(58) ||
          test_right(59) || test_right(60) || test_right(61) ||
          test_right(62) || test_right(63) || test_right(64) ||
          test_right(65) || test_right(66) || test_right(67) ||
          test_right(68) || test_right(69) || test_right(70)
          )
        var item1 = myMenu.addMenu('Contenu', '#', -1, 'Contenu');
      if (test_right(7) || test_right(15) || test_right(23))
        myMenu.addMenu('Pages', 'index.php?pid=2', item1, 'Pages');
      if (test_right(8) || test_right(16) || test_right(24))
        myMenu.addMenu('Blocs de contenu global', 'index.php?pid=51', item1, 'Blocs de contenu global');
      if (test_right(37) || test_right(38) || test_right(39))
        myMenu.addMenu('Actualités', 'index.php?pid=121', item1, 'Actualités');
      if (test_right(40) || test_right(41) || test_right(42))
        myMenu.addMenu('Albums photos', 'index.php?pid=131', item1, 'Albums photos');
      if (test_right(55) || test_right(56) || test_right(57))
        myMenu.addMenu('Objets', 'index.php?pid=171', item1, 'Objets');
      if (test_right(58))
        myMenu.addMenu('Réservations d\'objets', 'index.php?pid=174', item1, 'Réservations d\'objets');
      if (test_right(59) || test_right(60) || test_right(61))
        myMenu.addMenu('Activités', 'index.php?pid=181', item1, 'Activités');
      if (test_right(62) || test_right(63) || test_right(64))
        myMenu.addMenu('Evénements', 'index.php?pid=191', item1, 'Evénements');
      if (test_right(65) || test_right(66) || test_right(67))
        myMenu.addMenu('Documents', 'index.php?pid=201', item1, 'Documents');
      if (test_right(68) || test_right(69) || test_right(70))
        myMenu.addMenu('Bonnes adresses', 'index.php?pid=211', item1, 'Bonnes adresses');
      if (test_right(71) || test_right(72) || test_right(73))
        myMenu.addMenu('Rédactions', 'index.php?pid=221', item1, 'Rédactions');
      if (test_right(74) || test_right(75) || test_right(76))
        myMenu.addMenu('Vidéos', 'index.php?pid=231', item1, 'Vidéos');
      if (test_right(74))
        myMenu.addMenu('Commentaires', 'index.php?pid=241', item1, 'Commentaires');
        

      if (test_right(4) || test_right(12) || test_right(20) || 
          test_right(5) || test_right(13) || test_right(21) || 
	        test_right(6) || test_right(14) || test_right(22) || 
	        test_right(25) || test_right(26) || test_right(27) || 
          test_right(28) || test_right(29) || test_right(30))      
        var item2 = myMenu.addMenu('Disposition', '#', -1, 'Disposition');
      if (test_right(4) || test_right(12) || test_right(20))
        myMenu.addMenu('Feuilles de style', 'index.php?pid=11', item2, 'Feuilles de style');
      if (test_right(5) || test_right(13) || test_right(21))
        myMenu.addMenu('Gabarits', 'index.php?pid=21', item2, 'Gabarits');
      if (test_right(6) || test_right(14) || test_right(22))
        myMenu.addMenu('Section des gabarits', 'index.php?pid=31', item2, 'Section des gabarits');
      if (test_right(25) || test_right(26) || test_right(27))
        myMenu.addMenu('Catégories', 'index.php?pid=81', item2, 'Catégories');
      if (test_right(28) || test_right(29) || test_right(30))
        myMenu.addMenu('Menus', 'index.php?pid=91', item2, 'Menus');

      if (test_right(3) || test_right(11) || test_right(19))      
        var item3 = myMenu.addMenu('Administration', '#', -1, 'Administration');
      if (test_right(3) || test_right(11) || test_right(19))
        myMenu.addMenu('Langues', 'index.php?pid=41', item3, 'Langues');
      if (test_right(54))
        myMenu.addMenu('Configuration du site Internet', 'index.php?pid=160', item3, 'Configuration du site Internet');
      
      if (test_right(1) || test_right(9) || test_right(17) || 
          test_right(2) || test_right(10) || test_right(18) || 
	  test_right(34) || test_right(35) || test_right(36))
        var item4 = myMenu.addMenu('Utilisateurs/Groupes', '#', -1, 'Utilisateurs/Groupes');
      if (test_right(1) || test_right(9) || test_right(17))
        myMenu.addMenu('Utilisateurs', 'index.php?pid=61', item4, 'Utilisateurs');
      if (test_right(2) || test_right(10) || test_right(18))
        myMenu.addMenu('Groupes', 'index.php?pid=71', item4, 'Groupes');
      if (test_right(2) || test_right(10) || test_right(18))
        myMenu.addMenu('Appartenance aux groupes', 'index.php?pid=64', item4, 'Appartenance aux groupes');
      if (test_right(2) || test_right(10) || test_right(18))
        myMenu.addMenu('Gestion des permissions', 'index.php?pid=74', item4, 'Gestion des permissions');
      
      if (test_right(34) || test_right(35) || test_right(36)){
        myMenu.addMenu('Gestion des membres', 'index.php?pid=101', item4, 'Gestion des membres');
        myMenu.addMenu('Inscriptions en attente', 'index.php?pid=105', item4, 'Inscriptions en attente');
      }
     
      if (test_right(43) || test_right(44) || test_right(45) || test_right(47) || test_right(48) || test_right(49) || test_right(50) || test_right(51) || test_right(52) || test_right(53))
        var item5 = myMenu.addMenu('Shop', '#', -1, 'Shop');
      
      if (test_right(47)){
        myMenu.addMenu('Mon Shop', 'index.php?pid=141', item5, 'Mon Shop');
        myMenu.addMenu('Code de promotion', 'index.php?pid=156', item5, 'Code de promotion');
        myMenu.addMenu('Gestion des taxes', 'index.php?pid=271', item5, 'Gestion des taxes');
      }
        
      if (test_right(43) || test_right(44) || test_right(45)){
        myMenu.addMenu('Gestion des catégories', 'index.php?pid=149', item5, 'Gestion des catégories');
      }
      
      if (test_right(51) || test_right(52) || test_right(53)){
        myMenu.addMenu('Articles', 'index.php?pid=143', item5, 'Articles');
        myMenu.addMenu('Attributs d\'articles', 'index.php?pid=153', item5, 'Attributs d\'articles');
        myMenu.addMenu('Options d\'articles', 'index.php?pid=281', item5, 'Options d\'articles');
        myMenu.addMenu('Sets d\'options d\'articles', 'index.php?pid=291', item5, 'Sets d\'options d\'articles');
      }
        
      if (test_right(48) || test_right(49) || test_right(50))
        myMenu.addMenu('Commandes', 'index.php?pid=147', item5, 'Commandes');
        
      if (test_right(75) || test_right(76) || test_right(77))
        myMenu.addMenu('Transporteurs', 'index.php?pid=251', item5, 'Transporteurs');
      
      if (test_right(31) || test_right(32) || test_right(33)){
        var item6 = myMenu.addMenu('Newsletter', '#', -1, 'Newsletters');
        myMenu.addMenu('Liste des abonnés', 'index.php?pid=119', item6, 'Liste des abonnés');
        myMenu.addMenu('Liste des groupes d\'abonnés', 'index.php?pid=1193', item6, 'Liste des groupes d\'abonnés');
        myMenu.addMenu('Gabarit', 'index.php?pid=111', item6, 'Gabarit');
        myMenu.addMenu('Newsletters', 'index.php?pid=114', item6, 'Newsletters');
        myMenu.addMenu('Importation CSV', 'index.php?pid=117', item6, 'Newsletters');
      }

      
      myMenu.generate();
      
      // pour la déconnexion
      $('deconnection').addEvent('click',function(){
        if (confirm('<?php echo _("Etes-vous sûr de bien vouloir vous déconnecter ?"); ?>')){
          document.location.href = 'scripts/deconnection_admin.php';
        }        
      });
      
    });
  
  </script>
  
  </head>
  <body>
  
    <div id="top">
      <a href="http://www.fbw-one.com" target="_blank" title="FBW-One"><img src="design/banner.png" alt="<?php echo _("Console d'administration FBW-One"); ?>" border="0" /></a>
    </div>
    <div id="menu">
      <div id="logout">
        <a href="#" id="deconnection" title="<?php echo _("Déconnexion"); ?>"><img src="design/logout.gif" border="0" alt="<?php echo _("Déconnexion"); ?>" /></a>
      </div>
    </div>
    
    <table align="left" style="width:100%;border-collapse:collapse">
      <tr><td>
        <?php
          if (!isset($_GET['pid']) || 
              !is_numeric($_GET['pid']) || 
              !isset($adminPages[$_GET['pid']]) ||
              !file_exists($adminPages[$_GET['pid']]))
            echo _("Aucune page n'a été spécifiée");
          else
            require_once($adminPages[$_GET['pid']]);
        ?>
      </td></tr>
    </table>
    
    <div id="send_result"><div id="send_result_text"></div></div>

  </body>
</html>
