<?php
/**
 * Classe Miki_order
 * @package Miki
 */ 

/**
 * Représentation d'une commande.
 * 
 * Une commande est liée (Miki_shop) et à des articles (Miki_shop_article) issus d'un ou plusieurs shop(s)
 * 
 * @see Miki_shop
 * @see Miki_shop_article
 *  
 * @package Miki  
 */
class Miki_order{

  /**
   * Id de la commande
   *      
   * @var int
   * @access public   
   */
  public $id;
  
  /**
   * No de la commande
   *      
   * @var int
   * @access public   
   */
  public $no_order;
  
  /**
   * Id de la personne ayant effectuée la commande
   *      
   * @var int
   * @access public   
   */
  public $id_person;
  
  /**
   * Etat de la commande : 0 = Non finalisée, 1 = En attente de paiement, 2 = Payée, 3 = Annulée
   *      
   * @var int
   * @access public   
   */
  public $state;
  
  /**
   * Type de la commande. Peut-être utilisée pour différencier plusieurs type de commandes (2 = miki_deal)
   *      
   * @var int
   * @access public   
   */
  public $type;
  
  /**
   * Type de paiement. 1 = Paypal / Carte de crédits, 2 = Virement bancaire/postal, 3 = Bulletin de versement
   *      
   * @var string
   * @access public   
   */
  public $payement_type;
  
  /**
   * Taxes sur le montant final de la commande. Tableau dont l'indice est le nom de la taxe et la valeur de l'élément est la valeur de la taxe.
   *      
   * @var mixed
   * @access public   
   */
  public $taxes;
  
  /**
   * Sous-total de la commande (avant rabais)
   *      
   * @var float
   * @access public   
   */
  public $subtotal;
  
  /**
   * Rabais
   *      
   * @var float
   * @access public   
   */
  public $discount;
  
  /**
   * Total des frais de port pour la commande complète
   *      
   * @var float
   * @access public   
   */
  public $shipping_price;
  
  /**
   * Prix final de la commande (une fois le rabais déduit et les frais de port et la TVA ajoutés)
   *      
   * @var float
   * @access public   
   */
  public $price_total;
  
  /**
   * Date de création de la commande (lorsque le premier article a été mis dans le panier)
   *      
   * @var string
   * @access public   
   */
  public $date_created;
  
  /**
   * Date de validation de la commande
   *      
   * @var string
   * @access public   
   */
  public $date_completed;
  
  /**
   * Date de paiement de la commande
   *      
   * @var string
   * @access public   
   */
  public $date_payed;
  
  /**
   * Genre du contact de livraison (Monsieur, Madame, Mademoiselle, etc.)
   *      
   * @var string
   * @access public   
   */
  public $shipping_type;
  
  /**
   * Prénom du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_firstname;
  
  /**
   * Nom de famille du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_lastname;
  
  /**
   * Adresse du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_address;
  
  /**
   * Code postal du contact de livraison
   *      
   * @var int
   * @access public   
   */
  public $shipping_npa;
  
  /**
   * Localité du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_city;
  
  /**
   * Département (ou région) du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_dept;
  
  /**
   * Pays du contact de livraison
   *      
   * @var string
   * @access public   
   */
  public $shipping_country;
  
  /**
   * Genre du contact de paiement (Monsieur, Madame, Mademoiselle, etc.)
   *      
   * @var string
   * @access public   
   */
  public $billing_type;
  
  /**
   * Prénom du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_firstname;
  
  /**
   * Nom de famille du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_lastname;
  
  /**
   * Adresse du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_address;
  
  /**
   * Code postal du contact de paiement
   *      
   * @var int
   * @access public   
   */
  public $billing_npa;
  
  /**
   * Localité du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_city;
  
  /**
   * Département (ou région) du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_dept;
  
  /**
   * Pays du contact de paiement
   *      
   * @var string
   * @access public   
   */
  public $billing_country;
  
  /**
   * Code promotionnel utilisé pour cette commande
   *      
   * @var int
   * @access public   
   */
  public $id_code_promo;
  
  /**
   * Transporteur utilisé pour l'envoi de la commande
   *      
   * @var int
   * @access public   
   */
  public $id_transporter;
  
  /**
   * Numéro de colis pour suivre la livraison
   *      
   * @var string
   * @access public
   */
  public $tracking_number;
  
  
  
  
  /**
   * Constructeur. Si la variable $id est renseignée, charge la commande dont l'id a été donné
   * 
   * @param int $id Id de la commande à charger (optionnel)
   */
  function __construct($id = ""){
    // charge la commande si l'id est fourni
    if (!empty($id))
      $this->load($id);
      

    // si création d'une nouvelle commande, on lui affecte un nouveau numéro
    $alone = false;
    if ($this->no_order == 0 || $this->no_order == ""){
      $no_order_temp = "";
      
      // récupert un no de commande unique
      while(!$alone){
        // récupert un no de commande aléatoire
        $no_order_temp = $this->get_no_order();
        
        // teste que ce no ne soit pas encore utilisé
        $sql = sprintf("SELECT count(*) FROM miki_order WHERE no_order = %d",
          mysql_real_escape_string($no_order_temp));
        $result = mysql_query($sql);

        $row = mysql_fetch_array($result);
        if ($row[0] == 0)
          $alone = true;
      }
      
      // afecte ce no à la commande en cours
      $this->no_order = $no_order_temp;
    }
  }
  
  /**
   * Génère un no de commande aléatoire (numérique)
   * 
   * @return int      
   */   
  private function get_no_order(){
    $no_order = "";
    for ($i=0;$i<8;$i++)
  		$no_order .= chr(rand(48,57));
  		
  	return $no_order;
  }
  
