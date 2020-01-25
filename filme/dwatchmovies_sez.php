<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$l=$link;
$base = dirname($l);
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:71.0) Gecko/20100101 Firefox/71.0";
$host=parse_url($link)['host'];
//$l="https://dwatchmovies.pro/tvshow/law_and_order_special_victims_unit-en-s21";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
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
  $rest = substr($tit, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit=trim(str_replace($m[0],"",$tit));
  } else {
   $year="";
  }
echo '<h2>'.$tit.'</h2><br>';
echo '<table border="1" width="100%">'."\n\r";
//   <!--  <div class='col-md-4 text-center col-sm-6 col-xs-6'><a href='12_monkeys-en-s05'>... <figcaption>Season 5</figcaption>
$t1=explode("Choose Season to watch",$html);
$html=$t1[1];
     $html=preg_replace_callback(
      "/\<\!--.*?--\>/ms",
      function ($matches) {
       return "";
      },
      $html
     );
preg_match_all("/(\<\!--)?\s*\<div.*?href\=[\"|\'](.*?)[\"|\']\>.*?\<figcaption\>Season\s*(\d+)\</ms",$html,$m);

for ($k=0;$k<count($m[3]);$k++) {
  if (!$m[1][$k]) {
  $sez=$m[3][$k];
  $title="Season ".$sez;
  $link=$m[2][$k];
  if (strpos($link,"http") == false) $link=$base."/".$link;
  $ep="";
  //$year="";
  
  if ($n==0) echo '<TR>';
  $link_f='dwatchmovies_ep.php?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=&ep_tit=&year=".$year;
  echo '<td class="sez" align="center"><a href="'.$link_f.'" target="_blank">'.$title.'</a></TD>';
  $n++;
  if ($n == 4) {
  echo '</tr>';
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
echo '</table>';
?>
</body>
</html>
