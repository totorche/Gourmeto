<?php 
  require_once("include/headers.php");
  require_once("functions_pictures.php");
  
  if (!test_right(54)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // sauve les configurations
  try{
  
    // Pour la configuration générale
    Miki_configuration::add('sitename', $_POST['sitename']);
    
    // enlève le dernier slash "/" si il a été donné
    if (substr($_POST['site_url'], -1) == '/')
      $site_url = mb_substr($_POST['site_url'], 0, mb_strlen($_POST['site_url']) - 1);
    else
      $site_url = $_POST['site_url'];
    
    // récupert le http ou https ou autre première partie
    $pos_http = strripos($site_url, "://");
    if ($pos_http){
      $http = substr($site_url, 0, $pos_http + 3);
      $site_url = substr($site_url, $pos_http + 3);
    }
    else
      $http = "";
    
    // enlève le premier sous-domaine (souvent "www.")
    $site_url_parts = explode(".", $site_url);
    if (sizeof($site_url_parts) == 3)
      $site_url = $site_url_parts[1] ."." .$site_url_parts[2];
    
    // rajoute le http
    $site_url = $http .$site_url;

    Miki_configuration::add('site_url', $site_url);
    
    Miki_configuration::add('email_answer', $_POST['email_answer']);
    Miki_configuration::add('analytics', $_POST['analytics']);
    Miki_configuration::add('address_website_company', $_POST['address_website_company']);
    
    // upload le logo
    if ($_FILES["logo_website"]['error'] != 4){
      // traite le nom de destination
      $nom_destination = decode($_POST['sitename']);  
      $system = explode('.',mb_strtolower($_FILES["logo_website"]['name'], 'UTF-8'));
      $ext = $system[sizeof($system)-1];
      $nom_destination .= "." .$ext;
      
      // le fichier doit être au format jpg, gif ou png
    	if (!preg_match('/png/i',$ext) && !preg_match('/gif/i',$ext) && !preg_match('/jpg|jpeg/i',$ext)){
        throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG : $nom_destination - " .$fichier['name']));
      }
    
      // teste s'il y a eu une erreur
    	if ($_FILES["logo_website"]['error']) {
    		switch ($_FILES["logo_website"]['error']){
    			case 1: // UPLOAD_ERR_INI_SIZE
    				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
    				break;
    			case 2: // UPLOAD_ERR_FORM_SIZE
    				throw new Exception(_("Le fichier dépasse la limite autorisée (5Mo)"));
    				break;
    			case 3: // UPLOAD_ERR_PARTIAL
    				throw new Exception(_("L'envoi du fichier a été interrompu pendant le transfert"));
    				break;
    			case 4: // UPLOAD_ERR_NO_FILE
    				throw new Exception(_("Aucun fichier n'a été indiqué"));
    				break;
    			case 6: // UPLOAD_ERR_NO_TMP_DIR
    			  throw new Exception(_("Aucun dossier temporaire n'a été configuré. Veuillez contacter l'administrateur du site Internet."));
    			  break;
    			case 7: // UPLOAD_ERR_CANT_WRITE
    			  throw new Exception(_("Erreur d'écriture sur le disque"));
    			  break;
    			case 8: // UPLOAD_ERR_EXTENSION
    			  throw new Exception(_("L'extension du fichier n'est pas supportée"));
    			  break;
    		}
    	}
    	$size = "";
    	$file = $_FILES["logo_website"]['tmp_name'];
    	if (!is_uploaded_file($file) || $_FILES["logo_website"]['size'] > 5 * 1024 * 1024)
    		throw new Exception(_("Veuillez uploader des images plus petites que 5Mb"));
    	if (!($size = @getimagesize($file)))
    		throw new Exception(_("Veuillez n'uploader que des images. Les autres fichiers ne sont pas supportés"));
    	if (!in_array($size[2], array(1, 2, 3) ) )
    		throw new Exception(_("Le type de l'image n'est pas supporté. Les types supportés sont : JPEG, GIF et PNG"));
    		
    	// pas d'erreur --> upload
    	if ((isset($_FILES["logo_website"]['tmp_name']))&&($_FILES["logo_website"]['error'] == UPLOAD_ERR_OK)) {
    		if (!move_uploaded_file($_FILES["logo_website"]['tmp_name'], "../pictures/" .$nom_destination))
          exit();
    	}
    	
    	// redimensionne l'image
    	createthumb("../pictures/" .$nom_destination, "./" .$nom_destination, 500, 500, true);
    	
    	Miki_configuration::add('logo_website', $nom_destination);
    }
    
    // Pour la configuration des pings sur le blog
    if (isset($_POST['publish_news']))
      $publish_news = $_POST['publish_news'];
    else
      $publish_news = "0";
      
    Miki_configuration::add('publish_news', $publish_news);
    
    Miki_configuration::add('event_subscription', $_POST['event_subscription']);
    
    // Pour la configuration du paiement online des events
    if (isset($_POST['event_online_payement']))
      $event_online_payement = $_POST['event_online_payement'];
    else
      $event_online_payement = "0";
    
    Miki_configuration::add('event_online_payement', $event_online_payement);
    
    // Pour la configuration de la visibilité des inscrits aux events
    if (isset($_POST['event_view_subscriptions']))
      $event_view_subscriptions = $_POST['event_view_subscriptions'];
    else
      $event_view_subscriptions = "0";
    
    Miki_configuration::add('event_view_subscriptions', $event_view_subscriptions);
    
    if (isset($_POST['publish_shop_article']))
      $publish_shop_article = $_POST['publish_shop_article'];
    else
      $publish_shop_article = "0";
      
    Miki_configuration::add('publish_shop_article', $publish_shop_article);
    
    if (isset($_POST['publish_event']))
      $publish_event = $_POST['publish_event'];
    else
      $publish_event = "0";
      
    Miki_configuration::add('publish_event', $publish_event);
    
    Miki_configuration::add('publish_email_address', $_POST['publish_email_address']);
    
    
    // Pour la configuration des paiement online
    if (isset($_POST['payement_facture_avant']))
      $payement_facture_avant = $_POST['payement_facture_avant'];
    else
      $payement_facture_avant = "0";
      
    Miki_configuration::add('payement_facture_avant', $payement_facture_avant);
    
    
    if (isset($_POST['payement_facture_apres']))
      $payement_facture_apres = $_POST['payement_facture_apres'];
    else
      $payement_facture_apres = "0";
      
    Miki_configuration::add('payement_facture_apres', $payement_facture_apres);
    
    
    if (isset($_POST['payement_bank']))
      $payement_bank = $_POST['payement_bank'];
    else
      $payement_bank = "0";
      
    Miki_configuration::add('payement_bank', $payement_bank);
    
    
    if (isset($_POST['payement_bank_iban']))
      $payement_bank_iban = $_POST['payement_bank_iban'];
    else
      $payement_bank_iban = "";
    
    Miki_configuration::add('payement_bank_iban', $payement_bank_iban);
    
    
    if (isset($_POST['payement_bank_bic']))
      $payement_bank_bic = $_POST['payement_bank_bic'];
    else
      $payement_bank_bic = "";
    
    Miki_configuration::add('payement_bank_bic', $payement_bank_bic);
    
    
    if (isset($_POST['payement_paypal']))
      $payement_paypal = $_POST['payement_paypal'];
    else
      $payement_paypal = "0";
    
    Miki_configuration::add('payement_paypal', $payement_paypal);
    
    
    if (isset($_POST['payement_paypal_account']))
      $payement_paypal_account = $_POST['payement_paypal_account'];
    else
      $payement_paypal_account = "";
    
    Miki_configuration::add('payement_paypal_account', $payement_paypal_account);
    
    
    if (isset($_POST['payement_paypal_sandbox']))
      $payement_paypal_sandbox = $_POST['payement_paypal_sandbox'];
    else
      $payement_paypal_sandbox = "0";
    
    Miki_configuration::add('payement_paypal_sandbox', $payement_paypal_sandbox);
    
    
    if (isset($_POST['payement_paypal_url_return']))
      $payement_paypal_url_return = $_POST['payement_paypal_url_return'];
    else
      $payement_paypal_url_return = "";
    
    Miki_configuration::add('payement_paypal_url_return', $payement_paypal_url_return);
    
    
    if (isset($_POST['payement_paypal_url_return_error']))
      $payement_paypal_url_return_error = $_POST['payement_paypal_url_return_error'];
    else
      $payement_paypal_url_return_error = "";
    
    Miki_configuration::add('payement_paypal_url_return_error', $payement_paypal_url_return_error);
    
    
    if (isset($_POST['payement_paypal_secure']))
      $payement_paypal_secure = $_POST['payement_paypal_secure'];
    else
      $payement_paypal_secure = "0";
    
    Miki_configuration::add('payement_paypal_secure', $payement_paypal_secure);
    
    
    if (isset($_POST['payement_paypal_idcert']))
      $payement_paypal_idcert = $_POST['payement_paypal_idcert'];
    else
      $payement_paypal_idcert = "";
    
    Miki_configuration::add('payement_paypal_idcert', $payement_paypal_idcert);
    
    
    // ajoute le message à la session
    $_SESSION['success'] = 1;
    $_SESSION['msg'] = _("Le site Internet a été configuré avec succès");
    // puis redirige vers la page précédente
    $referer = "index.php?pid=160";
    echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }catch(Exception $e){
    $referer = "index.php?pid=160";
    // sauve les élément postés
    $_SESSION['saved_news'] = $news;
    
    // ajoute le message à la session
    $_SESSION['success'] = 0;
    $_SESSION['msg'] = $e->getMessage();
    // puis redirige vers la page précédente
    //echo "<script type='text/javascript'>document.location='" .$referer ."';</script>";
  }
?>