  /**
   * Charge une commande depuis un id
   *    
   * Si la commande n'existe pas, une exception est levée.
   *    
   * @param int $id id de la commande à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load($id){
    $sql = sprintf("SELECT * FROM miki_order WHERE id = %d",
      mysql_real_escape_string($id));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La commande demandée n'existe pas"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->no_order = $row['no_order'];
    $this->id_person = $row['id_person'];
    $this->state = $row['state'];
    $this->type = $row['type'];
    $this->payement_type = $row['payement_type'];
    $this->taxes = explode("&&", $row['taxes']);
    $this->subtotal = $row['subtotal'];
    $this->discount = $row['discount'];
    $this->shipping_price = $row['shipping_price'];
    $this->price_total = $row['price_total'];
    $this->date_created = $row['date_created'];
    $this->date_completed = $row['date_completed'];
    $this->date_payed = $row['date_payed'];
    
    $this->shipping_type = $row['shipping_type'];
    $this->shipping_firstname = $row['shipping_firstname'];
    $this->shipping_lastname = $row['shipping_lastname'];
    $this->shipping_address = $row['shipping_address'];
    $this->shipping_npa = $row['shipping_npa'];
    $this->shipping_city = $row['shipping_city'];
    $this->shipping_dept = $row['shipping_dept'];
    $this->shipping_country = $row['shipping_country'];
    
    $this->billing_type = $row['billing_type'];
    $this->billing_firstname = $row['billing_firstname'];
    $this->billing_lastname = $row['billing_lastname'];
    $this->billing_address = $row['billing_address'];
    $this->billing_npa = $row['billing_npa'];
    $this->billing_city = $row['billing_city'];
    $this->billing_dept = $row['billing_dept'];
    $this->billing_country = $row['billing_country'];
    
    $this->id_code_promo = $row['id_code_promo'];
    $this->tracking_number = $row['tracking_number'];
    
    // traite les taxes récupérées
    $tab_temp = array();
    foreach($this->taxes as $t){
      if ($t != ""){
        if (strstr($t, "%%") !== false){
          $taxe = explode("%%", $t);
          $tab_temp[$taxe[0]] = $taxe[1];
        }
      }
    }
    $this->taxes = $tab_temp;
    
    return true;
  }
  
  /**
   * Charge une commande depuis son numéro
   *    
   * Si la commande n'existe pas, une exception est levée.
   *    
   * @param int $no numéro de la commande à charger
   * @return boolean true si le chargement s'est déroulé correctement   
   */
  public function load_from_no($no){
    $sql = sprintf("SELECT * FROM miki_order WHERE no_order = %d",
      mysql_real_escape_string($no));
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 0)
      throw new Exception(_("La commande demandée n'existe pas : $sql"));
    $row = mysql_fetch_array($result);
    $this->id = $row['id'];
    $this->no_order = $row['no_order'];
    $this->id_person = $row['id_person'];
    $this->state = $row['state'];
    $this->type = $row['type'];
    $this->payement_type = $row['payement_type'];
    $this->taxes = explode("&&", $row['taxes']);
    $this->subtotal = $row['subtotal'];
    $this->discount = $row['discount'];
    $this->shipping_price = $row['shipping_price'];
    $this->price_total = $row['price_total'];
    $this->date_created = $row['date_created'];
    $this->date_completed = $row['date_completed'];
    $this->date_payed = $row['date_payed'];
    
    $this->shipping_type = $row['shipping_type'];
    $this->shipping_firstname = $row['shipping_firstname'];
    $this->shipping_lastname = $row['shipping_lastname'];
    $this->shipping_address = $row['shipping_address'];
    $this->shipping_npa = $row['shipping_npa'];
    $this->shipping_city = $row['shipping_city'];
    $this->shipping_dept = $row['shipping_dept'];
    $this->shipping_country = $row['shipping_country'];
    
    $this->billing_type = $row['billing_type'];
    $this->billing_firstname = $row['billing_firstname'];
    $this->billing_lastname = $row['billing_lastname'];
    $this->billing_address = $row['billing_address'];
    $this->billing_npa = $row['billing_npa'];
    $this->billing_city = $row['billing_city'];
    $this->billing_dept = $row['billing_dept'];
    $this->billing_country = $row['billing_country'];
    
    $this->id_code_promo = $row['id_code_promo'];
    $this->tracking_number = $row['tracking_number'];
    
    // traite les taxes récupérées
    $tab_temp = array();
    foreach($this->taxes as $t){
      if ($t != ""){
        if (strstr($t, "%%") !== false){
          $taxe = explode("%%", $t);
          $tab_temp[$taxe[0]] = $taxe[1];
        }
      }
    }
    $this->taxes = $tab_temp;
    
