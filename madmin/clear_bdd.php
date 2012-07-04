<?php
  if (isset($_COOKIE['clear_bdd']) && $_COOKIE['clear_bdd'] == 1){
    setcookie("clear_bdd", false, time() - 3600); 
    unset($_COOKIE["clear_bdd"]);
    
    $delete = true;
  }
  else{
    $delete = false;
  }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">

<head>
  <meta http-equiv="content-Type" content="text/html; charset=utf-8" />
  
  <script type="text/javascript">

    function clear_bdd(){
      if (confirm('Êtes-vous vraiment sûr de vouloir vider complètement cette base de données ?')){
        document.cookie = 'clear_bdd=1';
        document.location.reload();
      }
    }
  
  </script>
     
</head>
<body>

<?php

  if ($delete){
    
    include("../scripts/config.php");
    
    // suppression des liaisons
    $sql = sprintf("SELECT CONCAT('ALTER TABLE ', TABLE_SCHEMA, '.', TABLE_NAME, ' DROP FOREIGN KEY ', CONSTRAINT_NAME, ';') as req
                    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                    WHERE TABLE_SCHEMA = '%s' AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
    mysql_real_escape_string($bdd_bdd));
  
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql : <br />$sql");
      
    if (mysql_num_rows($result) == 0) 
      exit("Il n'y a aucune table à supprimer");
      
    while ($row = mysql_fetch_array($result)){
      $sql2 = $row['req'];
      $result2 = mysql_query($sql2) or die("Une erreur est survenue dans la requête sql : $sql2<br />");
    }
    
    
    
    // suppression des tables
    $sql = sprintf("SELECT CONCAT('DROP TABLE ', TABLE_SCHEMA, '.', TABLE_NAME, ';') as req
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_SCHEMA = '%s'",
    mysql_real_escape_string($bdd_bdd));
  
    $result = mysql_query($sql) or die("Une erreur est survenue dans la requête sql");
      
    if (mysql_num_rows($result) == 0) 
      exit("Il n'y a aucune table à supprimer");
      
    while ($row = mysql_fetch_array($result)){
      $sql2 = $row['req'];
      $result2 = mysql_query($sql2) or die("Une erreur est survenue dans la requête sql : $sql2<br />");
    }
  
  }
  else{
    echo "<input type='button' value='Vider complètement la base de données' onclick='javascript:clear_bdd();' />";
  }
?>

</body>
</html>