<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");

$tit="PROTV";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $tit; ?></title>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>

</head>
<body>
<div id="mainnav">
<H2></H2>
<h2><?php echo $tit; ?></h2>
<table border="1px" width="100%">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$n=0;

//echo '<h2>Antena Play</H2>';
echo '<table border="1px" width="100%">'."\n\r";
$l="https://protvplus.ro/page/all_shows/";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://protvplus.ro");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $html = curl_exec($ch);
  curl_close($ch);
  $x=json_decode($html,1);
  $r=$x["content"]["areas"];
  //print_r ($r);
  //die();
  for ($k=0;$k<count($r);$k++) {
    for ($z=0;$z<count($r[$k]["items"]);$z++) {
      $title= $r[$k]["items"][$z]["title"];
      $l1=  "https://protvplus.ro".$r[$k]["items"][$z]["target"];
      $image= $r[$k]["items"][$z]["poster"];
      //$link = "protv.php?query=1,".$link.",".urlencode($title);
    $link="protv.php?file=".urlencode($l1);
  if ($title) {
	if ($n == 0) echo "<TR>"."\n\r";
echo '
<TD><table border="0px">
<TD align="center" width="20%"><a href="'.$link.'&title='.urlencode($title).'" target="_blank"><img src="'.$image.'" width="171" height="96"><BR><b>'.$title.'</b></a></TD>

</TABLE></TD>
';
$n++;
    if ($n > 4) {
     echo '</TR>'."\n\r";
     $n=0;
    }
 }
}
}
 if ($n<0) echo "</TR>"."\n\r";
 echo '</table>';
?>
</div>
<br></body>
</html>
