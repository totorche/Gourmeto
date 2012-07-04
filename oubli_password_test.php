<?php
  if (!isset($_POST['email'])){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Il manque des informations") ."</div>
          </div>";
    exit();
  }
  
  require_once("include/headers.php");
  
  $email = $_POST['email'];
  
  $sql = sprintf("SELECT id FROM miki_account WHERE username = '%s'",
    mysql_real_escape_string($email));
  $result = mysql_query($sql);
  
  if (mysql_num_rows($result) == 0){
    echo "<div>
            <div id='result'>0</div>
            <div id='msg'>" ._("Votre adresse e-mail n'a pas été trouvée") ."</div>
          </div>";
    exit();
  }
  else{
    $row = mysql_fetch_array($result);
    $account_id = $row[0];
    $account = new Miki_account($account_id);
    
    try{
      $account->send_password();
    }catch(Exception $e){
      echo "<div>
              <div id='result'>0</div>
              <div id='msg'>" ._("Une erreur est survenue lors de l'envoi de votre mot de passe") ."<br />" .$e->getMessage() ."</div>
            </div>";
      exit();
    }
  
    echo "<div>
            <div id='result'>1</div>
            <div id='msg'>" ._("Un nouveau mot de passe a été envoyé à l'adresse <span style='font-weight: bold;'>$email</span>") ."</div>
          </div>";
    exit();
  }
?>