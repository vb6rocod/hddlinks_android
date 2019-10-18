<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Collection of 6000+ publicly available IPTV channels from all over the world.</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>IPTV channels from all over the world.</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//http://adevarul.ro/video-center/
$n=0;
$l="https://iptv-org.github.io/iptv/index.m3u";
//$l="https://raw.githubusercontent.com/freearhey/iptv/master/index.m3u";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$m3uFile=explode("\n",$html);
foreach($m3uFile as $key => $line) {
  if(strtoupper(substr($line, 0, 7)) === "#EXTINF") {
    $t1=explode(",",$line);
    $title=trim($t1[1]);
    $file = trim($m3uFile[$key + 1]);
    //$file = "https://raw.githubusercontent.com/freearhey/iptv/master/".$file;
    $file="https://iptv-org.github.io/iptv/".$file;
    $link="playlist.php?title=".urlencode($title)."&link=".$file;
    if ($title) {
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n > 4) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
 }
}
 if ($n<5) echo "</TR>"."\n\r";
 echo '</table>';
?>
</BODY>
</HTML>
