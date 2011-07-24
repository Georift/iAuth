<?php
// iAuth "auth.php"
// Holds vital auth classes.
// Must include mysql_connect before running.

class auth {
	
	// Will return true if the hash is valid.
	function checkHash($hash){
		$getHash = mysql_query("SELECT * FROM app_sessions WHERE hash = '".mysql_real_escape_string($hash)."'");
		if (mysql_num_rows($getHash) >= 1){
			return true;	
		}
		return false;
	}
	
	function getNews($lid){
		if ($lid == ""){ return false; }
		$userInfo = $this->loadUserData($lid);
		
		$query = "SELECT * FROM news WHERE aid = '".$userInfo['aid']."' ORDER BY time DESC LIMIT 1";
		$getNews = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($getNews) == 0){
			echo "No News";	
		}else{
			$newsInfo = mysql_fetch_assoc($getNews);
			echo $newsInfo['content'];
		}
	}
	
	function loadUserData($lid){
		if ($lid == ""){ return false; }
		
		$query = "SELECT * FROM licences WHERE id = '".$lid."'";
		$getData = mysql_query($query);
		
		if (mysql_num_rows($getData) >= 1){
			return mysql_fetch_assoc($getData);	
		}else{
			return false;	
		}
		
	}
	
	function validLogin($user, $pass, $hwid){
		if ($user == "" || $pass == "" || $hwid == ""){ return false; }
		
		$query = "SELECT * FROM licences WHERE user = '".$user."' AND pass = '".$pass."' AND hwid = '".$hwid."'";
		$getData = mysql_query($query);
		
		if (mysql_num_rows($getData) >= 1){
			$userData = mysql_fetch_assoc($getData);
			return $userData;
		}else{
			return false;
		}
	}
	
	// Check the current version of an application
	// and return the actual version.
	function checkVer($ver, $lid){
		$ver = mysql_real_escape_string($ver);
		$lid = mysql_real_escape_string($lid);
		if ($ver == "" || $lid == ""){
			return "Missing version information or application information.";	
			exit();
		}
		// The variables have been escaped process them
		$get_ver = mysql_query("SELECT version FROM applications WHERE id = '".$lid."'") or die(mysql_error());
		list($newVer) = mysql_fetch_row($get_ver);
		if ($ver < $newVer){
			echo "An update is ready for download.\nLatest Version: {$newVer}\nCurrent Version: {$ver}";	
		}else{
			echo "You are upto date.";	
		}
	}
	
	function checkBan($ip){
		$query = "SELECT * FROM bans WHERE ip = '".$ip."' AND expires >= '".time()."' AND exception = '0'";
		if (mysql_num_rows(mysql_query($query)) >= 1){
			return true;	
		}else{
			return false;	
		}
	}
	
	// Generate a 255 long string that will
	// act as our security string.
	function generateHash(){
		$key = "";
		$charset = "abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		for ($i = 1; $i<=50; $i++){
			$rand_char = mb_substr($charset, rand() % strlen($charset), 1);
			$key .= $rand_char;
		}
		return $key;
	}
	
	
	// Add the new session to the database.
	function createSession($hash, $lid){
		// Give the account one hour before it auto logs out.
		$expires_time = time() + 3600;
		// Check if the user has a previous session running on their licence.
		$get_active = mysql_query("SELECT * FROM app_sessions WHERE lid = '".mysql_real_escape_string($lid)."'");
		if (mysql_num_rows($get_active) >= 1){
			// The licence is currently active.
			// Log the other one out.
			mysql_query("DELETE * FROM app_sessions WHERE lid = '".mysql_real_escape_string($lid)."'");
		}
		// All of the previous sessions have been deleted.
		// Update to the new IP address
		mysql_query("INSERT INTO app_sessions(ip, hash, lid, expires) VALUES('".$_SERVER['REMOTE_ADDR']."', '".$hash."', '".$lid."', '".$expires_time."')");
		$this->logAccess($lid);
		return true;
	}
	
	// Log the access to the program
	// The verification of last log is in here
	// Make sure the lid input is secure.
	function logAccess($lid){
		if ($lid == ""){ exit(); }
		
		$get_aid = mysql_query("SELECT aid FROM licences WHERE id = '".$lid."'") or die(mysql_error());
		list($aid) = mysql_fetch_row($get_aid);

		$get_last = mysql_query("SELECT * FROM access_log WHERE lid = '".$lid."' AND time >= '".(time() - 3600)."'");
		if (mysql_num_rows($get_last) == 0){
			mysql_query("INSERT INTO access_log(ip, time, aid, lid) VALUES('".$_SERVER['REMOTE_ADDR']."', '".time()."', '".$aid."', '".$lid."')");
			return true;
		}else{
			return false;	
		}
	}

	function sessionExists($ip){
		if ($ip == ""){ return false; }
		$ip = mysql_real_escape_string($ip);

		$query = "SELECT * FROM app_sessions WHERE ip = '".$ip."' AND expires >= '".time()."'";
		$getSession = mysql_query($query);
		if (mysql_num_rows($getSession) >= 1){
			return true;
		}
		return false;
	}
	
	function getLicenceInfo($knownInfo){
		//if (is_array($knownIno) == false){ return false; }
		
		$query = "SELECT * FROM licences WHERE ";
		$loop = 1;
		foreach($knownInfo as $key => $value){
			$query .= mysql_real_escape_string($key)." = '".mysql_real_escape_string($value)."' ";
			if ($loop != count($knownInfo)){
				$query .= "AND ";	
			}
			$loop++;
		}
		
		$getInfo = mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($getInfo) == 0){
			return false;	
		}else{
			return mysql_fetch_assoc($getInfo);
		}
	}
	
}

?>