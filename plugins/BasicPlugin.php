<?php
// Basic plugin example.
if (IN_SCRIPT != 1)
	die("This script must not be accessed directly.");


// On first run init all hooks
$enabled = true;

if ($enabled == true){
	$plugin->addHook("miscHook", "BasicPlugin");
}

function BasicPlugin($sideBar) {
	if ($_GET['a'] == "loadList"){
		return "Content.";	
	}
}
?>