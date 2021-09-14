<?php
require_once("./class.MySQL.php");
require_once("./sanitizer.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon"
      type="image/png"
			href="favicon.ico" />
</head>
<body>
<div id="content">
<a href="./submit.php">Submit a new quote</a>
<?php
# doesn't need to be sanitized; not going to the database
#print_r(sanitize($_GET));
if ($_GET['sort'] == "vote")
  echo '<a href="/?sort=date">Sort by date</a>';
else
  echo '<a href="/?sort=vote">Sort by vote</a>';

echo '<span id="search">Search: press Ctrl+F</span>';

function  autolink($message) {
	//Convert all urls to links
	$message = preg_replace('#([\s|^])(www)#i', '$1http://$2', $message);
	$pattern = '#((http|https|ftp|telnet|news|gopher|file|wais):\/\/[^\s]+)#i';
	$replacement = '<a href="$1" target="_blank">$1</a>';
	$message = preg_replace($pattern, $replacement, $message);

	return $message;
}
$db = new MySQL();
if (sanitize($_GET['sort']) == "vote")
  $query = "SELECT * FROM qdb q LEFT JOIN qdb_votes qv ON qv.quote_id = q.id ORDER BY qv.votes DESC";
else
  $query = "SELECT * FROM qdb q LEFT JOIN qdb_votes qv ON qv.quote_id = q.id ORDER BY q.id DESC";

$rows = $db->setRunBuild($query);
$i = 1;
foreach ($rows as $key => $value) {
	echo "<div class='quoteContainer'>\n";
	echo "<div class='quoteIDBox'>\n";
	echo "<a href='./quote.php?id=".$value['id']."'>#".$value['id']."</a>";
  echo " votes: ";
  echo "<span id='voteValue".$value['id']."'>";
  if (isset($value['votes'])) echo $value['votes']; else echo "0";
  echo "</span>"; # end voteValue span
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
	echo "</div>"; // end quote_interior
	echo "</div>\n"; // end quote div
	echo "</div>\n"; // end container div
}
?>
<script src="./jquery-1.8.3.min.js"></script>
<script src="./jquery-ui.js"></script>
<script src="./lodash.js"></script>
<script>
// responsive voice removed because they added an api key requirement
// and i can't be arsed

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
	var id = p['id'] = $(this).attr('id');
  // incremement the votevalue
  $("#voteValue"+id)[0].innerHTML = parseInt($("#voteValue"+id)[0].innerHTML) + 1;
	p['vote'] = "1";
	$.post(
		'vote.php',
		p,
		function(data) {
			var jobj = jQuery.parseJSON(data);
			event.target.innerHTML = jobj.text;
		}
	);
  $(this).unbind('click');
  $(this).unbind('hover');
});
$(".downArrow").click(function(event) {
	var p = {};
	var id = p['id'] = $(this).attr('id');
  // decrement vote value
  $("#voteValue"+id)[0].innerHTML = parseInt($("#voteValue"+id)[0].innerHTML) - 1;
	p['vote'] = "-1";
	$.post(
		'vote.php',
		p,
		function(data) {
			var jobj = jQuery.parseJSON(data);
			event.target.innerHTML = jobj.text;
		}
	);
  $(this).unbind('click');
  $(this).unbind('hover');
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
echo "copyright LOLOLOL hlmtre 2015/2016";
echo "</div>"; # end content div
echo "</body></html>";

?>
