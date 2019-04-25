<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>filmenoihd</title>
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
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\n\r";
echo '<TR><th class="cat" colspan="3">
filmenoihd</Th></TR>';
echo '<TR><TD class="cat">'.'<a href="filmenoihd.php?page=1&tip=release&link=http://www.filmenoihd.biz/filme/&title=Toate+filmele" target="_blank">Recente</a></TD>
<TD class="form" colspan="2"><form action="filmenoihd.php" target="_blank">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="">
<input type="submit" id="send" value="Cauta..."></form>
</td>
</TR>';
$n=0;
$ua=$_SERVER['HTTP_USER_AGENT'];
$l="http://www.filmenoihd.biz/filme/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

$videos = explode('id="menu-item', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t0 = explode('href="',$video);
    $t1 = explode('"', $t0[1]);
    $link = $t1[0];
    $t2 = explode('>', $t0[1]);
    $t3 = explode('<', $t2[1]);
    $title = $t3[0];
    $link="filmenoihd.php?page=1&tip=release&link=".$link."&title=".urlencode($title);
    if (!preg_match("/\#|contact|imdb/i",$link)) {
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
