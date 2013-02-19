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
						<br />
						<div id="serialContainer">
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