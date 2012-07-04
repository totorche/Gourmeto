<?php
require_once("config.php");

system("mysqldump --host=$bdd_host --user=$bdd_user --password=$bdd_password $bdd_bdd > $bdd_bdd.sql");
system("gzip $bdd_bdd.sql");
?>