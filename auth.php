<?php
// Validate if the user is real and if not ban their IP
include("includes/mysql_connect.php");

$user = mysql_real_escape_string($_GET['user']);
$pass = md5($_GET['pass']);

// Check make sure the user has no previous bans.
// If they do alert them of it.
$get_banned = mysql_query("SELECT expires FROM bans WHERE ip = '".$_SERVER['REMOTE_ADDR']."' AND expires >= '".time()."' AND exception = '0'") or die(mysql_error());
if (mysql_num_rows($get_banned) >= 1){
	$set_time = mysql_fetch_row($get_banned);
	$time_left = $set_time[0] - time();
	die("Your IP has been banned and will be unbanned in ".date("i:s", $time_left)." minutes.");	
}

// The user has not been marked before.
// Try the login.
if ($user == "" OR $pass == ""){
	// Output failed if the operation
	// was not completed sucsessfully.
	echo "FAILED";	
}else{
	// Load the users licence.
	// Since we only need to know the application
	// id we don't need to select much for now.
	$run = mysql_query("SELECT aid,id FROM licences WHERE user = '".$user."' AND pass = '".$pass."'");
	// Set the time to check for any previous bans
	// Check for the bans
	$last_time = time() - 3600;
	$failed = mysql_query("SELECT * FROM fail_log WHERE time >= '".$last_time."' AND ip = '".$_SERVER['REMOTE_ADDR']."' AND type = '2' AND counted = '0'") or die(mysql_error());
	// Limit the allowance for failed entry's
	// to three. If they pass that add there
	// IP address to the bans table.
	if (mysql_num_rows($failed) >= 3){
		$new_time = time() + 3600;
		mysql_query("INSERT INTO bans(ip, time, expires) VALUES('".$_SERVER['REMOTE_ADDR']."','".time()."','".$new_time."')") or die(mysql_error());
		$last_time = time() - 3600;
		mysql_query("UPDATE fail_log SET counted = '1' WHERE time >= '".$last_time."' AND ip = '".$_SERVER['REMOTE_ADDR']."' AND type = '2'");
		die("You have been locked out for an hour. Please try again later.");
	}
	// Check if the username and password is valid
	// If not then store the failed attempt in fail_log
	// Once three are reached counted will be changed to 1
	// and they will be disregarded.
	if (mysql_num_rows($run)==0){
		echo "FAILED";	
		mysql_query("INSERT INTO fail_log(time,ip,type) VALUES('".time()."','".$_SERVER['REMOTE_ADDR']."','2')");
	}else{
		// User has passes the authentication process.
		// We can share any information needed now.
		echo "ALLOWED";
		$aid = mysql_fetch_row($run);
		$last_time = time() - 3600;
		// Log the access if the last access by this IP was an hour ago.
		$get_last = mysql_query("SELECT * FROM access_log WHERE time >= '".$last_time."' AND ip = '".$_SERVER['REMOTE_ADDR']."'");
		if (mysql_num_rows($get_last) != 1){
			if (isset($_GET['a']) == false){	
				// Get the licence ID and save it to database.
				//$get_licence = mysql_query("SELECT id FROM licence");
				mysql_query("INSERT INTO access_log (time ,aid ,ip, lid) VALUES ('".time()."','".$aid[0]."','".$_SERVER['REMOTE_ADDR']."', '".$aid[1]."')") or die(mysql_error());
			}
		}
	}
}
?>