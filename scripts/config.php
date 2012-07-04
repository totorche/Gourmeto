<?php

// MySQL configuration
$bdd_host     = "localhost";
$bdd_user     = "root";
$bdd_password = "";
$bdd_bdd      = "picshare";

// connect to MySQL
$conn = mysql_connect($bdd_host, $bdd_user, $bdd_password) or die("Cannot connect to the database : " . mysql_error());
if (!$conn) {
	exit("Impossible de se connecter à la base de données");
}

if (!mysql_select_db($bdd_bdd)) {
	exit("Cannot select the database '$bdd_bdd' : " . mysql_error());
}

mysql_query("SET NAMES UTF8");

?>