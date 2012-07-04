<?php

$fond1_fr = '<?php echo "<br /><br /><br />Merci et meilleures salutations.<br /><br />
          <span style=\"font-weight:bold\">$sitename</span><br /><br />
          <hr style=\"height:1px\" /><span style=\"font-size:12px\">
          <span style=\"font-weight:bold\">Remarque :</span><br />
          Ceci est un e-mail automatique, vous ne pouvez donc pas y répondre<hr style=\"height:1px\" /></span>"; ?>';
          
          
$fond2_fr = '<?php echo "<br /><br /><br />Merci et meilleures salutations.<br /><br />
          <span style=\"font-weight:bold\">$sitename</span><br /><br />
          <hr style=\"height:1px\" /><span style=\"font-size:12px\">
          <span style=\"font-weight:bold\">Remarque :</span><br /> 
          Ce message a été envoyé à $person_to->firstname $person_to->lastname par $sitename. 
          La présence de votre nom et prénom atteste que l\'expéditeur de ce message est bien $sitename. 
          Ceci est un e-mail automatique, vous ne pouvez donc pas y répondre.<hr style=\"height:1px\" /></span>"; ?>';


/***************************************************************************/


$mails_fr['proposer_categorie']['sujet'] = '<?php echo "Proposition de catégorie sur $sitename"; ?>';
$mails_fr['proposer_categorie']['texte'] = '<?php echo "Bonjour,<br /><br />Une nouvelle proposition de catégorie a été faite sur $sitename par le membre $account_to->username.<br /><br />
                                         La catégorie est la suivante : $proposition_categorie"; ?>' .$fond1_fr;
                                              
/***************************************************************************/

$mails_fr['contact']['sujet'] = '<?php echo "Message sur $sitename de la part de $firstname $lastname"; ?>';
$mails_fr['contact']['texte'] = '<?php echo "Bonjour,<br /><br />$firstname $lastname vous a envoyé un message depuis le site $sitename<br /><br />
                              Voici ses informations<br /><br />
                              <table>
                                <tr>
                                  <td>Nom :</td>
                                  <td>$lastname</td>
                                </tr>
                                <tr>
                                  <td>Prénom :</td>
                                  <td>$firstname</td>
                                </tr>
                                <tr>
                                  <td>Adresse :</td>
                                  <td>$address</td>
                                </tr>
                                <tr>
                                  <td>NPA / Ville :</td>
                                  <td>$npa $city</td>
                                </tr>
                                <tr>
                                  <td>Pays :</td>
                                  <td>$country</td>
                                </tr>
                                <tr>
                                  <td>Email :</td>
                                  <td>$email</td>
                                </tr>
                                <tr>
                                  <td>Téléphone :</td>
                                  <td>$tel1</td>
                                </tr>
                                <tr>
                                  <td colspan=\"2\">&nbsp;</td>
                                </tr>
                                <tr>
                                  <td style=\"vertical-align:top\">Voici son message :</td>
                                  <td>$message</td>
                                </tr>          
                              </table>"; ?>' .$fond1_fr;
                                        
/***************************************************************************/

$mails_fr['shop_payment_paypal_verification']['sujet'] = '<?php echo "Problème lors d\'un paiement Paypal sur $sitename"; ?>';
$mails_fr['shop_payment_paypal_verification']['texte'] = '<?php echo "Bonjour,
                                          <br /><br />
                                          Un paiement a été effectué par Paypal mais a connu un problème.
                                          <br /><br />
                                          Voici les informations de la commande : 
                                          <br /><br />
                                          - No de la commande : $no_facture<br />
                                          - No de la transaction Paypal : $no_transaction<br /><br />
                                          Les erreurs sont les suivantes : <br />
                                          $error<br /><br />
                                          Nous vous conseillons de vérifier et de régler ce problème au plus vite."; ?>' .$fond1_fr;
                                          
/***************************************************************************/

$mails_fr['shop_payment_ogone_verification']['sujet'] = '<?php echo "Problème lors d\'un paiement par carte de crédit sur $sitename"; ?>';
$mails_fr['shop_payment_ogone_verification']['texte'] = '<?php echo "Bonjour,
                                          <br /><br />
                                          Un paiement a été effectué par carte de crédit sur $sitename mais a connu un problème.
                                          <br /><br />
                                          Voici les informations de la commande : 
                                          <br /><br />
                                          - Date de la commande : $date<br />
                                          - Client : $person_name<br />
                                          - No de la commande : $order_no<br />
                                          - Montant de la commande : $order_total<br />
                                          - Type de carte utilisée : $card_type<br />
                                          - Status de la commande : $order_status<br />
                                          - Code d\'erreur : $error<br /><br />
                                          Nous vous conseillons de vérifier et de régler ce problème au plus vite."; ?>' .$fond1_fr;
                                          
/***************************************************************************/

$mails_fr['envoi_mot_de_passe']['sujet'] = '<?php echo "Votre nouveau mot de passe pour le site Internet $sitename"; ?>';   
$mails_fr['envoi_mot_de_passe']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                        Vous nous avez demandé de vous envoyer un nouveau mot de passe :<br /><br />
                                        <span style=\"font-weight:bold\">Votre nouveau mot de passe est le suivant : $password</span><br /><br />
                                        Merci de traiter ces informations avec confidentialité et n\'oubliez pas que vous pouvez à tout moment modifier votre mot de passe dans les paramètres de votre compte utilisateur. 
                                        "; ?>' .$fond2_fr;

/***************************************************************************/

$mails_fr['confirmation_inscription']['sujet'] = '<?php echo "Confirmation de votre inscription sur $sitename"; ?>';
$mails_fr['confirmation_inscription']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                              Votre inscription sur le site $sitename a été validée.<br /><br />
                                              Vous pouvez donc dès maintenant accéder à votre compte en cliquant <a href=\"$site_url\" target=\"_blank\">ici</a><br /><br />
                                              Merci de traiter ces informations avec confidentialité et n\'oubliez pas que vous pouvez à tout moment modifier votre mot de passe dans les paramètres de votre compte utilisateur 
                                              "; ?>' .$fond2_fr;
                      
/***************************************************************************/

