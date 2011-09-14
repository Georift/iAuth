<?php
/**
 *	Loads all the important classes for iAuth to run.
 */

/**
 *	Stops remote loading of this script.
 */
if (!defined("IN_SCRIPT")){
	die("This script cannot be accessed directly.");
}

/**
 *	Find the root directory and save it to ROOT
 */
define("ROOT", dirname(dirname(__FILE__)) . "/");

/**
 *	Check if the LOCK file exists in the install folder to prevent
 *	unauthorized use.
 */
if (file_exists(ROOT . "install/LOCK") == false){
	die("Please create a LOCK file in the install folder.");	
}

/**
 *	Checks if iAuth has already been installed.
 */
if (file_exists(ROOT . "includes/config.php") == false){
	die("Please run the install script.");	
} 

/**
 *	Loads MySQL database.
 */
require_once(ROOT . "includes/mysql_connect.php");

/**
 *	Loads testing database class.
 */
require_once(ROOT . "includes/database.class.php");
$db = new MySQL;

/**
 * Include the plugin class.
 */
require_once(ROOT . "includes/plugins.php");
$plugins = Plugins;

/**
 * Include and run the settings core class
 */
require_once(ROOT . "includes/settings.class.php");
$settings = new settings;

/**
 *	Loop through all settings and define them.
 *	Now we can access the settings anywhere we have a init.php included.
 */
$run = $db->select("settingsitems", "*");
while($row = $db->fetchAssoc($run)){
	define($row['code'], $row['value']);
}

/**
 * Include and run the bans api.
 */
require_once(ROOT . "includes/bans.php");
$bans = new Bans;

/**
 *	Includes the auth class.
 */
require_once(ROOT . "includes/auth.php");
$auth = new auth;

/**
 * Run all plugins that have hooked in.
 */
$f = glob(ROOT . "plugins/*.php");
foreach($f as $a){
	require_once($a);
}

/**
 *	Generate star ratings
 */
function startRating($int){
	if(is_int($int) == false)
		return false;
		
	if ($int >= 6)
		return false;
		
	$good = $int;
	$bad = 5 - $int;
	
	$text = "";
	
	for ($i = 1; $i <= $good; $i++)
		$text .= "<img src=\"images\star.png\" />";
		
	for ($i = 1; $i <= $bad; $i++)
		$text .= "<img src=\"images\stardark.png\" />";
	
	return $text;
}

/**
 *	Generate Time ago
 */
function timeAgo($d){
	if ($d <= 59){
		echo $d." seconds ago.";
	}elseif($d <= 3600){
		echo floor($d / 60)." minutes ago.";
	}elseif($d <= 86399){
		echo floor(($d / 60) / 60)." hours ago.";
	}elseif ($d <= 620082){
		echo floor((($d / 60) / 60) / 60)." days ago.";
	}else{
		echo date("h:i:s d/m/Y");
	}
}


 
?>