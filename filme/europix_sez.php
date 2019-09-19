<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$l=$link;
$base = dirname($l);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit.'</h2><br>';
echo '<table border="1" width="100%">'."\n\r";

$html=str_between($html,'div id="szzz">','</div');
$t1=explode("<!",$html);
$html=$t1[0];
$videos = explode('a href="', $html);
$n=0;
unset($videos[0]);
$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  $t2 = explode('"',$video);
  $link = $base."/".$t2[0];
  $title=trim(str_between($video,"span>","</span"));
  $title=prep_tit($title);
  preg_match("/season\s+(\d+)/i",$title,$m);
  $sez=$m[1];
  $ep="";
  $year="";
  
  if ($n==0) echo '<TR>';
  $link_f='europix_ep.php?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=&ep_tit=&year=".$year;
  echo '<td class="sez" align="center"><a href="'.$link_f.'" target="_blank">'.$title.'</a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
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
echo '</table>';
?>
</body>
</html>
