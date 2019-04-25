<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Digi24 Emisiuni</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<div id="mainnav">

<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>Digi24 Emisiuni</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>Digi24 Emisiuni</b></TD></TR>';
$n=0;
echo '<TR>';
$l="https://www.digi24.ro/emisiuni";
//echo '<TR><TD style="text-align:center">'.'<a href="digi_fata.php" target="_blank">In fata ta</a></TD>';
//echo '<TD style="text-align:center">'.'<a href="digi_starea.php" target="_blank">Starea Natiei</a></TD>';
$n=0;
//$cookie=$base_cookie."digi1.dat";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$html=str_between($html,'<section','</section');
//echo $html;
$videos = explode('<figure class="card', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
 $video=html_entity_decode($video);
 $title=str_between($video,'title="','"');
 $descriere=$title;
 $image=urldecode(str_between($video,'data-src="','"'));
 $link="https://www.digi24.ro".str_between($video,'href="','"');
    $link="digi24_e_main.php?page=1,".$link.",".urlencode($title);
    if (preg_match("/emisiuni\//",$link)) {
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
