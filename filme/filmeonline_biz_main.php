<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$main_title="filmeonline.biz";
$target="filmeonline_biz.php";
$fav_target="";
$recente="https://www.filmeonline.biz";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
<title><?php echo $main_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
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
<body>
<?php
include ("../common.php");

if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
$form='<TD class="form" colspan="2">
<form action="'.$target.'" target="_blank">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="">
<input type="submit" id="send" value="Cauta...">
</form>
</td>';
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
echo '<TR><th class="cat" colspan="3">'.$main_title.'</th></TR>';
echo '<TR><TD class="cat">'.'<a class ="nav" href="'.$target.'?page=1&tip=release&link='.urlencode(fix_t($recente)).'&title=Recente" target="_blank">Recente...</a></TD>';
echo $form;
echo '</TR>';
$n=0;
$l="https://www.filmeonline.biz";
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."biz.dat";
require( 'cryptoHelpers.php');
require( 'aes_small.php');
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
//echo $html;
if(preg_match_all('/toNumbers\(\"(\w+)\"/',$html,$m)) {
//print_r ($m);
$a=cryptoHelpers::toNumbers($m[1][0]);
$b=cryptoHelpers::toNumbers($m[1][1]);
$c=cryptoHelpers::toNumbers($m[1][2]);
$d=AES::decrypt($c,16,2,$a,16,$b);

$d1=cryptoHelpers::toHex($d);
$domain = '.filmeonline.biz';
$expire = time() + 3600;
$name   = 'BPC';
$value = $d1;

file_put_contents($cookie, "\n$domain\tTRUE\t/\tFALSE\t$expire\t$name\t$value", FILE_APPEND);
$domain = '.filmeonline.biz';
$expire = time() + 3600;
$name   = 'AdskeeperStorage';
$value="%7B%220%22%3A%7B%22svspr%22%3A%22https%3A%2F%2Fwww.filmeonline.biz%2F%22%2C%22svsds%22%3A3%2C%22TejndEEDj%22%3A%22O9NAVGvWV%22%7D%2C%22C203236%22%3A%7B%22page%22%3A2%2C%22time%22%3A1547734833174%7D%2C%22C203225%22%3A%7B%22page%22%3A1%2C%22time%22%3A1547734832995%7D%7D";

file_put_contents($cookie, "\n$domain\tTRUE\t/\tFALSE\t$expire\t$name\t$value", FILE_APPEND);
$ch = curl_init($l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$html=curl_exec($ch);
curl_close($ch);
}

$html = str_between($html,'<ul class="categories">',"</ul>" );

$videos = explode('<li', $html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
    $t0 = explode('href="',$video);
    $t1 = explode('"', $t0[1]);
    $link = $t1[0];
    $t2 = explode('>', $t0[1]);
    $t3 = explode('<', $t2[1]);
    $title = $t3[0];
    $link=$target."?page=1&tip=release&link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title));
    if (!preg_match("/IN CURAND|FILME SERIALE|serial/i",$title)) {
	if ($n == 0) echo "<TR>"."\r\n";
	echo '<TD class="cat">'.'<a class ="cat" href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n == 3) {
     echo '</TR>'."\r\n";
     $n=0;
    }
    }
}
  if ($n < 3 && $n > 0) {
    for ($k=0;$k<3-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
 echo '</table>';
?>
</BODY>
</HTML>
