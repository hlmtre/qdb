<html>
<head>
<?php
require_once("./header.php");
require_once("./util.php");

if (strlen($_GET['id']))
	$quote = getQuoteTextById($_GET['id']);
else
	header('Location: /');

?>
<script src="https://code.jquery.com/jquery-1.8.3.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" href="jquery-ui.css">
</head>
<body>
<div id="headerLine"></div>
Update quote
<form action="updatequote.php" method="post" id="quoteUpdate" name="quoteform">
<textarea rows="10" cols="150" id="quote" name="quote">
<?php echo $quote ?>
</textarea>
<br />
Super secret password: <input type="password" id="password" name="supersecretpassword">
<br />
<input type="submit" id="submitButton" value="Submit" name="submit">
<input type="hidden" id="verb" value="update" name="verb">
<input type="hidden" id="quoteid" value="<?php echo $_GET['id'] ?> " name="quoteid">
</form>
</body>
</html>
