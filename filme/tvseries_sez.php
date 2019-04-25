<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["file"];
$tip=$_GET["tip"];
$l="http://www.tvseries.net".$link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
$image="http://img.tvseries.net".str_between($html,'src="http://img.tvseries.net','"');
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body><div id="mainnav">
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2><br>';
echo '<table border="1" width="100%">'."\n\r";
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg

$html=str_between($html,"Season</h4>",'<div class="row">');
 $videos = explode('div class="column"', $html);
$n=0;
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
foreach($videos as $video) {
  $t1 = explode('href="', $video);
  $t2 = explode('"',$t1[1]);
  $link1 = $t2[0];
  //echo $link1;
  $t1 = explode('/i>', $video);
  $t2 = explode('<', $t1[1]);
  $title11 = trim($t2[0]);
  $title11=str_between($video,"<small>","</small");

  if ($n==0) echo '<TR>';
  echo '<td class="sez" align="center" width="25%"><a href="tvseries_ep.php?tip=serie&file='.$link1.'&title='.urlencode(fix_t($tit)).'&image='.$image.'&sez='.urlencode(fix_t($title11)).'" target="_blank">'.$title11.'</a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}  
echo '</table>';
?>
<br></div></body>
</html>
