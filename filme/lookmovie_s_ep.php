<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$year=$_GET['year'];
/* ======================================= */
$width="200px";
$height="100px";
$fs_target="lookmovie_fs.php";
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
$cookie=$base_cookie."lookmovie.txt";
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
//echo $link;
//print_r ($head);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,$last_good);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
//echo $h;
function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
       return substr($data, 3);
    }
    return $data;
}
$t1=explode("window.seasons='",$h);
$t2=explode("';",$t1[1]);
$z=str_replace('\\\\\\"',"'",trim($t2[0]));
$t1=explode("seasons:",$h);
$t2=explode("};",$t1[1]);
$z=str_replace('\\\\\\"',"'",trim($t2[0]));

//die();
$z=str_replace("\\","", $z);
$z=str_replace("\n","",$z);
//echo $z;
preg_match_all("/title\:\s+\'(.*?)\'\,\s+index\:\s+\'(.*?)\'\,\s+episode\:\s+\'(.*?)\'\,\s+id\_episode\:\s+\'?(.*?)\'?\,\s+season\:\s+\'(.*?)\'/si",$z,$m);
//print_r ($m);
$r=array();
for ($k=0;$k<count($m[5]);$k++) {
 $r[$m[5][$k]][$m[3][$k]]=array($m[1][$k],$m[3][$k],$m[4][$k],$m[5][$k]);
}
//print_r ($r);
//echo $z;
//die();
//$p=json_decode($z,1);
//print_r ($p);

//for ($k=0;$k<count($p);$k++) {
foreach ($p as $pp) {
 //for ($i=1;$i<count($p[$k]['episodes']);$i++) {
 foreach($pp['episodes'] as $ee) {
  $r[$pp['meta']['season']][] = array($ee['title'],$ee['episode_number'],$ee['id_episode'],$ee['still_path']);
 }
}
//print_r ($r);
// https://false-promise.lookmovie.ag/api/v1/storage/shows/?slug=10380768-love-life-2020&token=
// https://lookmovie.ag/manifests/shows/4UNbOWGB--phpVD_Mri7ug/1591460238/96045/master.m3u8
// https://lookmovie.ag/storage2/1591455930289/shows/10380768-love-life-2020/9171-S1-E3-1590996329/subtitles/en.vtt
// https://lookmovie.ag/api/v1/shows/episode-subtitles/?id_episode=96045
$t1=explode("window['show_storage'] =",$h);
$t2=explode("</script>",$t1[1]);
$t3=trim($t2[0]);
$rest = substr($t3, 0, -1);
$slug="";
$year="";
if (preg_match("/slug\:\s*\'(.*?)\'/",$rest,$s))
 $slug=$s[1];
if (preg_match("/year\:\s*\'(\d+)\'/",$rest,$y))
 $year=$y[1];

// title: 'O Brave New World', episode: '1', id_episode: 6168, season: '1'},
// title: 'Episode #1.3', index: '1', episode: '3', id_episode: 125400, season: '1'}
//preg_match_all("/title\:\s*\'(.*?)\'\,\s*episode\:\s*\'(\d+)\'\,\s*id_episode\:\s*(\d+)\,\s*season\:\s*\'(\d+)\'/",$h,$m);
//$r=array();
//for ($k=0;$k<count($m[1]);$k++) {
// $r[$m[4][$k]][]=array($m[4][$k],$m[1][$k],$m[2][$k],$m[3][$k]);
//}

$n=0;
foreach($r as $key=>$value) {
  $sezoane[]=$key;
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;

foreach($r as $key=>$value) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($key).'">Sezonul '.($key).'</a></TD>';
$p++;
if ($p == 10) {
 echo '</tr>';
 $p=0;
 }
}
if ($p < 10 && $p > 0 && $k > 9) {
 echo '</TR>'."\r\n";
}
echo '</TABLE>';

foreach($r as $key=>$value) {
  $season=$key;
  $sez = $season;
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;

  foreach($r[$sez] as $v) {
  //print_r ($v);
  $img_ep="";
  $episod="";
  $ep_tit="";
  $episod=$v[1];
  $link=$v[2];
  $img_ep="https://lookmovie.io/images/p/w300".$v[3];
  $title=$v[0];
  //$ep_tit=html_entity_decode($t2[0]);
  $title=str_replace("\\","",$title);
  $title=prep_tit($title);
  $ep_tit=trim(preg_replace("/Episode\s+\d+/","",$title));;
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;

  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$img_ep."&sez=".$season."&ep=".$episod."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&slug=".$slug;
   if ($n == 0) echo "<TR>"."\n\r";
   if ($has_img == "yes")
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank"><img width="'.$width.'" height="'.$height.'" src="'.$img_ep.'"><BR>'.$ep_tit_d.'</a></TD>'."\r\n";
   else
    echo '<TD class="mp" width="33%">'.'<a id="sez'.$sez.'" href="'.$link_f.'" target="_blank">'.$ep_tit_d.'</a></TD>'."\r\n";
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
