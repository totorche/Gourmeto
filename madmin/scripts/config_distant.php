<?php

// MySQL configuration
$bdd_host     = "mysql5-11";
$bdd_user     = "fbwoneadju";
$bdd_password = "adju16";
$bdd_bdd      = "fbwoneadju";

// connect to MySQL
$conn = mysql_connect($bdd_host, $bdd_user, $bdd_password) or die("Cannot connect to the database : " . mysql_error());
if (!$conn) {
	exit;
}

if (!mysql_select_db($bdd_bdd)) {
	echo "0;Cannot select the database '$bdd_bdd' : " . mysql_error();
	exit;
}

mysql_query("SET NAMES UTF8");

?>