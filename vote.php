<?php
require_once("./class.MySQL.php");
require_once("./sanitizer.php");

$db = new MySQL();
$query = "SELECT votes FROM qdb_votes WHERE quote_id = ".sanitize($_POST['id']);

$result = $db->setRunBuild($query);
$votes = $result[0]['votes'];
if (isset($votes)) {
  $newVote = $votes + sanitize($_POST['vote']);
  $query = "UPDATE qdb_votes SET votes = ".$newVote." WHERE quote_id = ".sanitize($_POST['id']);
  $result = $db->setRunBuild($query);
}
# new, unvoted
else {
  $query = "INSERT INTO qdb_votes (quote_id, votes) VALUES (".sanitize($_POST['id']).", 1)";
  $result = $db->setRunBuild($query);
}
$return = array();
if ($_POST['vote'] == 1)
	$return['text'] = "upvoted!";
else { $return['text'] = "downvoted!"; }

echo json_encode($return);
?>
