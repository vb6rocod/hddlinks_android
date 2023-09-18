<!DOCTYPE html>
<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$main_title="xhamster";
$target="xhamster.php";
$fav_target="";
$recente="";
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
      window.open("adult_fav.php");
    }
   }
document.onkeypress =  zx;
</script>
</head>
<body>
<?php
include ("../common.php");

if (file_exists($base_cookie."adult.dat"))
  $val_search=file_get_contents($base_cookie."adult.dat");
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
echo '<TR><TD class="cat">'.'<a class ="nav" href="">Reload...</a></TD>';
echo $form;
echo '</TR>';
$n=0;
$l="https://xhamster.com/categories";
//$l="https://xhamster.com/categories";
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/116.0";
$head=array('Cookie: lang=ro; contest_region=europe; prs=--; ss=%7B%22domainAccessibility%22%3Atrue%2C%22botDetection%22%3Anull%7D; parental-control=yes; cookie_accept=%7B%22e%22%3A1%2C%22n%22%3A1%2C%22tp%22%3A1%7D;');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $t1=explode('window.initials=',$html);
  $t2=explode(';</script>',$t1[1]);
  $r=json_decode($t2[0],1);
  //print_r ($r);
  $r1=$r['pageStore']['popular']['assignable'];
//$html=str_between($html,'<div class="letter-blocks page">','<div class="search">');
//$videos = explode('<a', $html);
//unset($videos[0]);
//$videos = array_values($videos);

//foreach($videos as $video) {
for ($z=0;$z<count($r1);$z++) {
for ($j=0;$j<count($r1[$z]['items']);$j++) {
    //$t1=explode('href="',$video);
    //$t2=explode('"',$t1[1]);
    //$link=$t2[0];
    $link=$r1[$z]['items'][$j]['url'];
  	//$t3=explode(">",$t1[1]);
  	//$t4=explode("<",$t3[1]);
  	//$title=trim($t4[0]);
  	//$title=prep_tit($title);
  	$title=$r1[$z]['items'][$j]['name'];
    $link=$target."?page=1&tip=release&link=".urlencode(fix_t($link))."&title=".urlencode(fix_t($title));
    if ($title) {
	if ($n == 0) echo "<TR>"."\r\n";
	echo '<TD class="cat">'.'<a class ="cat" href="'.$link.'" target="_blank">'.$title.'</a></TD>';
    $n++;
    if ($n == 3) {
     echo '</TR>'."\r\n";
     $n=0;
    }
    }
}}
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
