<!DOCTYPE html>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title>cineplex (filme)</title>
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
if (file_exists($base_pass."cineplex_host.txt"))
  $host=file_get_contents($base_pass."cineplex_host.txt");
else
  $host="cinogen.net";
$cookie=$base_cookie."cineplex.dat";
$cont=$base_pass."cineplex.txt";


echo '<table border="1px" width="100%">'."\n\r";
echo '<TR><th class="cat" colspan="3">cineplex (cont)</Th></TR>';


$n=0;
if (file_exists($cont) && !file_exists($cookie)) {
  $handle = fopen($cont, "r");
  $c = fread($handle, filesize($cont));
  fclose($handle);
  $a=explode("|",$c);
  $user=trim($a[0]);
  //$user=str_replace("@","%40",$user);
  $pass=trim($a[1]);
  $l="https://".$host."/";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  $l="https://".$host."/session/userlogin";
  $post="username=".$user."&password=".$pass."&remember=1";
  //echo $post;
  $head=array('Accept-Language: ro-ro,ro;q=0.8,en-us;q=0.6,en-gb;q=0.4,en;q=0.2','Accept-Encoding: gzip, deflate','Content-Type: application/x-www-form-urlencoded','Content-Length: '.strlen($post));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
}
$l="https://".$host."/movies";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h1 = curl_exec($ch);
  curl_close($ch);
  preg_match("/token_key\s*\=\s*\"([^\"]+)/",$h1,$m);
  //$t1=explode('token_key="',$h1);
  //$t2=explode('"',$t1[1]);
  $token=$m[1];
  //echo $h1;
echo '
<TR><TD class="cat" style="text-align:left"><a id="fav" href="cineplex_f_fav.php?token='.$token.'" target="_blank">Favorite</a></TD>
';
echo '<TD class="form" colspan="2"><form action="cineplex_f.php" target="_blank">
<input type="hidden" name="page" id="page" value="">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="gen" id="gen" value="">
<input type="hidden" name="token" id="token" value="'.$token.'">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="submit" id="send" value="cauta"></form></td>
';
$gen="";
$link="cineplex_f.php?tip=release&page=1&gen=".$gen."&token=".$token."&title=Recente";
$title="Recente";
echo '<TR><TD class="cat" ><a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
$n=1;
$out="";
  $t1=explode('dropdown genre-filter">',$h1);
  $t2=explode('</ul',$t1[1]);
  $videos = explode('input id="', $t2[0]);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('value="',$video);
    $t2=explode('"',$t1[1]);
    $gen=$t2[0];
    $t3=explode('>',$t1[1]);
    $t4=explode('<',$t3[2]);
    $title=$t4[0];
    $out=$out.'"'.$title.'" => "'.$gen.'",'."\n";
    $link="cineplex_f.php?tip=release&page=1&gen=".$gen."&token=".$token."&title=".$title;
    if ($n==0) echo '<TR>';
    echo '<TD class="cat"><a href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n>2) {
     echo '</tr>';
     $n=0;
    }
}
//echo "\n".$token."\n";
//echo $out;
//////////////////////////////////////////////////////////////////

//https://cineplex.to/movies?loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=movie&q=&token=f7f9ijktnc3blmeelnvo2n6j75
//https://cineplex.to/index/loadmoviesnew
//loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=movie&q=&token=f7f9ijktnc3blmeelnvo2n6j75
?>
</table>
</div></body>
</HTML>
