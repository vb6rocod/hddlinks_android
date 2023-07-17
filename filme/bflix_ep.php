<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$tit=prep_tit($tit);
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$sez=$_GET["sez"];
$ep=$_GET["ep"];
$ep_title=unfix_t(urldecode($_GET["ep_tit"]));
$ep_title=prep_tit($ep_title);
$year=$_GET["year"];
require_once("bunny1.php");
$bunny=new bunny();
/* ====================== */
$fs_target = "bflix_fs.php";
$width="200px";
$height="100px";
$has_img="no";
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
echo '<h2>'.$tit.'</h2><BR>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
//echo $link;
$info="";
$last_good="https://".parse_url($link)['host'];
//$id = substr(strrchr($link, "-"), 1);
//$vrf=encodeVrf($id,$key);
//$l=$last_good."/ajax/film/servers?id=".$id."&vrf=".$vrf."&token=";
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0',
'Accept: application/json, text/javascript, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: '.$link,
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  ///curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);


  $r=json_decode($h,1);
  $h=$r['result'];
  //echo $h;
////////////////////////
  if (preg_match("/id\=\"film\-detail\"\>/",$h)) {
  $t1=explode('id="film-detail">',$h);
  $t2=explode('<div>Tags',$t1[1]);
  $info= $t2[0];
  $info=strip_tags($info);
  } elseif (preg_match("/id\=\"w\-info\"\>/",$h)) {
  $t1=explode('id="w-info">',$h);
  $t2=explode('<div>Tags',$t1[1]);
  $info= $t2[0];
  $info=strip_tags($info);
  }
///////////////////////
  $t1=explode('data-id="',$h);
  $t2=explode('"',$t1[1]);
  $id=$t2[0];
  //https://bflix.ru/ajax/episode/list/23600?vrf=O09%2FYGJ3RHg%3D
  //$vrf=encodeVrf($id,$key);
  $vrf=$bunny->encodeVrf($id);
  $l=$last_good."/ajax/episode/list/".$id."?vrf=".$vrf;
  curl_setopt($ch, CURLOPT_URL, $l);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  $h=$r['result'];
  //echo $h;
$n=0;
$z=1;
$path = parse_url($link)['path'];
//echo $h;
$host=parse_url($link)['host'];
$videos = explode('<ul class="episodes"', $h);
$sezoane=array();
$link_sez=array();
//$link_sezoane=array();
unset($videos[0]);
$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  $t1=explode('data-season="',$video);
  $t2=explode('"',$t1[1]);
  $sezoane[]=$t2[0];

}
echo '<table border="1" width="100%">'."\n\r";

$p=0;
foreach($sezoane as $kk => $value) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($value).'">Sezonul '.($value).'</a></TD>';
$p++;
if ($p == 10) {
 echo '</tr>';
 $p=0;
 }
}
if ($p < 10 && $p > 0 && $k > 9) {
 for ($x=0;$x<10-$p;$x++) {
   echo '<TD></TD>'."\r\n";
 }
 echo '</TR>'."\r\n";
}
echo '</TABLE>';
//$z=1;
$p=0;
$k=0;

foreach($videos as $video) {
  $t1=explode('data-season="',$video);
  $t2=explode('"',$t1[1]);
  $sez = $t2[0];
  $season=$t2[0];
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;

  $vids = explode('<li>', $video);
  unset($vids[0]);
  $vids = array_values($vids);
  //$vids = array_reverse($vids);
  foreach($vids as $vid) {
  $img_ep="";
  $episod="";
  $ep_tit="";
  $t1=explode('data-num="',$vid);
  //$t2=explode("-",$t1[1]);
  $t3=explode('"',$t1[1]);
  $episod = $t3[0];
  $t1=explode('data-id="',$vid);
  $t2=explode('"',$t1[1]);
  $link=$t2[0]."&".$last_good;

  $t3=explode('class="num">',$vid);
  $t4=explode('<span>',$t3[1]);
  $t5=explode('</span',$t4[1]);
  $ep_tit = $t5[0];
  $img_ep="blank.jpg";



  $year="";
   $epNr=$episod;

  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;
  //$link="moviesjoy_fs.php?tip=series&link=".urlencode($l)."&title=".urlencode(fix_t($tit))."&ep_tit=".urlencode(fix_t($ep_tit1))."&ep=".$epNr."&sez=".$sez."&image=".$image;
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$sez."&ep=".$epNr."&ep_tit=".urlencode(fix_t($ep_tit))."&year=".$year."&host=".$host;
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
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }

}
echo '</table>';
curl_close($ch);
//echo '<BR>'.$info;
?>
</body>
</html>
