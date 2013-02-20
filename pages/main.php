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
		window.location = "<?php echo "index.php" ?>";
	</script>
    <?php
	die();
}
// Main page.
if ($_GET['a'] == ""){

// find the default application.
function findDefault(){
	global $db;
	
	$run = $db->select("applications", "*");
	$rows = $db->numRows($run);
	if ($rows >= 1){
		$get_rows = $db->select("applications", "*", array("defaults" => "1"));
		if ($db->numRows($get_rows) == 0){
			$db->update("applications", array("defaults" => "1"), "", "LIMIT 1");
			findDefault();
		}else{
			$rows_info = $db->fetchAssoc($get_rows);
			return $rows_info['id'];
		}
	}
}


$aid = findDefault();	
//$get_active = $db->numRows($db->query("SELECT * FROM licences WHERE aid = '{$aid}' AND active = '1'"));
$get_active = $db->numRows($db->select("licences", "*", array("active" => "1")));

//$get_inactive = $db->numRows($db->query("SELECT * FROM licences WHERE aid = '{$aid}' AND active = '0'"));
$get_inactive = $db->numRows($db->select("licences", "*", array("active" => "0")));

//$get_today = $db->query("SELECT * FROM access_log WHERE aid = '{$aid}'") or die(mysql_error());
$get_today = $db->select("access_log", "*", array("aid" => $aid)) or die(mysql_error());
$day_today = date("d", time());
$set_month = date("m", time());

$today = 0;
$yesterday = 0;
$twob4 = 0;
$threeb4 = 0;

if ($db->numRows($get_today) != 0){
	while($row = $db->fetchAssoc($get_today)){
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
<script src="js/jtip.js" type="text/javascript"></script>
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
$sideBar .= "Hey, <b>{$_SESSION['user']}</b>. Welcome to your control panel. Here you can manage all of your program's HWID licences.";
if ($_SESSION['lastlogin'] != 0){
	$sideBar .= "<br /><br /><p>You last logged in: <b>".date("d/m/Y H:i:s", $_SESSION['lastlogin'])."</b> from the host: <b>{$_SESSION['lasthost']}</b>.</p>";
}
$output = $plugin->runHook("indexSideBar", $sideBar);
echo $output;
?>
			</div>
		</div>
		<br />
		<?php
			// determine the latest version of iAuth from http://iauth.georift.net/VERSION
			// Only show latest version if not upto date.
			$version = file_get_contents("http://iauth.georift.net/VERSION");
			
			if ($version != VERSION){
				// out of date, alert the user.
				$alert = "<br /><a href='http://iauth.georift.net/' style='text-decoration: none;' target='_BLANK'><span style='color: red;'><b>A newer version of iAuth exists!<b></span></a>";
			}else{
				$alert = "<br /><span style='color: green;'><b>You are currently up to date.<b></span>";
			}
		
		?>
		<div class="widget">
			<div class="wTitle">iAuth Updates</div>
			<div class="wContent">
				Current Version: <b><?php echo VERSION; ?></b>
				<?php echo $alert; ?>
			</div>
		</div>
	</div>
	<div class="grid_8">
		<div class="widget">
			<div class="wTitle">Application Statistics</div>
			<div class="wContent">
			<?php
				$get_apps = $db->select("applications", "*");
				$get_licences = $db->select("licences", "*");
				if ($db->numRows($get_apps) == 0){
					echo "<center>No applications found. Please <a href=\"index.php?a=applications\">create</a> one.</center>";
				}else{
					if ($db->numRows($get_licences) == 0){
						echo "<center>No licences found. Please <a href=\"index.php?a=licences\">create</a> one.</center>";
					}else{
/**
					if($get_active != "" && $get_inactive != ""){
						echo "<div id=\"active_div\"></div>";
					}else{
						echo "<div id=\"active_div\"></div>";
						//echo "<center>No licences found. Please <a href=\"index.php?a=licences\">create</a> one</center>";
					} */
				?>
				<div id="active_div"></div><div id="chart_div"></div>
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
		/* Just hide the side bar.
		echo "<div class=\"grid_3\">";
			echo "All licences that are currently active are listed on the right hand side. To manage them click on the pencil icon, or to manage multiple licences at once use the check boxs and the mass tools at the bottom of the page.";
		echo "</div>";
		 **/
	
		                    
				// Submit form for create new serial.
					if ($_GET['sub'] == "submit_form"){
						$app = (int)$_POST['app'];
						$serial = mysql_real_escape_string($_POST['serial']);
						$user = mysql_real_escape_string($_POST['user']);
						if ($pass != ""){
							$pass = md5($_POST['pass']);
						}
						$date = strtotime(str_replace("/", "-", $_POST['date']));
						
						if ($serial == ""){
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
								
								$rowID = mysql_fetch_row(mysql_query("SELECT id FROM licences WHERE serial = '".$serial."'"));
							}
						}
					}
			
					
		
		// Left Side Bar
		echo "<div class=\"grid_12\">";
			// Load any input first.
			if (isset($_POST['sub']) == true){
				// The button has been pressed now we can look through the check boxes
				
				 foreach ($_POST['checkbox'] as $id => $value) {
					 // We have a list of all check checkboxes now.
					 // Only have to run the sql
					 if ($_POST['mass_action'] == "delete"){
						$db->delete("licences", array("id" => $id));
					 }elseif($_POST['mass_action'] == "suspend"){
						$db->update("licences", array("active" => "0"), array("id" => $id));
					 }elseif($_POST['mass_action'] == "unsuspend"){
						$db->update("licences", array("active" => "1"), array("id" => $id));
					 }
				 }
			}
			// Split the flow between Edit Or List
			if ($_GET['method'] == "edit"){
				// Check if the form has been submitted
				if (isset($_POST['updateLicence'])){
					// The form has been submitted
					$runArray = array();
					
					$runArray['user'] = $_POST['user'];
					$pass = md5($_POST['pass']);
					$runArray['hwid'] = md5($_POST['HWID']);
					if ($_POST['expires'] != ""){
						$runArray['expires'] = strtotime(str_replace("/", "-", $_POST['expires']));
						echo "Updated Licence Info";
					}
					if ($_POST['active'] == "on"){
						$runArray['active'] = 1;
					}else{
						$runArray['active'] = 0;
					}
					$runArray['aid'] = $_POST['app'];
					$passQuery = "";
					if ($_POST['pass'] != "Password Hidden"){
						$runArray['pass'] = md5($pass);
					}
					
					$runArray['serial'] = $_POST['serial'];

					$db->update("licences", $runArray, array("id" => $_GET['id']));
				}
				$id = $_GET['id'];

				if ($id == "" OR is_int($id) == false){
					echo "<b>ID is not valid.</b>";
				}else{
					// ID is valid.
					$userData = $auth->loadUserData($id);
					if ($userData == false){
						echo "ID not found.";
					}else{
						//print_r($userData);
						$appName = $db->fetchRow($db->select("applications", "name", array("id" => $userData['aid'])));
						
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
												/** Generate a list of all applications.
												$get_app = $db->query("SELECT * FROM applications WHERE active = '1'");
												if ($db->numRows($get_app) == 0){
													echo "Failed";	
												}else{
													while($row = $db->fetchAssoc($get_app)){
														if($userData['aid'] != $row['id']){
															echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
														}
													}
												}*/
											?>
										</select>
										<label>HWID:</label><input type="text" name="HWID" size="35" value="<?php echo $userData['hwid']; ?>" />
										<label>Expires:</label><input type="text" id="datepicker" name="expires" value="<?php If($userData['expires'] != ""){ echo date("d/m/Y", $userData['expires']); } ?>" />
										<label>Serial:</label><input type="text" size="35" name="serial" width="50px" value="<?php echo $userData['serial']; ?>" />
										<label>Username:</label><input type="text" name="user" value="<?php echo $userData['user']; ?>" />
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
				$queryArray = array();
				if (isset($_GET['aid']) == true){
					$queryArray['aid'] = $_GET['aid'];
					$queryArray['active'] = "1";
					$applet_name = $db->fetchRow($db->select("applications", "name", array("id" => $_GET['aid'])));
					$alert = "Only showing active licences from <a href=\"index.php?a=applications\">".$applet_name[0]."</a>\n";
					echo $alert;
				}
				?>
				
				<script type="text/javascript">
					$(function () {
						$('#allCheck').click(function() { 
							if (this.checked){
								$(':checkbox').each(function() {
									this.checked = true;
								});
							}else{
								$(':checkbox').each(function() {
									this.checked = false;
								});
							}
						});
					});
				</script>
				
				<?php
				echo "<form action=\"index.php?a=licences\" method=\"POST\" id='licensesform'><table style=\"width: 100%;\" class=\"table\" id=\"table\"><thead><tr><td><input type='checkbox' name='allCheck' id='allCheck' /></td><td>ID</td><td>Application</td><td>Serial</td><td>Expires</td><td>Active</td><td>Username</td><td></td></tr></thead>\n";
				
				//$get_licences = $db->query($query) or die(mysql_error());
				$get_licences = $db->select("licences", "*", $queryArray, "ORDER BY id") or die(mysql_error());
				if ($db->numRows($get_licences) == 0){
					echo "<tr class=\"none_tr\"><td colspan=\"50\"><center><b>No Licences Running.</b></center></td></tr>";	
				}else{
					
					while($row = $db->fetchAssoc($get_licences)){
						$active_license = $row['active'];
						$application_name = $db->fetchRow($db->select("applications", "name", array("id" => $row['aid'])));
						if ($row['expires'] == 0){ 
							$expires = "Never";
						}else{ 
							if ($row['expires'] > time()){
								$expires = date("d/m/Y",$row['expires']);
							}else{
								$expires = "<b>Expired</b>";
								// check that the license is now unactive.
								if($row['active']==1){
									$db->update("licences", array("active" => 0), array("id" => $row['id']));
									$active_license = 0;
								}
							}							
						}
						if ($active_license == 1){
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
							$( "#datepicker2").datepicker({ dateFormat: 'dd-mm-yy' });
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
							if ($db->numRows($db->select("applications")) == 0){
								echo "<p>Please create an application first.</p>";
							}else{
						?>
							<form id="newLicence" action="index.php?a=licences&sub=submit_form" method="post">
								<label>Application:</label><select name="app">
									<?php
										// Generate a list of all applications.
										$get_app = $db->select("applications", "*", array("active" => "1"));
										if ($db->numRows($get_app) == 0){
											echo "Failed";	
										}else{
											while($row = $db->fetchAssoc($get_app)){
												echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
											}
										}
									?>
								</select>
								<label>Serial: <a href="#" onclick="javascript:GenSerial();">Generate</a></label><input type="text" id="serial" name="serial" size="32"/>
								<label>Expires:</label><input type="text" id="datepicker" name="date" />
								<label>Duration:</label>
								<input type="submit" id="gen" name="gen" value="Go"/>
							</form>
							<?php } ?>
						</div>
					</div>
					<br />
					<div class="widget">
						<div class="wTitle">Bulk Generate Licenses</div>
						<div class="wContent hiddeninfo">
							<form action="custom.php?a=BulkGenerate" method="GET">
								<label>Application:</label><select name="app">
									<?php
										// Generate a list of all applications.
										$get_app = $db->select("applications", "*", array("active" => "1"));
										if ($db->numRows($get_app) == 0){
											echo "Failed";	
										}else{
											while($row = $db->fetchAssoc($get_app)){
												echo "<option value=\"".$row['id']."\">".$row['name']."</option>";
											}
										}
									?>
								</select>
								<input type="hidden" name="a" value="BulkGenerate" />
								<label>Number of Licences:</label><input type="text" name="number" value="50" />
								<label>Experation Date:</label><input type="text" id="datepicker2" name="expires"/>
								<input type="submit" name="sub" value="Generate" />
							</form>
						</div>
					</div>
					<?php
			}
		echo "</div>";
	echo "</div>";
}elseif($_GET['a'] == "applications"){

	if ($_GET['action'] == "newApp"){
		$name = mysql_real_escape_string($_POST['name']);
		
		mysql_query("INSERT INTO applications (name, active, defaults) VALUES('".$name."', '1', '0') ") or die(mysql_error());
		
		$get_apps = mysql_query("SELECT * FROM applications WHERE name = '".$name."' LIMIT 1");
		/*
		while($row = mysql_fetch_assoc($get_apps)){
			if ($row['active'] == 1){ $status = "Active"; }else{ $status = "Inactive"; }
			$users = mysql_num_rows(mysql_query("SELECT * FROM licences WHERE aid = '".$row['id']."' AND active = '1'"));
		}
		*/
	}
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
						//$db->query("DELETE FROM applications WHERE id = '".(int)$id."'"); 
						$db->delete("applications", array("id" => (int)$id)); 
					 }elseif($_POST['mass_action'] == "suspend"){
						 $db->update("applications", array("active" => "0"), array("id" => (int)$id));
						 $db->update("applications", array("defaults" => "0"), array("id" => (int)$id));

						 $db->update("applications", array("defaults" => "0"), array(), "LIMIT 1");
					 }elseif($_POST['mass_action'] == "unsuspend"){
						 $db->update("applciations", array("active" => "1"), array("id" => (int)$id));
					 }
				 }
			}
			
			if (isset($_GET['method']) == true && $_GET['method'] == "makeDefault"){
				$id = $_GET['id'];
				
				if (is_numeric($id) == false){
					echo "ID is invalid.";
				}else{
					$num = $db->select("licenses", "*", array("id" => $id));
					if ($db->numRows($num) == 0){
						echo "Can not set the default program to an appliation with no licenses.";
					}else{
						// Set all other ones to not default.
						//$db->query("UPDATE applications SET defaults = '0'");
						$db->update("applications", array("defaults" => "0"));
						// set only our id to default.
						//$db->query("UPDATE applications SET defaults = '1' WHERE id = '".mysql_real_escape_string($id)."'");
						$db->update("applications", array("defaults" => "1"), array("id" => $id));
						echo "Updated.";
					}
				}
			}
			
			if (isset($_GET['method']) == true && $_GET['method'] == "saveNews"){
				$id = $_GET['id'];
				$content = $_POST['content'];
				
				if ($db->numRows($db->select("news", "*", array("aid" => $id))) == 0){
					$db->insert("news", array("aid" => $id, "content" => $content));
				}else{
					$db->update("news", array("content" => $content), array("aid" => $id));
				}
				echo "News updated.";
			}
			
			if (isset($_GET['method']) == true && $_GET['method'] == "editNews"){
				$id = $_GET['id'];
				
				if (is_numeric($id) == false){
					echo "ID is invalid.";
				}else{
				?>
				<div class="widget">
					<div class="wTitle">Manage News</div>
					<div class="wContent">
						<form action="index.php?a=applications&method=saveNews&id=<?php echo $_GET['id']; ?>" method="POST">
							<div id="textHold">
								<textarea name="content" rows="5"><?php
									$run = $db->select("news", "content", array("aid" => $_GET['id']));
									if ($db->numRows($run) != 0){
										$array = $db->fetchRow($run);
										echo $array[0];
									}?></textarea><br />
								<input type="submit" name="newSubmit" value="Save News" />
							</div>
						</form>
					</div>
				</div><br />
				<?php
					
				}
			}
			
			if (isset($_GET['method']) == true && $_GET['method'] == "editNews"){
				$id = $_GET['id'];
				
				if (is_numeric($id) == false){
					echo "ID is invalid.";
				}else{
				?>
				<div class="widget">
					<div class="wTitle">Manage News</div>
					<div class="wContent">
						<?php
							$query = mysql_query("SELECT * FROM news WHERE aid = '".mysql_real_escape_string($id)."'");
							if (mysql_num_rows($query) == 0){
								echo "<p>No Application News Found. Make Some.</p>";
							}else{
								
							}
						?>
					</div>
				</div><br />
				<?php
					
				}
			}
		
			?>
            <form action="index.php?a=applications" method="post">
            	<table style="width: 100%;" id="table">
                	<thead><tr><td></td><td>ID</td><td>Name</td><td>Users</td><td>Status</td><td>Default</td><td>Version</td><!--<td>News</td>--></tr></thead>
             <?php
			 $get_apps = $db->select("applications");
			 
			 if ($db->numRows($get_apps)==0){
				echo "<tr><td colspan=\"50\"><b>No Applications Found</b></td></tr>"; 
			 }else{
				while($row = $db->fetchAssoc($get_apps)){
					if ($row['active'] == 1){ $status = "Active"; }else{ $status = "Inactive"; }
					$users = $db->numRows($db->select("licences", "*", array("aid" => $row['id'], "active" => "1")));
					$default = "No";
					if ($row['defaults'] == "1"){ $default = "Yes"; }
					$id = $row['id'];
					echo "<tr><td><input type=\"checkbox\" id=\"checkbox[{$id}]\" name=\"checkbox[{$id}]\" value=\"".$row['id']."\" /></td><td>".$row['id']."</td><td>".$row['name']."</td><td><a href=\"index.php?a=licences&aid=".$row['id']."\">".$users."</a></td><td>{$status}</td><td>{$default} - <a href=\"index.php?a=applications&method=makeDefault&id={$id}\">Make Default</a></td><td>".$row['version']."</td><!--<td><a href=\"index.php?a=applications&method=editNews&id={$id}\">Edit News</a></td>--></tr>";
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
					<form action="index.php?a=applications&action=newApp" method="POST" id="newapp">
						<label>Application Name:</label><input type="text" name="name" />
						<input type="submit" name="sub" id="sub" value="Create" />
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
					$db->query("UPDATE bans SET exception = '1' WHERE id = '".mysql_real_escape_string($id)."'");	
				}
				if ($_GET['action'] == "flush_fail"){
					$db->query("DELETE FROM fail_log");	
				}
				if ($_GET['action'] == "flush_bans"){
					$db->query("DELETE FROM bans");	
				}
				if ($_GET['action'] == "flush_inactive_bans"){
					$db->query("DELETE FROM bans WHERE expires <= '".time()."' OR  exception = '1'");	
				}
				if (isset($_POST['newPass'])) {
					if ($_POST['cpass'] == "" || $_POST['npass'] == "" || $_POST['npass1'] == ""){
						$passChangeContent = "Missing information.";
					}else{
						$userInfo = $db->query("SELECT * FROM users WHERE pass = '".md5($_POST['cpass'])."'");
						if ($db->numRows($userInfo) == 0){
							$passChangeContent = "Password incorrect.";
						}else{
							if ($_POST['npass'] != $_POST['npass1']){
								$passChangeContent = "Passwords are not the same.";
							}
							$db->update("users", array("pass" => md5($_POST['npass'])), array("id" => $_SESSION['id']));
							$passChangeContent = "Password Updated.";
						}
					}
				}
				if (isset($_POST['newPass'])) {
					if ($_POST['cpass'] == "" || $_POST['npass'] == "" || $_POST['npass1'] == ""){
						$passChangeContent = "Missing information.";
					}else{
						$userInfo = mysql_query("SELECT * FROM users WHERE pass = '".md5($_POST['cpass'])."'");
						if (mysql_num_rows($userInfo) == 0){
							$passChangeContent = "Password incorrect.";
						}else{
							if ($_POST['npass'] != $_POST['npass1']){
								$passChangeContent = "Passwords are not the same.";
							}
							mysql_query("UPDATE users SET pass = '".md5($_POST['npass'])."' WHERE id = '".mysql_real_escape_string($_SESSION['id'])."'");
							$passChangeContent = "Password Updated.";
						}
					}
				}
			?>

        <div class="widget">
<div class="wTitle">Flush</div>
<div class="wContent hiddenInfo">
<a href="index.php?a=admin&action=flush_fail">Flush Failed Access Attempts</a> <b>(<?php echo $db->numRows($db->select("fail_log", "*")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_bans">Flush Bans</a> <b>(<?php echo $db->numRows($db->select("bans", "*")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_inactive_bans">Flush Inactive Bans</a> <b>(<?php echo $db->numRows($db->query("SELECT * FROM bans WHERE expires <= '".time()."' OR  exception = '1'")); ?>)</b><br />
        <a href="index.php?a=admin&action=flush_fail">Flush Access Log</a> <b>(<?php echo $db->numRows($db->query("SELECT * FROM access_log")); ?>)</b></div>
</div><br />
		<div class="widget">
			<div class="wTitle">Change Password</div>
			<div class="wContent <?php if ($passChangeContent == ""){ ?>hiddenInfo<?php } ?>">
				<form action="index.php?a=admin" method="POST">
					<?php echo "<b>".$passChangeContent."</b>"; ?>
					<label>Current Password</label><input type="password" name="cpass" />
					<label>New Password</label><input type="password" name="npass" />
					<label>Comfirm Password</label><input type="password" name="npass1" />
					<input type="submit" name="newPass" value="Change Password" />
				</form>
			</div>
		</div>
		<br />
		<div class="widget">
			<div class="wTitle">Settings</div>
			<div class="wContent">
				<center><a href="index.php?a=settings">Settings</a></center>
			</div>
		</div>
		<br />
		</div>
        <div class="grid_8">
			<!--<div class="widget">
				<div class="wTitle">Change Password</div>
				<div class="wContent <?php if ($passChangeContent == ""){ ?>hiddenInfo<?php } ?>">
					<form action="index.php?a=admin" method="POST">
						<?php echo "<b>".$passChangeContent."</b>"; ?>
						<label>Current Password</label><input type="password" name="cpass" />
						<label>New Password</label><input type="password" name="npass" />
						<label>Comfirm Password</label><input type="password" name="npass1" />
						<input type="submit" name="newPass" value="Change Password" />
					</form>
				</div>
				</div>  -->
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
				
                $get_bans = $db->query($query) or die(mysql_error());
				if ($db->numRows($get_bans)==0){
					echo "<tr><td colspan=\"5\"><center>No Active Bans Found. <a href=\"index.php?a=admin&action=showExpired\">Show inactive bans</a></center></td></tr>";
				}else{
					while($row = $db->fetchAssoc($get_bans)){
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
                    
                    $count = $db->numRows($db->query($query));
					$total_count = $db->numRows($db->query("SELECT * FROM access_log"));
                    $get_access = $db->query($query) or die(mysql_error());
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
                    
                    
                    
                    if ($db->numRows($get_access) == 0){
                        echo "<tr><td colspan=\"3\"><center>No Access Logs Found.</center></td></tr>";
                    }else{
                        while($row = $db->fetchAssoc($get_access)){
                            $app_name = $db->fetchRow($db->query("SELECT name FROM applications WHERE id = '".$row['aid']."'"));
							
							$applicationName = $app_name[0];
							if ($applicationName == ""){
								$applicationName = "Missing/Deleted";
							}
							
                            echo "<tr><td>".date("d/m/Y h:i:s", $row['time'])."</td><td>".$applicationName."</td><td>".$row['ip']."</td></tr>";
                        }
                    }
                
                ?>
                </table>
                <span>Showing <b><?php echo $count; ?></b> of <b><?php echo $db->numRows($db->query("SELECT * FROM access_log")); ?></b></span>
                <span id="pagination" style="position: inline; float: right; margin-right: 10px;"><?php echo $page_str; ?></span>
         </div>
    </div>
</div>
    <?php
}elseif($_GET['a'] == "settings"){
	?>
	<div class="content">
		<div class="grid_4">
			<div class="widget">
				<div class="wTitle">Settings</div>
				<div class="wContent">
					Manage all core settings along with plugin settings here.
				</div>
			</div>
		</div>
		<div class="grid_8">
			<div class="widget">
				<div class="wTitle">Settings</div>
				<div class="wContent">
					
					<?php
						/***
						 *	Loop through the groups of settings.
						 */
						 $action = $_GET['action'];

						 if ($action == ""){
							echo "<center>";
							/***
							 *	Display a list of all settings group.
							 */
							$run = $db->query("SELECT * FROM settingsgroup");
							if ($db->numRows($run) == 0){
								echo "<center><p>No Settings Found.</p></center>";
							 }else{
								while($row = $db->fetchAssoc($run)){
									$activeSettings = $db->numRows($db->query("SELECT * FROM settingsitems WHERE sid = '".$row['id']."'"));
									echo "<a href=\"index.php?a=settings&action=detailed&id={$row['id']}\">".$row['name']." (<b>{$activeSettings}</b>)</a><br />";
								}
							 }
							 echo "</center>";
						 }elseif($action == "detailed"){
							/***
							 *	Show all active settings for that particuar group.
							 */
							$id = $_GET['id'];
							
							$run = $db->query("SELECT * FROM settingsitems WHERE sid = '".mysql_real_escape_string($id)."'") or die(mysql_error());
							if ($db->numRows($run) == 0){
								echo "<center><p>No settings active.</p></center>";
							}else{
								echo "<form action=\"index.php?a=settings&action=submit&id={$id}\" method=\"POST\">";
									while($row = $db->fetchAssoc($run)){
										if ($row['type'] == "text"){
											$type = "text";
											$value = $row['value'];
											echo "<label>{$row['name']}</label><input type=\"{$type}\" name=\"{$row['id']}\" value=\"{$value}\" />";
										}elseif($row['type'] == "checkbox"){
											$type = "checkbox";
											$value = "";
											if ($row['value'] == "on"){
												$value = "checked";
											}
											echo "<input style=\"display: inline;\" type=\"{$type}\" name=\"{$row['id']}\" {$value}/>{$row['name']}";
										}
										echo "<br />";
										
									}
									echo "<input type=\"submit\" name=\"sub\" value=\"Save Settings\" />";
								echo "</form>";
							}
						 }elseif($action == "submit"){
							/***
							 *	Save the settings now.
							 */
							$id = $_GET['id'];
							echo "<center>";
							$run = $db->query("SELECT * FROM settingsitems WHERE sid = '".mysql_real_escape_string($id)."'");
							if ($db->numRows($run) == 0){
								echo "ERROR: Missing application data.";
							}else{
								$yes = 0;
								while($row = $db->fetchAssoc($run)){
									if ($row['type'] == "checkbox" && $_POST[$row['id']] != $row['value']){
										$value = $_POST[$row['id']];
										
										if ($value == ""){
											$value == "off";
										}
									
										$db->update("settingsitems", array("value" => $value), array("id" => $row['id']));
										$yes++;
									}else{
										if ($_POST[$row['id']] != $row['value']){
											$db->query("UPDATE settingsitems SET value = '".mysql_real_escape_string($_POST[$row['id']])."' WHERE id = '".$row['id']."'");
											$yes++;
										}
									}
								}
								if ($yes >= 1){
									echo "Settings Updated.";
								}else{
									echo "No Settings Changed.";
								}
							}
							echo "</center>";
						 }
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
}elseif ($_GET['a'] == "support"){
	echo "<br /><center>The support system is currently under development.</center>"
	/**
	 *	This page will be where the admin can handle any support requests.
	 
	 */	
	?>
		<!-- Support still under contruction 
		<div class="content">
			<?php
				$id = $_GET['id'];
	
				if ($_GET['func'] == "claim"){
					$id = $_GET['id'];
					
					//$db->update("support", array("status" => "2", "operater" => $_SESSION['id']), array("id" => $id));
					?>
						<div class=\"grid_12\">
							<div class="widget">
								<div class="wTitle">Notfication</div>
								<div class="wContent">
									<center>You have claimed the ticket.</center>
								</div>
							</div>
						</div>
						<div class=\"clear\"></div>
						<br />
					<?php
				}
			?>
			<div class="grid_4">
				<div class="widget">
					<div class="wTitle">Stats</div>
					<div class="wContent">
						<?php
							if ($_GET['action'] != "detail"){
								/**
								 *	Stats to show here:
								 *	total tickets made
								 *	Average reply time
								 *	best support staff TODO
								 */
							}else{
								/**
								 *	Shows various things the user can do to the ticket.
								 */
								echo "<a href=\"index.php?a=support&action=functions&func=claim&id={$id}\">Claim This Ticket</a>";
								
								
							}
						?>
					</div>
				</div>
			</div>
			<div class="grid_8">
				<div class="widget">
				<div class="widget">
					<div class="wTitle">Replys</div>
					<div class="wContent">
						<?php
							/**
							 *	Show a detailed overview of the support ticket.
							 */
							
							$tickets = $db->select("support", "*", array("id" => $id));
							
							if ($db->numRows($tickets) == 0){
								// The ticket doesn't exist. Show an error.
								echo "<center><b>The ticket doesn't exists. <a href=\"index.php?a=support\">Go back.</a></b></center>";
							}else{
								// The ticket exists. We can contine.
								
								while($row = $db->fetchAssoc($tickets)){
									
									if ($row['operater'] != "0"){
										$getName = $db->select("users", "user", array("id" => $row['operater']));
										if ($db->numRows($getName) == 0){
											$name = "ERROR";
										}else{
											$names = $db->fetchRow($getName);
											$name = $names[0];
										}
									}else{
										$name = "Unclaimed";
									}
									
									/**
									 *	Generate the final name of the status
									 */
									$statusName = $db->select("ticketstatus", "name", array("id" => $row['status']));
									if ($db->numRows($statusName) == 0){
										$status = "ERROR";
									}else{
										$array = $db->fetchRow($statusName);
										$status = $array[0];
									}
									
									?>
										<table style="width: 100%;">
											<tr><td width="30%"><?php echo $row['subject']; ?></td><td width="15%"><?php echo startRating((int)$row['rating']); ?></td><td width="40%"><?php echo timeAgo(time() - $row['time']); ?></td></tr>
											<tr><td colspan="3" style="text-align: left;"><?php echo $row['body']; ?></td></tr>
											<tr><td width="30%"><a href="mailto:<?php echo $row['replymail']; ?>"><?php echo $row['replymail']; ?></a></td><td width="40%"><?php echo $status; ?></td><td width="40%"><?php echo $name; ?></td></tr>
										</table>
									<?php
								}
							}
						?>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<br />
			<div class="grid_4">&nbsp;</div>
			<div class="grid_8">
				<?php
					if ($_GET['action'] == "detail"){
				?>
				<div class="widget">
					<div class="wTitle">Reply To Ticket (Will automaticly claim)</div>
					<div class="wContent">
						<form action="index.php?a=support&action=functions&func=reply&id=<?php echo $id; ?>" method="POST">
							<textarea cols="100%" rows="5"></textarea><br />
							<select>
								<?php
									// First find the current status we are using for this ticket
									$current = $db->select("support", "status", array("id" => $id));
									if ($db->numRows($current) == 0){
										echo "ERROR.";
									}else{
										$value = $db->fetchRow($current);
										$current = $value[0];
										// Echo the default one first.
										$get = $db->select("ticketstatus", "*", array("id" => $current));
										if ($db->numRows($get) == 0){
											echo "ERROR.";
										}else{
											while($row = $db->fetchAssoc($get)){
												echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
											}
										}
									}
								
									// Find all active status adn list them.						
									$status = $db->query("SELECT * FROM ticketstatus WHERE id != '".$current."'");
									if ($db->numRows($status) == 0){
										echo "ERROR.";
									}else{
										while($row = $db->fetchAssoc($status)){
											// echo the results
											echo "<option value=\"{$row['id']}\">{$row['name']}</option>";
										}
									}
								?>
							</select>
						</form>
					</div>
				</div>
				<?php
					}else{
						echo "&nbsp;";
					}
				?>
			</div>
		</div>-->
	<?php
}
?>
	
</div>