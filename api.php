<?
require_once("./class.MySQL.php");
require_once("./sanitizer.php");

$db = new MySQL();

if ($_GET['q'] == "quotes" && $_GET['n'] == "all") {
  $query = "SELECT q.id as id, q.quote as quote, q.date as date FROM qdb q LEFT JOIN qdb_votes qv ON qv.quote_id = q.id";
  $rows = $db->setRunBuild($query);
  echo json_encode($rows);
}

elseif ($_GET['q'] == "quote" && is_numeric($_GET['id'])) {
  $query = "SELECT q.id as id, q.quote as quote, q.date as date FROM qdb q LEFT JOIN qdb_votes qv ON qv.quote_id = q.id WHERE q.id = ".sanitize($_GET['id']);
  $rows = $db->setRunBuild($query);
  echo json_encode($rows);
}

elseif ($_GET['q'] == "delete" && is_admin($_GET['user'])) {
  if (!is_numeric($_GET['id']) || $_GET['code'] != "IAMASECRETCODEAMA") {
    $ret['success'] = 'false';
    echo json_encode($ret);
    header(':', true, 400);
    return;
  }
  else {
# check first
    $query = "SELECT * FROM qdb WHERE id = ". sanitize($_GET['id']);
    $return = $db->setRunBuild($query);
    if (gettype($return) != "array") {
      $ret['success'] = 'false';
      echo json_encode($ret);
      header(':', true, 400);
    }
    $query = "DELETE FROM qdb WHERE id = " . sanitize($_GET['id']);
    $db->setRunBuild($query);
    $query = "DELETE FROM qdb_votes WHERE quote_id = ".sanitize($_GET['id']);
    $db->setRunBuild($query);
    $ret['success'] = 'true';
    echo json_encode($ret);
  }
}

elseif ($_POST['q'] == "new") {
  $f = submit();
  echo json_encode($f);
}

elseif ($_POST['q'] == "search") {
  if (strlen($_POST['terms']) < 1) {
    $ret['success'] = "false";
    echo json_encode($ret);
    return;
  }
  $query_substring = " LIKE '%";
  foreach (explode(" ", sanitize($_POST['terms'])) as $value) {
    $query_substring = $query_substring.$value;
  }
  $query_substring = $query_substring."%'";
  $query = "SELECT quote, date, id FROM qdb WHERE quote ".$query_substring;
  echo $query;
  $rows = $db->setRunBuild($query);
  echo json_encode($rows);
}
else {
# bad request
  header(':', true, 400);
}

function submit() {
  $dirtyquote = $_POST['quote'];
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
  return $return;
}

#exteremly hackish but i didn't want to bother with another db table right now
function is_admin($user) {
  if ($user == "hlmtre" || $user == "BoneKin")
    return True;
  return False;
}
