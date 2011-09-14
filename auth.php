<?php
define("IN_SCRIPT", 1);
// Validate if the user is real and if not ban their IP
require_once("includes/init.php");
/*
$user = mysql_real_escape_string($_GET['user']);
$pass = md5($_GET['pass']);
$hwid = md5($_GET['hwid']);
$lid = $_GET['aid'];
*/

$user = $_GET['user'];
$pass = md5($_GET['pass']);
$hwid = md5($_GET['hwid']);
$lid = $_GET['aid'];

// Ban checking
// Check make sure the user has no previous bans.
// If they do alert them of it.
if ($bans->isBanned($_SERVER['REMOTE_ADDR'])){
	die("Your access has been restricted. This ban only lasts <b>".$settings->loadValue("BANTIME")."</b> minutes.");	
}

switch($_GET['a']){
	case "verCheck":
		echo $auth->checkVer($_GET['ver'], $_GET['aid']);	
		break;
	case "login":
		if ($user == "" || $pass == ""){
			echo "Failed";	
		}else{
			if ($auth->sessionExists($_SERVER['REMOTE_ADDR']) == true){
				echo "A session for your ip already exists.";	
			}else{
				$userData = $auth->validLogin($user, $pass, $hwid);
				if (is_array($userData) == false){
					print_r($userData);
					echo "FAILED";
					$bans->addStrike($_SERVER['REMOTE_ADDR']);	
				}else{
					if ($userData['active'] == "1"){
						if ($userData['hwid'] == $hwid){
							if ($userData['expires'] <= time()){
								echo "ERROR: Your account has expired.";
								$bans->addStrike($_SERVER['REMOTE_ADDR']);
							}else{
								$auth->logAccess($userData['id']);
								$key = $auth->generateHash();
								$auth->createSession($key, $userData['id']);
								echo $key;
							}
						}else{
							echo "ERROR: HWID Invalid. Only try logging in on the computer you activated the serial.";
							$bans->addStrike($_SERVER['REMOTE_ADDR']);
						}
					}else{
						echo "ERROR: Your account is suspended.";
					}
				}
			}
		}
		break;
	case "appNews":
		if ($auth->checkHash($_GET['hash']) == false){
			echo "Invalid Session Hash.";
		}else{
			$getId = mysql_query("SELECT * FROM app_sessions WHERE hash = '".mysql_real_escape_string($_GET['hash'])."'");
			$hashInfo = mysql_fetch_assoc($getId);
			echo $auth->getNews($hashInfo['lid']);	
		}
		break;
	case "activateSerial":
		$serial = mysql_real_escape_string($_GET['serial']);
		$information = array("serial" => $_GET['serial']);
		
		$userInfo = $auth->getLicenceInfo($information);
		
		if ($userInfo == false){
			echo "ERROR: Serial is Invalid.";
			$bans->addStrike($_SERVER['REMOTE_ADDR']);
		}else{	
			if ($userInfo['active'] == "1"){
				if ($userInfo['user'] == "" && $userInfo['pass'] == ""){
					// Serial has not been activated.
					if ($_GET['user'] == "" || $_GET['pass'] == "" || $_GET['hwid'] == ""){
						echo "ERROR: Missing Username/Password/HWID.";
					}else{
						$user = mysql_real_escape_string($_GET['user']);
						$pass = md5($_GET['pass']);
						$hwid = md5($_GET['hwid']);
						$serial = mysql_real_escape_string($_GET['serial']);

						$getName = mysql_query("SELECT * FROM licences WHERE user = '{$user}'");
						
						if (mysql_num_rows($getName) == 0){
							mysql_query("UPDATE licences SET user = '{$user}', pass = '{$pass}', hwid = '{$hwid}' WHERE serial = '{$serial}'") or die(mysql_error());
							echo "SUCCESS: Serial has be activated with your details. You can now login.";
						}else{
							echo "ERROR: Username Taken.";
						}
					}
				}else{
					echo "ERROR: Serial has already been claimed.";
					$bans->addStrike($_SERVER['REMOTE_ADDR']);
				}	
			}else{
				echo "ERROR: Serial is not active.";
				$bans->addStrike($_SERVER['REMOTE_ADDR']);
			}
		}
		
		break;
		case "loadUserData":
			$hash = $_GET['hash'];
			// find the lid
			$run = $db->select("app_sessions", "lid", array("hash" => $hash));
			
			if ($db->numRows($run) == 0){
				echo "Failed.";
			}else{
				$array = $db->fetchRow($run);
				$run = $db->select("licences", "*", array("id" => $array[0]));
				
				print_r($db->fetchAssoc($run));
			}
		break;
}
?>