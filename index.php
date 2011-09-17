<?php
session_start();

/**
 * Defines for all scripts that we are executing from within iAuth
 */
define("IN_SCRIPT", 1);


/**
 * Defines the current version of iAuth
 */
define("VERSION", "1.02");

/**
 *	Load includes/init.php to inilize all the main classes.
 */
require_once("includes/init.php"); 

// Connect to mysql database and template files
include("includes/header.php");
include("includes/menu.php");

/**
 * Run the underMenu test hook.
 */
$plugin->runHook("underMenu", "");

/**
 * If the users hasen't done anything for an hour log them out.
 */
if (isset($_SESSION['lastActive'])==true && isset($_SESSION['user']) == true){
	if ((time() + 3600) <= $_SESSION['lastActive']){
		unset($_SESSION['user']);
		unset($_SESSION['lastActive']);
	}else{
		$_SESSION['lastActive'] = time();
	}
}

/**
 * Decide what page to load depending if they are logged in.
 */
if (isset($_SESSION['user'])==false ){
	include("pages/login.php");
}else{
	include("pages/main.php");
}

/**
 * Include the footer template.
 */
include("includes/footer.php");

?>