$mails_fr['refus_inscription']['sujet'] = '<?php echo "Refus de votre inscription sur $sitename"; ?>';
$mails_fr['refus_inscription']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                        Votre inscription sur le site $sitename a été refusée par les administrateur.<br /><br />
                                        Les informations entrées lors de la création de votre compte ne correspondent pas à la charte $sitename."; ?>' .$fond2_fr;

/***************************************************************************/  

$mails_fr['post_bid_acheteur']['sujet'] = '<?php echo "Votre enchère pour l\'article $article->title sur $sitename"; ?>';
$mails_fr['post_bid_acheteur']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                        Votre enchère d\'un montant de <span style=\'font-weight:bold\'>" .number_format($bid->price,2,\'.\',"") ." CHF</span> a été validée pour l\'article <span style=\'font-weight:bold\'>$article->title</span> sur le site $sitename.<br /><br />
                                        <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$bid->id_article\" title=\"Voir l\'article\">Voir l\'article</a>"; ?>' .$fond2_fr;

/***************************************************************************/  
                                        
$mails_fr['post_bid_meilleure_enchere']['sujet'] = '<?php echo "Votre enchère pour l\'article $article->title a été dépassée sur $sitename"; ?>';
$mails_fr['post_bid_meilleure_enchere']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                                 Votre enchère pour l\'article <span style=\'font-weight:bold\'>$article->title</span> a été dépassée sur le site $sitename.<br /><br />
                                                 <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$bid->id_article\" title=\"Voir l\'article\">Voir l\'article</a>"; ?>' .$fond2_fr;                                        

/***************************************************************************/

$mails_fr['post_bid_vendeur']['sujet'] = '<?php echo "Nouvelle enchère pour votre article $article->title sur $sitename"; ?>';
$mails_fr['post_bid_vendeur']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                       Le membre $account2->username a placé une enchère d\'un montant de <span style=\'font-weight:bold\'>" .number_format($bid->price,2,\'.\',"") ." CHF</span> pour votre article <span style=\'font-weight:bold\'>$article->title</span> sur le site $sitename.<br /><br />
                                       <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$bid->id_article\" title=\"Voir votre article\">Voir l\'article</a>"; ?>' .$fond2_fr;

/***************************************************************************/                             

