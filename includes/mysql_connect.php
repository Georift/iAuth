<?php

$DATABASE = ""; // Database where all the tables are stored.
$HOST = ""; // Usually "localhost"
$USER = ""; // Username Assigned to your database.
$PASS = ""; // Username's password

mysql_connect($HOST, $USER, $PASS);
mysql_select_db($DATABASE);

?>