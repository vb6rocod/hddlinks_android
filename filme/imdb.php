<!DOCTYPE html>
<?php
error_reporting(0);
//echo '<link rel="stylesheet" type="text/css" href="../custom.css" />';
include ("../common.php");
include ("../util.php");
$title=unfix_t(urldecode($_GET["title"]));
$tip=$_GET["tip"];
if ($tip=="serie" || $tip=="series") $tip="tv";
if (isset($_GET["year"]))
  $year=$_GET["year"];
else
  $year="";
if (isset($_GET["imdb"])) {
  $imdb=$_GET["imdb"];
  if ($imdb[0] !="t" && $imdb) $imdb = "tt".$imdb;
} else
  $imdb="";

$ttxml="";
$tit="";
//$year="";
$gen="";
$durata="";
//$imdb="";
$cast="";
$desc="";
$img="";
/* ================================ */
$f_TMDB=$base_pass."tmdb.txt";
$f_OMDB=$base_pass."omdb.txt";
/* ================================ */
?>
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

if (file_exists($f_TMDB)) {
   $TMDB=true;
} else
   $TMDB=false;

if (file_exists($f_OMDB)) {
   $OMDB=true;
} else
   $OMDB=false;

$title=prep_tit($title);
//echo $title."\n";

if (!$year) {
  preg_match("/\(([^\)]*)\)$/",$title,$y);
  if (isset($y[0])) {
    $title=trim(preg_replace("/\(([^\)]*)\)$/","",$title));
    if (preg_match("/[1|2]\d{3}/",$y[1],$z))
     $year=$z[0];
  }
}
//One Piece Season 1
preg_match("/\s*[\s\-]?\s*(season|sezon|mini)\w*\s+(\d+)/i",$title,$m);
//print_r ($m);
$title=trim(preg_replace("/\s*[\s\-]?\s*(season|sezon|mini)\w*\s+(\d+)/i","",$title));
$title=trim(preg_replace("/s\d{1,2}\s*e\d{1,2}/i","",$title));
//echo $year."\n";
//echo $title."\n";
//$title=preg_replace("/\s*(\:|\-)\s+(season|sezon|minis)+(.*?)/i","",$title);
//$t1=explode(" - ",$title);
//$title=trim($t1[0]);
//print_r ($_GET);
//echo $title."\n";
//die();
$title1=$title;
$key = file_get_contents($f_TMDB);
$file=$base_cookie."imdb_d.txt";
  //die();
