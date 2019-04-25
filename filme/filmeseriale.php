<!DOCTYPE html>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
include ("../common.php");
$link=$_GET["file"];
$tit=unfix_t(urldecode($_GET["title"]));
$tit=html_entity_decode($tit,ENT_QUOTES,'UTF-8');
$tit=str_replace(urldecode("%E2%80%99"),urldecode("%27"),$tit);
$f=$base_pass."tvplay.txt";
if (file_exists($f))
   $user=true;
else
   $user=false;

?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="../custom.css" />
      <meta charset="utf-8">
      <title><?php echo $tit; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body><BR>
<?php

//$link="filmeseriale.php?file=".$link1.",".urlencode(fix_t($title11));
echo '<h2>'.$tit.'</h2>';
echo '<div id="mainnav">';
echo '<table border="1" width="100%">'."\n\r";
//echo $link;
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" align="center">'.$tit.'</TD></TR></TABLE>';
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
  $t1=explode('class="cover"',$html);
  $t2=explode("(",$t1[1]);
  $t3=explode(")",$t2[1]);
  $img_ep=$t3[0];
  //$html=str_replace("script","zzzzz",$html);
  //echo $html;
$videos = explode('class="numerando">', $html);
unset($videos[0]);
$videos = array_values($videos);
$n=0;
$title1="";
$sezoane=array();
$last_sez="";
foreach($videos as $video) {
    $t1=explode('<',$video);
    $title1=trim($t1[0]);
    $title1=html_entity_decode($title1,ENT_QUOTES,'UTF-8');
    preg_match("/(\d+)\s+x\s+(\d+)/",$title1,$m);
    //print_r ($m);
    $sez=$m[1];
    $ep=$m[2];
    if ($last_sez <> $sez) {
       $last_sez=$sez;
       $sezoane[]=$sez;
    }
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez"><a href="#sez'.$sezoane[$k].'">Sezonul '.$sezoane[$k].'</a></TD>';
}
echo '</TR></TABLE>';

//print_r ($sezoane);
/////////////////////////////////////////////////////////////////////////////
if ($user) {
$api_key="f8cf02e6b30bf8cc33c04c60695781aa";
$api_url="https://api.themoviedb.org/3/search/tv?api_key=".$api_key."&query=".urlencode($tit);
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
$id_m=$r["results"][0]["id"];
}

$episoade=array();
 $videos = explode('class="numerando">', $html);
 unset($videos[0]);
 $videos = array_values($videos);
 foreach($videos as $video) {
    $n=0;
    $title1="";
    $first=true;
    $t1 = explode('href="', $video);
//if ( sizeof($t1)>1 ) {
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];

    $t1=explode('<',$video);
    $title1=trim($t1[0]);
    $title1=html_entity_decode($title1,ENT_QUOTES,'UTF-8');
    preg_match("/(\d+)\s+x\s+(\d+)/",$title1,$m);
    //print_r ($m);
    $sez=$m[1];
    $ep=$m[2];
    $t2=explode('class="episodiotitle">',$video);
    $t3=explode("<",$t2[1]);
    $title2=trim($t3[0]);
    $title2=html_entity_decode($title2,ENT_QUOTES,'UTF-8');
    $episoade[$sez][]=array("ep"=>$ep,"title"=>$title2,"link"=>$link);
}
//print_r ($episoade);
//die();
//for ($k=0;$k<count($sezoane);$k++) {
foreach ($episoade as $key=>$value) {
 $sez=$key;
 $first=true;
 $n=0;
 echo '<table border="1" width="100%">'."\n\r";
 echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';
 if ($user && $id_m) {
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
 }
 //print_r ($r);
 for ($k=0;$k<count($episoade[$sez]);$k++) {
  $ep=$episoade[$sez][$k]["ep"];
  $title2=$episoade[$sez][$k]["title"];
  $link=$episoade[$sez][$k]["link"];

    if ($r) {
     $title2 = $r["episodes"][$ep-1]["name"];
     $title2=html_entity_decode($title2,ENT_QUOTES,'UTF-8');
     $desc=$r["episodes"][$ep-1]["overview"];
     if (isset($r["episodes"][$ep-1]["still_path"]))
      $img_ep="http://image.tmdb.org/t/p/w780".$r["episodes"][$ep-1]["still_path"];
     else
      $img_ep1="blank.jpg";
    } else  {
      $img_ep1="blank.jpg";
    }
    if ($title2)
    $title=$sez."x".$ep." - ".$title2;
    else
    $title=$sez."x".$ep;
    $link22 = 'filme_link.php?file='.urlencode($link).",".urlencode(fix_t($tit." - ".$title));
    if ($n==0) echo "<TR>";
    if ($first) {
     echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link22.'" target="_blank"><img width="200px" height="100px" src="'.$img_ep.'"><BR>'.$title.'</a>';
     $first=false;
    } else {
     echo '<TD class="mp" width="33%" align="center">'.'<a href="'.$link22.'" target="_blank"><img width="200px" height="100px" src="'.$img_ep.'"><BR>'.$title.'</a>';
    }
    echo '</TD>'."\n\r";
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
}

echo '</table>';
}
echo '</div>';
?>
<br></body>
</html>
