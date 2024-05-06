<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Diverse</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>Diverse</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>Digi24 Emisiuni</b></TD></TR>';
$n=0;
$link="pl/liste.txt";
$html=file_get_contents($link);
$link="https://raw.githubusercontent.com/vb6rocod/hddlinks/master/liste.txt";
/*
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  echo $html;
  */
preg_match_all("/http\S+/",$html,$m);
//print_r ($m);

foreach($m[0] as $video) {
    $link=$video;
    if (preg_match("/username\=/",$video)) {
    $t1=explode("username=",$video);
    $t2=explode("&",$t1[1]);
    $title=$t2[0];
    } else {
     $title=parse_url($video)['host'];
    }
    if (strlen($title) > 25)
    $title1=substr($title,0,22)."...";
    else
    $title1=$title;
    $link="playlist.php?link=".urlencode($link)."&title=".urlencode($title);
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }

}
 if ($n<4) echo "</TR>"."\n\r";
 echo '</table>';
?>

</BODY>
</HTML>
