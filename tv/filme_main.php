<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>Filme</title>
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
echo '<h2>Filme</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>Digi24 Emisiuni</b></TD></TR>';
$n=0;
$l="http://revo.ddns.me:8080/get.php?username=Florin-Restream-Filme&password=rtFBXLaKD9&type=m3u_plus&output=mpegts";

$lines = file($l);
$r=array();
foreach ($lines as $nn => $line) {
if (preg_match("/\#EXTINF.+tvg\-name\=\"(.*?)\".+group\-title\=\"(.*?)\"/",$line,$m)) {
  //print_r ($m);
  $r[$m[2]][$m[1]]=$lines[$nn+1];
}
}
foreach ($r as $key=>$value) {
    $title=$key;
    $link="filme.php?&title=".urlencode($title);
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>

</BODY>
</HTML>
