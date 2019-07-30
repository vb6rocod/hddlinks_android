<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
include ("../util.php");
$title=unfix_t(urldecode($_GET["title"]));
$tip=$_GET["tip"];
if ($tip=="serie" || $tip=="series") $tip="tv";
if (isset($_GET["year"]))
  $year=$_GET["year"];
else
  $year="";
if (isset($_GET["imdb"]))
  $imdb=$_GET["imdb"];
  if ($imdb && $imdb[0] !="t") $imdb = "tt".$imdb;
else
  $imdb="";
$ttxml="";
$tit="";
$year="";
$gen="";
$durata="";
$imdb="";
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
<link rel="stylesheet" type="text/css" href="../custom.css" />
</head>
<body>
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
$rest = substr($title, -6);
if (preg_match("/\((\d+)\)/",$rest,$m)) {
 $year=$m[1];
 $title=trim(str_replace($m[0],"",$title));
} else {
 $year="";
}

$title=preg_replace("/\s*(\:|\-)\s+(season|sezon|minis)+(.*?)/i","",$title);
$t1=explode(" - ",$title);
$title=trim($t1[0]);
if (!$imdb) {
  if ($tip == "tv") {
    if (!$year)
     $find=$title." serie";
    else
     $find=$title." serie ".$year;
  } else {
    if (!$year)
     $find=$title." movie";
    else
     $find=$title." movie ".$year;
  }
  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match('/https:\/\/www.imdb.com\/title\/(tt\d+)/ms', $h, $match))
   $imdb=$match[1];
}
//$imdb="";
//echo $title."\n".$imdb."\n";
if (!$imdb) {
if ($TMDB) {
  $key = file_get_contents($f_TMDB);
  $api_url="https://api.themoviedb.org/3/search/".$tip."?api_key=".$key."&query=".urlencode($title)."&year=".$year;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  if (isset($r["results"][0]["id"])) {
   if ($r["results"][0]["poster_path"])
    $img="http://image.tmdb.org/t/p/w500".$r["results"][0]["poster_path"];
   else
    $img="blank.jpg";
   $desc=$r["results"][0]["overview"];
   $imdb=$r["results"][0]["vote_average"];
   if ($tip == "movie") {
     $tit=$r["results"][0]["title"];
     if (!$tit) $tit=$r["results"][0]["name"];
   } else {
     $tit=$r["results"][0]["original_name"];
     if (!$tit) $tit=$r["results"][0]["name"];
   }
   $id_m=$r["results"][0]["id"];
   $l="https://api.themoviedb.org/3/".$tip."/".$id_m."?api_key=".$key;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $p=json_decode($h,1);
   if (isset($p["release_date"])) {
     $y= $p["release_date"];
     $y=substr($y, 0, 4);
   } else {
    $y=$p["first_air_date"]." - ".$p["last_air_date"];
    $y1 = substr($p["first_air_date"],0,4);
    $y2 = substr($p["last_air_date"],0,4);
    $y=$y1;
   }
   $year=$y;
   $c=count($p["genres"]);
   $g="";
   for ($k=0;$k<$c;$k++) {
    $g .=$p["genres"][$k]["name"].",";
   }
   $gen=$g;
   $gen=substr($gen, 0, -1);
   if (isset($p["runtime"]))
     $d=$p["runtime"];
   else if (isset($p["episode_run_time"][0]))
     $d=$p["episode_run_time"][0];
   else
     $d="";
   $durata=$d;
   $cast="";
   $l="https://api.themoviedb.org/3/".$tip."/".$id_m."/credits?api_key=".$key;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   $c=count($r["cast"]);
   if ($c>20) $c=20;
   for ($k=0;$k<$c;$k++) {
    $cast .=$r["cast"][$k]["name"].",";
   }
   $cast = substr($cast, 0, -1);
  } else {
    $tit = $title;
}
} else if ($OMDB) {
  $key=file_get_contents($f_OMDB);
  if ($tip == "tv")
    $tip_omdb="series";
  else
    $tip_omdb="movie";
  $url="http://www.omdbapi.com/?apikey=".$key."&t=".urlencode($title)."&plot=full&type=".$tip_omdb;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  $tit=$r["Title"];
  $year=$r["Year"];
  $gen=$r["Genre"];
  $durata =$r["Runtime"];
  $durata = preg_replace("/\s+min/","",$durata);
  $imdb=$r["imdbRating"];
  $cast=$r["Actors"];
  $desc = $r["Plot"];
  $img=$r["Poster"];
  if (!$img || $img=="N/A") $img="blank.jpg";
} else {
  $tit=$title;
}
} else { // use imdb
if ($TMDB) {
  $key = file_get_contents($f_TMDB);
  $api_url="http://api.themoviedb.org/3/find/".$imdb."?api_key=".$key."&language=en-US&external_source=imdb_id";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  if (isset($r["movie_results"][0]))
    $tip="movie";
  else if (isset($r["tv_results"][0]))
    $tip="tv";
  if ($tip == "movie") {
   if ($r["movie_results"][0]["poster_path"])
     $img="http://image.tmdb.org/t/p/w500".$r["movie_results"][0]["poster_path"];
   else
     $img="blank.jpg";
   $desc=$r["movie_results"][0]["overview"];
   $imdb=$r["movie_results"][0]["vote_average"];
   $tit=$r["movie_results"][0]["title"];
   if (!$tit) $tit=$r["movie_results"][0]["name"];
   $id_m=$r["movie_results"][0]["id"];
  } else {
   if ($r["tv_results"][0]["poster_path"])
     $img="http://image.tmdb.org/t/p/w500".$r["tv_results"][0]["poster_path"];
   else
     $img="blank.jpg";
   $desc=$r["tv_results"][0]["overview"];
   $imdb=$r["tv_results"][0]["vote_average"];
   $tit=$r["tv_results"][0]["original_name"];
   if (!$tit) $tit=$r["tv_results"][0]["name"];
   $id_m=$r["tv_results"][0]["id"];
  }
   $l="https://api.themoviedb.org/3/".$tip."/".$id_m."?api_key=".$key;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $p=json_decode($h,1);
   if (isset($p["release_date"])) {
     $y= $p["release_date"];
     $y=substr($y, 0, 4);
   } else {
    $y=$p["first_air_date"]." - ".$p["last_air_date"];
    $y1 = substr($p["first_air_date"],0,4);
    $y2 = substr($p["last_air_date"],0,4);
    $y=$y1;
   }
   $year=$y;
   $c=count($p["genres"]);
   $g="";
   for ($k=0;$k<$c;$k++) {
    $g .=$p["genres"][$k]["name"].",";
   }
   $gen=$g;
   $gen=substr($gen, 0, -1);
   if (isset($p["runtime"]))
     $d=$p["runtime"];
   else if (isset($p["episode_run_time"][0]))
     $d=$p["episode_run_time"][0];
   else
     $d="";
   $durata=$d;
   $cast="";
   $l="https://api.themoviedb.org/3/".$tip."/".$id_m."/credits?api_key=".$key;
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $l);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
   curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
   curl_setopt($ch, CURLOPT_TIMEOUT, 15);
   $h = curl_exec($ch);
   curl_close($ch);
   $r=json_decode($h,1);
   $c=count($r["cast"]);
   if ($c>20) $c=20;
   for ($k=0;$k<$c;$k++) {
    $cast .=$r["cast"][$k]["name"].",";
   }
   $cast = substr($cast, 0, -1);
} else if ($OMDB) {
  $key=file_get_contents($f_OMDB);
  if ($tip == "tv")
    $tip_omdb="series";
  else
    $tip_omdb="movie";
  $imdb=preg_replace("/^tt0{2,}/","tt00",$imdb);
  $url="http://www.omdbapi.com/?apikey=".$key."&i=".$imdb."&plot=full";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $r=json_decode($h,1);
  $tit=$r["Title"];
  $year=$r["Year"];
  $gen=$r["Genre"];
  $durata =$r["Runtime"];
  $durata = preg_replace("/\s+min/","",$durata);
  $imdb=$r["imdbRating"];
  $cast=$r["Actors"];
  $desc = $r["Plot"];
  $img=$r["Poster"];
  if (!$img || $img=="N/A") $img="blank.jpg";
} else {
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
}
}
$ttxml .="<H2>".$tit."</H2><BR>"; //title
$ttxml .="Year: ".$year."<BR>";     //an
$ttxml .="Genre: ".$gen."<BR>"; //gen
if ($durata && $durata != "N/A")
 $ttxml .="Duration: ".$durata." min.<BR>"; //
else
 $ttxml .="Duration: ".$durata."<BR>";
$ttxml .="IMDB: ".$imdb."<BR>"; //imdb
$ttxml .="Cast: ".$cast; //actori
echo '<BR>';
echo '<table  width="100%">'."\n\r";
echo '<TR>';
echo '<TD width="250px" style="vertical-align:top"><img src="'.$img.'" width="250px" height="375px"></TD>';
echo '<TD class="cat" style="vertical-align:top">'.$ttxml.'</TD><TD width="5px"></TD></TR>';
echo '<TR><TD class="nav" colspan="3"><BR>'.$desc.'</TD></TR>';
echo '</TABLE>';
?>
</body>
</html>
  
