<?php
error_reporting(E_ERROR);
session_start();
define("IN_SCRIPT", 1);

require_once("includes/mysql_connect.php");

/**
 * Include and run the settings core class
 */

require_once("includes/settings.class.php");
$settings = new settings;

/**
 *	Loop through all settings and define them.
 *	Now we can access the settings anywhere we have a init.php included.
 */
$run = mysql_query("SELECT * FROM settingsitems");
while($row = mysql_fetch_assoc($run)){
	define($row['code'], $row['value']);
}

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
	if ($bans->isBanned($_SERVER['REMOTE_ADDR'])){
		die("You are currently banned from accessing this panel due to too many failed attempts. Try again soon.");
	}
	
	?>
	<script language="javascript" type="text/javascript" src="js/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/960.css">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
	<link rel="stylesheet" type="text/css" href="css/widgets.css" />
	<link rel="stylesheet" type="text/css" href="css/misc.css" />
	<div class="container_12">
		<br />
		<div class="grid_8 push_2">
			<div class="widget">
				<div class="wTitle">Serial Registration</div>
				<div class="wContent">
					<?php
					//$getData = true;
					if (isset($_GET['step'])){
						// the form has been submitted by the user.
						$getData = true;
						// Don't forget to double check the serial the user could have manipulated.
						$serial = $_POST['serial']; 
						$username = mysql_real_escape_string($_POST['username']);
						$password = mysql_real_escape_string($_POST['password']);
						$password2 = mysql_real_escape_string($_POST['password2']);
						$email = mysql_real_escape_string($_POST['email']);
						
						if ($password == "" || $username == ""){
							$error = "Missing Username/Password.";
						}else{
							if ($password != $password2){
								$error = "Passwords Don't Match.";
							}else{
								$serialCheck = mysql_query("SELECT * FROM licences WHERE serial = '".mysql_real_escape_string($serial)."'");
								if (mysql_num_rows($serialCheck) == 0){
									$error = "Serial is no longer active.";
								}else{
									// check that the serial isnt taken.
									$serialData = mysql_fetch_assoc($serialCheck);
									if ($serialData['user'] != ""){
										$error = "Serial is no longer active";
									}else{
										// the serial is active, check username is not taken.
										$getCheck = mysql_query("SELECT * FROM licences WHERE user = '".$username."'");
										if (mysql_num_rows($getCheck) != 0){
											$error = "Username is taken.";
										}else{
											// check if we require a email address.
											if (EMAILS == "on"){
												// we require the email address, check for it.
												if ($email == ""){
													$error = "Missing email address,";
												}else{
													// check if it is taken.
													$checkEmail = mysql_query("SELECT * FROM licences WHERE email = '".$email."'");
													if (mysql_num_rows($checkEmail) != 0){
														// the email address is taken and cannot be used.
														$error = "Email address is taken.";
													}else{
														// the email address is free and we can now activate the licence.
														// insert into table, username, password, email
														mysql_query("UPDATE licences SET user='".$username."', pass = '".md5($password)."', email= '".$email."' WHERE serial = '".mysql_real_escape_string($serial)."'");
														$finalize = true;
													}
												}
											}else{
												// email is off
												$error = "TODO";
											}
										}
									}
								}
							}
						}
					}
					
						// Check if the form has been submitted.
						if (isset($_POST['hidden'])){
							// our form has been submitted.
							$serial_u = $_POST['serial1']."-".$_POST['serial2']."-".$_POST['serial3']."-".$_POST['serial4']."-".$_POST['serial5'];
							$serial = mysql_real_escape_string($serial_u);
							
							if ($serial == "----"){
								$error = "Serial is missing.";
							}else{
								$check = mysql_query("SELECT * FROM licences WHERE serial = '".$serial."'");
								
								if (mysql_num_rows($check) == 0){
									// serial is invalid. Add to bans.
									$bans->addStrike($_SERVER['REMOTE_ADDR']);
									$error = "Serial is invalid. ".$serial;
								}else{
									// the serial came back valid.
									$serialData = mysql_fetch_assoc($check);
									if ($serialData['user'] != ""){
										// the serial is already used.
										$error = "Serial has already been activated.";
									}else{
										// the serial is still active.
										// set the variable user details true
										$getData = true;
									}
								}
							
							}
						}
					
					if ($getData == false){
					?>
						<script type="text/javascript">
							$(document).ready(function(){
								$("input.serial").keypress(function() {
								   if ($(this).val().length == 4){
								   	$(this).next("input.serial").focus();
								   }
								});
							});
						</script>
						<form action="misc.php?a=registerSerial" method="POST">
							<input type="hidden" name="hidden" value="yes" />
							<div id="serialContainer">
								<div id="errors" style="font-size: 15px; color: red; font-weight: bold; margin: 5px;"><?php if($error != ""){ echo $error; }else{ echo "&nbsp"; } ?> </div>
								<input type="text" name="serial1" class="serial" /> - 
								<input type="text" name="serial2" class="serial" /> - 
								<input type="text" name="serial3" class="serial" /> - 
								<input type="text" name="serial4" class="serial" /> - 
								<input type="text" name="serial5" class="serial" />
								<br /><br />
								<a onclick="document.forms[0].submit()" class="activate">Activate Serial</a>
							</div>
							<br />
						</form>
					<?php
					}elseif($getData == true && $finalize != true){
					?>
						<style>
							.form {
								padding: 15px;
								margin: auto 0;
							}
							.form label {
								margin-left: 10px;
								color: #637897;
							}
							
							input:hover, input:focus{
								border-color: #C9C9C9;
								-webkit-box-shadow: rgba(0, 0, 0, 0.15) 0px 0px 8px;
							}
							
							input, textarea {
								padding: 9px;
								border: solid 1px #E5E5E5;
								outline: 0;
								font: normal 13px/100% Verdana, Tahoma, sans-serif;
								width: 200px;
								background: #FFFFFF;
								box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;  
							    -moz-box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;  
							    -webkit-box-shadow: rgba(0,0,0, 0.1) 0px 0px 8px;
							    background: -webkit-gradient(linear, left top, left 25, from(#FFFFFF), color-stop(4%, #E3F4FF), to(#FFFFFF));  
    							background: -moz-linear-gradient(top, #FFFFFF, #E3F4FF 1px, #FFFFFF 25px); 
							}
							
							.submit input {  
								width: auto;  
								padding-top: 10px;
							    padding: 9px 15px;  
							    background: #617798;  
							    border: 0;  
							    font-size: 14px;  
							    color: #FFFFFF;  
							    -moz-border-radius: 5px;  
							    -webkit-border-radius: 5px; 
						    } 
							
						</style>
						<form action="misc.php?a=registerSerial&step=2" method="POST" class="form">
							<div id="errors" style="font-size: 15px; color: red; font-weight: bold; margin: 5px;"><?php if($error != ""){ echo $error; } ?></div>
							<input type="hidden" name="serial" value="<?php echo $serial; ?>" />
							<p class="username">
								<input type="text" name="username" id="username" autocomplete="off" />
								<label for="username">Username</label>
							</p>
							<p class="password">
								<input type="password" name="password" autocomplete="off" />
								<label for="password">Password</label>
							</p>
							<p class="password2">
								<input type="password" name="password2" autocomplete="off" />
								<label for="password">Password (again)</label>
							</p>
							<?php 
								if (EMAILS == "on"){
							?>
								<p class="email">
									<input type="text" name="email" autocomplete="off" />
									<label for="email">E-mail</label>
								</p>
							<?php
								}
							?>
							<p class="submit">
								<input type="submit" name="submit" value="Register" />
							</p>
						</form>
						
					<?php
					}elseif($finalize == true){
						?>
						<center>Thank you for activating your serial, you can now use your application with the credentials you provided here.</center>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

echo $plugin->runHook("miscHook", $content);

?>