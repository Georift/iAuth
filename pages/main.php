<?php

if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}

require_once("includes/auth.php");
$auth = new auth;

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
// Main page.
if ($_GET['a'] == ""){

// find the default application.
function findDefault(){
	if (mysql_num_rows(mysql_query("SELECT * FROM applications")) >= 1){
		$get_rows = mysql_query("SELECT * FROM applications WHERE defaults = '1'") or die(mysql_error());
		if (mysql_num_rows($get_rows) == 0){
			mysql_query("UPDATE applications SET defaults = '1' LIMIT 1") or die(mysql_error());
			findDefault();
		}else{
			$rows_info = mysql_fetch_assoc($get_rows);
			return $rows_info['id'];

		}
	}
}


$aid = findDefault();	
$get_active = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '{$aid}' AND active = '1'"));
$get_inactive = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '{$aid}' AND active = '0'"));

$get_today = mysql_query("SELECT * FROM access_log WHERE aid = '{$aid}'") or die(mysql_error());
$day_today = date("d", time());
$set_month = date("m", time());

$today = 0;
$yesterday = 0;
$twob4 = 0;
$threeb4 = 0;

if (mysql_num_rows($get_today)!=0){
	while($row = mysql_fetch_assoc($get_today)){
		$log_day = date("d", $row['time']);
		if ($set_month == date("m", $row['time'])){
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
	data.addColumn('string', 'Hourly Access To The Application');
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
	chart.draw(data, {width: 420, height: 240, title: 'Hourly Access To The Application'});
}
</script>
<?php

// User should be logged in.
// Start side bar left.
?>
<div class="content">
	<div class="grid_4">
		<div class="widget">
			<div class="wTitle">Panel Information</div>
			<div class="wContent">
<?php
$sideBar .= "Hey, <b>{$_SESSION['user']}</b>. Welcome to your control panel. Here you can manage all of your program's HWID licences.<br /><br />";
if ($_SESSION['lastlogin'] != 0){
	$sideBar .= "<p>You last logged in: <b>".date("d/m/Y H:i:s", $_SESSION['lastlogin'])."</b> from the host: <b>{$_SESSION['lasthost']}</b>.</p>";
}
$output = $plugin->runHook("indexSideBar", $sideBar);
echo $output;
?>
			</div>
		</div>
		<br />
		<?php
			try {
				$latestVer = file_get_contents("http://iauth.zxq.net/version.txt");
			}catch(Exception $e){
				$latestVer = "Failed.";
			}
			if ($latestVer > VERSION){
				$warning = "<span style = \"color: red;\"><b>New Version Avaliable.</b></span>";
			}
		?>
		<div class="widget">
			<div class="wTitle">Version Information</div>
			<div class="wContent">
				<p>
					Current Version: <b><?php echo VERSION; ?></b><br />
					Latest Version: <b><?php echo $latestVer; ?></b><br />
					<?php echo $warning; ?>
				</p>
			</div>
		</div>
	</div>
	<div class="grid_8">
		<div class="widget">
			<div class="wTitle">Application Statistics</div>
			<div class="wContent">
			<?php
				$get_apps = mysql_query("SELECT * FROM applications");
				$get_licences = mysql_query("SELECT * FROM licences");
				if (mysql_num_rows($get_apps) == 0){
					echo "<center>No applications found. Please <a href=\"index.php?a=applications\">create</a> one.</center>";
				}else{
					if (mysql_num_rows($get_licences) == 0){
						echo "<center>No licences found. Please <a href=\"index.php?a=licences\">create</a> one.</center>";
					}else{

					if($get_active != "" && $get_inactive != ""){
						echo "<div id=\"active_div\"></div>";
					}else{
						echo "<center>No licences found. Please <a href=\"index.php?a=licences\">create</a> one</center>";
					}
				?>
				<div id="chart_div"></div>
			<?php 
					}
				} 
			?>
			</div>
		</div>
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
			// Load any input first.
			if (isset($_POST['sub']) == true){
				// The button has been pressed now we can look through the check boxes
				
				 foreach ($_POST['checkbox'] as $id => $value) {
					 // We have a list of all check checkboxes now.
					 // Only have to run the sql
					 if ($_POST['mass_action'] == "delete"){
						mysql_query("DELETE FROM licences WHERE id = '".(int)$id."'"); 
					 }elseif($_POST['mass_action'] == "suspend"){
						 mysql_query("UPDATE licences SET active = '0' WHERE id = '".(int)$id."'");
					 }elseif($_POST['mass_action'] == "unsuspend"){
						 mysql_query("UPDATE licences SET active = '1' WHERE id = '".(int)$id."'");
					 }
				 }
			}
			// Split the flow between Edit Or List
			if ($_GET['method'] == "edit"){
				// Check if the form has been submitted
				if (isset($_POST['updateLicence'])){
					// The form has been submitted
					$user = mysql_real_escape_string($_POST['user']);
					$pass = md5($_POST['pass']);
					$hwid = mysql_real_escape_string($_POST['hwid']);
					if ($_POST['expires'] != ""){
						$expires = strtotime(str_replace("/", "-", $_POST['expires']));
						echo "Updated Licence Info";
					}
					if ($_POST['active'] == "on"){
						$active = 1;
					}else{
						$active = 0;
					}
					$application = mysql_real_escape_string($_POST['app']);
					$passQuery = "";
					if ($_POST['pass'] != "Password Hidden"){
						$passQuery = " pass = '".$pass."',";
					}
					if($_POST['pass'] == ""){
						$passQuery = " pass = '',";
					}
					mysql_query("UPDATE licences SET user = '".$user."',".$passQuery." hwid = '".$hwid."', expires = '".$expires."', aid = '".$application."', active = '".$active."' WHERE id = '".mysql_real_escape_string($_GET['id'])."'") or die(mysql_error());
				}
				$id = $_GET['id'];
				if ($id == "" AND is_int($id) == false){
					echo "<b>ID is not valid.</b>";
				}else{
					// ID is valid.
					$userData = $auth->loadUserData($id);
					if ($userData == false){
						echo "ID not found.";
					}else{
						//print_r($userData);
						$appName = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$userData['aid']."'"));
						
						?>
							<script>
								$(document).ready(function(){
									$( "#datepicker").datepicker();
									});
							</script>
							<div class="widget">
								<div class="wTitle">Edit Licence</div>
								<div class="wContent">
									<form action="index.php?a=licences&method=edit&id=<?php echo $userData['id']; ?>" method="POST">
										<label>Application:</label><select name="app"><option value="<?php echo $userData['aid']; ?>"><?php echo $appName[0]; ?></option>
											<?php
												/* Generate a list of all applications.
												$get_app = mysql_query("SELECT * FROM applications WHERE active = '1'");
												if (mysql_num_rows($get_app) == 0){
													echo "Failed";	
												}else{
													while($row = mysql_fetch_assoc($get_app)){
														if($userData['aid'] != $row['id']){
															echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
														}
													}
												}*/
											?>
										</select>
										<label>HWID:</label><input type="text" name="HWID" size="35" value="<?php echo $userData['HWID']; ?>" />
										<label>Expires:</label><input type="text" id="datepicker" name="expires" value="<?php If($userData['expires'] != ""){ echo date("d/m/Y", $userData['expires']); } ?>" />
										<label>Serial:</label><input type="text" size="35" name="serial" width="50px" value="<?php echo $userData['serial']; ?>" />
										<label>Username:</label><input type="text" name="user" value="<?php echo $userData['user']; ?>" />
										<label>Password:</label><input type="text" name="pass" value="<?php if($userData['pass'] != ""){ ?>Password Hidden<?php } ?>" />
										<input type="checkbox" name="active" <?php if ($userData['active'] == "1"){ echo "checked"; } ?> style="display: inline;">Active
										<input type="submit" name="updateLicence" value="Update" />
									</form>
								</div>
							</div>
						<?php
					}
				}
			}else{
				// Load all licences here
				$query = "SELECT * FROM licences";
				if (isset($_GET['aid']) == true){
					$query = $query . " WHERE aid = '".mysql_real_escape_string($_GET['aid'])."' AND active = '1'";	
					$applet_name = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".mysql_real_escape_string($_GET['aid'])."'"));
					$alert = "Only showing active licences from <a href=\"index.php?a=applications\">".$applet_name[0]."</a>\n";
					echo $alert;
				}
				
				echo "<form action=\"index.php?a=licences\" method=\"POST\"><table style=\"width: 100%;\" class=\"table\" id=\"table\"><thead><tr><td></td><td>ID</td><td>Application</td><td>Serial</td><td>Expires</td><td>Suspended</td><td>Username</td><td></td></tr></thead>\n";
				
				$get_licences = mysql_query($query) or die(mysql_error());
				if (mysql_num_rows($get_licences)==0){
					echo "<tr class=\"none_tr\"><td colspan=\"50\"><center><b>No Licences Running.</b></center></td></tr>";	
				}else{
					
					while($row = mysql_fetch_assoc($get_licences)){
						$application_name = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
						if ($row['expires'] == 0){ 
							$expires = "Never";
						}else{ 
							if ($row['expires'] > time()){
								$expires = date("d/m/Y",$row['expires']);
							}else{
								$expires = "<b>Expired</b>";
							}							
						}
						if ($row['active'] == 1){
							$active = "<img src=\"images/accept.png\" />";
						}else{ 
							$active = "<img src=\"images/cancel.png\" />";
						}
						
						$userName = $row['user'];
						if ($userName == ""){ $userName = "Unclaimed"; } else{ $userName = "<b>".$userName."</b>"; }
						
						$id = $row['id'];
						echo "<tr><td><input type=\"checkbox\" id=\"checkbox[{$id}]\" name=\"checkbox[{$id}]\" value=\"".$row['id']."\" /></td><td>".$row['id']."</td><td><a href=\"index.php?a=applications&id=".$row['aid']."\">".$application_name[0]."</td><td>".$row['serial']."</td><td>".$expires."</td><td>".$active."</td><td>{$userName}</td><td><a href=\"index.php?a=licences&method=edit&id=".$row['id']."\"><img src=\"images/edit.png\" /></a></td></tr>\n";
					}
				}
					echo "</table>";
					?>
						<br />
						<select name="mass_action" style="display: inline;">
							<option value="suspend">Suspend</option>
							<option value="delete">Delete</option>
							<option value="unsuspend">Unsuspend</option>
						</select>
						<input style="display: inline;" type="submit" name="sub" value="Go!" />
					</form>
					<script type="text/javascript">
						function showMake(){
							if ($("#slideBox").is(":hidden")){
								$('#slideBox').slideDown(500);
								$("#ShowLink").html("Hide New Licence");
							}else{
								$('#slideBox').slideUp(500);
								$("#ShowLink").html("Show New Licence");
							}
						}
						
						function GenSerial(){
							$.post("custom.php?a=genSerial", function(data){
								$("#serial").val(data);	
							});	
						}
						
						$(function() {
							$( "#datepicker").datepicker();
						});
						
						function GenTime(){
							var date = $("#datepicker").val();
							$.post("custom.php?a=genTime",{date: date}, function(data){
								$("#datepicker").val(data);	
							});
						}
						
						function PostNew(){
								$.post("custom.php?a=submit_form", $('#newLicence').serialize(), function(data){
									$(".none_tr").hide();
									$("#table").append(data);
								});
								showMake();
								return false;
						}
						
						function loadLicence(id){
							if ($("#infoBox").is(":hidden")){
								$.post("custom.php?a=load", {id: id}, function(data){
									$("#infoBox").html(data);
									$("#infoBox").slideDown(500);
								});
							}else{
								$("#infoBox").slideUp(500, function(){
									$.post("custom.php?a=load", {id: id}, function(data){
										$("#infoBox").html(data);
										$("#infoBox").slideDown(500);
									});
								});
							}
						}
						
					</script>
					<div id="infoBox" style="display:none; background-color: #EAEAEA; padding: 10px; width: 500px;">
					</div><br />
					<div class="widget">
						<div class="wTitle">Create New Licence</div>
						<div class="wContent hiddenInfo">
						<?php
							if (mysql_num_rows(mysql_query("SELECT * FROM applications")) == 0){
								echo "<p>Please create an application first.</p>";
							}else{
						?>
							<form id="newLicence" action="" method="post">
								<label>Application:</label><select name="app">
									<?php
										// Generate a list of all applications.
										$get_app = mysql_query("SELECT * FROM applications WHERE active = '1'");
										if (mysql_num_rows($get_app) == 0){
											echo "Failed";	
										}else{
											while($row = mysql_fetch_assoc($get_app)){
												echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
											}
										}
									?>
								</select>
								<label>Serial: <a href="#" onclick="javascript:GenSerial();">Generate</a></label><input type="text" id="serial" name="serial" size="32"/>
								<label>Expires:</label><input type="text" id="datepicker" name="date" />
								<input type="submit" id="gen" name="gen" value="Go" onclick="javascript:PostNew();return false;" />
							</form>
							<?php } ?>
						</div>
					</div>
					<?php
			}
		echo "</div>";
	echo "</div>";
}elseif($_GET['a'] == "applications"){
	// Application Page
	// List applications.
	echo "<div class=\"content\">";
		echo "<div class=\"grid_3\">";
			// Side bar
			echo "Here you can manage all applications currently linked to this software. You can manage it's status and do bulk actions to it such as mass mail. Public news, add new licences, ect. Be sure to set the default application to load when you have more than one so the stats on the main page will be updated with the one you want.";
		echo "</div>";
		
		echo "<div class=\"grid_9\">";
			
			if (isset($_POST['sub']) == true){
				// The button has been pressed now we can look through the check boxes
				
				 foreach ($_POST['checkbox'] as $id => $value) {
					 // We have a list of all check checkboxes now.
					 // Only have to run the sql
					 if ($_POST['mass_action'] == "delete"){
						mysql_query("DELETE FROM applications WHERE id = '".(int)$id."'"); 
					 }elseif($_POST['mass_action'] == "suspend"){
						 mysql_query("UPDATE applications SET active = '0' WHERE id = '".(int)$id."'");
						 mysql_query("UPDATE applications SET defaults = '0' WHERE id = '".(int)$id."'");
						 mysql_query("UPDATE applications SET defaults = '0' LIMIT 1");
					 }elseif($_POST['mass_action'] == "unsuspend"){
						 mysql_query("UPDATE applications SET active = '1' WHERE id = '".(int)$id."'");
					 }
				 }
			}
			
			if (isset($_GET['method']) == true && $_GET['method'] == "makeDefault"){
				$id = $_GET['id'];
				
				if (is_numeric($id) == false){
					echo "ID is invalid.";
				}else{
					// Set all other ones to not default.
					mysql_query("UPDATE applications SET defaults = '0'");
					// set only our id to default.
					mysql_query("UPDATE applications SET defaults = '1' WHERE id = '".mysql_real_escape_string($id)."'");
					echo "Updated.";
				}
			}
		
			?>
            <form action="index.php?a=applications" method="post">
            	<table style="width: 100%;" id="table">
                	<thead><tr><td></td><td>ID</td><td>Name</td><td>Users</td><td>Status</td><td>Default</td><td>Version</td></tr></thead>
             <?php
			 $get_apps = mysql_query("SELECT * FROM applications");
			 
			 if (mysql_num_rows($get_apps)==0){
				echo "<tr><td colspan=\"50\"><b>No Applications Found</b></td></tr>"; 
			 }else{
				while($row = mysql_fetch_assoc($get_apps)){
					if ($row['active'] == 1){ $status = "Active"; }else{ $status = "Inactive"; }
					$users = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '".$row['id']."' AND active = '1'"));
					$default = "No";
					if ($row['defaults'] == "1"){ $default = "Yes"; }
					$id = $row['id'];
					echo "<tr><td><input type=\"checkbox\" id=\"checkbox[{$id}]\" name=\"checkbox[{$id}]\" value=\"".$row['id']."\" /></td><td>".$row['id']."</td><td>".$row['name']."</td><td><a href=\"index.php?a=licences&aid=".$row['id']."\">".$users."</a></td><td>{$status}</td><td>{$default} - <a href=\"index.php?a=applications&method=makeDefault&id={$id}\">Make Default</a></td><td>".$row['version']."</td></tr>";
				}
			 }
			 
			 echo "</table>";
			 ?>
             	<br />
                 <select name="mass_action" style="display: inline;">
                    <option value="suspend">Deactivate</option>
                    <option value="delete">Delete</option>
                    <option value="unsuspend">Activate</option>
                </select>
                <input style="display: inline;" type="submit" name="sub" value="Go!" />
            </form>
             <br />
             <script type="text/javascript">
				function postForm(){
					$.post("custom.php?a=newApp", $("#newapp").serialize(), function(data){
						$("#table").append(data);	
					});
					return false;	
				}
			 </script>
			 <div class="widget">
			 	<div class="wTitle">Create Application</div>
				<div class="wContent hiddenInfo">
					<form action="" method="POST" id="newapp">
						<label>Application Name:</label><input type="text" name="name" />
						<input type="button" name="sub" id="sub" value="Create" onclick="javascript:postForm();return false;" />
					</form>
				 </div>
			 </div>
             
             </div>
             <?php
		echo "</div>";
	echo "</div>";
}elseif($_GET['a'] == "admin"){
	// Admin Panel Page
	?>
    <div class="content">
        <div class="grid_4">Admin panel will let you manage all the important settings involved with iAuth. You can also manage any IP bans to your programs.<br />
<br />
			<?php
				if ($_GET['action'] == "delete"){
					$id = $_GET['id'];
					mysql_query("UPDATE bans SET exception = '1' WHERE id = '".mysql_real_escape_string($id)."'");	
				}
				if ($_GET['action'] == "flush_fail"){
					mysql_query("DELETE FROM fail_log");	
				}
				if ($_GET['action'] == "flush_bans"){
					mysql_query("DELETE FROM bans");	
				}
				if ($_GET['action'] == "flush_inactive_bans"){
					mysql_query("DELETE FROM bans WHERE expires <= '".time()."' OR  exception = '1'");	
				}
			?>

        <div class="widget">
<div class="wTitle">Flush</div>
<div class="wContent hiddenInfo">
<a href="index.php?a=admin&action=flush_fail">Flush Failed Access Attempts</a> <b>(<?php echo mysql_num_rows(mysql_query("SELECT * FROM fail_log")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_bans">Flush Bans</a> <b>(<?php echo mysql_num_rows(mysql_query("SELECT * FROM bans")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_inactive_bans">Flush Inactive Bans</a> <b>(<?php echo mysql_num_rows(mysql_query("SELECT * FROM bans WHERE expires <= '".time()."' OR  exception = '1'")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_fail">Flush Access Log</a> <b>(<?php echo mysql_num_rows(mysql_query("SELECT * FROM access_log")); ?>)</b></div>
</div>
        </div>
        <div class="grid_8">
        	<span id="header"><h3>Ban List</h3></span>
        	<table style="width: 450px;">
            	<tr><td></td><td>IP</td><td>Expires</td><td>Active</td><td>Edit</td></tr>
            <?php
				// Generate the query
				$query = "SELECT * FROM bans";
				if (isset($_GET['action']) && $_GET['action'] == "showExpired"){
					// Do nothing.
				}else{
					$query = $query . " WHERE expires >= '".time()."' AND exception = '0' ORDER BY time ASC";
				}
				
                $get_bans = mysql_query($query) or die(mysql_error());
				if (mysql_num_rows($get_bans)==0){
					echo "<tr><td colspan=\"5\"><center>No Active Bans Found. <a href=\"index.php?a=admin&action=showExpired\">Show inactive bans</a></center></td></tr>";
				}else{
					while($row = mysql_fetch_assoc($get_bans)){
						// If the ban has expired don't show date or time.
						if ($row['expires'] <= time()){
							$expires = "Expired";	
						}else{
							$expires = date("d/m/Y h:i:s", $row['expires']);
						}
						// Check if it's active.
						if ($row['exception'] == "1" || $row['expires'] <= time()){
							$active = "<img src=\"images/cancel.png\" />";	
						}else{
							$active = "<img src=\"images/accept.png\" />";	
						}
						// Print out our row.
						echo "<tr><td><input type=\"checkbox\" \></td><td>".$row['ip']."</td><td>".$expires."</td><td>{$active}</td><td><a href=\"index.php?a=admin&action=delete&id=".$row['id']."\">Delete</a></td></tr>";
					}
					if ($_GET['action'] != "showExpired"){
						echo "<tr><td colspan=\"5\"><center><a href=\"index.php?a=admin&action=showExpired\">Show inactive Bans</a></center></td></tr>";
					}else{
						echo "<tr><td colspan=\"5\"><center><a href=\"index.php?a=admin\">Hide inactive Bans</a></center></td></tr>";
					}
				}
            ?>
			</table>
            <br />
            <?php
					$page = $_GET['page'];
                    
                    if ($page == 0){
                        $page = 1;	
                    }
					
					$query = "SELECT * FROM access_log ORDER BY time DESC";
                    $query = $query . " LIMIT " . (10 * ((int)$page - 1)) . "," . (10 * (int)$page);
                    
                    $count = mysql_num_rows(mysql_query($query));
					$total_count = mysql_num_rows(mysql_query("SELECT * FROM access_log"));
                    $get_access = mysql_query($query) or die(mysql_error());
					$total_pages = ceil($total_count / 10);
					
					$page_str = "Pages: ";
					for($i = 1; $i<=$total_pages; $i++){
						if ($_GET['page'] == $i){
							$page_str .= "<a href=\"index.php?a=admin&page={$i}\"><b>{$i}</b></a> ";
						}else{
							$page_str .= "<a href=\"index.php?a=admin&page={$i}\">{$i}</a> ";
						}
					}
					
					if ($count == 0){ $page_str = "Pages: 0"; }
			?>
            <div style="width: 450px;">
                <h4 style="display: inline;">Access Log</h4> <span id="pagination" style="position: inline; float: right; margin-right: 10px;"><?php echo $page_str; ?></span>
                <table class="table" width="450px;">
                    <thead><tr><td>Time</td><td>Application</td><td>IP</td></tr></thead>
                <?php
                    
                    
                    
                    if (mysql_num_rows($get_access) == 0){
                        echo "<tr><td colspan=\"3\"><center>No Access Logs Found.</center></td></tr>";
                    }else{
                        while($row = mysql_fetch_assoc($get_access)){
                            $app_name = mysql_fetch_row(mysql_query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
							
							$applicationName = $app_name[0];
							if ($applicationName == ""){
								$applicationName = "Missing/Deleted";
							}
							
                            echo "<tr><td>".date("d/m/Y h:i:s", $row['time'])."</td><td>".$applicationName."</td><td>".$row['ip']."</td></tr>";
                        }
                    }
                
                ?>
                </table>
                <span>Showing <b><?php echo $count; ?></b> of <b><?php echo mysql_num_rows(mysql_query("SELECT * FROM access_log")); ?></b></span>
                <span id="pagination" style="position: inline; float: right; margin-right: 10px;"><?php echo $page_str; ?></span>
         </div>
    </div>
</div>
    <?php
}elseif($_GET['a'] == "support"){
	// TODO Finish the support system.
	?>
	<div class="content">
		<div class="grid_4">
			Here you can manage all support tickets with your products.
		</div>
		<div class="grid_8">
			<?php	
				
			?>
		</div>
	</div>
	<?php
}
?>
	
</div>