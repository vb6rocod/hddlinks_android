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
//http://localhost/mobile1/scripts/filme/hdfull_s_ep.php?tip=serie&file=https://hdfull.me/show/babylon-5&title=Babylon+5&image=
//http://localhost/mobile1/scripts/filme/hdfull_s_ep.php?tip=serie&file=https://hdfull.me/serie/babylon-5&title=Babylon+5&image=https://hdfull.me/thumbs/show_068ddb35fce90ce67a09021ac6ff242a.jpg
$link=str_replace("/serie","/show",$link);
$requestLink=$link;
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"https://hdfull.me");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
  //echo $html;
  $h=str_between($html,'id="season-list"','</ul');
  //echo $h;
  $t0=explode('class="show-poster">',$html);
  $t1=explode('src="',$t0[1]);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
  $t1=explode("var sid = '",$html);
  $t2=explode("'",$t1[1]);
  $id_serial=$t2[0];
   echo '<table border="1px" width="100%">'."\n\r";
   $n=0;
 $videos = explode('season-', $h);
$sezoane=array();
unset($videos[0]);
$videos = array_values($videos);
//$videos = array_reverse($videos);
foreach($videos as $video) {
  $t1=explode("'",$video);
  $sezoane[]=$t1[0];
}
echo '<table border="1" width="100%">'."\n\r";
echo '<TR>';
$c=count($sezoane);
for ($k=0;$k<$c;$k++) {
echo '<td class="sez" style="color:black;text-align:center"><a href="#sez'.($sezoane[$k]).'">Sezonul '.($sezoane[$k]).'</a></TD>';
}
echo '</TR></TABLE>';

for ($k=0;$k<$c;$k++) {
  $sez=$sezoane[$k];
  $first=true;
  if ($first) {
    $first=false;
    $n=0;
    echo '<table border="1" width="100%">'."\n\r";
    echo '<TR><td class="sez" style="color:black;background-color:#0a6996;color:#64c8ff;text-align:center" colspan=3">Sezonul '.($sez).'</TD></TR>';

  $l="https://hdfull.me/a/episodes";
  $post="action=season&start=0&limit=0&show=".$id_serial."&season=".$sez;
  //echo $post;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch,CURLOPT_REFERER,"http://cecileplanche-psychologue-lyon.com");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h3 = curl_exec($ch);
  curl_close ($ch);
  $r=json_decode($h3,1);
  //print_r ($r);
  }
  for ($p=0;$p<count($r);$p++) {
  $season=$r[$p]["season"];
  $episod=$r[$p]["episode"];
  $img_ep="https://hdfull.me/tthumb/220x124/".$r[$p]["thumbnail"];
  $ep_tit=$r[$p]["title"]["en"];
  $id_ep=$r[$p]["id"];
  $title=$season."x".$episod." - ".$ep_tit;
  //$link1="cecileplanche_fs.php";
  $link2='hdfull_fs.php?tip=series&link='.$link.'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=".$season."&ep=".$episod."&ep_tit=".$title;

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
echo '</table>';
}
echo '</table>';
?>
<br></div></body>
</html>
