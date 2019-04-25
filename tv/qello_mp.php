<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$link=$_GET["link"];
$page_title=urldecode($link);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

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
echo '<h2>'.$page_title.'</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$ua="Mozilla/5.0 (Windows NT 10.0; rv:65.0) Gecko/20100101 Firefox/65.0";
$l="https://qello.com/tv?channel=".urlencode($link);
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://qello.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  $t1=explode('data-psettings="',$html);
  $t2=explode('"',$t1[1]);
  $out="[".$t2[0]."]";
  $out=htmlspecialchars_decode($out,ENT_QUOTES);
  $out=html_entity_decode($out,ENT_QUOTES);
  //echo $out;
  $x=json_decode($out,1);
  //print_r ($x);
  $r=$x[0]["playlistsongsarr"];
for ($k=0;$k<count($r);$k++) {
  $title=$r[$k]["title"];
  $image=$r[$k]["image"];
  $link=$r[$k]["sources"][1]["file"];
  if (strpos($link,"http") === false) $link="";
  $c="intent:".$link."#Intent;type=video/mp4;package=com.mxtech.videoplayer.".$mx.";S.title=".urlencode($title).";end";
  if ($link && $title) {
  if ($n==0) echo '<TR>';
  echo '<td class="mp" align="center" width="25%"><a href="'.$c.'" target="_blank"><img src="'.$image.'" width="200px" height="106px"><BR>'.$title.'</a></TD>';

  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
echo "</table>";
?>
</div>
<br>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