    return true;
  }
  
  /**
   * Sauvegarde la commande dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function save(){
    if (!isset($this->id_person))
      $this->id_person = 'NULL';
      
    if (!isset($this->id_code_promo))
      $this->id_code_promo = 'NULL';
      
    // si un l'id de la commande existe, c'est que la commande existe déjà dans la bdd. 
    // On fait donc un update
    if (isset($this->id))
      return $this->update();
      
    $this->state = 0;
    
    // concatène les taxes
    $tab_temp = array();
    if (is_array($this->taxes)){
      foreach($this->taxes as $name => $taxe){
        $tab_temp[] = "$name%%$taxe";
      }
      $taxes = implode("&&", $tab_temp);
    }
    else
      $taxes = "";
      
    $sql = sprintf("INSERT INTO miki_order 
                    (no_order, id_person, state, type, payement_type, taxes, subtotal, discount, shipping_price, price_total, date_created, 
                     shipping_type, shipping_firstname, shipping_lastname, shipping_address, shipping_npa, shipping_city, shipping_dept, shipping_country, 
                     billing_type, billing_firstname, billing_lastname, billing_address, billing_npa, billing_city, billing_dept, billing_country, id_code_promo, tracking_number) 
                    VALUES(%d, %s, %d, %d, '%s', '%s', '%F', '%F', '%F', '%F', NOW(), '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, '%s', '%s', '%s', %s, '%s')",
      mysql_real_escape_string($this->no_order),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->payement_type),
      mysql_real_escape_string($taxes),
      mysql_real_escape_string($this->subtotal),
      mysql_real_escape_string($this->discount),
      mysql_real_escape_string($this->shipping_price),
      mysql_real_escape_string($this->price_total),
      mysql_real_escape_string($this->shipping_type),
      mysql_real_escape_string($this->shipping_firstname),
      mysql_real_escape_string($this->shipping_lastname),
      mysql_real_escape_string($this->shipping_address),
      mysql_real_escape_string($this->shipping_npa),
      mysql_real_escape_string($this->shipping_city),
      mysql_real_escape_string($this->shipping_dept),
      mysql_real_escape_string($this->shipping_country),
      mysql_real_escape_string($this->billing_type),
      mysql_real_escape_string($this->billing_firstname),
      mysql_real_escape_string($this->billing_lastname),
      mysql_real_escape_string($this->billing_address),
      mysql_real_escape_string($this->billing_npa),
      mysql_real_escape_string($this->billing_city),
      mysql_real_escape_string($this->billing_dept),
      mysql_real_escape_string($this->billing_country),
      mysql_real_escape_string($this->id_code_promo),
      mysql_real_escape_string($this->tracking_number));
      
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'insertion de la commande dans la base de données : ") ."<br />" .mysql_error());
    // récupert l'id
    $this->id = mysql_insert_id();
    // recharge la page
    $this->load($this->id);
    return true;
  }
  
  /**
   * Met à jour la commande dans la base de données.
   * 
   * Si l'id existe déjà, une mise à jour (update) est effectuée à la place de l'ajout.
   * 
   * Le membre doit posséder une adresse e-mail unique (email). Si ce n'est pas le cas, une exception est levée.
   * 
   * Si une erreur survient, une exception est levée.
   *    
   * @return boolean   
   */
  public function update(){
    if (!isset($this->id_person))
      $this->id_person = 'NULL';
      
    if (!isset($this->id_code_promo))
      $this->id_code_promo = 'NULL';
      
    // si aucun id existe, c'est que la commande n'existe pas encore dans la bdd. 
    // On fait donc un insert
    if (!isset($this->id))
      return $this->save();
      
    // concatène les taxes
    $tab_temp = array();
    if (is_array($this->taxes)){
      foreach($this->taxes as $name => $taxe){
        $tab_temp[] = "$name%%$taxe";
      }
      $taxes = implode("&&", $tab_temp);
    }
    else
      $taxes = "";
      
    $sql = sprintf("UPDATE miki_order SET 
                    no_order = %d, id_person = %s, state = %d, type = %d, payement_type = '%s', taxes = '%s', subtotal = '%F', discount = '%F', shipping_price = '%F', price_total = '%F', date_payed = '%s',
                    shipping_type = '%s', shipping_firstname = '%s', shipping_lastname = '%s', shipping_address = '%s', shipping_npa = '%d', shipping_city = '%s', shipping_dept = '%s', shipping_country = '%s',
                    billing_type = '%s', billing_firstname = '%s', billing_lastname = '%s', billing_address = '%s', billing_npa = '%d', billing_city = '%s', billing_dept = '%s', billing_country = '%s', id_code_promo = %s, tracking_number = '%s'
                    WHERE id = %d",
      mysql_real_escape_string($this->no_order),
      mysql_real_escape_string($this->id_person),
      mysql_real_escape_string($this->state),
      mysql_real_escape_string($this->type),
      mysql_real_escape_string($this->payement_type),
      mysql_real_escape_string($taxes),
      mysql_real_escape_string($this->subtotal),
      mysql_real_escape_string($this->discount),
      mysql_real_escape_string($this->shipping_price),
      mysql_real_escape_string($this->price_total),
      mysql_real_escape_string($this->date_payed),
      mysql_real_escape_string($this->shipping_type),
      mysql_real_escape_string($this->shipping_firstname),
      mysql_real_escape_string($this->shipping_lastname),
      mysql_real_escape_string($this->shipping_address),
      mysql_real_escape_string($this->shipping_npa),
      mysql_real_escape_string($this->shipping_city),
      mysql_real_escape_string($this->shipping_dept),
      mysql_real_escape_string($this->shipping_country),
      mysql_real_escape_string($this->billing_type),
      mysql_real_escape_string($this->billing_firstname),
      mysql_real_escape_string($this->billing_lastname),
      mysql_real_escape_string($this->billing_address),
      mysql_real_escape_string($this->billing_npa),
      mysql_real_escape_string($this->billing_city),
      mysql_real_escape_string($this->billing_dept),
      mysql_real_escape_string($this->billing_country),
      mysql_real_escape_string($this->id_code_promo),
      mysql_real_escape_string($this->tracking_number),
      mysql_real_escape_string($this->id));

    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la mise à jour de la commande : ") ."<br />$sql<br />" .mysql_error());
    return true;
  }
  
  /**
   * Supprime la commande 
   * 
   * Si une erreur survient, une exception est levée.
   *      
   * @return boolean
   */
  public function delete(){
    $sql = sprintf("DELETE FROM miki_order WHERE id = %d",
      mysql_real_escape_string($this->id));
    if (!mysql_query($sql))
      throw new Exception(_("Erreur pendant la suppression de la commande : ") ."<br />" .mysql_error());
    return true;
  }
  
  /**
   * Définit la commande comme étant complète (commandée)
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @param boolean $send_mail Si true, on envoie les e-mails d'information. Sinon pas.
   * @param boolean $payed Si true, la commande sera définie comme étant payée.
   */
  public function set_completed($send_email = true, $payed = false){
    if (!isset($this->id_person) || !is_numeric($this->id_person))
      throw new Exception(_("Aucune personne n'est associée à la commande"));
    
    // si aucun id existe, c'est que la commande n'existe pas encore dans la bdd. 
    if (!isset($this->id))
      throw new Exception(_("La commande ne peut pas être complétée actuellement"));
    $person = new Miki_person($this->id_person);
    $company = new Miki_company($person->company_id);
    
    // si la commande a été payée
    if ($payed){
      $this->state = 2;
      
      if ($send_email){
        $sitename = Miki_configuration::get('sitename');
        $email_answer = Miki_configuration::get('email_answer');
        
        //création du mail à destination du client
        $mail = new miki_email('envoi_commande_payee_client', 'fr');
  
        $mail->From     = $email_answer;
        $mail->FromName = $sitename;
        
        // prépare les variables nécessaires à la création de l'e-mail
        $person_to = new Miki_person($this->id_person);
        $compte_bancaire['iban'] = Miki_configuration::get('payement_bank_iban');
        $compte_bancaire['bic'] = Miki_configuration::get('payement_bank_bic');
        
        $vars_array['order'] = $this;
        $vars_array['shop'] = $shop;
        $vars_array['person_to'] = $person_to;
        $vars_array['person_from'] = $person_to;
        $vars_array['sitename'] = $sitename;
        $vars_array['date_paiement'] = date("d/m/Y");
        $vars_array['time_paiement'] = date("H:i");
        $vars_array['compte_bancaire'] = $compte_bancaire;
        
        // initialise le contenu de l'e-mail
        $mail->init($vars_array);
        
        $mail->AddAddress($person_to->email1);
        if(!$mail->Send())
          throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail 2 : ") ."<br />" .$mail->ErrorInfo);
        $mail->ClearAddresses();
        
        //création du mail à destination du vendeur
        $mail = new miki_email('envoi_commande_payee_shop', 'fr');
  
        $mail->From     = $email_answer;
        $mail->FromName = $sitename;
        
        // initialise le contenu de l'e-mail
        $mail->init($vars_array);
        
        $mail->AddAddress($email_answer);
        
        if(!$mail->Send())
          throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail 2 : ") ."<br />" .$mail->ErrorInfo);
        $mail->ClearAddresses();
      }
      
      // met à jour la commande
      $sql = sprintf("UPDATE miki_order SET state = %d, date_completed = NOW(), date_payed = NOW() WHERE id = %d",
        mysql_real_escape_string($this->state),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result)
        throw new Exception(_("Erreur lors de la mise à jour de la commande : ") ."<br />" .mysql_error());
    }
    // si la commande n'a pas été payée
    else{
      $this->state = 1;
      
      // si on doit envoyer le mail de confirmation au client, on l'envoie
      if ($send_email){
        $sitename = Miki_configuration::get('sitename');
        $email_answer = Miki_configuration::get('email_answer');
        
        //création du mail à destination du client
        $mail = new miki_email('envoi_commande_non_payee_client', 'fr');
  
        $mail->From     = $email_answer;
        $mail->FromName = $sitename;
        
        // prépare les variables nécessaires à la création de l'e-mail
        $person_to = new Miki_person($this->id_person);
        $compte_bancaire['iban'] = Miki_configuration::get('payement_bank_iban');
        $compte_bancaire['bic'] = Miki_configuration::get('payement_bank_bic');
        
        // prépare les variables nécessaires à la création de l'e-mail
        $vars_array['order'] = $this;
        $vars_array['person_to'] = $person_to;
        $vars_array['sitename'] = $sitename;
        $vars_array['compte_bancaire'] = $compte_bancaire;
        
        // initialise le contenu de l'e-mail
        $mail->init($vars_array);
        
        $mail->AddAddress($person_to->email1);
        if(!$mail->Send())
          throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail 2 : ") ."<br />" .$mail->ErrorInfo);
        $mail->ClearAddresses();
        
        //création du mail à destination du vendeur
        $mail = new miki_email('envoi_commande_non_payee_shop', 'fr');
  
        $mail->From     = $email_answer;
        $mail->FromName = $sitename;
        
        // prépare les variables nécessaires à la création de l'e-mail
        $vars_array['person_from'] = $person_to;
        
        // initialise le contenu de l'e-mail
        $mail->init($vars_array);
        
        $mail->AddAddress($email_answer);
        
        if(!$mail->Send())
          throw new Exception(_("Une erreur est survenue lors de l'envoi de l'e-mail 2 : ") ."<br />" .$mail->ErrorInfo);
        $mail->ClearAddresses();
      }
      
      // met à jour la commande
      $sql = sprintf("UPDATE miki_order SET state = %d, date_completed = NOW() WHERE id = %d",
        mysql_real_escape_string($this->state),
        mysql_real_escape_string($this->id));
      $result = mysql_query($sql);
      if (!$result)
        throw new Exception(_("Erreur lors de la mise à jour de la commande : ") ."<br />" .mysql_error());
    }
    
    return true;
  }
  
  /**
   * Récupert les taxes pour la commande en cours
   * 
   * @param Miki_person $person Recherche les taxes pour la personne donnée. Si FALSE, tente de récupérer la personne déjà liée à la commande et recherche les taxes pour cette personne.
   * 
   * @static       
   * @see Miki_person   
   * @return mixed Un tableau à 2 dimensions dont les 1ers indices correspondent au nom de la taxe et les seconds indices au pays configuré et les valeurs correspondent au prix de la taxe liée
   */
  public function get_taxes($country = ""){
    // si la personne n'a pas été donnée ou que ce n'est pas un objet de type Miki_person
    if ($country == ""){
      // on tente de récupérer le pays définit pour la livraison
      if (isset($this->shipping_country) && $this->shipping_country != "" && $this->id_person != 'NULL'){
        $country = $this->shipping_country;
      }
      // Sinon on tente de récupérer la personne liée à la commande
      elseif (isset($this->id_person) && is_numeric($this->id_person)){
        try{
          $person = new Miki_person($this->id_person);
          $country = $person->country;
        }
        catch(Exception $e){
          return false;
        }
      }
      else{
        return false;
      }
    }
    
    // recherche toutes les taxes configurées
    $sql = "SELECT mt.name name, mtv.country country, mtv.value value 
            FROM miki_shop_tax mt 
            LEFT JOIN miki_shop_tax_value mtv ON mt.id = mtv.id_tax";
    
    $sql .= " ORDER BY mt.id, mtv.id ASC";
    
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br />$sql<br />");
    
    $taxes_temp = array();
    $taxes = array();
    
    // parcourt les résultats
    while($row = mysql_fetch_array($result)){
      $taxes_temp[$row['name']][$row['country']] = $row['value'];
    }
    
    // on ne retourne que les valeurs des taxes pour le pays concerné
    // parcourt les différents types de taxes
    foreach($taxes_temp as $index=>$tax){
      // si une valeur est définie pour le pays donné dans le type de taxe en cours, on prend cette valeur
      if (isset($tax[$country])){
        $taxes[$index][$country] = $tax[$country];
      }
      // sinon on prend la valeur par défaut pour la taxe en cours
      elseif (isset($tax["all"])){
        $taxes[$index][$country] = $tax["all"];
      }
      // si aucune valeur par défaut de définie (problème quelque part) on donne la valeur '0' au pays concerné
      else{
        $taxes[$index][$country] = 0;
      }
    }
    
    return $taxes;
  }
  
  /**
   * Récupert les frais de port de la commande en fonction de la personne qui le commande et du transporteur sélectionné.
   * 
   * Comme les articles d'une commande peuvent être issus de différents shop gérant différemment les frais de port, 
   * on récupert les frais de ports pour chaque shop concerné.
   * 
   * @param Miki_person $person Données de la personne pour le calcul des frais de port. Si cette donnée est omise, on récupérera la personne liée à la commande
   * @param int $id_transporter Récupert les frais de port de la commande pour le transporteur dont l'id est donné. Si vide, prend le transporteur affecté à la commande ou le transporteur par défaut.        
   *      
   * @return mixed Un tableau dont les indices correspondent à l'id de chaque shop concerné et les valeurs correspondent au prix de livraison pour le shop
   */   
  public function get_shipping($person = false, $id_transporter = ""){
    
    // si la personne n'a pas été donnée ou que ce n'est pas un objet de type Miki_person
    if (!$person || !($person instanceof Miki_person)){
      
      // on tente de récupérer la personne liée à la commande
      if (!isset($this->id_person) || $this->id_person == "" || $this->id_person == 'NULL')
        return false;
      
      try{
        $person = new Miki_person($this->id_person);
      }
      catch(Exception $e){
        return false;
      }
    }
    
    $shipping = array();
    
    // recherche la somme totale du poid et du prix des articles de la commande triés par shop    
    $sql = sprintf("SELECT msa.id_shop, sum(moa.nb * msa.weight) weight, sum(moa.nb * moa.price) price
                    FROM miki_order_article moa, 
                         miki_shop_article msa 
                    WHERE moa.id_order = %d AND
                          msa.id = moa.id_article
                    GROUP BY msa.id_shop",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    // calcul le prix de la livraison pour chaque shop
    while($row = mysql_fetch_array($result)){
      // récupert le shop de l'article en cours
      $shop = new Miki_shop($row[0]);
      
      $id_transport = $this->id_transporter;
      
      // si le transporteur n'est pas encore défini, on prend le transporteur par défaut
      if (empty($id_transporter) || $id_transporter == 'NULL'){
        $id_transporter = Miki_configuration::get('default_shop_transporter');
        
        // si aucun transporteur par défaut n'est défini, on arrête là
        if ($id_transporter === false)
          return false;
      }
      
      // récupert la méthode d'envoi selon le transporteur choisi
      $shipping_method = $shop->get_shipping_method($id_transporter);
      
      // si le transporteur n'est pas défini
      if ($shipping_method === false){
        $shipping_method = "";
      }
      
      // en fonction du poid des articles
      if ($shipping_method == 1){
        $shop_shipping = new Miki_shop_shipping_weight_price(0, $shop->id, $id_transporter);
        $shipping[$row[0]] = $shop_shipping->get_shipping($row[1], $person);
      }
      // en fonction du montant
      elseif ($shipping_method == 2){
        $shop_shipping = new Miki_shop_shipping_weight_price(0, $shop->id, $id_transporter);
        $shipping[$row[0]] = $shop_shipping->get_shipping($row[2], $person);
      }
      // frais fixes en fonction du pays
      elseif ($shipping_method == 3){
        $shop_shipping = new Miki_shop_shipping_fix(1, $shop->id, $id_transporter);
        $shipping[$row[0]] = $shop_shipping->get_shipping("", $person);
      }
      // pas de frais de port
      elseif ($shipping_method == 4){
        $shop_shipping = new Miki_shop_shipping_none(2, $shop->id, $id_transporter);
        $shipping[$row[0]] = $shop_shipping->get_shipping("", $person);
      }
    }
    
    return $shipping;
  }
  
  /**
   * Récupert les différents shops d'où proviennent les articles de la commande
   * 
   * @return mixed Un tableau d'éléments de type Miki_shop correspondant aux shops trouvés      
   */   
  public function get_shops(){
    $return = array();
    
    // recherche les shops    
    $sql = sprintf("SELECT distinct msa.id_shop FROM 
                    miki_order_article moa, miki_shop_article msa 
                    WHERE moa.id_order = %d AND 
                          msa.id = moa.id_article",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    
    // calcul le prix de la livraison pour chaque shop
    while($row = mysql_fetch_array($result)){
      $element = new Miki_shop($row[0]);
      $return[] = $element;
    }
    
    return $return;
  }
  
  /**
   * Ajoute un article à la commande
   * 
   * Si une erreur survient, une exception est levée.      
   *
   * @param int $id_article Id de l'article à ajouter
   * @param int $nb Nombre d'exemplaire de l'article à ajouter
   * @param boolean $replace Si true et si l'article existe déjà, on remplace le nombre d'articles commandés par le nouveau nombre donné. Si false et si l'article existe déjà, on ajoute le nombre d'articles donné au nombre déjà existant.       
   * @param string $options Les options de l'article séparées par "&&". Exemple : 1&&2
   * @param string $attributes Les attributs de l'article séparés par "&&". Exemple : couleur=bleu&&grandeur=L   
   * @param boolean $is_miki_deal Pour savoir si on a à faire un un deal. Si oui, on prend le prix et la quantité du deal.
   * 
   * @return int L'id de l'article dans la commande         
   */     
  public function add_article($id_article, $nb = 1, $replace = true, $attributes = "", $options = "", $is_miki_deal = false){
    $article = new Miki_shop_article($id_article);
    
    // regarde si l'article existe déjà dans la commande avec les mêmes attributs et les mêmes options
    $id_order_article = $this->has_article($id_article, $attributes, $options, $is_miki_deal);
    
    // vérifie si on utilise la gestion des stock ou pas
    $use_stock = Miki_configuration::get('miki_shop_gestion_stock');
    
    // si gestion des stocks
    if ($use_stock){
      // vérifie pour chaque option si son stock est suffisant
      $options = explode("&&", $options);
      foreach($options as $option_id){
        $option = new Miki_shop_article_option($option_id);
        if ($option->use_stock && $option->get_quantity_available($this) < $nb){
          throw new Exception(_("La quantité demandée pour cet article est supérieure au stock disponible"));
        }
      }
    }

    // récupert la quantité totale de l'article demandée    
    try{
      if ($replace || $id_order_article === false){
        $nb_articles = $nb;
        //$is_miki_deal = false;
      }
      else{
        $order_article = new Miki_order_article($id_order_article);
        $nb_articles = $order_article->nb + $nb;
        //$is_miki_deal = $order_article->miki_deal;
      }
    }
    catch(Exception $e){
      $nb_articles = $nb;
    }
    
    // récupert le prix de l'article (selon si c'est un deal, s'il y a une promotion ou rien)
    try{
      // si c'est un deal
      if ($is_miki_deal){
        $deal = current(Miki_deal::get_all_deals($article->id, true));
        $price = $deal->price;
        $quantity_max = $deal->quantity;
      }
      // si ce n'est pas un deal
      else{
        // si il y a une promotion
        $promo = $article->get_promotion();
        if ($promo)
          $price = $promo;
        else
          $price = $article->price;
        
        // récupert le nombre de fois où cet article est déjà dans la commande (sans tenir compte des attributs ni des options) 
        $quantity_already_in_basket = Miki_order_article::get_nb_articles_by_order($article->id, $this->id, false);
        
        // si l'article existe déjà dans la commande avec les mêmes attributs et les mêmes options
        if (isset($order_article) && $order_article instanceof Miki_order_article){
          // on enlève la quantité de cet article de la quantité totale
          $quantity_already_in_basket -= $order_article->nb;
        }
        
        // puis calcul la quantité max disponible pour cet article
        $quantity_max = $article->quantity - $quantity_already_in_basket;
      }
    }
    catch(Exception $e){
      throw new Exception(_("Erreur lors de l'ajout de l'article dans la commande donnée : ") ."<br />" .$e->getMessage());
    }
    
    // vérifie que la quantité donnée soit positive
    if ($nb_articles < 1){
      throw new Exception(_("La quantité n'est pas conforme"));
    }
    
    // si gestion des stocks, vérifie qu'il y ait assez de stock
    if ($use_stock && $nb_articles > $quantity_max){
      throw new Exception(_("La quantité demandée pour cet article est supérieure au stock disponible"));
    }
    
    $shipping = 0;
    
    // si l'article existe déjà dans la commande avec les mêmes attributs, on le modifie
    if ($id_order_article !== false){
      if ($nb == 0){
        $this->remove_article($id_order_article);
      }
    
      // si on doit remplacer le nombre d'articles commandés par le nouveau nombre donné 
      if ($replace){
        $sql = sprintf("UPDATE miki_order_article SET nb = %d, price = '%F', shipping = '%F', miki_deal = %d WHERE id = %d",
          mysql_real_escape_string($nb),
          mysql_real_escape_string($price),
          mysql_real_escape_string($shipping),
          mysql_real_escape_string($is_miki_deal ? 1 : 0),
          mysql_real_escape_string($id_order_article));
      }
      // si on doit ajouter le nombre d'articles commandés au nombre actuel 
      else{
        $sql = sprintf("UPDATE miki_order_article SET nb = nb + %d, price = '%F', shipping = '%F', miki_deal = %d WHERE id = %d",
          mysql_real_escape_string($nb),
          mysql_real_escape_string($price),
          mysql_real_escape_string($shipping),
          mysql_real_escape_string($is_miki_deal ? 1 : 0),
          mysql_real_escape_string($id_order_article));
      }
    }
    // si l'article n'existe pas encore dans la commande avec les mêmes attributs, on l'ajoute
    else{
      if ($nb > 0){
        $sql = sprintf("INSERT INTO miki_order_article (id_order, id_article, nb, price, shipping, attributes, miki_deal) VALUES(%d, %d, %d, '%F', '%F', '%s', %d)",
            mysql_real_escape_string($this->id),
            mysql_real_escape_string($id_article),
            mysql_real_escape_string($nb),
            mysql_real_escape_string($price),
            mysql_real_escape_string($shipping),
            mysql_real_escape_string($attributes),
            mysql_real_escape_string($is_miki_deal ? 1 : 0));
      }
    }

    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de l'article dans la commande donnée : ") ."<br />" .mysql_error());
      
    // retourne l'id de l'article dans la commande
    if ($id_order_article !== false){
      return $id_order_article;
    }
    else{
      return mysql_insert_id();
    }
  }
  
  /** 
   * Supprime un article (id issu de Miki_order_article) de la commande
   * 
   * Si une erreur survient, une exception est levée.
   * 
   * @see Miki_order_article      
   *      
   * @param int $id_order_article Id de l'article (id issu de Miki_order_article) à supprimer
   */   
  public function remove_article($id_order_article){
    $sql = sprintf("DELETE FROM miki_order_article WHERE id = %d",
      mysql_real_escape_string($id_order_article));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression de l'article de la commande : ") ."<br />" .mysql_error());
  }  
  
  /**
   * Recherche si l'article (Miki_shop_article) donné (avec ses attributs et ses options) fait partie de la commande
   * 
   * @see Miki_shop_article
   *      
   * @param int $id_article Id de l'article
   * @param string $attributes Attributs de l'article
   * @param string $options Les options de l'article séparées par "&&". Exemple : 1&&2
   * @param boolean $is_miki_deal Pour savoir si on a à faire un un deal.      
   * 
   * @return mixed L'id de l'élément représentant l'article dans la commande ou FALSE si il n'existe pas dans la commande      
   */   
  public function has_article($id_article, $attributes, $options, $is_miki_deal){
     if (!is_numeric($id_article))
      return false;

    // créé la requête SQL pour vérifier si l'article existe avec les options données
    $options = explode("&&", $options);
    $options_sql = "";
    foreach($options as $option){
      if (is_numeric($option)){
        $options_sql .= sprintf(" AND EXISTS (SELECT * FROM miki_order_article_s_miki_shop_article_option WHERE miki_order_article_id = miki_order_article.id AND miki_shop_article_option_id = %d)",
          mysql_real_escape_string($option));
      }
    }
    
    $sql = sprintf("SELECT id FROM miki_order_article WHERE id_order = %d AND id_article = %d AND attributes = '%s' AND miki_deal = %d",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($id_article),
        mysql_real_escape_string($attributes),
        mysql_real_escape_string($is_miki_deal ? 1 : 0));
    
    // ajoute la requête SQL pour vérifier si l'article existe avec les options données
    $sql .= $options_sql;
    $result = mysql_query($sql) or die("Erreur SQL : $sql");
    if (mysql_num_rows($result) == 1){
      $row = mysql_fetch_array($result);
      return $row[0];
    }
    else{
      return false;
    }
  }
  
  /**
   * Recherche tous les articles de la commande
   *
   * @param int $shop_id Si != 0, on ne récupert que les articles de cette commande pour le shop donné
   * 
   * @return mixed Un tableau d'éléments Miki_order_article représentant les articles trouvés    
   */ 
  public function get_all_articles($shop_id = ""){
    $return = array();
    
    if ($shop_id == ""){
      $sql = sprintf("SELECT id FROM miki_order_article WHERE id_order = %d ORDER BY id_article ASC",
        mysql_real_escape_string($this->id));
    }
    else{
      $sql = sprintf("SELECT moa.id FROM 
                      miki_order_article moa, miki_shop_article msa 
                      WHERE moa.id_order = %d AND
                            msa.id = moa.id_article AND
                            msa.id_shop = %d",
        mysql_real_escape_string($this->id),
        mysql_real_escape_string($shop_id));
    }
    $result = mysql_query($sql) or die("Erreur sql : $sql");
    while($row = mysql_fetch_array($result)){
      $return[] = new Miki_order_article($row[0]);
    }
    return $return;
  }
  
  /**
   * Recherche le nombre d'articles de la commande
   *
   * @param boolean $count_all Si TRUE, on compte le nombre total d'exemplaires de chaque article de la commande. Si FALSE, on ne compte qu'un seul exemplaire par article
   * @param int $shop_id Si != 0, on ne récupert que les articles de cette commande pour le shop donné
   * 
   * @return int Le nombre d'articles trouvés   
   */ 
  public function get_nb_articles($count_all = true, $shop_id = ""){
    $return = array();
    
    if ($shop_id == ""){
      // si on compte le nombre total d'exemplaires de chaque article
      if ($count_all){
        $sql = sprintf("SELECT SUM(nb) FROM miki_order_article WHERE id_order = %d",
          mysql_real_escape_string($this->id));
      }
      // si on ne compte qu'un seul exemplaire par article
      else{
        $sql = sprintf("SELECT COUNT(*) FROM miki_order_article WHERE id_order = %d",
          mysql_real_escape_string($this->id));
      }
    }
    else{
      // si on compte le nombre total d'exemplaires de chaque article
      if ($count_all){
        $sql = sprintf("SELECT SUM(moa.nb) FROM 
                        miki_order_article moa, miki_shop_article msa 
                        WHERE moa.id_order = %d AND
                              msa.id = moa.id_article AND
                              msa.id_shop = %d",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($shop_id));
      }
      // si on ne compte qu'un seul exemplaire par article
      else{
        $sql = sprintf("SELECT COUNT(moa.id) FROM 
                        miki_order_article moa, miki_shop_article msa 
                        WHERE moa.id_order = %d AND
                              msa.id = moa.id_article AND
                              msa.id_shop = %d",
          mysql_real_escape_string($this->id),
          mysql_real_escape_string($shop_id));
      }
    }
    $result = mysql_query($sql) or die("Erreur sql : $sql");
    $row = mysql_fetch_array($result);
    
    $total = $row[0];
    if ($total == "")
      $total = 0;
      
    return $total;
  }
  
  /**
   * Calcul le prix total de la commande
   * 
   * @return float Le prix total de la commande
   */     
  public function get_total(){
    $total = 0;
    
    // récupert tous les articles de la commande
    $articles = $this->get_all_articles();
    
    // parcourt ces articles
    foreach($articles as $a){
      $price = $a->get_price(true);
      
      // puis calcul le prix
      $total += $a->nb * $price;
    }
    
    return $total;
  }
  
  /**
   * Met à jour les stocks
   * 
   * @return boolean TRUE si réussite, FALSE sinon         
   */     
  public function update_stock(){
    try{
      // récupert tous les articles de la commande
      $articles = $this->get_all_articles();
      
      // parcourt tous les articles de la commande
      foreach($articles as $a){
        // récupert les détails de l'article en cours
        $article = new Miki_shop_article($a->id_article);
        
        // si c'est un deal
        if ($a->miki_deal){
          // récupert le deal concernant l'article en cours
          $deal = current(Miki_deal::get_all_deals($a->id_article, true));

          // vérifie que le deal soit disponible pour la quantité demandée
          if ($deal->quantity < $a->nb)
            throw new Exception(sprintf(_("L'article '%s' n'est plus disponible dans la quantité demandée"), $article->name[$_SESSION['lang']]));
            
          // enlève l'article du stock disponible pour le deal
          $deal->quantity -= $a->nb;
          
          // puis met à jour le deal
          $deal->update();
        }
        // sinon si c'est un article normal
        else{
          // vérifie que l'article soit disponible pour la quantité demandée
          if ($article->quantity < $a->nb)
            throw new Exception(sprintf(_("L'article '%s' n'est plus disponible dans la quantité demandée"), $article->name[$_SESSION['lang']]));
            
          // enlève l'article du stock
          $article->quantity -= $a->nb;
          
          // puis met à jour l'article
          $article->update();
          
          // si l'article est un article configurable, on gère le stock des options éventuelles
          if ($article->type == 2){
            $options = $a->get_options();
            foreach($options as $option){
              // si oui et que l'option utilise également la gestion du stock
              if ($option->use_stock){
                // enlève l'option du stock
                $option->quantity -= $a->nb;
                // puis met à jour l'option
                $option->update();
              }
            }
          }
        }
      }
    }
    catch(Exception $e){
      return false;
    }
    
    return true;
  }
  
  /**
   * Vide la commande de ses articles
   * 
   * Si une erreur survient, une exception est levée.      
   */   
  public function clear(){
    $sql = sprintf("DELETE FROM miki_order_article WHERE id_order = %d",
      mysql_real_escape_string($this->id));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de la suppression des articles de la commande donnée : ") ."<br />" .mysql_error());
  }
  
  /**
   * Ajoute l'id de transaction Paypal dans la base de données
   * 
   * Si une erreur survient, une exception est levée.      
   */   
  public function add_paypal_transaction($transaction){
    $sql = sprintf("INSERT INTO miki_order_paypal_transaction (id_order, id_transaction, date) VALUES(%d, '%s', NOW())",
      mysql_real_escape_string($this->id),
      mysql_real_escape_string($transaction));
    $result = mysql_query($sql);
    if (!$result)
      throw new Exception(_("Erreur lors de l'ajout de la transaction Paypal dans la base de données : ") ."<br />" .mysql_error());
  }
  
  /**
   * Vérifie si l'id de transaction Paypal existe déjà dans la base de données
   * 
   * @return boolean      
   */   
  public function paypal_transaction_exists($transaction){
    $sql = sprintf("SELECT count(*) FROM miki_order_paypal_transaction WHERE id_transaction = '%s'",
      mysql_real_escape_string($transaction));
    $result = mysql_query($sql);
    $row = mysql_fetch_array($result);
    if ($row[0] > 0)
      return true;
    else
      return false;
  }
  
  /**
   * Récupère toutes les commandes
   * 
   * @param int $id_person si != "", on récupert seulement les commandes de la personne donnée
   * @param int $state si != "", on récupert seulement les commandes dans l'état donné   
   *      
   * @static 
   * @return mixed Un tableau d'éléments de type Miki_order représentant les commandes trouvées   
   */      
  public static function get_all_orders($id_person = "", $state = ""){
    $return = array();
    $sql = "SELECT * FROM miki_order WHERE 1";
    
    $where = "";
    
    if ($id_person != "")
      $sql .= sprintf(" AND id_person = %d", mysql_real_escape_string($id_person));
    if ($state != "")
      $sql .= sprintf(" AND state = %d", mysql_real_escape_string($state));

    $result = mysql_query($sql);
    while($row = mysql_fetch_array($result)){
      $element = new Miki_order($row['id']);
      $return[] = $element;
    }
    return $return;
  }
  
  /**
   * Rercherche le nombre total de commandes
   * 
   * @static   
   * @return int
   */         
  public static function get_nb_orders(){
    $return = array();
    $sql = "SELECT count(mo.id)
            FROM miki_person mp, miki_order mo
            WHERE mp.id = mo.id_person";
    $result = mysql_query($sql);

    $row = mysql_fetch_array($result);
    return $row[0];
  }
}
?>