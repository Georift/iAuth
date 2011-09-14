<?php
/**
 *	Loads settings from the admin panel for scripts to use.
 */

if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}

class settings {
	function loadValue($code){
		$run = mysql_query("SELECT value FROM settingsitems WHERE code = '".mysql_real_escape_string($code)."'");
		if (mysql_num_rows($run) == 0){
			return false;
		}else{
			$array = mysql_fetch_row($run);
			return $array[0];
		}
	}
}
?>