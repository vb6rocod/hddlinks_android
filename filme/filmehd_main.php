<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>filmehd</title>
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
<body>
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
$form='<TD class="form" colspan="2"><form action="filmehd.php" target="_blank">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="">
<input type="submit" id="send" value="Cauta..."></form>
</td>';
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\n\r";
echo '<TR><th class="cat" colspan="3">filmehd</th><TR>';
echo '<TR><TD class="cat">'.'<a class ="nav" href="filmehd.php?page=1&tip=release&link=https://filmehd.net&title=Recente" target="_blank">Recente...</a></TD>';
echo $form;
echo '</TR>';
$n=0;
$l = "https://filmehd.net";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$html=str_between($html,'menu-seria-categorys-container','</div>');
//echo $html;
$videos = explode('menu-item menu-item-type-taxonomy', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $video=urlencode($video);
	$t1 = explode('href%3D%22', $video);
	$t2 = explode('%22', $t1[1]);
	$link = urldecode($t2[0]);
	$link = str_replace(' ','%20',$link);
	$link = str_replace('[','%5B',$link);
	$link = str_replace(']','%5D',$link);

	$t3 = explode('%3E', $t1[1]);
	$t4 = explode('%3C', $t3[1]);
	$title = urldecode($t4[0]);
    $link="filmehd.php?page=1&tip=release&link=".$link."&title=".urlencode($title);
	if ($n == 0) echo "<TR>"."\n\r";
    echo '<TD class="cat">'.'<a class ="nav" href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>
<body><div id="mainnav">
</HTML>
