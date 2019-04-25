<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["link"];
$tip=$_GET["tip"];
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
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
echo '<h2>'.$tit.'</h2>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg

$requestLink=$link;
//echo $link;
//die();
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_REFERER, "https://www1.swatchseries.to");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close ($ch);
  //echo $h;
  
   echo '<table border="1px" width="100%">'."\n\r";
   $n=0;
 $videos = explode('div  itemprop="season', $h);
$sezoane=array();
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
foreach($videos as $video) {
  $t1=explode('itemprop="name">Season',$video);
  $t2=explode("<",$t1[1]);
  $sezoane[]=trim($t2[0]);
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';

foreach($videos as $video) {
  $t1=explode('itemprop="name">Season',$video);
  $t2=explode("<",$t1[1]);
  $season=trim($t2[0]);
  $sez = $season;
  $first=true;
  $vids = explode('li id="episode', $video);
unset($vids[0]);
//$videos = array_values($videos);
$vids = array_reverse($vids);
foreach($vids as $vid) {
  if ($first) {
    $first=false;
    $n=0;
    echo '<table border="1" width="100%">'."\n\r";
    echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';


  }
  $t1=explode('episodenumber" content="',$vid);
  $t2=explode('"',$t1[1]);
  $episod=trim($t2[0]);;

  $img_ep=$image;
  $t0=explode('itemprop="name"',$vid);
  $t1=explode('>',$t0[1]);
  $t2=explode('<',$t1[1]);
  $ep_tit=$t2[0];
  //$ep_tit=html_entity_decode($t2[0]);
  $ep_tit=str_replace("&nbsp;"," ",$ep_tit);
  $ep_tit=trim(preg_replace("/Episode\s+\d+/","",$ep_tit));
  //echo $ep_tit;
  $title=$season."x".$episod." - ".$ep_tit;
  $t1=explode('href="',$vid);
  $t2=explode('"',$t1[1]);
  $link=$t2[0];
  //$link1="cecileplanche_fs.php";
  $link2='swatchseries_fs.php?tip=series&link='.$link.'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$season."&ep=".$episod."&ep_tit=".$title;

  if ($n == 0) echo "<TR>"."\n\r";
  if ($p==0)
  echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
  else
  echo '<TD class="mp" width="33%" align="center">'.'<a href="'.$link2.'" target="_blank">'.$title.'</a></TD>';
  $n++;

  if ($n > 2) {
    echo '</TR>'."\n\r";
    $n=0;
  }

}
echo '</table>';
}
echo '</table>';
?>
<br></div></body>
</html>
