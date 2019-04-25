<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>tvpemobil</title>
      <link rel="stylesheet" type="text/css" href="../custom.css" />
     <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<div id="mainnav">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$f=$base_pass."tvpemobil.txt";
if (file_exists($f)) {
$h=file_get_contents($f);
$t1=explode("|",$h);
$user=$t1[0];
if (sizeof ($t1) > 1 )
	$pass=$t1[1];
} else {
$user="";
$pass="";
}
if ($user)
echo '<h2>tvpemobil</h2>';
else
echo '<h2>tvpemobil (necesita cont - vezi setari)</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>tvpemobil</b></TD></TR>';
//http://adevarul.ro/video-center/
$n=0;

$l="http://tvpemobil.net/wap/logare.php?nume=".$user."&parola=".$pass;
$ua     =   $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."tvmobil.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER, "http://tvpemobil.net");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

  $t1=explode('sid=',$html);
  $t2=explode('"',$t1[1]);
  $sid=$t2[0];
$l="http://tvpemobil.net/wap/index.php?action=main&sid=".$sid;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
$t1=explode("Tv Online:",$html);
$t2=explode("Total Canale",$t1[1]);
$html=$t2[0];
$videos = explode('href="', $html);

unset($videos[0]);
$videos = array_values($videos);
///gallery_video/thumbs3_212949.jpg
foreach($videos as $video) {
  $t1=explode('"',$video);
  $link=$t1[0];
  $link=str_replace("&amp;","&",$link);
  $t1=explode(">",$video);
  $t2=explode('<',$t1[1]);
  $title=trim($t2[0]);
  $title=html_entity_decode($title,ENT_QUOTES,'UTF-8');

    if ((strpos($link,"sid=") !== false)) {
    $link="tvpemobil.php?page=1&link=".urlencode($link)."&title=".urlencode($title);
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div>
<BODY>
</HTML>
