<?php
require_once("./class.MySQL.php");
require_once("./sanitizer.php");
require_once("./util.php");
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<?php require_once("./script.php") ?>
</head>
<body>
<div id="content">
<?php

session_start();
if (!is_numeric($_GET['id'])) {
  http_response_code(400);
  echo "Bad ID.";
  exit;
}

$db = new MySQL();
$i = 1;
$query = "SELECT * FROM qdb WHERE id = " . sanitize($_GET['id']);
$result = $db->setRunBuild($query);
#print_r($result);
if (! $result) {
  http_response_code(404);
  echo "Bad ID.";
  exit;
}
?>
<span id='header'>
<a href="/">Back to quotes list</a>
<a href="./update.php?id=<?php echo $_GET['id'] ?>">Update</a>
<?php

if (isset($_SESSION['action_message'])) {
  echo $_SESSION['action_message'];
  unset($_SESSION['action_message']);
}

echo "</span>";

foreach ($result as $key => $value) {
  echo "<div class='quoteContainer'>\n";
  echo "<div class='quoteIDBox'>\n";
  echo "<a href='./quote.php?id=".$value['id']."'>#".$value['id']."</a>";
  echo "<span class='quoteDate'>";
  echo $value['date'];
  echo "</span>";
  echo "<div class='downArrow' id='".$value['id']."'>";
  echo "&darr;";
  echo "</div>";
  echo "<div class='upArrow' id='".$value['id']."'>";
  echo "&uarr;";
  echo "</div>";
  echo "</div>"; // quote id
  echo "<div class='quote' id='quote".$value['id']."'>\n";
  echo "<div class='quote_interior' name='".$value['id']."'>";
  $value['quote'] = str_replace("<","< ", $value['quote']);
  echo str_replace("\n", "<br />", autolink($value['quote']));
  echo "<br />\n";
  echo "</div>";
  echo "</div>\n"; // end quote div
  echo "</div>\n"; // end container div
}
?>
<script>

$(".upArrow").hover(
function() {
  $(this).css('color','green');
},
function() {
  $(this).css('color','');
});
$(".downArrow").hover(
function() {
  $(this).css('color','red');
},
function() {
  $(this).css('color','');
});


$(".upArrow").click(function(event) {
  var p = {};
  p['id'] = $(this).attr('id');
  p['verb'] = "upvote";
  $.post(
    'ajax_functions.php',
    p,
    function(data) {
      var jobj = jQuery.parseJSON(data);
      event.target.innerHTML = jobj.text;
    }
  );
});
$(".downArrow").click(function(event) {
  console.log("foobar");
  var p = {};
  p['id'] = $(this).attr('id');
  p['verb'] = "downvote";
  $.post(
    'ajax_functions.php',
    p,
    function(data) {
      var jobj = jQuery.parseJSON(data);
      event.target.innerHTML = jobj.text;
      console.log(jobj);
    }
  );
});

function getRandomInt(min,max){
    if((typeof min === "number") && Math.floor(min) === min && (typeof max === "number") && Math.floor(max) === max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }else{
        //logger.error('min or max number is not a valid number');
        throw 'min or max number is not a valid number';
    }
}
</script>
<?php
echo "copyright LOLOLOL valve corporation";
echo "</div>"; // end content div
echo "</body></html>";

?>
</body>
</html>
