<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=urldecode($_GET["link"]);
$tip=$_GET["tip"];
$year=$_GET['year'];
/* ======================================= */
$width="200px";
$height="100px";
$fs_target="filme_link.php";
$has_img="yes";
/* ======================================= */
$f=$base_pass."tmdb.txt";
if (file_exists($f)) {
   $api_key = file_get_contents($f);
   $user=true;
} else
   $user=false;
//if ($user) {
$rest = substr($tit, -6);
if (preg_match("/\((\d+)\)/",$rest,$m)) {
 $year=$m[1];
 $tit_imdb=trim(str_replace($m[0],"",$tit));
} else {
 $year="";
 $tit_imdb=$tit;
}
  if (!$year)
   $find=$tit_imdb." serie";
  else
   $find=$tit_imdb." serie ".$year;

  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  //echo $url;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/https:\/\/www.imdb.com\/title\/(tt\d+)/ms', $h, $match))
   $imdb=$match[1];
  else
   $imdb="";
//} else {
//  $id_m="";
//}
if ($imdb) {
//$api_url="https://api.themoviedb.org/3/search/tv?api_key=".$api_key."&query=".urlencode($tit_imdb);
$api_url="https://api.themoviedb.org/3/find/".$imdb."?api_key=".$api_key."&language=en-US&external_source=imdb_id";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT  ,10);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
$r=json_decode($h,1);
//print_r ($r);
if (isset($r['tv_results'][0]['id']))
  $id_m= $r['tv_results'][0]['id'];
else
  $id_m="";
//$id_m=$r["results"][0]["id"];
} else {
  $id_m="";
}
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
$ua = $_SERVER['HTTP_USER_AGENT'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://filmeseriale.online");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $t1=explode('meta property="og:image',$html);
  $t2=explode('content="',$t1[1]);
  $t3=explode('"',$t2[1]);
  $img_ep_o=$t3[0];

$n=0;
$videos = explode('span class="se-t', $html);
$sezoane=array();
unset($videos[0]);
$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  $t1=explode('>',$video);
  $t2=explode('<',$t1[1]);
  $sezoane[]=trim($t2[0]);
}
echo '<table border="1" width="100%">'."\n\r";

$p=0;
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
if ($p==0) echo '<TR>';
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
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
$r=array();
foreach($videos as $video) {
  $t1=explode('>',$video);
  $t2=explode('<',$t1[1]);
  $season=trim($t2[0]);
  $sez = $season;
  if ($id_m) {
       $l="https://api.themoviedb.org/3/tv/".$id_m."/season/".$sez."?api_key=".$api_key;
       //echo $l;
       $r=array();
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $l);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT  ,10);
       curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
       curl_setopt($ch, CURLOPT_TIMEOUT, 15);
       $h = curl_exec($ch);
       curl_close($ch);
       $r=json_decode($h,1);
  } else {
    $r=getIMDBSeason($imdb,$sez);
  }
  echo '<table border="1" width="100%">'."\n\r";
  echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan="3">Sezonul '.($sez).'</TD></TR>';
  $n=0;
  $vids = explode('div class="numerando">', $video);
  unset($vids[0]);
  $vids = array_values($vids);
  //$vids = array_reverse($vids);
  foreach($vids as $vid) {
  //$img_ep="";
  $episod="";
  $ep_tit="";
  $t1=explode("<",$vid);
  $t2=explode("x",$t1[0]);
  $episod=trim($t2[1]);
  $t1=explode('href="',$vid);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  if (!$imdb) {
  $t3=explode(">",$t1[1]);
  $t4=explode('<',$t3[1]);
  $title=$t4[0];
  $title=str_replace("&nbsp;"," ",$title);
  $ep_tit=prep_tit($title);
  } else if (!$user && $r) {  // imdb
    $ep_tit = $r[$episod]['title'];
    $img_ep= $r[$episod]['poster'];
  } else {  // tmdb
     $ep_tit = $r["episodes"][$episod-1]["name"];
     if (isset($r["episodes"][$episod-1]["still_path"]))
      $img_ep="http://image.tmdb.org/t/p/w780".$r["episodes"][$episod-1]["still_path"];
     else
      $img_ep=$img_ep_o;
  }
  if ($ep_tit)
   $ep_tit_d=$season."x".$episod." ".$ep_tit;
  else
   $ep_tit_d=$season."x".$episod;
  $tit_link = $tit." ".$ep_tit_d;
  if ($episod) {
  $link_f=$fs_target.'?file='.urlencode($link).'&title='.urlencode(fix_t($tit_link));
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
}
?>
</body>
</html>
