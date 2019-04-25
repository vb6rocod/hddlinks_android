<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>filme3d</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body><div id="mainnav">
<H2></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
/*
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><font size="4"><b>voxfilmeonline</b></font></TD></TR>';
echo '<TR><TD colspan="3"><font size="4">'.'<a href="voxfilmeonline.php?page=1,https://voxfilmeonline.net/,Toate+Filmele" target="_blank"><b>TOATE FILMELE</b></a></font></TD>';
//<TD colspan="2"><font size="4"><form action="hdfilm_s.php" target="_blank">Cautare film:  <input type="text" id="src" name="src"><input type="submit" value="send"></form></font></td>
echo '</TR>';
*/
echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="6"><b>filme3d</b></font></TD></TR>';
echo '<TR><TD><font size="4">'.'<a href="filmehdgratis.php?page=1&link=https://www.filme3d.net/&title=Toate+filmele" target="_blank"><b>TOATE FILMELE</b></a></font></TD>
<TD colspan="2"><font size="4"><form action="filmehdgratis.php" target="_blank">Cautare film:  <input type="text" id="link" name="link"><input type="hidden" name="page" id="page" value=""><input type="hidden" name="title" id="title" value=""><input type="submit" value="send"></form></font></td>
</TR>';
$n=0;
$l="https://voxfilmeonline.net/";
$l="http://topfilmenoi.net/";
$l="https://www.filmehdgratis.biz/";
$l="https://www.filme3d.net/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$html=str_between($html,'div class="meniu">','id="pagina">');
$videos = explode('<li', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t0 = explode('href="',$video);
    $t1 = explode('"', $t0[1]);
    $link = $t1[0];
    $t2 = explode('>', $t1[1]);
    $t3 = explode('<', $t2[1]);
    $title = $t3[0];
    $link="filmehdgratis.php?page=1&link=".$link."&title=".urlencode($title);
    if (!preg_match("/IN CURAND|FILME SERIALE/",$title)) {
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
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
