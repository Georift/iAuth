<?php
session_start();
define("IN_SCRIPT", 1);
define("baseurl", "http://localhost/hwid/");
include("includes/mysql_connect.php");
include("includes/header.php");
include("includes/menu.php");

// Check make sure the last activity was less than an hour ago.
if (isset($_SESSION['lastActive'])==true && isset($_SESSION['user']) == true){
	if ((time() + 3600) <= $_SESSION['lastActive']){
		unset($_SESSION['user']);
		unset($_SESSION['lastActive']);
	}else{
		$_SESSION['lastActive'] = time();
	}
}

if (isset($_SESSION['user'])==false ){
	include("pages/login.php");
}else{
	include("pages/main.php");
}

include("includes/footer.php");

?>