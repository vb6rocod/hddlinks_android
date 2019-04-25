<!DOCTYPE html>
<?php
include ("../common.php");
$cookie=$base_cookie."antena.dat";
$file = $_GET["file"];
$title = $_GET["title"];
$title=str_replace("\'","'",$title);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
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
  on();
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
    off();
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
<style>
#overlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 2;
    cursor: pointer;
}

#text{
    position: absolute;
    top: 50%;
    left: 50%;
    font-size: 50px;
    color: white;
    transform: translate(-50%,-50%);
    -ms-transform: translate(-50%,-50%);
}
</style>
</head>
<body>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
   <a href='' id='mytest1'></a>
   <div id="mainnav">
<H2></H2>
<table border="1px" width="100%">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

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
echo '<h2>'.str_replace("\'","'",$title).'</H2>';
echo '<table border="1px" width="100%">'."\n\r";

$link="http://antenaplay.ro/".$file;
//$id1 = substr(strrchr($file, "/"), 1);
//echo $link;
//$html=file_get_contents($link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "http://antenaplay.ro/");
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$videos=explode('<div class="span3 spanX',$html);
unset($videos[0]);
$videos = array_values($videos);
$n=0;
foreach($videos as $video) {
    if ($n == 0) echo "<TR>"."\n\r";
    $cat=str_between($video,'<a href="','"');
	$title=str_between($video,'title="','"');
	$title=trim(str_between($video,'<p>','</p>'));
	$data=trim(str_between($video,'</p>','</div'));
	$title=$title." - ".$data;
	//$data=str_between($video,'class="sname" style="display:none;">','</div>');
	//$data=str_replace("<div>","",$data);
	//$data=str_replace("<p>									","",$data);
	//$data=str_replace("							   								</p>","",$data);
	$image=str_between($video,'real-src="','"');
	if (!$image) $image=str_between($video,'src="','"');
    $link="direct_link.php?link=".urlencode($cat).'&title='.$title."&from=antenaplay&mod=direct";
    //$link1="direct_link.php?link=".$cat."&title=".urlencode($title)."&from=antenaplay&mod=direct";
    $l="link=".urlencode(fix_t($cat))."&title=".urlencode(fix_t($title))."&from=antenaplay&mod=direct";
  if ($title && $cat && strpos($cat,$file) !== false) {
	if ($n == 0) echo "<TR>"."\n\r";
echo '<TD><table border="0px">';
if ($flash != "mp")
echo '<TD align="center"><a href="'.$link.'&title='.$title.'" target="_blank"><img src="'.$image.'" width="171" height="96"></a><BR><a href="'.$link.'" target="_blank"><b>'.$title.'</b></a></TD>';
    else
  echo '<td align="center" width="20%"><font size="4">'.'<a onclick="ajaxrequest('."'".$l."', '"."')".'"'." style='cursor:pointer;'>".'<img src="'.$image.'" width="171px" height="96px"><BR><font size="4">'.$title.'</a></font></TD>';
echo '
</TABLE></TD>
';
$n++;
    if ($n > 4) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
}
 if ($n<0) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div>
<br>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
