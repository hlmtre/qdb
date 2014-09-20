<?
require_once("./header.php");
require_once("./sanitizer.php");

if ($_POST['verb'] == "update") {
	print_r($_POST);
}

if ($_POST['verb'] == "submit") {
	$dirtyquote = $_POST['quotebox'];
	$cleaned = mysql_escape_string($dirtyquote);
	$db = new MySQL();
	$query = "INSERT INTO qdb (quote) VALUES ('".$cleaned."')";
	$return = array();

	if ($db->isConnected()) $return['connected'] = "true";
	else $return['connected'] = "false";


	if ($db->executeSQL($query)) {
		$return['status'] = "success";
	}
	else $return['status'] = "failure";
	if ($return['status'] == "failure") $return['reason'] = $db->getError();

	$id = $db->getLastInsertID();
	$return['id'] = $id;
	
	$return['submitted'] = $cleaned;
}

if ($_POST['verb'] == "upvote") {
	$return = array();
	$return['text'] = "upvoted!";
}

if ($_POST['verb'] == "downvote") {
	$return = array();
	$return['text'] = "downvoted!";
}

echo json_encode($return);
?>
