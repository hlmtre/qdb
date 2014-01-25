<?
require_once("./class.MySQL.php");
require_once("./sanitizer.php");
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<a href="./qdb.php">Back to quotes list</a>
<?
function  autolink($message) { 
	//Convert all urls to links
	$message = preg_replace('#([\s|^])(www)#i', '$1http://$2', $message);
	$pattern = '#((http|https|ftp|telnet|news|gopher|file|wais):\/\/[^\s]+)#i';
	$replacement = '<a href="$1" target="_blank">$1</a>';
	$message = preg_replace($pattern, $replacement, $message);

	/* Convert all E-mail matches to appropriate HTML links */
	//$pattern = '#([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.';
	//$pattern .= '[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)#i';
	//$replacement = '<a href="mailto:\\1">\\1</a>';
	//$message = preg_replace($pattern, $replacement, $message);
	return $message;
}
$db = new MySQL();
$i = 1;
$query = "SELECT * FROM qdb WHERE id = " . sanitize($_GET['id']);
$result = $db->setRunBuild($query);
#print_r($rows);
foreach ($result as $key => $value) {
	echo "<div class='quoteContainer'>\n";
	echo "<div class='quoteIDBox'>\n";
	echo "<a href='./quote.php?id=".$value['id']."'>#".$value['id']."</a>";
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
</script>
<?
echo "copyright LOLOLOL valve corporation";
echo "</body></html>";

?>
</body>
</html>