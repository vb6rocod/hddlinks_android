<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["imdb"];
$tip=$_GET["tip"];
$year=$_GET["year"];
$token=$_GET["token"];
if ($year)
$tit2=$tit." (".$year.")";
else
$tit2=$tit;
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body><div id="mainnav">
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
echo '<h2>'.$tit2.'</h2>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg
$id=str_between($link,"series/","-");
$cookie=$base_cookie."cineplex.dat";
if (file_exists($base_pass."cineplex_host.txt"))
  $host=file_get_contents($base_pass."cineplex_host.txt");
else
  $host="cinogen.net";
$episoade=array();
$sez="1";
while (true) {
  $l="https://".$host."/series/season?id=".$id."&s=".$sez."&token=".$token."&_";
  //echo $l;
  //echo $post;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://cineplex.to");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($html,1);
  //print_r ($r);
  //die();
  if (isset($r)) {
  for ($p=0;$p<count($r);$p++) {
  $season=$sez;
  $episod=$r[$p]["episode_number"];
  $img_ep=$r[$p]["poster"];
  if (!$img_ep) $img_ep="blank.jpg";
  $ep_tit=$r[$p]["title"];
  $title=$season."x".$episod." - ".$ep_tit;
  $episoade[$sez][]=array("episod"=>$episod,"img_ep"=>$img_ep,"ep_tit"=>$ep_tit);
  }
  $sez++;
  } else {
    break;
  }
}
//print_r ($episoade);
//die();
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($episoade);
foreach ($episoade as $key => $value) {
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($key).'">Sezonul '.($key).'</a></TD>';
}
echo '</TR></TABLE>';
foreach ($episoade as $key => $value) {
  $season=$key;
  $sez=$key;
  //echo $sez;
  //echo count($episoade[$sez]);
  //$p=0;
  $n=0;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.$sez.'</td></tr>';
  for ($p=0;$p<count($episoade[$sez]);$p++) {
  $episod=$episoade[$key][$p]["episod"];
  $ep_tit=$episoade[$key][$p]["ep_tit"];
  $img_ep=$episoade[$key][$p]["img_ep"];
  $title=$season."x".$episod." - ".$ep_tit;
  //$link1="cecileplanche_fs.php";
  //$link_fs='cineplex_fs.php?tip=movie&imdb='.$imdb.'&title='.urlencode(fix_t($title1)).'&image='.$image."&year=".$year."&token=".$token;

  $link2='cineplex_fs.php?tip=series&imdb='.$link.'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&token=".$token;

  if ($n == 0) echo "<TR>"."\n\r";
  if ($p==0)
  echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link2.'" target="_blank"><img width="200px" height="100px" src="'.$img_ep.'"><BR>'.$title.'</a></TD>';
  else
  echo '<TD class="mp" width="33%" align="center">'.'<a href="'.$link2.'" target="_blank"><img width="200px" height="100px" src="'.$img_ep.'"><BR>'.$title.'</a></TD>';
  $n++;

  if ($n > 2) {
    echo '</TR>'."\n\r";
    $n=0;
  }


}
echo "</TABLE>";
}
?>
<br></div></body>
</html>
