<?php
session_start();
// For custom requests that require only
// output being the information. ie. Ajax requests.
include("includes/mysql_connect.php");

if (isset($_SESSION['user'])==false){
	die();	
}

if ($_GET['a'] == "genSerial"){
	$salt = "jk28sk";
	$rand_str = rand(0,10000001228);
	echo md5($salt.$rand_str);
}

if ($_GET['a'] == "genTime"){
	//echo $_POST['date']."1";
	print_r(strtotime($_POST['date']));
}

if ($_GET['a'] == "load"){
	$get_all = mysql_query("SELECT * FROM licences WHERE id = '".(int)$_POST['id']."'");
	while($row = mysql_fetch_assoc($get_all)){
		if ($row['user'] == ""){ $user = "N/A"; }else{ $user = $row['user']; }
		echo "<b>User:</b> ".$user."<br />";
		echo "<b>Expires:</b> ".date("d/m/Y", $row['expires'])."<br />";
		echo "<b>Serial:</b> ".$row['serial']."<br />";
		$get_app = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
		echo "<b>Application:</b> ".$get_app[0]."<br />";
		echo "<a href=\"#\" onclick=\"javascript:$('#infoBox').slideUp(500);\">Close</a>";
	}
}

if ($_GET['a'] == "newApp"){
	$name = mysql_real_escape_string($_POST['name']);
	$default = $_POST['default'];
	$active = $_POST['active'];
	
	if ($default == "on"){ $default = 1; }else{ $default = 0; }
	if ($active == "on"){ $active = 1; }else{ $active = 0; }
	
	if ($default == 1){
		mysql_query("UPDATE applications SET defaults = '0'");
	}	
	
	mysql_query("INSERT INTO applications (name, active, defaults) VALUES('".$name."', '".$active."', '".$default."') ") or die(mysql_error());
	
	$get_apps = mysql_query("SELECT * FROM applications WHERE name = '".$name."' LIMIT 1");
	
	while($row = mysql_fetch_assoc($get_apps)){
		if ($row['active'] == 1){ $status = "Active"; }else{ $status = "Inactive"; }
		$users = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '".$row['id']."' AND active = '1'"));
		$default = "No";
		if ($row['default'] == "1"){ $default = "Yes"; }
		echo "<tr><td><input type=\"checkbox\" /></td><td>".$row['id']."</td><td>".$row['name']."</td><td><a href=\"index.php?a=licences&aid=".$row['id']."\">".$users."</a></td><td>{$status}</td><td>{$default}</td></tr>";
	}
	
}

if ($_GET['a'] == "submit_form"){
	$app = (int)$_POST['app'];
	$serial = mysql_real_escape_string($_POST['serial']);
	$user = mysql_real_escape_string($_POST['user']);
	$pass = md5($_POST['pass']);
	$date = strtotime($_POST['date']);
	
	if ($serial == "" || $date == ""){
		echo "Failed";	
	}else{
		mysql_query("INSERT INTO licences(aid, expires, active, serial, user, pass) VALUES('".(int)$app."','".$date."','1','".$serial."','".$user."','".$pass."')") or die(mysql_error());
		
		$get_app = mysql_query("SELECT * FROM licences WHERE serial = '".$serial."'");
		
		while($row = mysql_fetch_assoc($get_app)){
			
			$application_name = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
			
			if ($row['expires'] == 0){ $expires = "Never"; }else{ $expires = date("d/m/Y",$row['expires']); }
			if ($row['active'] == 1){
				$active = "<img src=\"images/accept.png\" />";
			}else{ 
				$active = "<img src=\"images/cancel.png\" />";
			}
			
			echo "<tr><td><input type=\"checkbox\" id=\"checkbox[{$id}]\" name=\"checkbox[{$id}]\" value=\"".$row['id']."\" /></td><td>".$row['id']."</td><td><a href=\"index.php?a=applications&id=".$row['aid']."\">".$application_name[0]."</td><td>".$row['serial']."</td><td>".$expires."</td><td>".$active."</td></tr>";
		}
		
		
	}
}

?>