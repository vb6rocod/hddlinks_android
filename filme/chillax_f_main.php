<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>chillax (filme)</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body><div id="mainnav">

<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
$cookie=$base_cookie."chillax.dat";
if (file_exists($cookie)) unlink ($cookie);


echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><th class="cat" colspan="3">chillax</Th></TR>';


$n=0;
/*
$l="https://chillax.ws/movies";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h1 = curl_exec($ch);
  curl_close($ch);
  $t1=explode('token_key="',$h1);
  $t2=explode('"',$t1[1]);
  $token=$t2[0];
  //echo $h1;
*/

$token="111111111111111111111111";
$id="941223";
$l="https://chillax.ws/movies/getMovieLink?id=".$id."&token=".$token."&oPid=&_=";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://chillax.ws");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  //curl_setopt($ch, CURLOPT_NOBODY,1);
  curl_exec($ch);
  curl_close($ch);
  $x1=file_get_contents($cookie);
  $x1=str_replace("deleted","true",$x1);
  file_put_contents($cookie,$x1);
  $t1=explode("PHPSESSID",$x1);
  $t2=explode("\n",$t1[1]);
  $token=trim($t2[0]);
  //notice=true
  //
echo '
<TR><TD class="cat" style="text-align:left"><a id="fav" href="chillax_f_fav.php?token='.$token.'" target="_blank">Favorite</a></TD>
';
echo '<TD class="form" colspan="2"><form action="chillax_f.php" target="_blank">
<input type="hidden" name="page" id="page" value="">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="gen" id="gen" value="">
<input type="hidden" name="token" id="token" value="'.$token.'">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="submit" id="send" value="Cauta"></form></td>
';
$gen="";
$genres=array("Action" => "action",
"Adventure" => "adventure",
"Animation" => "animation",
"Biography" => "biography",
"Comedy" => "comedy",
"Crime" => "crime",
"Documentary" => "documentary",
"Drama" => "drama",
"Family" => "family",
"Fantasy" => "fantasy",
"History" => "history",
"Horror" => "horror",
"Music" => "music",
"Mystery" => "mystery",
"Romance" => "romance",
"Science Fiction" => "science_fiction",
"Thriller" => "thriller",
"War" => "war",
"Western" => "western"
);
$link="chillax_f.php?tip=release&page=1&gen=".$gen."&token=".$token."&title=Recente";
$title="Recente";
echo '<TR><TD class="cat"><a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
$n=1;

  foreach($genres as $key => $value) {
    $gen=$value;
    $title=$key;
    $link="chillax_f.php?tip=release&page=1&gen=".$gen."&token=".$token."&title=".$title;
    if ($n==0) echo '<TR>';
    echo '<TD class="cat"><a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n>2) {
     echo '</tr>';
     $n=0;
    }
}
//////////////////////////////////////////////////////////////////

//https://cineplex.to/movies?loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=movie&q=&token=f7f9ijktnc3blmeelnvo2n6j75
//https://cineplex.to/index/loadmoviesnew
//loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=movie&q=&token=f7f9ijktnc3blmeelnvo2n6j75
?>
</table>
</div></body>
</HTML>
