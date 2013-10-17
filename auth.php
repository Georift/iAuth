<?php
error_reporting(E_ERROR);
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
		// now that you can have both username/password/hwid or simply hwid, take that into account.
		
		if ($_GET['hwid'] != ""){
			// they are giving us a HWID, check if we actually need a to look for login info.
			$licenseData = mysql_query("SELECT * FROM licences WHERE hwid = '".$hwid."'");
			if (mysql_num_rows($licenseData) == 0){
				// error with getting it. invalid maybe?
				echo "ERROR: Invalid HWID.";
				$bans->addStrike($_SERVER['REMOTE_ADDR']);
			}else{
				$licenseInfo = mysql_fetch_assoc($licenseData);
				$appID = $licenseInfo['aid'];
				
				$checkApp = mysql_query("SELECT * FROM applications WHERE id = '".$appID."'");
				if (mysql_num_rows($checkApp) == 0){
					// missing application?
					echo "ERROR: Missing application information.";
				}else{
						
					if ($auth->sessionExists($_SERVER['REMOTE_ADDR']) == true){
						// destroy the previous hash.
						mysql_query("UPDATE app_sessions WHERE ip = '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."' SET expires = '".(time() - 10)."'");
					}
						
					// load the app info and check if we need to use login or not.
					$appInfo = mysql_fetch_assoc($checkApp);
					if ($appInfo['login'] == "1"){
						// we require login information.
						if ($user == "" || $pass == ""){
							echo "ERROR: Missing login information.";	
						}else{
							// procceed.	
							$userData = $auth->validLogin($user, $pass, $hwid);
							if (is_array($userData) == false){
								//print_r($auth->validLogin("Geo", "Password", ""));
								echo "ERROR: Incorrect Login Information.";
								$bans->addStrike($_SERVER['REMOTE_ADDR']);	
							}else{
								
								if ($userData['active'] == "1"){
									if ($userData['hwid'] == $hwid){
										if ($userData['expires'] <= time() AND $userData['expires'] != 0){
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
		
					}else{
						// we do not require login information.
						if ($licenseInfo['active'] == "1"){
							// it's active, check it's expiration.
							if ($licenseInfo['expires'] <= time()){
								// license has expired.
								echo "ERROR: License has expired.";
							}else{
								$licenseID = $licenseInfo['id'];
								$accessHash = $auth->generateHash();
								
								$auth->createSession($accessHash, $licenseID);
								echo $accessHash;
							}
						}else{
							echo "ERROR: License is suspended.";
						}
					}
				}
			}
		}else{
			echo "ERROR: Missing HWID";
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
				
				// check if the application requires a login from the user.
				$app = mysql_query("SELECT * FROM applications WHERE id = '".$userInfo['aid']."'");
				if (mysql_num_rows($app) == 0){
					echo "ERROR: An unexpected error occured.";
				}else{
					// get the information.
					$appInfo = mysql_fetch_assoc($app);
					$loadLogin = $appInfo['login'];
					
					if ($loadLogin == "0"){
						// we don't actually need to get the username/password information
						// as this application is happy with a simple HWID.
						
						// unlike the other method, we can't check the username/password to see if it's claimed.
						// we have to use the HWID to check.
						
						// we need to check if we have got a HWID from them yet.
						if ($_GET['hwid'] == ""){
							echo "ERROR: Missing HWID.";
						}else{
							if ($userInfo['hwid'] != ""){
								// the serial is claimed.
								echo "ERROR: Serial has been claimed.";
								$bans->addStrike($_SERVER['REMOTE_ADDR']);
							}else{
								mysql_query("UPDATE licences SET hwid = '".md5($_GET['hwid'])."' WHERE serial = '".$userInfo['serial']."'") or die(mysql_error());
								echo "SUCCESS: Serial has been activated with your HWID";
							}
						}
					}else{
						// We are required to get the login information from the us
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
								
					}
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
				echo "ERROR: Invalid hash.";
				$bans->addStrike($_SERVER['REMOTE_ADDR']);
			}else{
				$array = $db->fetchRow($run);
				$run = $db->select("licences", "*", array("id" => $array[0]));
				
				print_r($db->fetchAssoc($run));
			}
		break;
		case "timeLeft":
			$hash = $_GET['hash'];
			// find the lid
			$run = $db->select("app_sessions", "lid", array("hash" => $hash));
			
			if ($db->numRows($run) == 0){
				echo "ERROR: Invalid session.";
				$bans->addStrike($_SERVER['REMOTE_ADDR']);
			}else{
				$array = $db->fetchRow($run);
				$getData = $db->select("licences", "*", array("id" => $array[0]));
				$data = $db->fetchAssoc($getData);
				// Find the seconds left on the serial.
				$timeLeft = $data['expires'];
				
				if ($timeLeft == "0"){
					// the licence is lifetime.
					echo "Lifetime Licence.";
				}else{
					// work out the time left.
					$deltaTime = $timeLeft - time();
					$daysLeft = $deltaTime / 86400;
					$niceDaysLeft = floor($daysLeft);
					if ($niceDaysLeft == "0"){
						$niceDaysLeft = "<1";
					}
					echo $niceDaysLeft;
				}
			}
		break;
}
?>