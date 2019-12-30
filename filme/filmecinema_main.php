<!DOCTYPE html>
<?php
include ("../common.php");
$main_title="filmecinema";
$target="filmecinema.php";
$fav_target="";
$recente="https://www.filmecinema.net";
function decode_code($code){
    return preg_replace_callback(
        "@\\\(x)?([0-9a-f]{2,3})@",
        function($m){
            return chr($m[1]?hexdec($m[2]):octdec($m[2]));
        },
        $code
    );
}
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$l="https://www.filmecinema.net";
$cookie=$base_cookie."biz.dat";
require( 'cryptoHelpers.php');
require( 'aes_small.php');
$ua = $_SERVER['HTTP_USER_AGENT'];
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
  $html=decode_code($html);
  //echo $html;
if(preg_match_all('/toNumbers\(\"(\w+)\"/',$html)) {
//print_r ($m);
if (preg_match("/var\s*(\w+)\s*\=\s*atob\(\"(\S+)\"\)/mei",$html,$p)) {
  $html=str_replace("toNumbers(".$p[1],'toNumbers("'.base64_decode($p[2]).'"',$html);
}
if (preg_match("/(_0x[a-zA-Z0-9]+)\s*\=\s*\[\"(\S+)\"/mei",$html,$p)) {
 //print_r ($p);
 $html=str_replace("toNumbers(".$p[1]."[0]",'toNumbers("'.$p[2].'"',$html);
}
preg_match_all('/toNumbers\(\"(\w+)\"/',$html,$m);
$a=cryptoHelpers::toNumbers($m[1][1]);
$b=cryptoHelpers::toNumbers($m[1][0]);
$c=cryptoHelpers::toNumbers($m[1][2]);
$d=AES::decrypt($c,16,2,$a,16,$b);
/*
decrypt(c3, 2, a1, b2))  //b2,a1,c3
decrypt(c, 2, a, b) //a,b,c
*/
$d1=cryptoHelpers::toHex($d);
//echo $d1;
$domain = 'www.filmecinema.net';
$expire = time() + 3600;
$name   = 'vDDoS';
$value = $d1;

file_put_contents($cookie, "\n$domain\tTRUE\t/\tFALSE\t$expire\t$name\t$value", FILE_APPEND);
}
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

$ch = curl_init($l);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$html=curl_exec($ch);
curl_close($ch);


$html=str_between($html,'ul class="nav-category',"</ul");
//echo $html;
$videos = explode('<li', $html);
unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
    $t0 = explode('href="',$video);
    $t1 = explode('"', $t0[1]);
    $link = "https://www.filmecinema.net".$t1[0];
    $t2 = explode('/i>', $t0[1]);
    $t3 = explode('<', $t2[1]);
    $title = trim($t3[0]);
    $link=$target."?page=1&tip=release&link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title));
    if (!preg_match("/IN CURAND|FILME SERIALE/",$title)) {
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
