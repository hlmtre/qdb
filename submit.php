<html>
<head>
<?php
require_once("./header.php");

?>
<script src="./jquery-1.8.3.min.js"></script>
<script src="./jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div id="headerLine"></div>
Submit quote
<form action="ajax_functions.php" method="post" id="quoteForm">
<textarea rows="5" cols="50" id="quotebox">
</textarea>
<input type="submit" id="submitButton" value="Submit">
</form>
<!-- gotta put the jquery binding after the element is created -->
<script type="text/javascript">
$(document).ready(function() {
	$("#quoteForm").submit(function(event) { 
		
		var foo = $("#quotebox").val();
		var p = {};
		p['verb'] = "submit";
		p['quotebox'] = foo;
		event.preventDefault();
		$.post(
			'ajax_functions.php',
			p, 
			function(data) {
				var jobj = jQuery.parseJSON(data);
				if (jobj.status == "success")
					$("#headerLine").html("<span id='success' style='color:green'>Successfully submitted.</span>");
				else if (jobj.status == "failure")
					$("#headerLine").html("<span id='failure' style='color:red'>Failure for some reason. Sorry.</span>");

				setTimeout(function() {
					$("#headerLine").hide('blind', {}, 500)
					window.location.href="/#"+jobj.id;
				}, 2000);
			}
		);
	});
}); // end document.ready
</script>
</body>
</html>
