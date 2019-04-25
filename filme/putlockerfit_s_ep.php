<!DOCTYPE html>
<?php
include ("../common.php");
$tit=unfix_t(urldecode($_GET["title"]));
$image=$_GET["image"];
$link=$_GET["file"];
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
echo '<h2>'.$tit.'</h2>';
echo '<table border="1" width="100%">'."\n\r";
//echo '<TR><td style="color:#000000;background-color:deepskyblue;text-align:center" colspan="3" align="center">'.$tit.'</TD></TR>';
///flixanity_s_ep.php?tip=serie&file=http://flixanity.watch/the-walking-dead&title=The Walking Dead&image=http://flixanity.watch/thumbs/show_85a60e7d66f57fb9d75de9eefe36c42c.jpg

$requestLink=$link;
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://putlocker0.com/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $t1=explode('twitter:image" content="',$html);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
   echo '<table border="1px" width="100%">'."\n\r";
   $n=0;
 $videos = explode('class="btn-season', $html);
$sezoane=array();
unset($videos[0]);
//$videos = array_values($videos);
$videos = array_reverse($videos);
foreach($videos as $video) {
  //$t1=explode('class="title">Season',$video);
  preg_match("/season\-(\d+)/",$video,$m);
  $sezoane[]=trim($m[1]);
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';

foreach($videos as $video) {
 // $t1=explode('class="title">Season',$video);
  preg_match("/season\-(\d+)/",$video,$m);
  $season=trim($m[1]);
  $sez = $season;
  $first=true;
  $vids = explode('class="btn-episode', $video);
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
    $t1=explode('href="',$vid);
    $t2=explode('"',$t1[1]);
    $link1=trim($t2[0]);
    $ep_tit=trim(str_between($vid,'title="','"'));
    $ep_tit=str_replace("?","",$ep_tit);
    $ep_tit=html_entity_decode($ep_tit,ENT_QUOTES,'UTF-8');
    $num=str_between($vid,'strong>','<');
    $ep_tit=trim(preg_replace("/".$tit."/","",$ep_tit));
    $ep_tit=trim(preg_replace("/season\s+(\d+)\s+episode\s+(\d+)/i","",$ep_tit));
  //$t1=explode('>',$video);
  //season-1-episode-3
  //Star Trek: Enterprise Season 4 Episode 22
  if (preg_match("/season\s+(\d+)\s+episode\s+(\d+)/i",$num,$m)) {
  //print_r ($m);
  $season=$m[1];
  $episod=$m[2];
  $ep_tit=$season."x".$episod." ".$ep_tit;
  } else
  $ep_tit=$season."x".$episod;

  //$link='watchfree_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
  $link2='putlockerfit_fs.php?tip=series&link='.$link1.'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$season."&ep=".$episod."&ep_tit=".$ep_tit;
  //$link2='tvseries_fs.php?tip=serie&file='.$link1.'&title='.$season.'|'.$episod.'|'.$tit.'|'.$ep_tit.'&image='.$image;
      if ($n == 0) echo "<TR>"."\n\r";
		echo '<TD class="mp" width="33%" align="center">'.'<a id="sez'.$sez.'" href="'.$link2.'" target="_blank"><img width="200px" height="100px" src="'.$image.'"><BR>'.$ep_tit.'</a>';
		echo '</TD>'."\n\r";
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
