<?
require_once("./class.MySQL.php");
require_once("./sanitizer.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<a href="./submit.php">Submit a new quote</a>
<?
if (sanitize($_GET['sort']) == "vote")
  echo '<a href="./qdb.php?sort=date">Sort by date</a>';
else
  echo '<a href="./qdb.php?sort=vote">Sort by vote</a>';
  
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
  $query = "SELECT * FROM qdb q LEFT JOIN qdb_votes qv ON qv.quote_id = q.id ORDER BY q.date DESC";

$rows = $db->setRunBuild($query);
$i = 1;
foreach ($rows as $key => $value) {
	echo "<div class='quoteContainer'>\n";
	echo "<div class='quoteIDBox'>\n";
	echo "<a href='./quote.php?id=".$value['id']."'>#".$value['id']."</a>";
  echo " votes: ";
  if (isset($value['votes']) ) echo "<span id='voteValue".$value['id']."'>" .$value['votes']; else echo "0";
  echo "</span>"; # end voteValue span
	echo "<div class='downArrow' id='".$value['id']."'>";
	echo "&darr;";
	echo "</div>";
	echo "<div class='upArrow' id='".$value['id']."'>";
	echo "&uarr;";
	echo "</div>";
	echo "</div>"; // quote id
	echo "<div class='quote' id='quote".$value['id'].">\n";
	echo "<a name='".$value['id']."'>";
	$value['quote'] = str_replace("<","< ", $value['quote']);
	echo str_replace("\n", "<br />", autolink($value['quote']));
	echo "<br />\n";
	echo "</a>";
	echo "</div>\n"; // end quote div
	echo "</div>\n"; // end container div
}
?>
<script src="./jquery-1.8.3.min.js"></script>
<script src="./jquery-ui.js"></script>
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
	p['id'] = $(this).attr('id');
	p['vote'] = "-1";
	$.post(
		'vote.php',
		p, 
		function(data) {
			var jobj = jQuery.parseJSON(data);
			event.target.innerHTML = jobj.text;
			console.log(jobj);
		}
	);
  $(this).unbind('click');
  $(this).unbind('hover');
});
</script>
<?
echo "copyright LOLOLOL valve corporation";
echo "</body></html>";

?>