$imdb_arr=array();
if (isset($_GET['tmdb'])) {
  $tmdb_id=$_GET['tmdb'];
  $x=getTMDBDetail($tmdb_id,"",$key,$tip,1,1);
  $out=$tmdb_id."|0|".$tip;
  file_put_contents($file,$out);
  echo $x;
} elseif ($imdb) {
  $out=$imdb."|0|".$tip;
  file_put_contents($file,$out);
  $x=getTMDBDetail("",$imdb,$key,$tip,1,1);
  echo $x;
} else {
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&exact=true&ref_=fn_tt_ex";
  //https://www.imdb.com/find/?q=draga%20mos%20craciun&s=tt&exact=true&ref_=fn_tt_ex
  if ($tip=="tv")
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&ttype=tv&exact=true&ref_=fn_tt_ex";
  else
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&ttype=ft&exact=true&ref_=fn_tt_ex";
  //echo $url."\n";

  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&s=tt&exact=true&ref_=fn_tt_ex";
  //echo $url;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  $t1=explode('type="application/json">',$h);
  $t2=explode('</script>',$t1[1]);
  $x=$t2[0];
  $y=json_decode($x,1);
  $m=$y['props']['pageProps']['titleResults']['results'];
  if(count($m) == 0) {
  $url="https://www.imdb.com/find/?q=".rawurlencode($title)."&ref_=fn_al";
  curl_setopt($ch, CURLOPT_URL, $url);
  $h = curl_exec($ch);
  $t1=explode('type="application/json">',$h);
  $t2=explode('</script>',$t1[1]);
  $x=$t2[0];
  $y=json_decode($x,1);
  $m=$y['props']['pageProps']['titleResults']['results'];
  //print_r ($y);
  }
  curl_close($ch);


//////////////////////////////////////////////////////////////////////////
  //print_r ($y);
  $arr_imdb=array();
  $arr_match=array();

  //print_r ($m);
  if (count($m)>0) { // find result save to arr_imdb, not filter
   foreach($m as $k=>$v) {
   $tip_imdb=$v['imageType'];
   if (isset($v['titleReleaseText']))
   $year_imdb=substr($v['titleReleaseText'],0,4);
   else
   $year_imdb=0;
   $title_imdb=$v['titleNameText'];
   $id_imdb=$v['id'];
   $img_imdb=$v['titlePosterImageModel']['url'];
   $arr_imdb[]=array($id_imdb,$tip,$year_imdb,$title_imdb,$img_imdb,$tip_imdb);
  }
  //print_r ($arr_imdb);

  if ($tip=="tv") {
  if ($year) { // year is set
   foreach ($arr_imdb as $k=>$v) {
   $tip_imdb=$v[5];
   $year_imdb=$v[2];
   if (preg_match("/series/i",$tip_imdb)) {
      if ($year==$year_imdb) {// year match
       $arr_match[]=$v;
       //break;
      }
     }
   }
   if (count($arr_match)==0) {
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   $tip_imdb=$v[5];
   if (preg_match("/series/i",$tip_imdb)) {
      if (abs($year-$year_imdb) <2) {// year match +- 1
       $arr_match[]=$v;
       //break;
      }
     }
   }
   }
   if (count($arr_match)==0) {
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   $tip_imdb=$v[5];
   if (preg_match("/series/i",$tip_imdb) && $year_imdb>0) {
       $arr_match[]=$v;
   }
   }
   }
  } else { // year no set
   foreach ($arr_imdb as $k=>$v) {
   $tip_imdb=$v[5];
   $year_imdb=$v[2];
   if (preg_match("/series/i",$tip_imdb) && $year_imdb>0) {
     $arr_match[]=$v;
   }
   }
  }
  //print_r ($arr_match);
  } else { // movie
  if ($year) { // year is set
  //print_r ($arr_imdb);
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   //echo "\n".$year_imdb."\n".$year;
   $tip_imdb=$v[5];
   if (preg_match("/movie|short|tvMiniSeries/i",$tip_imdb)) {
      if ($year==$year_imdb) {// year match
       $arr_match[]=$v;
       //break;
      }
     }
   }
   //print_r ($arr_match);
   if (count($arr_match)==0) {
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   $tip_imdb=$v[5];
   if (preg_match("/movie|short|tvMiniSeries/i",$tip_imdb)) {
      if (abs($year-$year_imdb) <2) {// year match +- 1
       $arr_match[]=$v;
       //break;
      }
     }
   }
   }
   if (count($arr_match)==0) {
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   $tip_imdb=$v[5];
   if (preg_match("/movie|short|tvMiniSeries/i",$tip_imdb) && $year_imdb>0) {
       $arr_match[]=$v;
   }
   }
   }
  } else { // year no set
   foreach ($arr_imdb as $k=>$v) {
   $year_imdb=$v[2];
   $tip_imdb=$v[5];
   if (preg_match("/movie|short|tvMiniSeries/i",$tip_imdb) && $year_imdb>0) {
     $arr_match[]=$v;
   }
   }
  }
  } // end movie
  //print_r ($arr_match);
  if (count($arr_match) > 0) {
  $out="";
  foreach ($arr_match as $k=>$v) {
   $out .='"'.$v[0].'",';
  }
  $out=substr($out,0,-1);
  $out=$out."|0|".$tip;
  file_put_contents($file,$out);
  $x=getTMDBDetail("",$arr_match[0][0],$key,$tip,1,count($arr_match));
  echo $x;
  }
 } // end if (count($m)>0)
}
//echo count($arr_match);
if (count($arr_match) == 0  && !isset($_GET['tmdb']) && !$imdb) {  // not found imdb
  if (count($arr_imdb) > 0) {
   //$arr_imdb[]=array($id_imdb,$tip,$year_imdb,$title_imdb,$img_imdb,$tip_imdb);
   $id_imdb=$arr_imdb[0][0];
   $out=$id_imdb."|0|".$tip;
   file_put_contents($file,$out);
   $x=getTMDBDetail("",$id_imdb,$key,$tip,1,1);
   echo $x;
  } else { // not found
   $out="0"."|0|".$tip;
   file_put_contents($file,$out);
   $x=getTMDBDetail("0","",$key,$tip,1,1);
   echo $x;
  }
}
/*
  $r=getIMDBDetail($imdb);
  $img=$r['poster'];
  $tit=$r["Title"];
  $desc=$r['plot'];
  $year=$r["Year"];
  $imdb=$r["imdbRating"];
  $cast=$r["Actors"];
  $durata=$r["Runtime"];
  $durata = preg_replace("/\s+min/","",$durata);
  $gen=$r["Genre"];
*/
//$imdb="";
//$year="1984";

?>
</div>
</body>
</html>
  
