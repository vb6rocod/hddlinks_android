<!DOCTYPE html>
<?php
error_reporting(0);
ini_set('memory_limit', '-1');
if (isset($_GET['link'])) {
  $link= $_GET['link'];
} else
 $link="";
$pg_tit=urldecode($_GET["title"]);
if (isset($_GET['page']))
 $page=$_GET['page'];
else
 $page=0;
if (isset($_GET['group']))
 $group=$_GET['group'];
else
 $group="no";
$step=200;
////////////////////////

?>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $pg_tit; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body width="100%">

<h2><?php echo $pg_tit; ?></H2>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];

$n=0;
$w=0;
function remove_empty_lines($string) {
    $lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $string));
    $lines = array_map('trim', $lines);
    $lines = array_filter($lines, function($value) {
        return $value !== '';
    });
    return implode("\n", $lines);
    //return $lines;
}
$m3uFile="pl/".$pg_tit;
if ($link) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $m3ufile = curl_exec($ch);
  curl_close($ch);
} else {
$m3ufile = file_get_contents($m3uFile);
}

$m3ufile = remove_empty_lines($m3ufile);
$re = '/#EXTINF:(.+?)\n(#.*?\n)?((http|rtmp)\S+)/m';
preg_match_all($re, $m3ufile, $matches);
$tot=count($matches[0]);
$rr=array();
$rrr=array();

  for ($z=0;$z<count($matches[1]);$z++) {
   $file=$matches[3][$z];
   $line=$matches[1][$z];
   if (preg_match("/tvg-name\=\"([^\"]+)\"/",$line,$x)) {
     $title=trim($x[1]);
   } else {
     $t=explode(",",$line);
     $title=trim($t[count($t)-1]);
   }
   if (preg_match("/group-title\=\"([^\"]+)\"/",$line,$s))
    $group1=$s[1];
   else
    $group1="no";
   //echo $title."\n".$file."\n";
  $rr[$group1][]=array($title,$file);
  }
  $rrr=array_keys($rr);
echo '<table border="1px" width="100%">'."\n\r";
foreach($rr as $key=>$value) {
    $title1=$key;
    $link1="playlist.php?link=".$link."&title=".$pg_tit."&group=".urlencode($key);
	if ($n == 0) echo "<TR>"."\n\r";
	echo '<TD class="cat">'.'<a href="'.$link1.'" target="_blank">'.$title1.'</a></TD>';
    $n++;
    if ($n > 2) {
     echo '</TR>'."\n\r";
     $n=0;
    }
}
 if ($n<3) echo "</TR>"."\n\r";
 echo '</table>';
?>

</BODY>
</HTML>
