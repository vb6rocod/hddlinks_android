<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$n=0;

$id = unfix_t(urldecode($_POST["link"]));
$id=str_replace("dolce","telekom",$id);
$id=str_ireplace("Digisport","digi sport",$id);
$id=str_ireplace("RO_","",$id);
//preg_match_all("/(\w?)(\d?)/",$id,$m);
//print_r ($m);
//$id=preg_replace("/(\w)(\d)/",'$1 $2',$id);
$id=strtolower(str_replace(" ","-",$id));

//echo $id;
$link="https://android.cinemagia.ro/program-tv/".$id."/";
//echo $link."<BR>";
//$link="http://port.ro/pls/w/tv.channel?i_ch=10017&i_xday=5"
//$link="https://android.cinemagia.ro/program-tv/tvr-moldova/";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:22.0) Gecko/20100101 Firefox/22.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt ($ch, CURLOPT_REFERER, "https://android.cinemagia.ro/program-tv/");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
$p=explode('<li id="alarmContainer',$html);
$c=count($p);
 $ora=trim(str_between($p[$c-1],'<span class="hour">','</span>'));
 $title=trim(str_between($p[$c-1],'<b>','</b>'));
echo $ora.' '.$title."\r\n";
$videos = explode('<li class="show', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $ora=trim(str_between($video,'<span class="hour">','</span>'));
 $title=str_between($video,'<b>','</b>');
 $title = trim(preg_replace("/(<\/?)(\w+)([^>]*>)/e","",$title));
 echo $ora." ".$title."\r\n";
}
//} else {
//print "FARA PROGRAM";
//}
?>
