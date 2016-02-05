<html>
<head>
<?
require_once("./header.php");
require_once("./util.php");

if (strlen($_GET['id']))
	$quote = getQuoteTextById($_GET['id']);
else
	header('Location: /');

?>
<script src="./jquery-1.8.3.min.js"></script>
<script src="./jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="jquery-ui.css">
</head>
<body>
<div id="headerLine"></div>
Update quote
<form action="updatequote.php" method="post" id="quoteUpdate" name="quoteform">
<textarea rows="10" cols="150" id="quote" name="quote">
<? echo $quote ?>
</textarea>
<br />
Super secret password: <input type="password" id="password" name="supersecretpassword">
<br />
<input type="submit" id="submitButton" value="Submit" name="submit">
<input type="hidden" id="verb" value="update" name="verb">
<input type="hidden" id="quoteid" value="<? echo $_GET['id'] ?> " name="quoteid">
</form>
</body>
</html>
