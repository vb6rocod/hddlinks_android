<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>topfilmenoi</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body><div id="mainnav">
<H2></H2>
<?php
include ("../common.php");
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
/*
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><b>voxfilmeonline</b></TD></TR>';
echo '<TR><TD colspan="3">'.'<a href="voxfilmeonline.php?page=1,https://voxfilmeonline.net/,Toate+Filmele" target="_blank"><b>TOATE FILMELE</b></a></TD>';
//<TD colspan="2"><form action="hdfilm_s.php" target="_blank">Cautare film:  <input type="text" id="src" name="src"><input type="submit" value="send"></form></td>
echo '</TR>';
*/
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><th class="cat" colspan="3">topfilmenoi</TH></TR>';
echo '<TR><TD class="cat">'.'<a href="topfilmenoi.php?page=1&link=http://topfilmenoi.net&title=Recente" target="_blank">Recente</a></TD>
<TD class="form" colspan="2"><form action="topfilmenoi.php" target="_blank">
Cautare film:  <input type="text" id="link" name="link" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="">
<input type="hidden" name="title" id="title" value="">
<input type="submit" id="send" value="Cauta"></form></td>
</TR>';
$n=0;

$l="http://topfilmenoi.net/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

$videos = explode('class="cat-item cat-item', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t0 = explode('href="',$video);
    $t1 = explode('"', $t0[1]);
    $link = $t1[0];
    $t2 = explode('>', $t0[1]);
    $t3 = explode('<', $t2[1]);
    $title = $t3[0];
    $link="topfilmenoi.php?page=1&link=".$link."&title=".urlencode($title);
    if (!preg_match("/IN CURAND|FILME SERIALE/",$title)) {
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
</DIV>
<BODY>
</HTML>
