<?php
ini_set('memory_limit', '-1');
function getSiteHost($siteLink) {
		// parse url and get different components
		$siteParts = parse_url($siteLink);
		$port="";
		if (isset($siteParts['port'])) {
		$port=$siteParts['port'];
		if (!$port || $port==80)
          $port="";
        else
          $port=":".$port;
        }
		// extract full host components and return host
		return $siteParts['scheme'].'://'.$siteParts['host'].$port;
}
include ("../common.php");
$link=$_GET['link'];
$title=$_GET['title'];
$q=parse_url($link)['query'];
parse_str($q,$x);
$user=$x['username'];
$pass=$x['password'];
$cookie=$base_cookie."/iptv.dat";
if (file_exists($cookie)) unlink($cookie);
$l1=getSiteHost($link);
$l=getSiteHost($link)."/client_area/index.php?username=".$user."&password=".$pass;
//echo $l;
  $t1=explode("index.php",$l);
  $live=$t1[0]."live.php";
  $vod=$t1[0]."vod.php";
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:149.0) Gecko/20100101 Firefox/149.0";
//echo $live;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_COOKIEFILE,$cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR,$cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  //echo $h;
  curl_setopt($ch, CURLOPT_URL, $live);
  $h = curl_exec($ch);
//echo $live;

  if (preg_match("/Expire Date/",$h)) {
  $t1=explode("Expire Date:",$h);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[2]);
  $exp=$t3[0];
  } else {
  $exp="expired";
  }
  //echo $exp;
  //$h = curl_exec($ch);
  
echo '
<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>'.$title.' - Expire Date:'.$exp.'</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
td {
    font-style: bold;
    font-size: 20px;
    text-align: left;
}
th {
		padding: 5px;
		border: 0;
		text-align: center;
		/* border: 1px solid #0000005c; */ /* Supprimer cette ligne pour retirer les traits verticaux */
		font-family: "sans-serif", monospace;
		font-size: 18px;
		color: burlywood;
        background-color: #22252a;
}
</style>
</head>
<body>';
$n=0;
echo '<h2>'.$title." - Expire Date:".$exp.'</h2>';
echo '<table border="1px" width="100%">'."\n\r";
echo '<th colspan="4">Live</Th>';
$videos=explode('onClick="window.location=',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode("'",$video);

   $file=$l1."/client_area/".$t1[1];

   $t1=explode('>',$video);
   $t2=explode('<',$t1[1]);
   $title1=trim($t2[0]);
    $link="iptv2.php?title=".urlencode($title1)."&link=".$file."&page=0";
    if ($title) {
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link.'" target="_blank">'.$title1.'</a></TD>';
    $n++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
  }
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
  curl_setopt($ch, CURLOPT_URL, $vod);
  $h = curl_exec($ch);

  curl_close($ch);
 $n=0;
echo '<table border="1px" width="100%">'."\n\r";
echo '<th colspan="4">VOD</Th>';
$videos=explode('onClick="window.location=',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1=explode("'",$video);

   $file=$l1."/client_area/".$t1[1];

   $t1=explode('>',$video);
   $t2=explode('<',$t1[1]);
   $title1=trim($t2[0]);
    $link="iptv2.php?title=".urlencode($title1)."&link=".$file."&page=0";
    if ($title) {
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="imdb">'.'<a href="'.$link.'" target="_blank">'.$title1.'</a></TD>';
    $n++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
  }
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
</BODY>
</HTML>
