<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
$width="200px";
$height=intval(200*(176/268))."px";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>arconaitv</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
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
      document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
</script>
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
/////////////////////////////////////////////////////////////////////////
$ua = $_SERVER['HTTP_USER_AGENT'];
  if ($flash=="flash")
  $user_agent     =   $_SERVER['HTTP_USER_AGENT'];
  else {
  $user_agent = 'Mozilla/5.0(Linux;Android 7.1.2;ro;RO;MXQ-4K Build/MXQ-4K) MXPlayer/1.8.10';
  $user_agent = 'Mozilla/5.0(Linux;Android 10.1.2) MXPlayer';
  }
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";

echo '<h2>arconaitv</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$link="https://www.arconaitv.us/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//$html = file_get_contents($link);
$videos = explode("<div class='stream-category'>", $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    //echo '<table border="1px" width="100%">'."\n\r";
    $t1=explode('<',$video);
    $cat=$t1[0];
    //echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=4"><font size="4">'.$cat.'</font></TD></TR><TR>';
    $vids = explode("<a", $video);
    unset($vids[0]);
    $vids = array_values($vids);

    foreach($vids as $vid) {
      $t1=explode("stream.php?id=",$vid);
      $t2=explode("'",$t1[1]);
      $link=$t2[0];
      $link="https://www.arconaitv.us/stream.php?id=".$link;
      $t1=explode("title='",$vid);
      $t2=explode("'",$t1[1]);
      $title=$t2[0];
    //$link=str_replace("tiny-","",$image);
    //$link=str_replace("jpg","mp4",$link);
    $link1="direct_link.php?link=".$link."&title=".urlencode(fix_t($title))."&from=arconaitv&mod=direct";
    $l="link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title))."&from=arconaitv&mod=direct";
  if ($link && $title) {
  if ($n==0) echo '<TR>';
  if ($flash != "mp")
  echo '<td class="mp" align="center" width="25%"><a href="'.$link1.'" target="_blank">'.$title.'</a></TD>';
    else
  echo '<td class="mp" align="center" width="25%">'.'<a onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".''.$title.'</a></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
}
echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>