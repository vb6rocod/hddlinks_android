<!DOCTYPE html>
<?php
include ("../common.php");
$pg_title=urldecode($_GET["title"]);
$link_pg=$_GET["link"];
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $pg_title; ?></title>
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
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance
  on();
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
<link rel="stylesheet" type="text/css" href="../custom.css" />
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
$n=0;
echo '<h2>'.$pg_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
echo "<TR>"."\n\r";
$l="http://tvron.net/".$link_pg;

  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $html;

$videos = explode('div id=si', $html);
//echo count($videos);
//print_r ($videos);
if (count($videos) > 1) {
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    $t1=explode(' ',$video);
    $l="http://tvron.net/cache/frame/".$link_pg."/s".$t1[0].".html";

    //echo $l;
    $ch = curl_init($l);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
    curl_setopt($ch,CURLOPT_REFERER,$l);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h1 = curl_exec($ch);
    curl_close ($ch);
    //echo $h;

    //echo $h1;
    if (preg_match('/((sop\:)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $tip="sop";
      $link=$m[0];
      $title="sop";
      $link3="sop_link.php?file=".$link."&title=".urlencode($title);
      if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link3.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/((acestream\:)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $tip="acestream";
      $title="acestream";
      $link=$m[0];
      $link2="intent:".$link."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
      if ($flash == "mp" || $flash=="chrome")
      echo '<TD class="mp">'.'<a href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4|flv|ts)))/', $h1, $m)) {
      $file=$m[1];
      $from="";
      $mod="direct";
      $title=$m[2];
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=".$mod;
    if ($flash != "mp")
    echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    else
    echo '<TD class="mp">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a></TD>';
   }
}
} else {

    $h1=$html;
    if (preg_match('/((sop)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $title="sop";
      $link=$m[0];
      $link3="sop_link.php?file=".$link."&title=".urlencode($title);
      if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link3.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/((acestream)[\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*)/', $h1, $m)) {
      $title="acestream";
      $link=$m[0];
      $link2="intent:".$link."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
      if ($flash == "mp" || $flash=="chrome")
      echo '<TD class="mp">'.'<a href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
      else
      echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    } elseif (preg_match('/([http|https][\.\d\w\-\.\/\\\:\?\&\#\%\_\,]*(\.(m3u8|mp4|flv|ts)))/', $h1, $m)) {
      $file=$m[1];
      $from="";
      $title=$m[2];
      $mod="direct";
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($pg_title))."&from=".$from."&mod=".$mod;
    if ($flash != "mp")
    echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    else
    echo '<TD class="mp">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title.'</a></TD>';
   }
}
echo "</TR>";
echo "</table>";
//play(127,0, 'telekom-sport-online')
?>
<br></div>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
