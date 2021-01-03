<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$link=urldecode($_GET["file"]);

/* ======================================= */
$width="200px";
$height="100px";
$fs_target="filme_link.php";
$has_img="no";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
<title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<?php

function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2>';
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
$link=str_replace(" ","%20",$link);
$link=str_replace("+","%2B",$link);
//echo $link;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://www.peserialehd.us");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$html = curl_exec($ch);
curl_close($ch);
//echo $html;
$t1=explode('<tbody>',$html);
$t2=explode('</tbody>',$t1[1]);
$html=$t2[0];
//echo $html;
$n=0;
echo '<table border="1" width="100%">'."\n\r";
$videos = explode('<td', $html);

unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2=explode('"',$t1[1]);
  $link="https://www.peserialehd.us".$t2[0];
  //echo $t2[1];
  //echo $link;
  $t3=explode(">",$t1[1]);
  $t4=explode("<",$t3[1]);
  $tit_link=$t4[0];
  //echo "==".$tit_link;
  if (preg_match("/EPISOD(ul)?\s*(\d+)/i",$tit_link,$m)) {
  //print_r ($m);
   $episod=$m[2];
   $ep_tit=$m[0];
  }
  if ($episod) {
  $link_f=$fs_target.'?file='.urlencode($link).'&title='.urlencode(fix_t($tit_link));
   if ($n == 0) echo "<TR>"."\n\r";
    echo '<TD class="mp" width="33%">'.'<a href="'.$link_f.'" target="_blank">'.$ep_tit.'</a></TD>'."\r\n";
   $n++;
   if ($n == 3) {
    echo '</TR>'."\n\r";
    $n=0;
   }
   }

}
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
echo '</table>';
//}
?>
</body>
</html>
