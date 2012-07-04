<?php
/**
 * Classe Miki_email
 * @package Miki
 */ 

/**
 * Inclut les fonctions d'envoi d'e-mail
 */ 
require_once('class.phpmailer.php');

/**
 * Inclut les mails en langue française
 */ 
require_once('include/mail/mail_fr.php');

/**
 * Inclut les mails en langue allemande
 */ 
//require_once('include/mail/mail_de.php');


/**
 * Représentation d'un e-mail.
 *  
 * Cette classe étend la class PHPMailer qui permet d'envoyer des e-mails.
 * Cette classe permet de rechercher le sujet et le corp d'un mail dans le
 * fichier des e-mails selon la langue en cours d'utilisation 
 * 
 * @package Miki  
 */ 
class Miki_email extends PHPMailer{
  
  /**
   * Nom de l'e-mail
   *      
   * @var string
   * @access public   
   */
  public $email_name;
  
  /**
   * Contenu des différents e-mails
   *      
   * @var string
   * @access private   
   */
  private $mails;
  
  /**
   * Constructeur. Configure le mail.
   * 
   * @param string $email_name Nom de l'e-mail
   * @param string $lang Langue dans lequel le texte sera envoyé         
   */
  function __construct($email_name, $lang = 'fr'){
    $this->email_name = $email_name;
    $this->mails = $GLOBALS["mails_$lang"];
    $this->SetLanguage($_SESSION['lang']);
    $this->CharSet	=	"UTF-8";
    $this->IsHTML(true);
    
    global $is_windows;
    
    if (isset($is_windows) && $is_windows === true)
      $this->IsSMTP();
    else
      $this->IsMail();
  }
  
  /**
   * Change la langue de l'e-mail
   * 
   * @param string $lang Langue dans lequel le texte sera envoyé       
   */     
  public function set_language($lang){
    $this->mails = $GLOBALS["mails_$lang"];
  }
  
  /**
   * Initialise les variable de l'e-mail.
   * 
   * Le sujet et le corp de l'e-mail peuvent contenir différentes variables. Ces variables sont renseignées grâce à cette fonction
   * 
   * @param mixed $vars Tableau dont l'indice correspond au nom de la variable a créer et l'élément à la valeur de la variable            
   */     
  public function init($vars = ""){
    $this->get_subject($vars);
    $this->get_body($vars);
  }
  
  /**
   * Récupert le sujet de l'e-mail. 
   * 
   * Récupert le sujet de l'e-mail et renseigne les différentes variables trouvées.
   * 
   * @access private      
   */     
  private function get_subject($vars = ""){
    // récupert les variables (leur contenu + leur nom)
    foreach($vars as $key => $val){
      $temp = $key;
      $$temp = $val;
    }
    
    // récupert le sujet de l'e-mail
    ob_start();
    eval("?>" .$this->mails[$this->email_name]['sujet']); 
    $this->Subject = ob_get_contents();
    ob_end_clean();
  }
  
  /**
   * Récupert le corps de l'e-mail. 
   * 
   * Récupert le corps de l'e-mail et renseigne les différentes variables trouvées.
   * 
   * @access private      
   */
  private function get_body($vars = ""){
    // récupert les variables (leur contenu + leur nom)
    foreach($vars as $key => $val){
      $temp = $key;
      $$temp = $val;
    }
    
    // récupert le corp de l'e-mail
    ob_start();
    eval("?>" .$this->mails[$this->email_name]['texte']); 
    $this->Body = ob_get_contents();
    ob_end_clean();
  }
}

?>