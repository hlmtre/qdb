<?php
require_once("./header.php");
require_once("./sanitizer.php");
require_once('./recaptchakey.php');

if ($_POST['verb'] == "update") {
  print_r($_POST);
}

if ($_POST['verb'] == "submit") {
# BEGIN Setting reCaptcha v3 validation data
  $url = "https://www.google.com/recaptcha/api/siteverify";
  $data = [
    'secret' => $recaptcha_secret_key,
    'response' => $_POST['token'],
    'remoteip' => $_SERVER['REMOTE_ADDR']
  ];

  $options = array(
    'http' => array(
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
    )
  );

# Creates and returns stream context with options supplied in options preset 
  $context  = stream_context_create($options);
# file_get_contents() is the preferred way to read the contents of a file into a string
  $response = file_get_contents($url, false, $context);
# Takes a JSON encoded string and converts it into a PHP variable
  $res = json_decode($response, true);
# END setting reCaptcha v3 validation data

    // print_r($response); 
# NOTE:
# Post form OR output alert and bypass post if false. Score conditional is optional
# since the successful score default is set at >= 0.5 by Google. Some developers want to
# be able to control score result conditions, so I included that in this example.

  $return = array();
  if ($res['success'] == true && $res['score'] >= 0.5) {
    $dirtyquote = $_POST['quotebox'];
    $db = new MySQL();
    //$cleaned = mysqli_real_escape_string($db->getDB(), $dirtyquote);
    $cleaned = filter_input(INPUT_POST, 'quotebox', FILTER_SANITIZE_STRING);
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
    $_SESSION['action_message'] = "<div id='success'>Successfully submitted.</div>";
    $_SESSION['recaptcha_score'] = $res['score'];
    header('Location: ./quote.php?id='.$return['id']);
  } 
  else {
    $return['status'] = ['failure'];
    $_SESSION['action_message'] = "<div id='failure'>Failed reCaptcha check.</div>";
    header('Location: ./submit.php');
  }
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
