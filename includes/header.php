<?php
if (IN_SCRIPT != 1){
	die("This script must not be accessed directly.");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="css/960.css">
    <link rel="stylesheet" type="text/css" href="css/reset.css">
    <link rel="stylesheet" type="text/css" href="css/main.css"/>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script language="javascript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script> 
    <script type="text/javascript">
		$(document).ready(function(){
			$("tr:odd").addClass("odd");
		});
	</script>
    <title>iAuth</title>
</head>
<body>
	<div class="container_12">
    	<div class="grid_12" id="header"> 
        	<a href="index.php"><img src="images/header.png" name="header_img" id="header_img" /></a>
        </div>
