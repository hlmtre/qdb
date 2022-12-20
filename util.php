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
function  autolink($message) {
  //Convert all urls to links
  $message = preg_replace('#([\s|^])(www)#i', '$1http://$2', $message);
  $pattern = '#((http|https|ftp|telnet|news|gopher|file|wais):\/\/[^\s]+)#i';
  $replacement = '<a href="$1" target="_blank">$1</a>';
  $message = preg_replace($pattern, $replacement, $message);

  return $message;
}

?>
