<?php

if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}

if ($_GET['a'] == "logout"){
	unset($_SESSION['user']);
	unset($_SESSION['lastActive']);
	unset($_SESSION['lastlogin']);
	unset($_SESSION['lasthost']);
	?>
    <script type="text/javascript">
		window.location = "<?php echo baseurl."index.php" ?>";
	</script>
    <?php
	die();
}

if ($_GET['a'] == ""){
$get_active = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '1' AND active = '1'"));
$get_inactive = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '1' AND active = '0'"));

$get_today = mysql_query("SELECT * FROM access_log WHERE aid = '1'") or die(mysql_error());
$day_today = date("d", time());

$today = 0;
$yesterday = 0;
$twob4 = 0;
$threeb4 = 0;

if (mysql_num_rows($get_today)!=0){
	while($row = mysql_fetch_assoc($get_today)){
		$log_day = date("d", $row['time']);
		if ($log_day == $day_today){
			$today++;	
		}elseif($log_day == ($day_today - 1)){
			$yesterday++;	
		}elseif($log_day == ($day_today - 2)){
			$twob4++;
		}elseif($log_day == ($day_today - 3)){
			$threeb4++;
		}else{
			// Not within our time span ignore.	
		}
	}
}

?>
<script type="text/javascript">
// Number of Active Licences to inactive.
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Licences');
	data.addColumn('number', 'Active');
	data.addRows(5);
	data.setValue(0, 0, 'Active');
	data.setValue(0, 1, <?php echo $get_active; ?>);
	data.setValue(1, 0, 'Inactive');
	data.setValue(1, 1, <?php echo $get_inactive; ?>);
	
	var chart = new google.visualization.PieChart(document.getElementById('active_div'));
	chart.draw(data, {width: 420, height: 240, title: 'Licences Active'});
}
google.setOnLoadCallback(drawChart2);
function drawChart2() {
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Access To Application');
	data.addColumn('number', 'Count');
	data.addRows(4);
	data.setValue(0, 0, '<?php echo date("d/m/Y", time() - (86400 * 3)); ?>');
	data.setValue(0, 1, <?php echo $threeb4; ?>);
	data.setValue(1, 0, '<?php echo date("d/m/Y", time() - (86400 * 2)); ?>');
	data.setValue(1, 1, <?php echo $twob4; ?>);
	data.setValue(2, 0, '<?php echo date("d/m/Y", time() - (86400 * 1)); ?>');
	data.setValue(2, 1, <?php echo $yesterday; ?>);
	data.setValue(3, 0, '<?php echo date("d/m/Y", time()); ?>');
	data.setValue(3, 1, <?php echo $today; ?>);
	
	var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
	chart.draw(data, {width: 420, height: 240, title: 'Access To Application'});
}
</script>
<?php

// User should be logged in.
// Start side bar left.
?>
<div class="content">
	<div class="grid_4">
<?php

echo "Hey, <b>{$_SESSION['user']}</b>. Welcome to your control panel. Here you can manage all of your program's HWID licences.<br />";
echo "<p>You last logged in: <b>".date("d/m/Y H:i:s", $_SESSION['lastlogin'])."</b> from the host: <b>{$_SESSION['lasthost']}</b>. Not you? Change your password.</p>";
?>
	</div>
	<div class="grid_8">
    	<h3 style="text-align: center;">Test Application - Statistics</h3>
        <div id="active_div"></div><div id="chart_div"></div>
    </div>
<?php
}elseif($_GET['a']=="licences"){
	echo "<div class=\"content\">";
		// Load a list of all licences.
		// Left Side Bar
		echo "<div class=\"grid_3\">";
			echo "All licences that are currently active are listed on the right hand side. To manage them click on the pencil icon, or to manage multiple licences at once use the check boxs and the mass tools at the bottom of the page.";
		echo "</div>";
		
		// Left Side Bar
		echo "<div class=\"grid_9\">";
			// Load all licences here
			$query = "SELECT * FROM licences";
			if (isset($_GET['aid']) == true){
				$query = $query . " WHERE aid = '".mysql_real_escape_string($_GET['aid'])."'";	
			}
			
			$get_licences = mysql_query($query) or die(mysql_error());
			if (mysql_num_rows($get_licences)==0){
				echo "No Licences Running.";	
			}else{
				echo "<table class=\"table\"><thead><tr><td></td><td>ID</td><td>Application</td><td>Serial</td><td>Expires</td><td>Active</td></tr></thead>";
				while($row = mysql_fetch_assoc($get_licences)){
					$application_name = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
					echo "<tr><td><input type=\"checkbox\" name=\"checkbox[]\" value=\"".$row['id']."\" /></td><td>".$row['id']."</td><td>".$application_name[0]."</td><td>".$row['serial']."</td><td>".$row['expires']."</td><td>".$row['active']."</td></tr>";
				}
				echo "</table>";
				?>
                <br />
                <select name="mass_action">
                	<option value="delete">Delete</option>
                    <option value="suspend">Suspend</option>
                    <option value="unsuspend">Unsuspend</option>
                </select>
                <input type="submit" name="sub" value="Go!" />
                <?php
			}
		echo "</div>";
	echo "</div>";
}elseif($_GET['a'] == "applications"){
	// List applications.
	echo "<div class=\"content\">";
		echo "<div class=\"grid_4\">";
			// Side bar
			echo "Here you can manage all applications currently linked to this software. You can manage it's status and do bulk actions to it such as mass mail. Public news, add new licences, ect. Be sure to set the default application to load when you have more than one so the stats on the main page will be updated with the one you want.";
		echo "</div>";
		
		echo "<div class=\"grid_8\">";
			?>
            	<script></script>
            	<a href="#" onclick="javascript:loadNewApp();">Create Application</a><br />
                <div class="NewApp"></div><br />
            	<table style="width: 350px;">
                	<thead><tr><td></td><td>ID</td><td>Name</td><td>Users</td><td>Status</td></tr></thead>
             <?php
			 $get_apps = mysql_query("SELECT * FROM applications");
			 
			 if (mysql_num_rows($get_apps)==0){
				echo "<tr><td colspan=\"5\"><b>No Applications Found</b></td></tr>"; 
			 }else{
				while($row = mysql_fetch_assoc($get_apps)){
					if ($row['active'] == 1){ $status = "Active"; }else{ $status = "Inactive"; }
					$users = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '".$row['id']."' AND active = '1'"));
					echo "<tr><td><input type=\"checkbox\" /></td><td>".$row['id']."</td><td>".$row['name']."</td><td><a href=\"index.php?a=licences&aid=".$row['id']."\">".$users."</a></td><td>{$status}</td></tr>";
				}
			 }
			 
			 echo "</table>";
		echo "</div>";
	echo "</div>";
}
?>
	
</div>