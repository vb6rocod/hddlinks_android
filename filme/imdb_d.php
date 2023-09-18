<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />

</head>
<body>
<div id="imdb_d">
<?php
include ("../util.php");
include ("../common.php");
$f_TMDB=$base_pass."tmdb.txt";
$key = file_get_contents($f_TMDB);
/*
$imdb=$_GET['imdb'];
$start=$_GET['s'];
$end=$_GET['e'];
$tip=$_GET['tip'];
*/
$file=$base_cookie."imdb_d.txt";
$h=file_get_contents($file);
$t1=explode("|",$h);
$a=$t1[0];
$start=$t1[1];
$tip=$t1[2];
$tt=explode(",",$a);
$end=count($tt);
$d=$_GET['sens'];
if ($d=="plus") {
  $start=$start+1;
  if ($start == $end) {
   $start=0;
  }
}
if ($d=="minus") {
  $start=$start-1;
  if ($start<0) {
    $start=$end-1;
  }
}
$curr=str_replace('"',"",$tt[$start]);
$imdb=$curr;
if ($curr[0]=="t") {
 $x=getTMDBDetail("",$imdb,$key,$tip,$start+1,$end);
} else {
 $x=getTMDBDetail($imdb,"",$key,$tip,$start+1,$end);
}
  $out=$a."|".$start."|".$tip;
  file_put_contents($file,$out);
echo $x;
?>
</div>
</body>
</html>
