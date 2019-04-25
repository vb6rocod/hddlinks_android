<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>pornhub</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     //alert (charCode);
     if (charCode == "53"  && e.target.type != "text") {
      document.getElementById("send").click();
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body><div id="mainnav">

<?php
include ("../common.php");
if (file_exists($base_cookie."adult.dat"))
  $val_search=file_get_contents($base_cookie."adult.dat");
else
  $val_search="";
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><td style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3"><font size="6"><b>pornhub</b></TD></TR>';
echo '<TR><TD class="cat" colspan="1">'.'<a href="pornhub.php?page=1,o=bw,Recente" target="_blank"><b>Recente</b></a></TD>';
echo '<TD class="form" colspan="2"><form action="pornhub.php" target="_blank">Cautare <input type="hidden" name="page1" id="page1" value="1"><input type="text" id="src" name="src" value="'.$val_search.'"><input type="submit" value="send" id="send"></form></td>';
//http://www.pornhub.com/searches.html?q=&page=2
echo '</TR>';
$n=0;
$l="https://www.pornhub.com/categories";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://www.pornhub.com");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//$html=str_between($html,'<ul class="categories-menu','</ul');
//echo $html;
$videos = explode('class="sidebarIndent', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    $t=explode('href="/video?',$video);
    $t1=explode('"',$t[1]);
    $link=$t1[0];
    $t2=explode('categoryName">',$t[1]);
    $t3=explode("<",$t2[1]);
  	$title=$t3[0];
    $link="pornhub.php?page=1,".$link.",".urlencode($title);
    if ($title) {
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
<body><div id="mainnav">
</HTML>
