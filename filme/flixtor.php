<?php
set_time_limit(0);
header('Content-type: video/mp4');
//$movie="https://dl.flixtor.ac/files/yts/1080p/42389.mp4";
/*
$movie_file=substr(strrchr($movie, "/"), 1);
header('Content-type: application/vnd.apple.mpegURL');
header('Cookie: approve=1; approve_search=yes');
header('Content-Disposition: attachment; filename="'.$movie_file.'"');
header("Location: $movie");
*/
$movie=urldecode($_GET["file"]);
//$movie="https://dl.flixtor.ac/files/yts/1080p/42389.mp4";
$head=array('Accept: */*',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://flixtor.ac/movies',
'X-Requested-With: XMLHttpRequest',
'Connection: keep-alive',
'Cookie: approve=1; approve_search=yes'
);

$l=$movie;
$ua     =   $_SERVER['HTTP_USER_AGENT'];
//echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  //curl_setopt($ch,CURLOPT_REFERER,"https://flixtor.ac/movies");
  //curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  //curl_setopt($ch, CURLOPT_ENCODING, "");
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  //curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_exec($ch);
  curl_close($ch);
?>

