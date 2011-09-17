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
?>
<!-- LiveZilla Chat Button Link Code (ALWAYS PLACE IN BODY ELEMENT) --><div style="display:none;"><a href="javascript:void(window.open('http://georift.co.cc/help/chat.php','','width=590,height=610,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes'))"><img id="chat_button_image" src="http://georift.co.cc/help/image.php?id=03&amp;type=overlay" width="32" height="112" border="0" alt="LiveZilla Live Help" /></a></div><!-- http://www.LiveZilla.net Chat Button Link Code --><!-- LiveZilla Tracking Code (ALWAYS PLACE IN BODY ELEMENT) --><div id="livezilla_tracking" style="display:none"></div><script type="text/javascript">
/* <![CDATA[ */
var script = document.createElement("script");script.type="text/javascript";var src = "http://georift.co.cc/help/server.php?request=track&output=jcrpt&fbpos=10&fbml=0&fbmt=0&fbmr=0&fbmb=0&fbw=32&fbh=112&nse="+Math.random();setTimeout("script.src=src;document.getElementById('livezilla_tracking').appendChild(script)",1);
/* ]]> */
</script><noscript><img src="http://georift.co.cc/help/server.php?request=track&amp;output=nojcrpt&amp;fbpos=10&amp;fbml=0&amp;fbmt=0&amp;fbmr=0&amp;fbmb=0&amp;fbw=32&amp;fbh=112" width="0" height="0" style="visibility:hidden;" alt="" /></noscript><!-- http://www.LiveZilla.net Tracking Code --><?php
}
?>	
	<div style="position: fixed; bottom: 10px; left: 10px;"><a href="http://dotvps.net"><img src="images/banner.png" /></a></div>
	<div class="container_12">
    	<div class="grid_12" id="header"> 
		<?php
			$output = '<a href="index.php"><img src="images/header.png" name="header_img" id="header_img" /></a>';
			$output = $plugin->runHook("preHeader", $output);
			echo $output;
		?>
        </div>
