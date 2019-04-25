<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>privesc.eu</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
<div id="mainnav">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>privesc.eu</h2>';
echo '<table border="1px" width="100%">'."\n\r";
//echo '<TR><td style="color:black;background-color:deepskyblue;text-align:center" colspan="3"><font size="4"><b>privesc.eu</b></font></TD></TR>';

$n=0;
if ($n == 0) echo "<TR>"."\n\r";
$title="Toate";
$link="https://www.privesc.eu/arhiva/categorii/Toate";
$link="privesc.php?page=1&link=".$link."&title=".urlencode($title);
echo '<TD style="text-align:center"><font size="4">'.'<a href="'.$link.'" target="_blank">'.$title.'</a></font></TD>';
$n=1;
$l="https://www.privesc.eu/Arhiva/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
$html=str_between($html,'<div class="list-group">','<div class="panel panel-default">');
$videos = explode('<a class', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    $title = str_between($video,"title='","'");
    //$title=fix_s($title);
    $t1 = explode("href='",$video);
    $t2 = explode("'",$t1[1]);
    $link ="https://www.privesc.eu".$t2[0];
    //$link=str_replace(",","*",$link);
    $link="privesc.php?page=1&link=".$link."&title=".urlencode($title);
    if ((strpos($title,"Toate") === false)) {

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
</div>
<BODY>
</HTML>
