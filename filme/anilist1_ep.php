<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$tit=$_GET["title"];
$link=$_GET["link"];
$sez=$_GET['sez'];
$width="200px";
$height="278px";
/* ==================================================== */
$fs_target="anilist1_fs.php";
/* ==================================================== */

/* ==================================================== */
$tit=unfix_t(urldecode($tit));
$link=unfix_t(urldecode($link));
/* ==================================================== */
$ua="Mozilla/5.0 (Windows NT 10.0; rv:63.0) Gecko/20100101 Firefox/63.0";
$link=str_replace(" ","%20",$link);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);


$page_title=$tit." Season ".$sez;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />


</head>
<body>
<?php
$w=0;
$n=0;
echo '<H2>'.$page_title.'</H2>'."\r\n";

echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
$t1=explode("<ul",$html);
$t2=explode("</ul",$t1[1]);
$html=$t2[0];


$videos = explode('href="', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t1 = explode('"', $video);
    $link = "https://anilist1.ir/".$t1[0];
    $t2 = explode('flex-1 truncate">', $video);
    $t3 = explode('<', $t2[1]);
    $title = trim($t3[0]);
    $title=prep_tit($title);
    $ep_tit="";
  $year="";
  $imdb="";
  $ep="";

  $image="blank.jpg";
  if ($title <> "..") {
   if (preg_match("/S\d+E(\d+)/",$title,$m))
    $ep=round($m[1]);
   elseif (preg_match("/Part(\d+)/i",$title,$n))
    $ep=$n[1];
   else
    $ep="1";
    $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=".$ep."&ep_tit=".$ep_tit."&year=".$year;
  if ($n==0) echo '<TR>'."\r\n";
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    '.$title.'</a>';
    echo '</TD>'."\r\n";
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
}
}
  /*
  if ($n < 4 && $n > 0) {
    for ($k=0;$k<4-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
  */
echo "</table>"."\r\n";
?>
<br></body>
</html>
