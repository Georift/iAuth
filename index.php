<?php
session_start();
define("IN_SCRIPT", 1);
define("VERSION", "1.01");

// Keeps the user from screwing up.
if (file_exists("install/LOCK") == false){
	die("Please create a LOCK file in the install folder.");	
}
if (file_exists("includes/config.php") == false){
	die("Please run the install script.");	
}

// Init the Plugin class.
require_once("includes/plugins.php");

//$plugin->loadPlugins();
// Loop through the plugin directory and load all binds.
$f = glob("plugins/*.php");
foreach($f as $a){
	require_once($a);
}

// Connect to mysql database and template files.
include("includes/mysql_connect.php");
include("includes/header.php");
include("includes/menu.php");
//$plugin->addHook("underMenu", "BasicPlugin");

$plugin->runHook("underMenu", "");

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