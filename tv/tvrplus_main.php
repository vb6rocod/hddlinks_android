<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>TVR+ Emisiuni</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body><div id="mainnav">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>TVR+ Emisiuni</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//http://adevarul.ro/video-center/
$n=0;
$l="http://www.tvrplus.ro/emisiuni";
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
  
$html=str_between($html,'<div id="main-section">','</ul');
$videos = explode('<li', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    //$title=fix_s($title);
    $t1 = explode('href="',$video);
    $t2 = explode('"',$t1[1]);
    $l1 =$t2[0];
    if (strpos($l1,"http") !== false) {
    $h="";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $l1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $h = curl_exec($ch);
    curl_close($ch);
    $vids = explode('div class="item">', $h);
    unset($vids[0]);
    $vids = array_values($vids);
    foreach($vids as $vid) {
      $t1=explode('href="',$vid);
      $t2=explode('"',$t1[1]);
      $l2=$t2[0];
      $t1=explode('src="',$vid);
      $t2=explode('"',$t1[1]);
      $image=$t2[0];
      $t1=explode('strong>',$vid);
      $t2=explode('<',$t1[1]);
      $title=$t2[0];
    $link="tvrplus.php?link=".$l2."&title=".urlencode($title);
    if ((strpos($title,"Toate") === false)) {
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="mp">'.'<a href="'.$link.'" target="_blank"><img src="'.$image.'" width="250px" height="150px"><BR>'.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
 }
 }
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div><BODY>
</HTML>
