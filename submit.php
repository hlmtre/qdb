<?php
require_once("./header.php");
?>
<body>
<div id="content"</div>
<div id="headerLine"></div>
<a href="./qdb.php">Back to quotes list</a> Submit quote
<form method="post" id="quoteForm" action="ajax_functions.php">
  <textarea rows="5" id="quotebox" name="quotebox" form="quoteForm" required="required" placeholder="Quote here..."></textarea>
  <br/>
  <span id="warning">Do not submit web crawler gibberish, links to warez, or other dated references to objectionable material.</span>
  <input type="submit" name="submit" value="Submit" name="post">
  <input type="hidden" id="token" name ="token">
  <input type="hidden" id="verb" name="verb" value="submit">
  <?php /* invisible input field required to get the intended function to ajax_functions.php */ ?>
</form>

<!-- gotta put the jquery binding after the element is created -->
<script type="text/javascript">
  var t = '6LffaIojAAAAACdpwvpoGbkmEpMY8IrEN54Y3p2t';
  grecaptcha.ready(function() {
    grecaptcha.execute(t, {action: 'homepage'}).then(function(token) {
      // console.log(token);
      document.getElementById("token").value = token;
    });
    // refresh token every minute to prevent expiration
    setInterval(function(){
      grecaptcha.execute(t, {action: 'homepage'}).then(function(token) {
        //console.log( 'refreshed token:', token );
        document.getElementById("token").value = token;
        });
      }, 60000);

    });
    /*
  function submit_quote() {
    event.preventDefault();
    var foo = $("#quotebox").val();
    var p = {};
    p['verb'] = "submit";
    p['quotebox'] = foo;
    $.post(
      'ajax_functions.php',
      p,
      function(data) {
        var jobj = jQuery.parseJSON(data);
        if (jobj.status == "success")
          $("#headerLine").html("<span id='success' style='color:green'>Successfully submitted.</span>");
        else if (jobj.status == "failure")
          $("#headerLine").html("<span id='failure' style='color:red'>Failure for some reason. Sorry.</span>");
        else
          $("#headerLine").html("span id='uh oh'>lol wut</span>");

        setTimeout(function() {
          $("#headerLine").hide('blind', {}, 500)
          window.location.href="/#"+jobj.id;
        }, 2000);
      }
    );
  }

    */
</script>
</body>
</html>
