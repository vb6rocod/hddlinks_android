<!DOCTYPE html>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$n=0;
if (isset($_POST["link"]))
  $id = unfix_t(urldecode($_POST["link"]));
else
  $id = unfix_t(urldecode($_GET["link"]));
$page_title = $id;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<BODY>
<?php
/*
$id=str_replace("dolce","telekom",$id);
$id=str_ireplace("Digisport","digi sport",$id);
$id=str_ireplace("RO_","",$id);
$id=str_ireplace("Digi24","digi 24",$id);
//preg_match_all("/(\w?)(\d?)/",$id,$m);
//print_r ($m);
//$id=preg_replace("/(\w)(\d)/",'$1 $2',$id);
$id=strtolower(str_replace(" ","-",$id));
*/
/* ======================================================== */
$find=$id;
  $url = "https://www.google.com/search?q=".rawurlencode($find)."+program+tv+cinemagia.ro";
  //echo $url;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
  //https://www.cinemagia.ro/program-tv/digi-24/
  // https://www.cinemagia.ro/program-tv/hbo-2/
  if (preg_match('/https\:\/\/www\.cinemagia\.ro\/program\-tv\/post\/(.*?)\//ms', $h, $match))  {
   $id=$match[1];

/* ========================================================= */

$link="https://android.cinemagia.ro/program-tv/".$id."/";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0');
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
$t1=explode('<title>',$html);
$t2=explode('<',$t1[1]);
$page_tit=$t2[0];
echo '<H2>'.$page_tit.'</H2>';
echo '<table  width="100%">'."\n\r";
//$p=explode('<li id="alarmContainer',$html);
//$c=count($p);
// $ora=trim(str_between($p[$c-1],'<span class="time">','</span>'));
// $title=trim(str_between($p[$c-1],'<h3>','</h3>'));
//echo $ora.' '.$title."\r\n";
//echo $ora." ".$title."<BR>";
//echo '<TR><TD class="cat" width="5%">'.$ora.'</TD><TD class="cat">'.$title.'</TD></TR>';
$videos = explode('<li id="alarmContainer_', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('class="',$video);
 $t2=explode('"',$t1[1]);
 $tip=$t2[0];
 $ora=trim(str_between($video,'<span class="time">','</span>'));
 $title=str_between($video,'<h3>','</h3>');
 $title = trim(preg_replace("/(<\/?)(\w+)([^>]*>)/e","",$title));
 //echo $ora." ".$title."\r\n";
 //echo $ora." ".$title."<BR>";
 if ($tip <> "old_show")
 echo '<TR><TD class="cat" width="5%">'.$ora.'</TD><TD class="cat">'.$title.'</TD></TR>';
}
echo '</TABLE>';
} else {
echo '<H2>'.$page_title.'</H2>';
echo "<BR>FARA PROGRAM";
}
?>
</BODY>
</HTML>
