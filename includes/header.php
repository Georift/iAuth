<?php
if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/960.css">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/main.css"/>
    <link rel="stylesheet" type="text/css" href="css/ui-lightness/jquery-ui-1.8.14.custom.css"/>
	<link rel="stylesheet" type="text/css" href="css/widgets.css" />
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.min.js"></script>
    <!--<script type="text/javascript" src="js/jquery-1.5.1.min.js"></script> Soon To Be Removed.-->
    <script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
	<script type="text/javascript" src="js/main.js"></script>
   <script type="text/javascript">
		$(document).ready(function(){
				$.datepicker.regional['en-AU'] = {
					closeText: 'Done',
					prevText: 'Prev',
					nextText: 'Next',
					currentText: 'Today',
					monthNames: ['January','February','March','April','May','June',
					'July','August','September','October','November','December'],
					monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
					'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
					dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
					dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
					dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
					weekHeader: 'Wk',
					dateFormat: 'dd/mm/yy',
					firstDay: 1,
					isRTL: false,
					showMonthAfterYear: false,
					yearSuffix: ''};
				$.datepicker.setDefaults($.datepicker.regional['en-AU']);
		});
	</script>
	
    <title>iAuth</title>
</head>
<body>
<?php
if (isset($_SESSION['user'])){
}
?>	
	<div class="container_12">
    	<div class="grid_12" id="header"> 
		<?php
			$output = '<a href="index.php"><img src="images/header.png" name="header_img" id="header_img" /></a>';
			$output = $plugin->runHook("preHeader", $output);
			echo $output;
		?>
        </div>
