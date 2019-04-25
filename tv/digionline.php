<!DOCTYPE html>
<html>
   <head>

      <meta charset="utf-8">
      <title>Digi online</title>
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
//http://balancer.digi24.ro/streamer.php?&scope=digisport-1&key=8efad55b27ff79e42135cee5c7b78e1d&outputFormat=json&type=abr&quality=hq
$l="http://www.digi-online.ro/tv/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,"http://www.digi-online.ro/");
  //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; U; Android 2.1-update1; ru-ru; GT-I9000 Build/ECLAIR) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17');
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  $html=curl_exec($ch);
  curl_close($ch);
$videos = explode('<li><a href="/tv', $html);
unset($videos[0]);
$n=0;
$videos = array_values($videos);
  foreach($videos as $video) {
  $t1=explode('title="',$video);
  $t2=explode('"',$t1[1]);
  $title=$t2[0];
    $title1=strtolower($title);
    $t1=explode("(",$title1);
    $title1=trim($t1[0]);
    $title1=str_replace(" ","-",$title1);
    $title=ucwords($title);
  $t1=explode("/",$video);
  $t2=explode("/",$t1[1]);
  $file=strtolower($title);
  $file=str_replace(" ","",$file);
  //$file=str_replace("+","",$t2[0]);
  
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=digi&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=digi&mod=direct";
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
if ($n<6) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div></body>
</html>
