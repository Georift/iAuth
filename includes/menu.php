<?php
if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}

if (isset($_SESSION['user']) == true){

?>

<script>
	$(function(){
		$(".button").button();
	});
</script>

<a href="index.php" style="text-decoration: none;"><button id="button" class="grid_2 button">Home</button></a>
<a href="index.php?a=licences" style="text-decoration: none;"><button id="button" class="grid_2 button">Licences</button></a>
<a href="index.php?a=applications" style="text-decoration: none;"><button id="button" class="grid_2 button">Applications</button></a>
<a href="index.php?a=admin" style="text-decoration: none;"><button id="button" class="grid_2 button">Admin</button></a>
<!--<div class="grid_2 menu"><a href="#">Admins</a></div>-->
<a href="index.php?a=logout" style="text-decoration: none;"><button id="button" class="grid_2 button">Logout</button></a>
<div class="clear"></div>

<?php } ?>