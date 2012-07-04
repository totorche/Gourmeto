<?php 
  require_once("include/headers.php");
  
  require_once ('scripts/class.phpmailer.php');
  
  if (!test_right(31) && !test_right(32) && !test_right(33)){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  if (!isset($_POST['id_newsletter']) || !is_numeric($_POST['id_newsletter'])){
    echo "<script type='text/javascript'>document.location='" .$_SESSION['url_back'] ."';</script>";
    exit();
  }
  
  // si on envoit à tous les groupes ou juste à un groupe
  if (!isset($_POST['id_group']) || $_POST['id_group'] == ""){
    $id_group = false;
  }
  else{
    $id_group = $_POST['id_group'];
  }
   
  // si le champ "email" est donné, c'est qu'on effectue seulement un test d'envoi
  if (isset($_POST['email']) && $_POST['email'] != "")
    $email = $_POST['email'];
  else
    $email = "";
  
  $nb_max_sendings = 50;
  
  // envoi de la newsletter
  try{
    $newsletter = new Miki_newsletter($_POST['id_newsletter']); 
    $template = new Miki_newsletter_template($newsletter->template_id);
    $template->content = stripslashes($template->content);
    
    $site_url = Miki_configuration::get('site_url');
    $sitename = Miki_configuration::get('sitename');
    $email_answer = Miki_configuration::get('email_answer');
    
    /*$code = "<html>
             <head>
               <title>" .stripslashes($newsletter->subject) ."</title>
             </head>
             <body>
               <img src='$site_url/stats_newsletter-$newsletter->id-[miki_newsletter_member_id].gif' style='width:1px;height:1px' />
               $template->content
               <div style='margin: 10px 0'>
                 Pour bien recevoir nos prochains e-mails, ajoutez notre e-mail ($email_answer) dans votre carnet d'adresses.
                 <br /><br />
                 Vous avez reçu ce message, car vous êtes abonné à la newsletter \"$sitename\" en tant que \"[miki_newsletter_email]\".
                 <br /><br />
                 Pour cesser de recevoir nos offres, <a href='$site_url/newsletter_unsubscribe.php?m=[miki_newsletter_email_encoded]" .($id_group ? "&g=$id_group" : "") ."&u=[miki_newsletter_member_id_encoded]' title='désabonnez-vous'>désabonnez-vous</a>. 
               </div>
             </body>
             </html>";*/
             
    $code = "<html>
             <head>
               <title>" .stripslashes($newsletter->subject) ."</title>
             </head>
             <body>
               <img src='$site_url/stats_newsletter-$newsletter->id-[miki_newsletter_member_id].gif' style='width:1px;height:1px' />
               $template->content
               <div style='margin: 10px 0; font-size: 10px; color: #999999; line-height: 12px; font-family: verdana'>
                 Vous avez reçu ce message, car vous êtes abonné à la newsletter $sitename en tant que \"[miki_newsletter_email]\".
                 <br />
                 Ceci est un message d'information émis par $sitename. 
                 <br /><br />
                 Si vous ne souhaitez plus recevoir de messages comme celui-ci de $sitename, <a href='$site_url/newsletter_unsubscribe.php?m=[miki_newsletter_email_encoded]" .($id_group ? "&g=$id_group" : "") ."&u=[miki_newsletter_member_id_encoded]' title='désabonnez-vous'>cliquez sur ce lien</a>
                 <br />
                 La confidentialité de vos données personnelles nous tient particulièrement à cœur.
                 <br /><br />
                 Pour recevoir correctement nos prochains e-mails, ajoutez notre e-mail ($email_answer) dans votre carnet d'adresses.
                 <br /><br />
                 © " .date("Y") ." $sitename
               </div>
             </body>
             </html>";
             
    // évalue le code final (exécute le php s'il y en a)
    ob_start();
    eval("?>" .$code);
    $code = ob_get_contents();
    ob_end_clean();
             
    $code = str_replace("[miki_content]", $newsletter->content, $code);
    
    // si l'envoi n'a pas été initialisé, on l'initialise
    if (isset($_POST['init']) && $_POST['init'] == 1)
      $newsletter->init_send($id_group);
    
    // si on envoi la newsletter pour de vrai on récupert les e-mails de destination
    if ($email == ""){
      $list = $newsletter->get_emails();
    }
    // sinon on prépare l'adresse de test
    else{
      $list[0]['id'] = -1;
      $list[0]['member_id'] = 0;
      $list[0]['email'] = $email;
      $list[0]['firstname'] = false;
      $list[0]['lastname'] = false;
    }
    
    $sendings = 0;

    // création du mail
    $mail = new phpmailer();
    $mail->SetLanguage('fr');
    $mail->CharSet	=	"UTF-8";
    $mail->From     = $email_answer;
    $mail->FromName = $sitename;
    $mail->IsMail();
    $mail->IsHTML(true);
    
    $mail->Subject = stripslashes($newsletter->subject);

    // envoi des e-mails
    while(sizeof($list) > 0 && $sendings < $nb_max_sendings){
      $address = array_shift($list);
      $mail->AddAddress($address['email']);
      
      $code_temp = $code;      
      
      if ($address['firstname']){
        // remplace le code miki du prénom
        $code_temp = str_replace("[miki_newsletter_firstname]", $address['firstname'], $code_temp);
      }
      
      if ($address['lastname']){
        // remplace le code miki du nom
        $code_temp = str_replace("[miki_newsletter_lastname]", $address['lastname'], $code_temp);
      }
      
      // remplace le code miki de l'id du membre
      $code_temp = str_replace("[miki_newsletter_member_id_encoded]", base64_encode($address['member_id']), $code_temp);
      $code_temp = str_replace("[miki_newsletter_member_id]", $address['member_id'], $code_temp);    
      
      // remplace les autres codes miki
      $code_temp = str_replace("[miki_newsletter_email]", $address['email'], $code_temp);
      $code_temp = str_replace("[miki_newsletter_email_encoded]", base64_encode($address['email']), $code_temp);
      $code_temp = str_replace("[miki_newsletter_title]", stripslashes($newsletter->subject), $code_temp);
      
      $mail->Body = $code_temp;
      
      // envoi de l'e-mail en cours
      if(!$mail->Send())
        throw new Exception(_("Une erreur est survenue lors de l'envoi à '" .$list[$sendings]['email'] ."'") ."<br />" .$mail->ErrorInfo);
      
      // comptabilise l'envoi de la newsletter si on est en mode d'envoi réel et pas en mode test
      if ($email == "")
        $newsletter->add_email_sent();
      
      // Si l'e-mail a été envoyé, on le supprime l'adresse de la liste des e-mails à envoyer
      $newsletter->remove_email($address['id']);
        
      $mail->ClearAddresses();
      
      $sendings++;
    }
    
    // si la newsletter a été envoyée à l'adresse e-mail de test
    if ($email != ""){
      exit("OK-2");
    }
    // si la newsletter a été envoyée à tous les destinataires
    elseif(sizeof($list) == 0){
      $newsletter->state = 2;
      $newsletter->update();
      exit("OK-2");
    }
    // sinon, on recharge la pages et on continue l'envoi
    else{
      // on regarde le pourcentage effectué
      $percent = ($sendings / (sizeof($list) + $sendings)) * 100;
      
      exit("OK-1-$percent");
    }
  }catch(Exception $e){
    $newsletter->state = 1;
    $newsletter->update();
    exit("ERR-" .$e->getMessage());
  }
?>