$mails_fr['article_vendu_vendeur']['sujet'] = '<?php echo "Votre article a été acheté sur $sitename"; ?>';
$mails_fr['article_vendu_vendeur']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                            $person2->firstname $person2->lastname a remporté les enchères de votre article <span style=\'font-weight:bold\'>$article->title</span> sur le site $sitename.<br /><br />
                                            <table><tr><td colspan=\'2\'>
                                              Voici les détails de la vente :
                                              <br /><br />
                                            </td></tr><tr><td style=\'width:150px\'>
                                              - No de l\'article : 
                                            </td><td>
                                              <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->id</a>
                                            </td></tr><tr><td>
                                              - Titre de l\'article :
                                            </td><td>
                                              <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->title</a>
                                            </td></tr><tr><td>";
                                            
                                              // achat via enchères ou achat direct
                                              if ($article->sell_state == 2) 
                                                echo "- Prix de l\'achat : </td><td>" .number_format($article->get_max_bid()->price,2,\'.\',"") ." CHF</td></tr><tr><td>"; 
                                              elseif ($article->sell_state == 3) 
                                                echo "- Prix de l\'achat : </td><td>" .number_format($article->direct_price,2,\'.\',"") ." CHF</td></tr><tr><td style=\'vertical-align:top\'>";
                                            
                                              echo "- Frais et conditions de livraison :  
                                            </td><td>
                                              Frais de port : " .number_format($article->shipping_price,2,\'.\',"") ." CHF";
                                              
                                              // conditions d\'envoi
                                              if ($article->shipping_type == 0)
                                                echo "<br /><br />$article->shipping_text : " .$article_shipping[$article->shipping_type];
                                                       
                                      echo "</td></tr><tr><td style=\'vertical-align:top\'>
                                              - Informations sur le paiement : 
                                            </td><td>";
                                              
                                              // conditions de paiement 
                                              if ($article->payement_condition != 0)
                                                echo $article_payement[$article->payement_condition];
                                              else
                                                echo $article->payement_text;
                                                   
                                              echo "<br /><br />Méthodes de paiement : <br />";
                                                    
                                              $methodes = explode(",", $article->payement_method);
                                              foreach($methodes as $methode){
                                                echo $article_payement_method[$methode] ."<br />";
                                              }
                                             
                                      echo "</td></tr><tr><td colspan=\'2\'>
                                              <br /><br />
                                              Veuillez contacter l\'acheteur de votre article afin de régler avec lui la suite des opérations (paiement, livraison, etc.) :
                                              <br /><br />
                                            </td></tr><tr><td>
                                              - Nom d\'utilisateur :
                                            </td><td>
                                              $account2->username
                                            </td></tr><tr><td>
                                              - Prénom et nom : 
                                            </td><td>
                                              $person2->firstname $person2->lastname
                                            </td></tr><tr><td>
                                              - Adresse : 
                                            </td><td>
                                              $person2->address
                                            </td></tr><tr><td>
                                              - Code postal / localité :
                                            </td><td>
                                              $person2->npa $person2->city
                                            </td></tr><tr><td>
                                              - Téléphone 1 :
                                            </td><td>
                                              $person2->tel1
                                            </td></tr><tr><td>
                                              - Téléphone 2 :
                                            </td><td>
                                              $person2->tel2
                                            </td></tr><tr><td>
                                              - E-mail :
                                            </td><td>
                                              $person2->email1
                                            </td></tr></table>
                                            <br /><br />
                                            N\'oubliez pas de remplir les évaluations. Les évaluations permettent de noter les membres de $sitename avec qui vous avez fait affaire et ainsi de permettre un contrôle mutuel basé sur l\'expérience des membres.<br /><br />
                                            Allez voir les <a href=\'http://www.mobil-it.ch/index.php?pn=evaluations_a_faire\'>évaluations</a> que vous devez remplir.<br /><br />"; ?>' .$fond2_fr;

/***************************************************************************/

$mails_fr['article_vendu_acheteur']['sujet'] = '<?php echo "Vous avez acheté l\'article no $article->id sur $sitename"; ?>';
$mails_fr['article_vendu_acheteur']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
              Vous avez remporté les enchères pour l\'article <span style=\'font-weight:bold\'>$article->title</span> sur le site $sitename.<br /><br />
              <table><tr><td colspan=\'2\'>
                Voici les détails de la vente :
                <br /><br />
              </td></tr><tr><td style=\'width:150px\'>
                - No de l\'article :
              </td><td>
                <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->id</a>
              </td></tr><tr><td>
                - Titre de l\'article :
              </td><td>
                <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->title</a>
              </td></tr><tr><td>";
              
              // achat via enchères
              if ($article->sell_state == 2){
                echo "  - Prix de l\'achat :
                      </td><td>"
                        .number_format($article->get_max_bid()->price,2,\'.\',"")
                      ."</td></tr><tr><td style=\'vertical-align:top\'>";
              }
              // achat direct
              elseif ($article->sell_state == 3){
                echo "  - Prix de l\'achat : 
                      </td><td>"
                        .number_format($article->direct_price,2,\'.\',"")
                      ."</td></tr><tr><td style=\'vertical-align:top\'>";
              }
                
              echo "  - Frais et conditions de livraison : 
                    </td><td>
                      Frais de port : " .number_format($article->shipping_price,2,\'.\',"\'") ." CHF<br /><br />";
                        
                      if ($article->shipping_type == 0)
                        echo $article->shipping_text;
                      else
                        echo $article_shipping[$article->shipping_type];         
                        
              echo "</td></tr><tr><td style=\'vertical-align:top\'>
                      - Informations sur le paiement :
                    </td><td>";
                                            
                    if ($article->payement_condition != 0)
                      echo $article_payement[$article->payement_condition];
                    else
                      echo $article->payement_text;
                    
                    echo "<br /><br />Méthodes de paiement : <br />";
                     
                    $methodes = explode(",", $article->payement_method);
                    foreach($methodes as $methode){
                      echo $article_payement_method[$methode] ."<br />";
                    }
               
               echo "</td></tr><tr><td colspan=\'2\'>
                      <br /><br /><br /><br />
                      Si le vendeur ne vous contacte pas dans les prochains jours, envoyez-lui un e-mail ou appelez-le afin de discuter avec lui de la suite des opérations :
                      <br /><br />
                    </td></tr><tr><td>
                      - Nom d\'utilisateur :
                    </td><td>
                      $account2->username
                    </td></tr><tr><td>
                      - Prénom et nom :
                    </td><td>
                      $person2->firstname $person2->lastname
                    </td></tr><tr><td>
                      - Adresse :
                    </td><td>
                      $person2->address
                    </td></tr><tr><td>
                      - Code postal / localité :
                    </td><td>
                      $person2->npa $person2->city
                    </td></tr><tr><td>
                      - Téléphone 1 :
                    </td><td>
                      $person2->tel1
                    </td></tr><tr><td>
                      - Téléphone 2 :
                    </td><td>
                      $person2->tel2
                    </td></tr><tr><td>
                      - E-mail :
                    </td><td>
                      $person2->email1
                    </td></tr></table>
                    <br /><br />
                    N\'oubliez pas de remplir les évaluations. Les évaluations permettent de noter les membres de $sitename avec qui vous avez fait affaire et ainsi de permettre un contrôle mutuel basé sur l\'expérience des membres.<br /><br />
                    Allez voir les <a href=\'http://www.mobil-it.ch/index.php?pn=evaluations_a_faire\'>évaluations</a> que vous devez remplir.<br /><br />
                    Toute l\'équipe de $sitename vous remercie de votre fidélité"; ?>' .$fond2_fr;

/***************************************************************************/

$mails_fr['remettre_en_vente']['sujet'] = '<?php echo "Votre article n\'a pas trouvé d\'acheteur sur $sitename"; ?>';
$mails_fr['remettre_en_vente']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
              Votre article <span style=\'font-weight:bold\'>$article->title</span> n\'a pas trouvé d\'acheteur sur le site $sitename.<br /><br />
              <table><tr><td colspan=\'2\'>
                Voici les détails de votre article : 
                <br /><br />
              </td></tr><tr><td>
                - No de l\'article :
              </td><td>
                <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->id</a>
              </td></tr><tr><td>
                - Titre de l\'article :
              </td><td>
                <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$article->id\" title=\"Voir l\'article\">$article->title</a>
              </td></tr></table>
              <br /><br />
              Vous pouvez remettre en vente votre article en cliquant ici : <a href=\"http://www.mobil-it.ch/index.php?pn=article_revendre_rapide&aid=$article->id\" title=\"Remettre en vente mon article\">Remettre en vente mon article</a><br /><br />
              Toute l\'équipe de $sitename vous remercie de votre fidélité"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['poser_question_article']['sujet'] = '<?php echo "Nouvelle $type à propos de l\'article $article->title sur $sitename"; ?>';
$mails_fr['poser_question_article']['texte'] = '<?php echo "Bonjour,<br /><br />
           Une nouvelle $type a été postée à propos de l\'article \'<span style=\'font-weight:bold\'>$article->title</span>\' sur le site $sitename : <br /><br />$question->text<br /><br />
           <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$question->id_article\" title=\"Répondre à la question\">Répondre à la question</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['evaluer_personne']['sujet'] = '<?php echo "Vous avez été évalué sur $sitename"; ?>';
$mails_fr['evaluer_personne']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
           $person2->firstname $person2->lastname vous a évalué sur à propos de l\'article <span style=\'font-weight:bold\'>$article->title</span> sur le site $sitename.<br /><br />
           Votre évaluation : ";
           
           if ($evaluation->score == 1)
             echo "Négatif";
           elseif ($evaluation->score == 2)
             echo "Neutre";
           elseif ($evaluation->score == 3)
             echo "Positif";
           
      echo "<br /><br />
           - Commentaire : " .stripslashes($evaluation->comment) ."<br /><br />
           <a href=\"http://www.mobil-it.ch/index.php?pn=evaluations_recues\" title=\"Voir vos évaluations\">Voir vos évaluations</a><br /><br />
           <a href=\"http://www.mobil-it.ch/index.php?pn=article_voir&aid=$evaluation->id_article\" title=\"Voir l\'article\">Voir l\'article</a><br /><br />
           Toute l\'équipe de $sitename vous remercie de votre fidélité"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_invitation_event']['sujet'] = '<?php echo "Vous avez reçu une invitation de la part de la société $company_from->name sur $sitename"; ?>';
$mails_fr['envoi_invitation_event']['texte'] = '<?php echo "Bonjour,<br /><br />La société $company_from->name vous a envoyé une invitation pour l\'événement <span style=\'font-weight:bold\'>$event->title</span><br /><br />
                                            Pour en savoir plus au sujet de cet événement ou pour vous inscrire, cliquez <a href=\'$site_url/index.php?pn=event_voir_admin&eid=$event->id\'>ici</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_message']['sujet'] = '<?php echo "Vous avez reçu un message de la part de $person_from->firstname $person_from->lastname sur $sitename"; ?>';
$mails_fr['envoi_message']['texte'] = '<?php echo "Bonjour,<br /><br />$person_from->firstname $person_from->lastname vous a envoyé le message suivant :
                                    <br /><br /><span style=\'font-weight:bold\'>$message->subject</span><br /><br />$message->text<br /><br />
                                    Pour répondre à ce message, cliquez sur <a href=\'$site_url/index.php?pn=messagerie_voir_message&mid=$message->id\'>répondre</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_commande_payee_client']['sujet'] = '<?php echo "Confirmation de votre commande n° $order->no_order sur $sitename"; ?>';
$mails_fr['envoi_commande_payee_client']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,
                 <br /><br />
                 Voici les détails de votre commande passée sur le site $sitename :
                 <br /><br />
        
                 <table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-right:auto;width:19cm;border-collapse:collapse;margin-top:20px\">
                  <tr>
                    <td class=\"titre_panier\" style=\"width:100px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Quantité</td>
                    <td class=\"titre_panier\" style=\"width:600px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Article</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix unitaire</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix total</td>
                  </tr>";
                  
                  // récupert tous les articles de la commande en cours
                  $articles = $order->get_all_articles();
                  
                  if (sizeof($articles) == 0){
                    echo "<tr>
                            <td colspan=\'4\' class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>Aucun article n\'a été commandé</td>
                          </tr>";
                  }
                  else{
                    // parcourt tous les articles de la commande
                    foreach($articles as $a){
                      $article = new Miki_shop_article($a->id_article);
                      
                      // récupert les attributs proposés pour l\'article en cours
                      $attributes_dispo = $article->get_attributes();
                      $attributes_text = "";
                      
                      // recherche les valeurs des attributs entrées par le client
                      if ($a->attributes != ""){
                        $tab = explode("&&", $a->attributes);
                        
                        foreach($tab as $t){
                          $temp = explode("=", $t);
                          $attributes_text .= $attributes_dispo[$temp[0]]["name"] ." : " .$temp[1] ."<br />";
                        }
                      }
                      
                      // Récupert les options si l\'article est un article configurable
                      $options_text = "";
              
                      if ($article->type == 2){
                        $options = $a->get_options();
                        foreach($options as $option){
                          $options_text .= $option->ref ." - " .$option->name[$_SESSION["lang"]] ."<br />";
                        }
                      }
                      
                      // récupert le prix de l\'article en prenant en compte les options éventuelles mais pas les promotions
                      $price = $a->get_price();
                        
                      // pour le calcul tu total HT
                      $total_ht = $order->subtotal + $order->shipping_price;
                        
                      echo "<tr>
                              <td class=\'article_panier\' style=\'text-align:center;border:solid 1px #336699;padding:0 5px 0 5px\'>$a->nb</td>
                              <td class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>" .$article->get_name(\'fr\'); 
                              
                              if (!empty($attributes_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Attributs</div>$attributes_text</div>";
                              if (!empty($options_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Options</div>$options_text</div>";
                              
                        echo "</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($price,2,\'.\',"\'") ." CHF</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($a->nb * $price,2,\'.\',"\'") ." CHF</td>
                            </tr>";
                    }
                    
                    echo "<tr>
                            <td colspan=\'3\' style=\'text-align:right;padding:5px\'>
                              Sous-total :
                              <br /><br />";
                              
                              if ($order->discount > 0)
                                echo "Rabais :<br /><br />";
                                                              
                        echo "Frais de livraison :
                              <br /><br />
                              Montant total de votre commande HT :
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0)
                                  echo "$tax_name (" .$tax_value ."%) :<br /><br />";
                              }
                              
                        echo "<span style=\'font-weight:bold\'>Montant total de votre commande TTC :</span>
                            </td>
                            <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>
                              " .number_format($order->subtotal,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              if ($order->discount > 0){
                                echo number_format($order->discount,2,\'.\',"\'") ." CHF<br /><br />";
                                $total_ht -= $order->discount;
                              }
                              
                        echo  number_format($order->shipping_price,2,\'.\',"\'") ." CHF
                              <br /><br />
                              " .number_format($total_ht,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0){
                                  $tax = round($total_ht * $tax_value / 100, 2);
                                  echo number_format($tax,2,\'.\',"\'") ." CHF<br /><br />";
                                }
                              }
                              
                        echo "<span style=\'font-weight:bold\'>" .number_format($order->price_total,2,\'.\',"\'") ." CHF</span>
                            </td>
                          </tr>";
                  }
                  echo "</table>";
                          
                  ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_commande_payee_shop']['sujet'] = '<?php echo "Commande n° $order->no_order de la part de $person_from->firstname $person_from->lastname sur $sitename"; ?>';
$mails_fr['envoi_commande_payee_shop']['texte'] = '<?php echo "Bonjour, 
                 <br /><br />
                 $person_from->firstname $person_from->lastname a passé une commande sur $sitename.
                 <br /><br />
                 Voici les détails : 
                 <br /><br />
                 
                 <table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-right:auto;width:19cm;border-collapse:collapse;margin-top:20px\">
                  <tr>
                    <td class=\"titre_panier\" style=\"width:100px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Quantité</td>
                    <td class=\"titre_panier\" style=\"width:600px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Article</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix unitaire</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix total</td>
                  </tr>";
                  
                  // récupert tous les articles de la commande en cours
                  $articles = $order->get_all_articles();
                  
                  if (sizeof($articles) == 0){
                    echo "<tr>
                            <td colspan=\'4\' class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>Aucun article n\'a été commandé</td>
                          </tr>";
                  }
                  else{
                  
                    // parcourt tous les articles de la commande
                    foreach($articles as $a){
                      $article = new Miki_shop_article($a->id_article);
                      
                      // récupert les attributs proposés pour l\'article en cours
                      $attributes_dispo = $article->get_attributes();
                      $attributes_text = "";
                      
                      // recherche les valeurs des attributs entrées par le client
                      if ($a->attributes != ""){
                        $tab = explode("&&", $a->attributes);
                        
                        foreach($tab as $t){
                          $temp = explode("=", $t);
                          $attributes_text .= $attributes_dispo[$temp[0]]["name"] ." : " .$temp[1] ."<br />";
                        }
                      }
                      
                      // Récupert les options si l\'article est un article configurable
                      $options_text = "";
              
                      if ($article->type == 2){
                        $options = $a->get_options();
                        foreach($options as $option){
                          $options_text .= $option->ref ." - " .$option->name[$_SESSION["lang"]] ."<br />";
                        }
                      }
                      
                      // récupert le prix de l\'article en prenant en compte les options éventuelles mais pas les promotions
                      $price = $a->get_price();
                        
                      // pour le calcul tu total HT
                      $total_ht = $order->subtotal + $order->shipping_price;
                      
                      echo "<tr>
                              <td class=\'article_panier\' style=\'text-align:center;border:solid 1px #336699;padding:0 5px 0 5px\'>$a->nb</td>
                              <td class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>" .$article->get_name(\'fr\');
                           
                              if (!empty($attributes_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Attributs</div>$attributes_text</div>";
                              if (!empty($options_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Options</div>$options_text</div>";
                           
                        echo "</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($price,2,\'.\',"\'") ." CHF</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($a->nb * $price,2,\'.\',"\'") ." CHF</td>
                            </tr>";
                    }
                    
                    echo "<tr>
                            <td colspan=\'3\' style=\'text-align:right;padding:5px\'>
                              Sous-total :
                              <br /><br />";
                              
                              if ($order->discount > 0)
                                echo "Rabais :<br /><br />";
                                                              
                        echo "Frais de livraison :
                              <br /><br />
                              Montant total de votre commande HT :
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0)
                                  echo "$tax_name (" .$tax_value ."%) :<br /><br />";
                              }
                              
                        echo "<span style=\'font-weight:bold\'>Montant total de votre commande TTC :</span>
                            </td>
                            <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>
                              " .number_format($order->subtotal,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              if ($order->discount > 0){
                                echo number_format($order->discount,2,\'.\',"\'") ." CHF<br /><br />";
                                $total_ht -= $order->discount;
                              }
                              
                        echo  number_format($order->shipping_price,2,\'.\',"\'") ." CHF
                              <br /><br />
                              " .number_format($total_ht,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0){
                                  $tax = round($total_ht * $tax_value / 100, 2);
                                  echo number_format($tax,2,\'.\',"\'") ." CHF<br /><br />";
                                }
                              }
                              
                        echo "<span style=\'font-weight:bold\'>" .number_format($order->price_total,2,\'.\',"\'") ." CHF</span>
                            </td>
                          </tr>";
                  }
                      
                  echo "</table>";
            
                  $date = explode(" ", $order->date_payed);
                  $time = $date[1];
                  $date = explode("-", $date[0]);
                  $date = $date[2] ."/" .$date[1] ."/" .$date[0];
                  $time = explode(":", $time);
                  $time = $time[0] ."h" .$time[1];
            
                  echo "<br /><br /><br />La commande a été payée par carte de crédit (via Paypal) le $date_paiement à $time_paiement.";
                   
                  echo "<table style=\'margin: 30px 0;\'>
                          <tr>
                            <td style=\'padding-right: 20px;\'>
                              <span style=\'text-decoration: underline\'>Adresse de livraison du client : </span><br /><br />
                              $order->shipping_type<br />
                              $order->shipping_firstname $order->shipping_lastname<br />
                              $order->shipping_address<br />
                              $order->shipping_npa $order->shipping_city<br />
                              $order->shipping_country
                            </td>
                            <td style=\'padding-left: 20px;\'>
                              <span style=\'text-decoration: underline\'>Adresse de paiement du client : </span><br /><br />
                              $order->billing_type<br />
                              $order->billing_firstname $order->billing_lastname<br />
                              $order->billing_address<br />
                              $order->billing_npa $order->billing_city<br />
                              $order->billing_country
                            </td>
                          </tr>
                        </table>
                        <div>
                          <span style=\'text-decoration: underline\'>Coordonnées de contact du client : </span><br /><br />
                          $person_from->tel1<br />
                          $person_from->email1<br />
                        </div>";
                                          
                  ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_commande_non_payee_client']['sujet'] = '<?php echo "Confirmation de votre commande n° $order->no_order sur $sitename"; ?>';
$mails_fr['envoi_commande_non_payee_client']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,
                 <br /><br />
                 Voici les détails de votre commande passée sur le site $sitename :
                 <br /><br />
        
                 <table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-right:auto;width:19cm;border-collapse:collapse;margin-top:20px\">
                  <tr>
                    <td class=\"titre_panier\" style=\"width:100px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Quantité</td>
                    <td class=\"titre_panier\" style=\"width:600px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Article</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix unitaire</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix total</td>
                  </tr>";
                  
                  // récupert tous les articles de la commande en cours
                  $articles = $order->get_all_articles();
                  
                  // S\'il n\'y a aucun article dans la commande
                  if (sizeof($articles) == 0){
                    echo "<tr>
                            <td colspan=\'4\' class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>Aucun article n\'a été commandé</td>
                          </tr>";
                  }
                  // S\'il y a des articles dans la commande
                  else{
                    // parcourt tous les articles de la commande
                    foreach($articles as $a){
                      $article = new Miki_shop_article($a->id_article);
                      
                      // récupert les attributs proposés pour l\'article en cours
                      $attributes_dispo = $article->get_attributes();
                      $attributes_text = "";
                      
                      // recherche les valeurs des attributs entrées par le client
                      if ($a->attributes != ""){
                        $tab = explode("&&", $a->attributes);
                        
                        foreach($tab as $t){
                          $temp = explode("=", $t);
                          $attributes_text .= $attributes_dispo[$temp[0]]["name"] ." : " .$temp[1] ."<br />";
                        }
                      }
                      
                      // Récupert les options si l\'article est un article configurable
                      $options_text = "";
              
                      if ($article->type == 2){
                        $options = $a->get_options();
                        foreach($options as $option){
                          $options_text .= $option->ref ." - " .$option->name[$_SESSION["lang"]] ."<br />";
                        }
                      }
                      
                      // récupert le prix de l\'article en prenant en compte les options éventuelles mais pas les promotions
                      $price = $a->get_price();
                        
                      // pour le calcul du total HT
                      $total_ht = $order->subtotal + $order->shipping_price;
                        
                      echo "<tr>
                              <td class=\'article_panier\' style=\'text-align:center;border:solid 1px #336699;padding:0 5px 0 5px\'>$a->nb</td>
                              <td class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>" .$article->get_name(\'fr\');
                           
                              if (!empty($attributes_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Attributs</div>$attributes_text</div>";
                              if (!empty($options_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Options</div>$options_text</div>";
                           
                        echo "</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($price,2,\'.\',"\'") ." CHF</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($a->nb * $price,2,\'.\',"\'") ." CHF</td>
                            </tr>";
                    }
                    
                    echo "<tr>
                            <td colspan=\'3\' style=\'text-align:right;padding:5px\'>
                              Sous-total :
                              <br /><br />";
                              
                              if ($order->discount > 0)
                                echo "Rabais :<br /><br />";
                                                              
                        echo "Frais de livraison :
                              <br /><br />
                              Montant total de votre commande HT :
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0)
                                  echo "$tax_name (" .$tax_value ."%) :<br /><br />";
                              }
                              
                        echo "<span style=\'font-weight:bold\'>Montant total de votre commande TTC :</span>
                            </td>
                            <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>
                              " .number_format($order->subtotal,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              if ($order->discount > 0){
                                echo number_format($order->discount,2,\'.\',"\'") ." CHF<br /><br />";
                                $total_ht -= $order->discount;
                              }
                              
                        echo  number_format($order->shipping_price,2,\'.\',"\'") ." CHF
                              <br /><br />
                              " .number_format($total_ht,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0){
                                  $tax = round($total_ht * $tax_value / 100, 2);
                                  echo number_format($tax,2,\'.\',"\'") ." CHF<br /><br />";
                                }
                              }
                              
                        echo "<span style=\'font-weight:bold\'>" .number_format($order->price_total,2,\'.\',"\'") ." CHF</span>
                            </td>
                          </tr>";
                  }
                  echo "</table>";
                  
                  // S\'il y a des articles dans la commande
                  if (sizeof($articles) > 0){
                  
                    // paiement par facture (paiement avant envoi)
                    if ($order->payement_type == \'facture_avant\'){
                      echo "<br /><br /><br />
                            Vous aller recevoir dans les prochains jours par courrier postal une facture que vous devrez régler afin de finaliser votre commande.<br />
                            Votre commande vous sera envoyée une fois le paiement reçu.";
                    }
                    // paiement par facture (paiement avant envoi)
                    elseif ($order->payement_type == \'facture_apres\'){
                      echo "<br /><br /><br />
                            Votre commande vous sera livrée tout prochainement et sera accompagnée de la facture que nous vous remercions de bien vouloir régler dans les 30 jours.";
                    }
                    // paiement par virement bancaire (paiement avant envoi)
                    elseif ($order->payement_type == \'bank\'){
                      echo "<br /><br /><br />
                            Vous avez choisi de régler la facture au moyen d\'un virement bancaire.<br />
                            Merci de bien vouloir régler le montant de " .number_format($order->price_total,2,\'.\',"\'") ." CHF sur le compte suivant : 
                            <br /><br />
                            N° IBAN : " .$compte_bancaire[\'iban\'] ."<br />
                            N° SWIFT/BIC : " .$compte_bancaire[\'bic\'] ."
                            <br /><br />
                            Votre commande vous sera envoyée une fois le paiement reçu.";
                    }
                  }
                          
                  ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_commande_non_payee_shop']['sujet'] = '<?php echo "Commande n° $order->no_order de la part de $person_from->firstname $person_from->lastname sur $sitename"; ?>';
$mails_fr['envoi_commande_non_payee_shop']['texte'] = '<?php echo "Bonjour, 
                   <br /><br />
                   $person_from->firstname $person_from->lastname a passé une commande sur $sitename.
                   <br /><br />
                   Voici les détails : 
                   <br /><br />
                   
                   <table cellspacing=\"0\" cellpadding=\"0\" style=\"margin-right:auto;width:19cm;border-collapse:collapse;margin-top:20px\">
                  <tr>
                    <td class=\"titre_panier\" style=\"width:100px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Quantité</td>
                    <td class=\"titre_panier\" style=\"width:600px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Article</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix unitaire</td>
                    <td class=\"titre_panier\" style=\"width:200px;background-color:#336699;border:solid 1px #336699;color:#ffffff;font-weight:bold;text-align:center;height:10px\">Prix total</td>
                  </tr>";
                  
                  // récupert tous les articles de la commande en cours
                  $articles = $order->get_all_articles();
                  
                  if (sizeof($articles) == 0){
                    echo "<tr>
                            <td colspan=\'4\' class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>Aucun article n\'a été commandé</td>
                          </tr>";
                  }
                  else{
                  
                    // parcourt tous les articles de la commande
                    foreach($articles as $a){
                      $article = new Miki_shop_article($a->id_article);
                      
                      // récupert les attributs proposés pour l\'article en cours
                      $attributes_dispo = $article->get_attributes();
                      $attributes_text = "";
                      
                      // recherche les valeurs des attributs entrées par le client
                      if ($a->attributes != ""){
                        $tab = explode("&&", $a->attributes);
                        
                        foreach($tab as $t){
                          $temp = explode("=", $t);
                          $attributes_text .= $attributes_dispo[$temp[0]]["name"] ." : " .$temp[1] ."<br />";
                        }
                      }
                      
                      // Récupert les options si l\'article est un article configurable
                      $options_text = "";
              
                      if ($article->type == 2){
                        $options = $a->get_options();
                        foreach($options as $option){
                          $options_text .= $option->ref ." - " .$option->name[$_SESSION["lang"]] ."<br />";
                        }
                      }
                      
                      // récupert le prix de l\'article en prenant en compte les options éventuelles mais pas les promotions
                      $price = $a->get_price();
                      
                      // pour le calcul du total HT
                      $total_ht = $order->subtotal + $order->shipping_price;
                        
                      echo "<tr>
                              <td class=\'article_panier\' style=\'text-align:center;border:solid 1px #336699;padding:0 5px 0 5px\'>$a->nb</td>
                              <td class=\'article_panier\' style=\'border:solid 1px #336699;padding:0 5px 0 5px\'>" .$article->get_name(\'fr\');

                              if (!empty($attributes_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Attributs</div>$attributes_text</div>";
                              if (!empty($options_text)) echo "<div style=\'font-size: 0.8em;\'><div style=\'font-weight: bold; margin-top: 5px;\'>Options</div>$options_text</div>";
                              
                        echo "</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($price,2,\'.\',"\'") ." CHF</td>
                              <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>" .number_format($a->nb * $price,2,\'.\',"\'") ." CHF</td>
                            </tr>";
                    }
                    
                    echo "<tr>
                            <td colspan=\'3\' style=\'text-align:right;padding:5px\'>
                              Sous-total :
                              <br /><br />";
                              
                              if ($order->discount > 0)
                                echo "Rabais :<br /><br />";
                                                              
                        echo "Frais de livraison :
                              <br /><br />
                              Montant total de votre commande HT :
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0)
                                  echo "$tax_name (" .$tax_value ."%) :<br /><br />";
                              }
                              
                        echo "<span style=\'font-weight:bold\'>Montant total de votre commande TTC :</span>
                            </td>
                            <td class=\'article_panier\' style=\'text-align:right;border:solid 1px #336699;padding:0 5px 0 5px\'>
                              " .number_format($order->subtotal,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              if ($order->discount > 0){
                                echo "- " .number_format($order->discount,2,\'.\',"\'") ." CHF<br /><br />";
                                $total_ht -= $order->discount;
                              }
                              
                        echo  number_format($order->shipping_price,2,\'.\',"\'") ." CHF
                              <br /><br />
                              " .number_format($total_ht,2,\'.\',"\'") ." CHF
                              <br /><br />";
                              
                              foreach($order->taxes as $tax_name => $tax_value){
                                if ($tax_value > 0){
                                  $tax = round($total_ht * $tax_value / 100, 2);
                                  echo number_format($tax,2,\'.\',"\'") ." CHF<br /><br />";
                                }
                              }
                              
                        echo "<span style=\'font-weight:bold\'>" .number_format($order->price_total,2,\'.\',"\'") ." CHF</span>
                            </td>
                          </tr>";
                  }
                      
            echo "</table>
                  <br /><br /><br />Le moyen de paiement choisi par le client est : ";
                  
                  // paiement par facture (paiement avant envoi)
                  if ($order->payement_type == \'facture_avant\'){
                    echo "Sur facture (paiement avant envoi)";
                  }
                  // paiement par facture (paiement avant envoi)
                  elseif ($order->payement_type == \'facture_apres\'){
                    echo "Sur facture (paiement après envoi)";
                  }
                  // paiement par virement bancaire (paiement avant envoi)
                  elseif ($order->payement_type == \'bank\'){
                    echo "Virement bancaire (paiement avant envoi)";
                  }
                  // paiement par Paypal
                  elseif ($order->payement_type == \'paypal\'){
                    echo "Paiement via Paypal (y compris cartes de crédit Visa + Mastercard)";
                  }
                  // paiement par carte de crédit via le système Ogone
                  elseif ($order->payement_type == \'ogone\'){
                    echo "Paiement par carte de crédit";
                  }
                 
            echo "<table style=\'margin: 30px 0;\'>
                    <tr>
                      <td style=\'padding-right: 20px;\'>
                        <span style=\'text-decoration: underline\'>Adresse de livraison du client : </span><br /><br />
                        $order->shipping_type<br />
                        $order->shipping_firstname $order->shipping_lastname<br />
                        $order->shipping_address<br />
                        $order->shipping_npa $order->shipping_city<br />
                        $order->shipping_country
                      </td>
                      <td style=\'padding-left: 20px;\'>
                        <span style=\'text-decoration: underline\'>Adresse de paiement du client : </span><br /><br />
                        $order->billing_type<br />
                        $order->billing_firstname $order->billing_lastname<br />
                        $order->billing_address<br />
                        $order->billing_npa $order->billing_city<br />
                        $order->billing_country
                      </td>
                    </tr>
                  </table>
                  <div>
                    <span style=\'text-decoration: underline\'>Coordonnées de contact du client : </span><br /><br />
                    $person_from->tel1<br />
                    $person_from->email1<br />
                  </div>";
                                        
                 ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['demande_contact']['sujet'] = '<?php echo "Vous avez reçu une demande de contact de la part de la société $company_from->name sur $sitename"; ?>';
$mails_fr['demande_contact']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                     $person_from->firstname $person_from->lastname de la société $company_from->name aimerait vous ajouter à ses contacts.<br /><br />
                                     Il vous a écrit le message suivant :<br />" .stripslashes($message) ."<br /><br />
                                     Pour visualiser le profil de $person_from->firstname $person_from->lastname, cliquez sur le lien suivant : <a href=\'http://www.mobil-it.ch/index.php?pn=view_profile&cid=$person_from->id\'>profil</a><br />
                                     Pour valider la demande de contact cliquez sur le lien suivant : <a href=\'http://www.mobil-it.ch/index.php?pn=contact_ask_list\'>valider</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['demande_contact_confirme']['sujet'] = '<?php echo "Demande de contact confirmée sur $sitename"; ?>';
$mails_fr['demande_contact_confirme']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                              $person_from->firstname $person_from->lastname de la société $company_from->name a accepté votre demande de contact.<br /><br />
                                              Pour voir son profil, cliquez sur le lien suivant : <a href=\'http://www.mobil-it.ch/index.php?pn=view_profile&cid=$person_from->id\'>profil</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_inscription']['sujet'] = '<?php echo "Votre inscription sur $sitename"; ?>';
$mails_fr['envoi_inscription']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname,<br /><br />
                                        Toute l\'équipe de $sitename vous remercie pour votre inscription.<br /><br />
                                        Votre inscription doit maintenant être confirmée par un administrateur de $sitename.<br /><br />
                                        Vous serez dès lors informé par e-mail et vous pourrez vous connecter sur $sitename grâce aux données de connexion suivantes : <br /><br /> 
                                        - Nom d\'utilisateur : $account_to->username<br />
                                        - Mot de passe : $password<br /><br />
                                        Vous pouvez accéder à la page d\'accueil en <a href=\'$site_url\'>cliquant ici</a><br /><br />
                                        Nous espérons que vous prendrez beaucoup de plaisir à utiliser $sitename"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['inscription_a_confirmer']['sujet'] = '<?php echo "Nouvelle demande d\'inscription sur $sitename"; ?>';
$mails_fr['inscription_a_confirmer']['texte'] = '<?php echo "Bonjour,<br /><br />
                                              $person_from->firstname $person_from->lastname s\'est enregistré(e) sur $sitename et est en attente de validation de votre part.<br /><br />
                                              Vous pouvez accéder à la console d\'administration en <a href=\'$site_url/madmin\'>cliquant ici</a>"; ?>' .$fond1_fr;
                                       
/***************************************************************************/

$mails_fr['invitation_site']['sujet'] = '<?php echo "Invitation de la part de $person_from->firstname $person_from->lastname sur $sitename"; ?>';
$mails_fr['invitation_site']['texte'] = '<?php echo "Bonjour $firstname $lastname<br /><br />
                                      $person_from->firstname $person_from->lastname aimerait vous inviter sur $sitename<br /><br />
                                      Voici son message :<br /><br />" .nl2br(stripslashes($message)) ."<br /><br /><br />
                                      Pour accéder au site $sitename, <a href=\'$site_url\'>cliquez ici</a>"; ?>' .$fond1_fr;
                                       
/***************************************************************************/

$mails_fr['inscription_social_group']['sujet'] = '<?php echo "Nouvelle inscription sur votre groupe \'$group->name\'"; ?>';
$mails_fr['inscription_social_group']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname<br /><br />
                                              $person_from->firstname $person_from->lastname de la société $company_from->name s\'est inscrit sur votre groupe \'$group->name\' et attend votre validation.<br /><br />
                                              Pour traiter cette inscription, cliquer <a href=\'$site_url/index.php?pn=social_group_moderer&gid=$group->id\'>ici</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['validation_inscription_social_group']['sujet'] = '<?php echo "Votre inscription au groupe \'$group->name\' a été validée"; ?>';
$mails_fr['validation_inscription_social_group']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname<br /><br />
                                                          Votre inscription au groupe \'$group->name\' a été validée<br /><br />
                                                          Pour vous rendre sur la page de ce groupe, cliquer <a href=\'$site_url/index.php?pn=social_group_voir&gid=$group->id\'>ici</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['invitation_social_group']['sujet'] = '<?php echo "Vous avez reçu une invitation de la part de la société $company_from->name"; ?>';
$mails_fr['invitation_social_group']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname<br /><br />
                                              La société $company_from->name vous a envoyé une invitation pour le groupe <span style=\font-weight:bold\>$group->name</span><br /><br />
                                              Pour en savoir plus au sujet de ce groupe ou pour vous inscrire, cliquez <a href=\'$site_url/index.php?pn=social_group_voir&gid=$group->id\'>ici</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['suivi_social_group']['sujet'] = '<?php echo "Un nouveau message a été posté dans la discussion $group->name sur $sitename"; ?>';
$mails_fr['suivi_social_group']['texte'] = '<?php echo "Bonjour $person_to->firstname $person_to->lastname<br /><br />
                                              Un nouveau message a été posté dans la discussion <span style=\'font-weight:bold\'>$group->name</span> sur $sitename.<br /><br />
                                              Pour voir ce message, cliquez <a href=\'$site_url/index.php?pn=social_group_article_voir&aid=$article->id\'>ici</a><br /><br />
                                              Pour vous désinscrire du suivi de cette discussion, cliquez <a href=\'$site_url/desinscription_social_group.php?gid=$group->id&pid=$person_to->id\'>ici</a>"; ?>' .$fond2_fr;
                                       
/***************************************************************************/

$mails_fr['ajout_social_group']['sujet'] = '<?php echo "Un nouveau groupe a été créé sur $sitename"; ?>';
$mails_fr['ajout_social_group']['texte'] = '<?php echo "Bonjour,<br /><br />
                                                        Un nouveau groupe a été créé sur $sitename : <a href=\'$groupe_url\' title=\'Voir le groupe\'>$groupe_name</a>
                                                        <br /><br />
                                                        Vous pouvez accéder à ce groupe en <a href=\'$groupe_url\'>cliquant ici</a><br /><br />"; ?>' .$fond1_fr;
                                       
/***************************************************************************/

$mails_fr['envoi_object_booking']['sujet'] = '<?php echo "Une nouvelle réservation a été effectuée sur $sitename pour l\'objet $object_title"; ?>';
$mails_fr['envoi_object_booking']['texte'] = '<?php echo "Bonjour,<br /><br />
                                                        Une nouvelle réservation a été effectuée sur $sitename pour l\'objet $object_title
                                                        <br /><br />
                                                        Voici les détails de la réservation : 
                                                        <br /><br />
                                                        Depuis le : $date_start<br />
                                                        Jusqu\'au : $date_stop<br />
                                                        Nom : $lastname<br />
                                                        Prénom : $firstname<br />
                                                        Téléphone : $tel<br />
                                                        Adresse e-mail : $email
                                                        <br /><br />"; ?>' .$fond1_fr;
                                       
/***************************************************************************/

$mails_fr['event_subscription']['sujet'] = '<?php echo "\'$event_name\' sur $sitename - $person->firstname $person->lastname s\'est inscrit"; ?>';
$mails_fr['event_subscription']['texte'] = '<?php echo "Bonjour,<br /><br />
                                                        Une nouvelle inscription a été effectuée pour votre événement \'$event_name\'sur $sitename.
                                                        <br /><br />
                                                        Voici les détails de l\'inscription: 
                                                        <br /><br />
                                                        Etat : $subscription_state<br /><br />
                                                        Nom : $person->firstname<br />
                                                        Prénom : $person->lastname<br />
                                                        Adresse : $person->address<br />
                                                        Code postal, localité : $person->npa $person->city<br />
                                                        Pays : $person->country<br />
                                                        Téléphone : $person->tel1<br />
                                                        Adresse e-mail : $person->email1
                                                        <br /><br />"; ?>' .$fond1_fr;
                                       
/***************************************************************************/


$GLOBALS['mails_fr'] = $mails_fr;                           
?>