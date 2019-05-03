<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$title=unfix_t(urldecode($_GET["title"]));
$tip=$_GET["tip"];
if ($tip=="serie" || $tip=="series") $tip="tv";
if (isset($_GET["year"]))
  $year=$_GET["year"];
else
  $year="";
if (isset($_GET["imdb"]))
  $imdb=$_GET["imdb"];
else
  $imdb="";
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript" src="../jquery.fancybox.js?v=2.1.5"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
</script>
</head>
<body>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$f=$base_pass."tmdb.txt";
if (file_exists($f)) {
   $key = file_get_contents($f);
   $useIMDB=false;
} else
   $useIMDB=true;
$f=$base_pass."omdb.txt";  // use omdb over tmdb
if (file_exists($f)) $useIMDB=true;
  $title=htmlspecialchars_decode($title,ENT_QUOTES);
  $title=html_entity_decode($title,ENT_QUOTES);
  $title=str_replace("&#8211;","-",$title);
  $title=str_replace("&#8217;","'",$title);
  $title=preg_replace("/sezonul\s*\d+/i","",$title);
  $title=preg_replace("/season\s*\d+/i","",$title);
  $title=trim(preg_replace("/(gratis|subtitrat|onlin|film|sbtitrat|\shd)(.*)/i","",$title));
  //echo $title;
  if (!$year) {
  if (preg_match("/\(?((1|2)\d{3})\)?/",$title,$r)) {
     //print_r ($r);
     $year=$r[1];
  }
  }
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
  //$find=$title;
  //$ua       = $_SERVER["HTTP_USER_AGENT"];
  $url = "https://www.google.com/search?q=imdb+" . rawurlencode($find);
  //echo $url;
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
  //print_r ($match);
}
//echo $imdb;
if (!$imdb && !$useIMDB) { // TMDB key =>use TMDB
  $t1=explode(" - ",$title);
  $t=trim($t1[0]);
  $rest = substr($t, -6);
  if (preg_match("/\(?((1|2)\d{3})\)?/",$rest,$m)) $t=str_replace($m[0],"",$t);
  //$t=preg_replace("/\(?((1|2)\d{3})\)?/","",$t);
  $tit3=trim($t);
  $tit3=str_replace(urldecode("%E2%80%99"),urldecode("%27"),$tit3);
  //echo $tit3;
  //$tit3="Grey’s Anatomy";
  //echo urlencode($tit3)."\n";
  //$tit3="Grey's Anatomy";
  //echo urlencode($tit3)."\n";
  $api_url="https://api.themoviedb.org/3/search/".$tip."?api_key=".$key."&query=".urlencode($tit3)."&year=".$year;
  //echo $api_url;
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
if ($r["results"][0]["id"]) {
//$img="https://api.themoviedb.org".$r["results"][0]["poster_path"];
$img="http://image.tmdb.org/t/p/w500".$r["results"][0]["poster_path"];
if (strpos($img,"http") === false) $img="";

$desc=$r["results"][0]["overview"];
$imdb="TMDB: ".$r["results"][0]["vote_average"];
$tit=$r["results"][0]["title"];
if (!$tit) $tit=$r["results"][0]["name"];
$id_m=$r["results"][0]["id"];
$l="https://api.themoviedb.org/3/".$tip."/".$id_m."?api_key=".$key;
//$l="https://api.themoviedb.org/3/tv/".$id_m."?api_key=".$key;   //."&append_to_response=videos"
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
//print_r ($JSON);

//$imdb1=json_decode($JSON["Ratings"][0],1);
//$imdb=$imdb1["Value"];
$y= $p["release_date"];
if ($y) $y=substr($y, 0, 4);  //2007-06-22
if (!$y) {
$y=$p["first_air_date"]." - ".$p["last_air_date"];
$y1 = substr($p["first_air_date"],0,4);
$y2 = substr($p["last_air_date"],0,4);
$y=$y1;
}
$year="Year: ".$y;
$c=count($p["genres"]);
$g="";
for ($k=0;$k<$c;$k++) {
 $g .=$p["genres"][$k]["name"].",";
}
$gen="Genre: ".$g;
$gen=substr($gen, 0, -1);
$d=$p["runtime"];
if (!$d) $d=$p["episode_run_time"][0];
$durata="Duration: ".$d. "min";
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
//print_r ($r);
$c=count($r["cast"]);
$cast="<b>Cast: </b>";
for ($k=0;$k<$c;$k++) {
 $cast .=$r["cast"][$k]["name"].",";
}
$cast = substr($cast, 0, -1);
$ttxml='<font size="4">';
} else {
$iit = $tit3;
}
} else if ($imdb && $useIMDB && !file_exists($base_pass."omdb.txt")) {   //no TMDB key or OMDB key => use IMDB
  include ("../util.php");
  //echo $imdb;
  $r=getIMDBDetail($imdb);
  $img=$r['poster'];
  $tit=$r["Title"];
  $desc=$r['plot'];
  $year="Year: ".$r["Year"];
  $imdb="IMDB: ".$r["imdbRating"];
  $cast="<b>Cast: </b>".$r["Actors"];
  $durata="Duration: ".$r["Runtime"];
  $gen="Genre: ".$r["Genre"];
} else if ($imdb && !$useIMDB) {   //TMDB key  => use TMDB
//$imdb=str_replace("tt","",$imdb);
//echo $imdb;
if (strpos($imdb,"tt") === false) $imdb="tt".$imdb;
$l="http://api.themoviedb.org/3/find/".$imdb."?api_key=".$key."&language=en-US&external_source=imdb_id";
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $Data = curl_exec($ch);
  curl_close($ch);
$r = json_decode($Data,1);
//print_r ($r);
//if ($tip=="movie") {
  if (isset($r["movie_results"][0]))
    $tip="movie";
  else if (isset($r["tv_results"][0]))
    $tip="tv";
//}
if ($tip=="movie") {
//if ($r["movie_results"][0]["id"]) {
//$img="https://api.themoviedb.org".$r["results"][0]["poster_path"];
$img="http://image.tmdb.org/t/p/w500".$r["movie_results"][0]["poster_path"];
if (strpos($img,"http") === false) $img="";

$desc=$r["movie_results"][0]["overview"];
$imdb="TMDB: ".$r["movie_results"][0]["vote_average"];
$tit=$r["movie_results"][0]["title"];
if (!$tit) $tit=$r["movie_results"][0]["name"];
$id_m=$r["movie_results"][0]["id"];
} else {
$img="http://image.tmdb.org/t/p/w500".$r["tv_results"][0]["poster_path"];
if (strpos($img,"http") === false) $img="";

$desc=$r["tv_results"][0]["overview"];
$imdb="TMDB: ".$r["tv_results"][0]["vote_average"];
$tit=$r["tv_results"][0]["title"];
if (!$tit) $tit=$r["tv_results"][0]["name"];
$id_m=$r["tv_results"][0]["id"];
}
$l="http://api.themoviedb.org/3/".$tip."/".$id_m."?api_key=".$key;
//$l="https://api.themoviedb.org/3/tv/".$id_m."?api_key=".$key;   //."&append_to_response=videos"
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "http://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  $p=json_decode($h,1);
  //print_r ($p);
//print_r ($JSON);

//$imdb1=json_decode($JSON["Ratings"][0],1);
//$imdb=$imdb1["Value"];
$y= $p["release_date"];
if ($y) $y=substr($y, 0, 4);  //2007-06-22
if (!$y) {
$y=$p["first_air_date"]." - ".$p["last_air_date"];
$y1 = substr($p["first_air_date"],0,4);
$y2 = substr($p["last_air_date"],0,4);
$y=$y1;
}
$year="Year: ".$y;
$c=count($p["genres"]);
$g="";
for ($k=0;$k<$c;$k++) {
 $g .=$p["genres"][$k]["name"].",";
}
$gen="Genre: ".$g;
$gen=substr($gen, 0, -1);
$d=$p["runtime"];
if (!$d) $d=$p["episode_run_time"][0];
$durata="Duration: ".$d. "min";
$cast="";
$l="http://api.themoviedb.org/3/".$tip."/".$id_m."/credits?api_key=".$key;
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
//print_r ($r);
$c=count($r["cast"]);
$cast="Cast: ";
for ($k=0;$k<$c;$k++) {
 $cast .=$r["cast"][$k]["name"].",";
}
$cast = substr($cast, 0, -1);
} else if ($imdb && file_exists($base_pass."omdb.txt") && $useIMDB) {  //No TMDB key OMDB key use OMDB
  //echo $imdb;
  //tt0122459
  //tt000000000122459  --->> bad
  $imdb=preg_replace("/^tt0{2,}/","tt0",$imdb);
  $key=file_get_contents($base_pass."omdb.txt");
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
  //print_r ($r);
  $tit=$r["Title"];
  $year="Year: ".$r["Year"];
  $gen="Genre: ".$r["Genre"];
  $durata ="Duration: ".$r["Runtime"];
  $imdb="IMDB: ".$r["imdbRating"];
  $cast="Cast: ".$r["Actors"];
  $desc = $r["Plot"];
  $img=$r["Poster"];
  if (!$img) $img="blank.jpg";
}
//print_r ($r);

$ttxml .="<H2>".$tit."</H2><BR>"; //title
$ttxml .= $year."<BR>";     //an
$ttxml .=$gen."<BR>"; //gen
$ttxml .=$durata."<BR>"; //regie
$ttxml .=$imdb."<BR></font>"; //imdb
$ttxml .=$cast.""; //actori
//$ttxml .=$desc."<BR></font>"; //descriere
//echo '<BR>';
//echo '<H2>'.$title.'</H2>';
echo '<BR>';
echo '<table  width="100%">'."\n\r";
echo '<TR>';
echo '<TD width="250px" style="vertical-align:top"><img src="'.$img.'" width="250px" height="375px"></TD>';
echo '<TD <td style="vertical-align:top">'.$ttxml.'</TD><TD width="5px"></TD></TR>';
echo '<TR><TD colspan="3"><BR><font size="4"><b>'.$desc.'</b></font></TD></TR>';
echo '</TABLE>';
?>
</body>
</html>
  
