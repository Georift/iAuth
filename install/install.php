<?php
error_reporting(E_ERROR);
?><html>
	<head>
		<title>iAuth Installation</title>
		<link rel="stylesheet" type="text/css" href="../css/960.css">
		<link rel="stylesheet" type="text/css" href="../css/reset.css">
		<link rel="stylesheet" type="text/css" href="../css/main.css"/>
		<link rel="stylesheet" type="text/css" href="../css/widgets.css" />
	</head>
	
	<body>
		<div style="margin-top: 30px;"></div>
		<div class="container_12">
			<div class="grid_6 push_3">
				<div class="widget">
					<div class="wTitle">iAuth Installation</div>
					<div class="wContent" style="padding: 10px;">
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
<label>MySQL Host:</label><input type="text" name="db_host"/>
<label>MySQL Database:</label><input type="text" name="db_name" />
<label>MySQL Username:</label><input type="text" name="db_user" />
<label>MySQL Password:</label><input type="password" name="db_pass"/>
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
<label>Admin Username:</label><input type="text" name="user" />
<label>Admin Password:</label><input type="password" name="pass" />
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
		
		if (!@mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_pass'])){
			echo "<b>Unable to connect to mysql database using:</b> <br />Host: <b>".$_POST['db_host']."</b><br />User: <b>".$_POST['db_user']."</b><br />Pass: <b>".$_POST['db_pass']."";
		}else{
		
			if (!@mysql_select_db($_POST['db_name'])){
				echo "<b>Unable to connect to <b>".$_POST['db_name']."</b>";
			}else{
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
				
				/*
				function LoadSQL($file){
					$sql = fopen($file, "r");
					$query = fread($sql, filesize($file));
					fclose($sql);
					mysql_query($query) or die(mysql_error());
					return "<span style=\"color: green;\"><b>".$file."</b> has been loaded into the database.</span><br />";
				}
				*/
				
				require_once("sql/createTables.php");
				foreach ($table as $value){
					mysql_query($value) or die("<span style=\"color: red;\"><b>".mysql_error()."</b></span><br />");
					echo "<span style=\"color: green;\"><b>SQL query excecuted.</b></span><br />";
				}
				
				require_once("sql/insertData.php");
				foreach ($rows as $value){
					mysql_query($value) or die("<span style=\"color: red;\"><b>".mysql_error()."</b></span><br />");
					echo "<span style=\"color: green;\"><b>SQL query excecuted.</b></span><br />";
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
		
	}
}

?>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>