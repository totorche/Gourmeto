<?					
  require_once("include/headers.php");
  require_once("class.phpmailer.php");

  /** Error reporting */
  error_reporting(E_ALL);
  
  /** Include path **/
  ini_set('include_path', ini_get('include_path').':./scripts/phpexcel/');
  
  
  
  if (isset($_POST['search']))
    $search = mb_strtolower($_POST['search'], 'UTF-8');
  else
    $search = "";
    
  if (isset($_POST['order']))
    $order = $_POST['order'];
  else
    $order = "";
    
  if (isset($_POST['order_type']) && $_POST['order_type'] !== "")
    $order_type = $_POST['order_type'];
  else
    $order_type = "asc";
  
  
  
  $nom_fichier = "liste_membres_seven.xls";
  
  /** PHPExcel */
  include 'PHPExcel.php';
  
  /** PHPExcel_Writer_Excel2007 */
  include 'PHPExcel/Writer/Excel5.php';
  
  // Create new PHPExcel object
  $objPHPExcel = new PHPExcel();
  
  // met la feuille en mode Paysage A4
  $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
  $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
  
  // Set properties
  $objPHPExcel->getProperties()->setCreator("Seven-Club");
  $objPHPExcel->getProperties()->setTitle("Liste des membres - Club Seven");
  
  // Add some data
  $objPHPExcel->setActiveSheetIndex(0);
  $objPHPExcel->getActiveSheet()->getStyle('A1:U1')->getFont()->setBold(true);
  
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
  $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(26);
  $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(35);
  $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(35);
  $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(13);
  $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(16);
  $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
  $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
  $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
  
  $objPHPExcel->getActiveSheet()->SetCellValue('A1', "Card");
  $objPHPExcel->getActiveSheet()->SetCellValue('B1', "Titre");
  $objPHPExcel->getActiveSheet()->SetCellValue('C1', "Nom");
  $objPHPExcel->getActiveSheet()->SetCellValue('D1', "Prénom");
  $objPHPExcel->getActiveSheet()->SetCellValue('E1', "Adresse");
  $objPHPExcel->getActiveSheet()->SetCellValue('F1', "NPA");
  $objPHPExcel->getActiveSheet()->SetCellValue('G1', "Localité");
  $objPHPExcel->getActiveSheet()->SetCellValue('H1', "E-mail privé");
  $objPHPExcel->getActiveSheet()->SetCellValue('I1', "E-mail pro");
  $objPHPExcel->getActiveSheet()->SetCellValue('J1', "Mobile");
  $objPHPExcel->getActiveSheet()->SetCellValue('K1', "Black/iPhone");
  $objPHPExcel->getActiveSheet()->SetCellValue('L1', "Maison");
  $objPHPExcel->getActiveSheet()->SetCellValue('M1', "Agenda EH;EW");
  $objPHPExcel->getActiveSheet()->SetCellValue('N1', "SMS");
  $objPHPExcel->getActiveSheet()->SetCellValue('O1', "Date naissance");
  $objPHPExcel->getActiveSheet()->SetCellValue('P1', "Alcools");
  $objPHPExcel->getActiveSheet()->SetCellValue('Q1', "Long Drinks");
  $objPHPExcel->getActiveSheet()->SetCellValue('R1', "Cocktails");
  $objPHPExcel->getActiveSheet()->SetCellValue('S1', "Artistes");
  $objPHPExcel->getActiveSheet()->SetCellValue('T1', "Groupes");
  $objPHPExcel->getActiveSheet()->SetCellValue('U1', "Musique");

  // recherche des personnes
  $persons = array();
  $sql = "select mp.*, ma.username, ma.type,
          (YEAR(CURRENT_DATE) - YEAR(mp.birthday)) - (RIGHT(CURRENT_DATE,5) < RIGHT(mp.birthday,5)) AS age
          from miki_person mp, miki_account ma
          where ma.state > 0 and
                ma.person_id = mp.id and (
                LOWER(ma.id) = '$search' or 
                LOWER(mp.number) = '$search' or 
                LOWER(mp.firstname) like '%$search%' or 
                LOWER(mp.lastname) like '%$search%' or 
                LOWER(mp.npa) like '%$search%' or 
                LOWER(mp.city) like '%$search%' or 
                LOWER(mp.dept) like '%$search%' or 
                LOWER(mp.country) like '%$search%' or 
                LOWER(mp.email1) like '%$search%' or 
                LOWER(ma.username) like '%$search%')";
  
  if ($order == "")
    $sql .= " order by ma.type, ma.username asc";
  elseif($order == "no")
    $sql .= " order by CAST(mp.number AS UNSIGNED) $order_type";
  elseif($order == "username")
    $sql .= " order by LCASE(ma.username) collate utf8_general_ci $order_type";
  elseif($order == "firstname")
    $sql .= " order by LCASE(mp.firstname) collate utf8_general_ci $order_type";
  elseif($order == "lastname")
    $sql .= " order by LCASE(mp.lastname) collate utf8_general_ci $order_type";
  elseif($order == "country")
    $sql .= " order by LCASE(mp.country) collate utf8_general_ci $order_type";  
  elseif($order == "city")
    $sql .= " order by LCASE(mp.city) collate utf8_general_ci $order_type";  
  elseif($order == "date_created")
    $sql .= " order by ma.date_created $order_type";
  elseif($order == "account_type")
    $sql .= " order by ma.type $order_type";
  elseif($order == "email")
    $sql .= " order by LCASE(mp.email1) collate utf8_general_ci $order_type";
  elseif($order == "age")
    $sql .= " order by age $order_type";
    
  $result = mysql_query($sql);

  while($row = mysql_fetch_array($result)){
    $persons[] = new Miki_person($row[0]);
  }
	
	$ligne = 2;
	
  foreach($persons as $person){
    
    // récupert le type de téléphone que possède le membre
    $type_mobile = array();
    if (isset($person->others['iphone']) && $person->others['iphone'] == 1)
      $type_mobile[] = "iP";
    if (isset($person->others['blackberry']) && $person->others['blackberry'] == 1)
      $type_mobile[] = "B";
    $type_mobile = implode("; ", $type_mobile);
    
    // si le membre reçoit les alertes par SMS
    if (isset($person->others['sms_events']) && $person->others['sms_events'] == 1)
      $sms = "Yes";
    else
      $sms = "No";
    
    // vérifie si le membre est inscrit à la newsletter des événements  
    $email_notification = array();
    if (Miki_newsletter_member::email_exists($person->email1)){
      $member = new Miki_newsletter_member();
      $member->load_from_email($person->email1);
      $email_notification[] = "EH";
    }
    if (Miki_newsletter_member::email_exists($person->email2)){
      $member = new Miki_newsletter_member();
      $member->load_from_email($person->email2);
      $email_notification[] = "EW";
    }
    $email_notification = implode("; ", $email_notification);
    
    // récupert la date d'anniversaire
    $birthday = explode("-", $person->birthday);
    $year = $birthday[0];
    $month = $birthday[1];
    $day = $birthday[2];
    
    // enlève les premiers '0'
    if (substr($day, 0, 1) == '0')
      $day = substr($day, 1, 1);
    if (substr($month, 0, 1) == '0')
      $month = substr($month, 1, 1);
      
    $musique = "";
    if (isset($person->others['musique_blues']) && $person->others['musique_blues'] == 1) $musique .=  "Blues, "; 
    if (isset($person->others['musique_fusion']) && $person->others['musique_fusion'] == 1) $musique .=  "Fusion, "; 
    if (isset($person->others['musique_pop']) && $person->others['musique_pop'] == 1) $musique .=  "Pop, "; 
    if (isset($person->others['musique_tango']) && $person->others['musique_tango'] == 1) $musique .=  "Tango, "; 
    if (isset($person->others['musique_rock&roll']) && $person->others['musique_rock&roll'] == 1) $musique .=  "Rock & Roll, "; 
    if (isset($person->others['musique_classique']) && $person->others['musique_classique'] == 1) $musique .=  "Classique, "; 
    if (isset($person->others['musique_hiphop']) && $person->others['musique_hiphop'] == 1) $musique .=  "Hip Hop, "; 
    if (isset($person->others['musique_jazz']) && $person->others['musique_jazz'] == 1) $musique .=  "Jazz, "; 
    if (isset($person->others['musique_latin']) && $person->others['musique_latin'] == 1) $musique .=  "Latin, "; 
    if (isset($person->others['musique_dance']) && $person->others['musique_dance'] == 1) $musique .=  "Dance, "; 
    if (isset($person->others['musique_country']) && $person->others['musique_country'] == 1) $musique .=  "Country, "; 
    if (isset($person->others['musique_reggae']) && $person->others['musique_reggae'] == 1) $musique .=  "Reggae, "; 
    if (isset($person->others['musique_instrumental']) && $person->others['musique_instrumental'] == 1) $musique .=  "Instrumental, "; 
    if (isset($person->others['musique_opera']) && $person->others['musique_opera'] == 1) $musique .=  "Opéra, "; 
    if (isset($person->others['musique_autres']) && $person->others['musique_autres'] == 1) $musique .=  "Autres, ";
    $musique = substr($musique, 0, strlen($musique) - 2);
    
    if (isset($person->others['alcools_preferes']))
      $alcools = $person->others['alcools_preferes'];
    else
      $alcools = "";
      
    if (isset($person->others['long_drink_preferes']))
      $long_drink = $person->others['long_drink_preferes'];
    else
      $long_drink = "";
      
    if (isset($person->others['cocktails_preferes']))
      $cocktails = $person->others['cocktails_preferes'];
    else
      $cocktails = "";
      
    if (isset($person->others['artistes_preferes']))
      $artistes = $person->others['artistes_preferes'];
    else
      $artistes = "";
    
    if (isset($person->others['groupes_preferes']))
      $groupes = $person->others['groupes_preferes'];
    else
      $groupes = "";
    
    $objPHPExcel->getActiveSheet()->getCell("A$ligne")->setValueExplicit($person->number, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("B$ligne")->setValueExplicit($person->type, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("C$ligne")->setValueExplicit($person->lastname, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("D$ligne")->setValueExplicit($person->firstname, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("E$ligne")->setValueExplicit($person->address, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("F$ligne")->setValueExplicit($person->npa, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("G$ligne")->setValueExplicit($person->city, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("H$ligne")->setValueExplicit($person->email1, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("I$ligne")->setValueExplicit($person->email2, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("J$ligne")->setValueExplicit($person->tel1, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("K$ligne")->setValueExplicit($type_mobile, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("L$ligne")->setValueExplicit($person->tel2, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("M$ligne")->setValueExplicit($email_notification, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("N$ligne")->setValueExplicit($sms, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("O$ligne")->setValueExplicit("$day/$month/$year", PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("P$ligne")->setValueExplicit($alcools, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("Q$ligne")->setValueExplicit($long_drink, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("R$ligne")->setValueExplicit($cocktails, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("S$ligne")->setValueExplicit($artistes, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("T$ligne")->setValueExplicit($groupes, PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->getCell("U$ligne")->setValueExplicit($musique, PHPExcel_Cell_DataType::TYPE_STRING);
    
    $ligne++;
	}
	
	$objPHPExcel->getActiveSheet()->getStyle("A1:U" .($ligne - 1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	
	// Save Excel 2007 file
  $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
  $objWriter->save($nom_fichier);
  
  
  // envoi le mail à l'OVJ
  /*$mail = new phpmailer();
  $mail->SetLanguage('fr');
  
  $mail->CharSet	=	"UTF-8";
  $mail->From = "info@seven-club.ch";
  $mail->FromName = "Seven-club.ch";
  $mail->IsMail();
  $mail->isHTML(true);
                 
  $subject = "Liste";
      
  // contenu html
  $body = "Bonjour,<br /><br />
           Vous trouverez ci-joint le rapport de vente des plaques d'immatriculation JU sur Adjuger.ch pour la période du vendredi $debut_affichage au jeudi $fin_affichage.
           <br /><br /><br />Merci et meilleures salutations.<br /><br />
           <span style=\"font-weight:bold\">Adjuger.ch</span><br /><br />
           <hr style=\"height:1px\" /><span style=\"font-size:12px\">
           <span style=\"font-weight:bold\">Remarque :</span><br />
           Ceci est un e-mail automatique, vous ne pouvez donc pas y répondre<hr style=\"height:1px\" /></span>";   
  
  $mail->Subject = $subject;
  $mail->Body = $body;
  
  $mail->addAttachment("../reports/ovj/$nom_fichier");
  
  $mail->AddAddress("herve@fbw-one.com");
  $mail->AddAddress("bernard@fbw-one.com");
  $mail->AddAddress("plaques.ovj@jura.ch");
  
  if(!$mail->Send()){
    throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail") ."<br />" .$mail->ErrorInfo);
  }
  $mail->ClearAddresses();*/
  
  //echo "Terminé !";
  
  header("location: $nom_fichier");
?>