<?
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
<?
# doesn't need to be sanitized; not going to the database
#print_r(sanitize($_GET));
if ($_GET['sort'] == "vote")
  echo '<a href="./qdb.php?sort=date">Sort by date</a>';
else
  echo '<a href="./qdb.php?sort=vote">Sort by vote</a>';

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
	echo "<button class='quotePlayBtn playBtn' id='".$value['id']"'>&#9658;</button><button class='quoteCancelBtn playBtn'>&#9632;</button>";
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
<script src="./lodash.js"></script>
<script src="http://code.responsivevoice.org/responsivevoice.js"></script>
<script>
_u = _.noConflict();

var playStatus = true;
var voiceArray = ['UK English Female',
				'US English Female',
				'UK English Male',
				'Spanish Female',
				'Australian Female'];
var pitchArray =[0.5,0.6,0.7,0.8,0.9,1,1.1,1.2,1.3,1.4,1.5,1.6];

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

$(".quoteCancelBtn").click(function(){
	responsiveVoice.cancel();
	playStatus = false;
});

$(".quotePlayBtn").click(function(){
	var quoteNum = $(this).attr('id');
	var quoteText = $('#quote'+quoteNum).html();
	var quoteArray = quoteText.split('<br>');

	var nameArray = [];
	var nameToVoiceArray = [{"name": "default", "voice":"UK English Male", "pitch":1}];
	var textVoiceArray = [];

	_u.forEach(quoteArray, function(value, key){
		var regExp = /&lt;(.*?)&gt;/;
		var match = regExp.exec(value);		
		var text = stripCharacters(value);
		if (_u.isNull(match)){
			textVoiceArray.push({'text':text,'name':'default'});
		}else{
			nameArray.push(match[1].trim());
			textVoiceArray.push({'text':text,'name':match[1].trim()});
		}
	});
	nameArray = _u.uniq(nameArray);
	_u.forEach(nameArray, function(value, key){
		var randomVoice = voiceArray[getRandomInt(0,voiceArray.length-1)];
		var randomPitch = pitchArray[getRandomInt(0,pitchArray.length-1)];
		var nameObj = {'name':value, 'voice':randomVoice, 'pitch':randomPitch};
		nameToVoiceArray.push(nameObj);
	});

	_u.forEach(textVoiceArray, function(value,key){
		var matchName = _u.find(nameToVoiceArray, ['name', value.name]);
		textVoiceArray[key] = _u.merge(value, matchName);
	});

	_u.remove(textVoiceArray, function(n) {
  		return _u.isEmpty(n.text);
	});

	playNextLine(0);

	function playNextLine(key){
		if(key != textVoiceArray.length && playStatus == true){
			responsiveVoice.speak(textVoiceArray[key].text, textVoiceArray[key].voice, {pitch:textVoiceArray[key].pitch, onend: checkPlayStatus(key)});
		}else{
			playStatus = true;
			return;
		}
	}

	function checkPlayStatus(key){
		_u.delay(function(key) {
			if(responsiveVoice.isPlaying()) {
  				checkPlayStatus(key);
			}else{
				playNextLine(key+1);
			}
		}, 100, key);
	}

	function stripCharacters(value){
		var text = value.replace(/&lt;(.*?)&gt;/gi,'').trim();
			text = text.replace(/&gt;/,'');
			text = text.replace(/<a(.*?)<\/a>/gi, "link").trim();
			text = text.replace(/<\/a>/gi, "").trim();
			text = text.replace(/<a(.*?)>/gi, "").trim();
			text = text.replace(/\*/gi,'').trim();
			text = text.replace(/Ã¢â‚¬â„¢/gi,"'").trim();
			text = text.replace(/(?:\r\n|\r|\n)/gi, '').trim();
			text = text.replace(/hlmtre/gi, 'hellmighter').trim();
			text = text.replace(/dorj/gi, 'doorge').trim();
			text = text.replace(/muh/gi, 'mah').trim();
			text = text.replace(/\( ͡° ͜ʖ ͡°\)/gi, 'I want da booty');
			text = text.replace(/\[(.*?)\]/gi,'').trim();
			text = text.replace(/\((.*?)\)/gi,'').trim();
			text = text.replace(/[^a-zA-Z0-9' ]/gi, " ").trim();
			return text;
	}
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
<?
echo "copyright LOLOLOL hlmtre 2015/2016";
echo "</div>"; # end content div
echo "</body></html>";

?>
