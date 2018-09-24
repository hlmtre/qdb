<?php
require_once("./header.php");
require_once("./sanitizer.php");

function getQuoteTextById($id) {
	if (is_int(intval($_GET['id']))) {
		$db = new MySQL();
		$query = "SELECT quote from qdb WHERE id = " . $_GET['id'];

		$ret = $db->setRunBuild($query);
		return $ret[0]['quote'];
	}
	else {
		exit();
	}
}
?>
