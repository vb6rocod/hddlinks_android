<!doctype html>
<?php
$mcloud=urldecode($_GET['id']);
$title=urldecode($_GET['title']);
$tip=$_GET['tip'];
//echo $mcloud;
//die();
echo '
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
    <script src="https://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script>
    $( document ).ready(function() {
       // alert( "document loaded" );
    });

    $( window ).on( "load", function() { ';
if ($tip=="flash") {
echo '

const myTimeout = setTimeout(myGreeting, 500);

function myGreeting() {
      document.getElementById("mytest1").href="link1.php?file='.urlencode($mcloud).'&title='.urlencode($title).'";
      document.getElementById("mytest1").click();
}

';
} else {
echo
'

function myGO() {
  link1="'.urlencode($mcloud).'";
  link="'.urlencode($title).'";
  on();
  var request =  new XMLHttpRequest();
  var the_data = "link=" + link1 + "&title=" + link;
  var php_file="link1.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      off();
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
      history.go(-1);
    }
  }
}
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
const myTimeout = setTimeout(myGO, 500);

';
}

echo '
    });
    </script>
</head>
<body>';
echo "<a href='' id='mytest1'></a>";
echo '<iframe id="mcloud" src="mcloud.php?id='.$mcloud.'" style="display: none;"></iframe>';
echo '
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
';

?>
