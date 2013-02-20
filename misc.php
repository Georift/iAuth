<?php
error_reporting(E_ERROR);
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
	if ($bans->isbanned()){
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
						// Check if the form has been submitted.
						if (isset($_POST['hidden'])){
							
							// our form has been submitted.
							$serial_u = $_POST['serial1']."-".$_POST['serial2']."-".$_POST['serial3']."-".$_POST['serial4']."-".$_POST['serial5'];
							$serial = mysql_real_escape_string($serial_u);
							
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
									
								}
							}
						}
					
					
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
							<a onclick="document.forms[0].submit()" href="#" class="activate">Activate Serial</a>
						</div>
						<br />
					</form>
				</div>
			</div>
		</div>
	</div>
	<?php
}

echo $plugin->runHook("miscHook", $content);

?>