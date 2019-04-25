<!DOCTYPE html>
<html>
   <head>

      <meta charset="utf-8">
      <title>Digi TV</title>
      <link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

      <script type="text/javascript" src="../jquery.fancybox.js?v=2.1.5"></script>
      <link rel="stylesheet" type="text/css" href="../jquery.fancybox.css?v=2.1.5" media="screen" />

<script type="text/javascript">
// create the XMLHttpRequest object, according browser
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

// sends data to a php file, via POST, and displays the received answer
function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
    //alert (request.responseText);
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function prog(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  var the_data = link;
  var php_file='prog.php';
  request.open('POST', php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
      alert (request.responseText);
    }
  }
}
</script>
<script type="text/javascript">
$(document).ready(function() {
	$(".various").fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none'
	});
	$('.fancybox').fancybox();
 $("html").niceScroll({styler:"fb",cursorcolor:"#000"});
});
</script>
  </head>
   <body>
   <a href='' id='mytest1'></a>
   <div id="mainnav">
<h2>Digi Online</H2>

<table border="1px" width="100%">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
$n=0;
$m3uFile="Digi.m3u";
$m3uFile = file($m3uFile);
foreach($m3uFile as $key => $line) {
  if(strtoupper(substr($line, 0, 7)) === "#EXTINF") {
    $t1=explode(",",$line);
    $title=trim($t1[1]);
    $file = trim($m3uFile[$key + 1]);
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=digifree&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=digifree&mod=direct";
    if (strpos($link,"html")=== false) {
    if ($n == 0) echo "<TR>"."\n\r";
    if ($flash != "mp")
    echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
    else
    echo '<TD style="text-align:center"><font size="4">'.'<a onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".$title.'</a></font></TD>';
    $n++;
    $l="link=".urlencode(fix_t($title));
    echo '<td style="text-align:right;width:5px"><a onclick="prog('."'".$l."', '"."')".'"'." style='cursor:pointer;'>"."PROG".'</a></font></TD>';
    $n++;
    if ($n > 5) {
     echo '</TR>'."\n\r";
     $n=0;
    }
  }
  }
}
if ($n<6) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div></body>
</html>
