<?php

if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}

echo "<div class=\"content\">";

// User shouldn't be logged in.
if (isset($_POST['sub'])){
	foreach($_POST as $key => $value){
		$post[$key] = mysql_real_escape_string($value);
	}
	$user_check = mysql_query("SELECT * FROM users WHERE user = '".$post['user']."' AND pass = '".md5($_POST['pass'])."'");
	if (mysql_num_rows($user_check) == 0){
		$e[] = "Username Or Password Invalid.";
	}else{
		while($row = mysql_fetch_assoc($user_check)){
			if ($row['activated'] == "0"){
				$e[] = "Your account is not activated.";
			}else{
				$_SESSION['id'] = $row['id'];
				$_SESSION['user'] = $row['user'];
				$_SESSION['lastActive'] = time();
				$_SESSION['lasthost'] = $row['lasthost'];
				$_SESSION['lastlogin'] = $row['lastlogin'];
				mysql_query("UPDATE users SET lastlogin = '".time()."', lasthost = '".mysql_real_escape_string(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' WHERE id = '".$row['id']."'") or die(mysql_error());
				$plugin->runHook("loginComplete", "");
				?>
				<script type="text/javascript">
                    window.location = "<?php echo "index.php" ?>";
                </script>
                <?php
				
				// The user is logged in.
			}
		}
	}
	if (isset($e)){
		foreach($e as $value){
			echo "<span style=\"color:red;\"><b>{$value}</b></span><br />";
		}
	}
}
?>
<div class="grid_4 push_3">
	<div class="widget">
		<div class="wTitle">Please Login</div>
		<div class="wContent"  style="padding: 5px;">
			<form action="./index.php" method="POST">
				<label style="width: 75px;">Username:</label><input type="text" name="user" />
				<label style="width: 75px;">Password:</label><input type="password" name="pass" />
				<input type="submit" style="padding: 5px;" name="sub" value="Login" />
			</form>
		</div>
	</div>
</div>

</div>
