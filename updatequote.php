<?
require_once("./util.php");
require_once("./header.php");
require_once("./sanitizer.php");

$mysqli = new mysqli("localhost", "pybot", "pyb07", "pybot");

if ($_POST['supersecretpassword'] == "IAMASECRETCODEAMA")
{
	$sql  = $mysqli->prepare('UPDATE qdb SET quote = ? WHERE id = ?');
	$sql-> bind_param('si', $_POST['quote'], $_POST['quoteid']);

	if ($sql->execute()) {
		header('Location: quote.php?id=' . sanitize($_POST['quoteid'] ));
	}
	else
		echo $db->sError; 

}
?>
