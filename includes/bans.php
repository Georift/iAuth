<?php

class Bans{
	
	function isBanned($ip){
		if ($ip == ""){ return false; }	
		if (BANENABLED != "on"){ return false; }
		
		$query = "SELECT * FROM bans WHERE ip = '".mysql_real_escape_string($ip)."' AND exception = '0' AND expires >= '".time()."'";
		$getBan = mysql_query($query) or die(mysql_error());
		
		if (mysql_num_rows($getBan) >= 1){
			return true;
		}else{
			return false;
		}
	}

	function addStrike($ip){
		if ($ip == ""){ return false; }
		// Check that bans are enabled.
		if (BANENABLED != "on"){ return false; }
		
		$ip = mysql_real_escape_string($ip);
		
		if ($this->isBanned($ip) == true){
			return false;	
		}
		
		$time = time() - (BANTIME * 60);
		$query = "SELECT * FROM fail_log WHERE time >= '".$time."' AND counted = '0' AND ip = '".$ip."'";
		$getCount = mysql_query($query);
		if (mysql_num_rows($getCount) >= 3){
			mysql_query("INSERT INTO bans(ip,time,expires) VALUES('".$ip."', '".time()."', '".(time() + (BANTIME * 60))."')");
			mysql_query("UPDATE fail_log SET counted = '1' WHERE time >= '".$time."' AND counted = '0' AND ip = '".$ip."'");
			return true;	
		}else{
				$query = "INSERT INTO fail_log(time,ip) VALUES('".time()."','".$ip."')";
				mysql_query($query);
				return false;
		}
	}
	
	function addBan($ip, $time = 0){
		if ($ip == ""){ return false; }
		if (BANENABLED != "on"){ return false; }
		
		mysql_query("INSERT INTO bans(ip,time,expires) VALUES('".mysql_real_escape_string($ip)."', '".time()."', '".(time() + (BANTIME * 60))."')");
		return true;
	}
	
	function liftBan($ip){
		if ($ip == ""){ return false; }
		if (BANENABLED != "on"){ return false; }
		
		mysql_query("UPDATE bans SET exception = '1' WHERE ip = '".mysql_real_escape_string($ip)."'");
		return true;	
	}
}

?>