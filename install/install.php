<?php

if (file_exists("../includes/config.php") == true){
	if (file_exists("LOCK") == false){
		$file = fopen("LOCK", "w");
		fclose($file);	
	}
	die("We are already installed. Please log in <a href=\"../index.php\">here</a>.");	
}else{
	if (file_exists("LOCK")){
		die("Please delete the \"LOCK\" file from the install directory.");	
	}
	$step = $_GET['step'];
	if ($step == ""){
		echo "Welcome to the installation process. It will help you install your copy of iAuth, please click next to continue.<br />";
		echo "<a href=\"install.php?step=2\">Next Step</a>";	
	}elseif($step == "2"){
		echo "To install we need your database information. Please fill in the required fields bellow.<br /><br />\n";
		?>
<form action="install.php?step=3" method="POST">
MySQL Host: <input type="text" name="db_host"/><br />
MySQL Database: <input type="text" name="db_name" /><br />
MySQL Username: <input type="text" name="db_user" /><br />
MySQL Password: <input type="password" name="db_pass"/><br />
<input type="submit" name="sub" value="Next Step" />
</form>
        <?php
	}elseif($step == "3"){
		$hidden_vals = "";
		foreach ($_POST as $key => $value){
			if ($key != "sub"){
				$hidden_vals = $hidden_vals . "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\" />\n";
			}
		}
		?>
<form action="install.php?step=4" method="POST">
<?php echo $hidden_vals; ?>
Admin Username: <input type="text" name="user" /><br />
Admin Password: <input type="password" name="pass" /><br />
<input type="submit" name="sub" value="Next Step" />
</form>
        <?php
	}elseif($step == "4"){
		// check that all values are there.
		foreach ($_POST as $key => $value){
			if ($key != "sub"){
				if ($value == ""){
					//die("Missing Information, please go and fill in all fields. <a href=\"install.php?step=2\">Go back to start</a>");	
				}
			}
		}
		// The should be if we get to this point.
		// create file.
		$config_file = "../includes/config.php";
		$con_file = fopen($config_file, "w") or die("Unable to create config file.");
		
		// Generate the config files.
		$file_str .= "<?php\n";
		$file_str .= "// Created at: ".date("d/m/Y h:i:s", time())."\n";
		$file_str .= "define(\"HOST\", \"".$_POST['db_host']."\");\n";
		$file_str .= "define(\"USER\", \"".$_POST['db_user']."\");\n";
		$file_str .= "define(\"PASS\", \"".$_POST['db_pass']."\");\n";
		$file_str .= "define(\"DATABASE\", \"".$_POST['db_name']."\");\n";
		$file_str .= "?>";
		
		fwrite($con_file, $file_str);
		fclose($con_file);
				
		mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass']);
		mysql_select_db($_POST['db_name']);
		
		function LoadSQL($file){
			$sql = fopen($file, "r");
			$query = fread($sql, filesize($file));
			fclose($sql);
			mysql_query($query);
			return "<span style=\"color: green;\"><b>".$file."</b> has been loaded into the database.</span><br />";
		}
			
		$dir = "sql/*";	
		foreach(glob($dir) as $file)
		{
			echo LoadSQL($file);
		}
		
		// Now include the admin users.
		mysql_query("INSERT INTO users(user,pass,activated) VALUES('".mysql_real_escape_string($_POST['user'])."', '".md5($_POST['pass'])."', '1')") or die(mysql_error());
		echo "<br /><span style=\"color: green;\"><b>Admin User Created. Install Complete.</b></span><br />";

		// Create a lock file to prevent tampering with install.
		fopen("LOCK", "w");
		echo "<span style=\"color: green;\"><b>LOCK file created.</b></span>";
		
		// Everything is installed.
		echo "<br /><br />Your install of iAuth is complete. You can now login with the details you provided.";
		
	}
}

?>