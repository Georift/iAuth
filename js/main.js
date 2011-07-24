// JavaScript Document

$(document).ready(function(){
	$("tr:odd").addClass("odd");
	$(".wTitle").click(function(){
		$(this).next(".wContent").slideToggle(400);
	});
});