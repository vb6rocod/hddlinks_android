<!DOCTYPE html>
<?php
include ("../common.php");


?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Youtube Music Live</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="../jquery.nicescroll.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
	$("html").niceScroll({styler:"fb",cursorcolor:"#000"});
  });
</script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$n=0;
echo '<h2 style="background-color:deepskyblue;color:black">Youtube Music Live</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$key="AIzaSyDhpkA0op8Cyb_Yu1yQa1_aPSr7YtMacYU";
$l="https://www.googleapis.com/youtube/v3/search?part=snippet&eventType=live&maxResults=50&order=relevance&q=muzica%7Cmusic%7Cpop%7Cdance%7Crock&regionCode=ro&relevanceLanguage=ro&type=video&key=".$key;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $p=json_decode($html,1);
  $items = $p["items"];
  for ($k=0;$k<count($items);$k++) {
  if ($items[$k]["snippet"]["liveBroadcastContent"] == "live") {
    $link = "https://www.youtube.com/watch?v=".$items[$k]["id"]["videoId"];
    $image = $items[$k]["snippet"]["thumbnails"]["medium"]["url"];
    $title = $items[$k]["snippet"]["title"];
  if ($link <> "") {
  if ($n==0) echo '<TR>';
  echo '<td align="center" width="25%"><a href="direct_link.php?file='.urlencode($link).'&title='.$title.' " target="_blank"><img src="'.$image.'" width="200px" height="106px"><BR><font size="4">'.$title.'</font></a></TD>';
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
<br></body>
</html>
