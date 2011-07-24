<?php

$plugin = new Plugins;

class Plugins {
	var $hooked = array();

	// Let plugins hook to different areas of the script
	function addHook($hookName, $functionName){
		$this->hooked[$hookName][$functionName] = $functionName;
		//echo $hooked[$hookname];
	}
	
	function runHook($hookName, $content){
		if (count($this->hooked) != 0 && array_key_exists($hookName, $this->hooked) == true){
			foreach ($this->hooked[$hookName] as $key => $value){
				if (file_exists("plugins/".$value.".php") == true){
					require_once("plugins/".$value.".php");
					
					eval("\$output = ".$value."(\$content);");
					if ($output != ""){
						$content = $output;
					}
				}
			}
		}
		return $content;
	}
	
	function loadPlugins(){
		$f = glob("plugins/*.php");
		foreach($f as $a){
			require_once($a);
		}
	}
}

?>