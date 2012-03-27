<?php
session_start();
define("IN_SCRIPT", 1);

require_once("includes/mysql_connect.php");
require_once("includes/plugins.php");
require_once("includes/auth.php");
require_once("includes/bans.php");
$plugin = new Plugins;
$auth = new auth;
$bans = new Bans;

$f = glob("plugins/*.php");
foreach($f as $a){
	require_once($a);
}

if ($_GET['a'] == "registerSerial"){
	?>
	<link rel="stylesheet" type="text/css" href="css/960.css">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
   <!-- <link rel="stylesheet" type="text/css" href="css/main.css"/>-->
    <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.14.custom.css"/>
	<link rel="stylesheet" type="text/css" href="css/widgets.css" />
	<div class="container_12">
		<br />
		<div class="grid_6 push_3">
			<div class="widget">
				<div class="wTitle">Serial Registration</div>
				<div class="wContent">
				<?php
					if (isset($_POST['sub'])==true){
						$user = mysql_real_escape_string($_POST['user']);
						$pass = mysql_real_escape_string($_POST['pass']);
						$pass2 = mysql_real_escape_string($_POST['pass2']);
						$serial = mysql_real_escape_string($_POST['serial']);	
						
						if ($user == "" || $pass == "" || $serial == ""){
							echo "Your missing some information.";	
						}else{
							$done = true;
							if ($pass == $pass2){
								$userData = $auth->getLicenceInfo(array("serial" => $serial));
								
								if ($userData == false){
									echo "Invalid Serial.";
									$bans->addStrike($_SERVER['REMOTE_ADDR']);
								}elseif ($userData['user'] != "" || $userData['pass'] != ""){
									echo "Serial has been activated already.";	
								}else{
									if ($userData['active'] == "0"){
										echo "This serial is currently suspended.";	
									}else{
										if ($auth->getLicenceInfo(array("user" => $user)) != false){
											echo "Username Taken.";
										}else{
											if (strlen($user) >= 3 && strlen($user) <= 25 && strlen($pass) >= 5 && strlen($pass) <= 30){
												mysql_query("UPDATE licences SET user = '".$user."', pass = '".md5($_POST['pass'])."' WHERE serial = '".$userData['serial']."'") or die(mysql_error());
												$done = true;
												echo "Serial Activated, Please try logging in with your application.";
											}else{
												echo "Username/Password length's are incorrect.";	
											}
										}
									}
								}
							}else{
								echo "Passwords doesn't match.";	
							}
						}
					}
					if ($done == false){
						?>
						<form action="misc.php?a=registerSerial" method="POST">
							Serial: <input type="text" name="serial" /><br />
							Username: <input type="text" name="user" /><br />
							Password: <input type="password" name="pass" /><br />
                            Again: <input type="password" name="pass2" /><br />
							<input type="submit" name="sub" value="Go" />
						</form>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

echo $plugin->runHook("miscHook", $content);

?>