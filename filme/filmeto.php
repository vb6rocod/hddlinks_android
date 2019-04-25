<!DOCTYPE html>
<?php
error_reporting(0);
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
include ("../common.php");

if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];

if ($tip=="search") {
$page_title = "Cautare: ".$tit;
file_put_contents($base_cookie."filme.dat",urldecode($tit));
} else
$page_title=$tit;
$base=basename($_SERVER['SCRIPT_FILENAME']);
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);

if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);
if (!isset($_GET["page"]))
  $page=1;
else
  $page=$_GET["page"];
$next=$base."?page=".($page+1)."&".$p;
$prev=$base."?page=".($page-1)."&".$p;
$form='<form action="'.$base.'" target="_blank">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" id="page" name="page" value="'.$page.'">
<input type="hidden" id="tip" name="tip" value="search">
<input type="submit" id="send" value="Cauta !"></form>';
$r=array();
$head=array('Accept: text/html, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://www.filme-online.to/film/gan/Perdida',
'X-Requested-With: XMLHttpRequest');
if ($tip=="release") {
  $l="https://www.filme-online.to/ajax/filtru.php";
  $post="tip=film&offset=".(($page-1)*48)."&sort=last";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

} else {
  //c=md5(b+s7euu24fblrg914z)
  /* //rezerva
  $e-"https://www.filme-online.to/assets/js/onmovies.js";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $h = curl_exec($ch);
  curl_close($ch);
  $t1=explode("c=md6(",$h);
  $t2=explode(")",$t1[1]);
  $token=md5($t2[0]);
  */
  $search=str_replace(" ","+",$tit);
  $l="https://www.filme-online.to/search/".$search;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);

}
  //echo $html;
  $videos = explode('data-movie-id=', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('mli-info"><h2>',$video);
    $t2=explode("<",$t1[1]);
    $title=decode_entities($t2[0]);
    $t1=explode('data-original="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    if (strpos($link,"/film") !== false) array_push($r ,array($title,$link, $image));
  }
  //print_r ($r);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=movie&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    }
    return true;
}
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false && e.target.type != "text") {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
    }
   }
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>


</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<div id="mainnav">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
if (file_exists($base_pass."lista.txt")) {
$tabel=trim(file_get_contents($base_pass."lista.txt"));
} else {
$tabel="TABEL";
}
$w=0;
$n=0;
$w=0;
echo '<H2>'.$page_title.'</H2>';



if ($tip=="release") {
echo '<table border="0px" width="100%"><TR>'."\n\r";
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
} else {
echo '<TD class="form" style="height:10px;text-align:left" colspan="3">'.$form.'</TD>';
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo '</TABLE>';
}
if ($tabel=="TABEL") {
echo '<table border="1px" width="100%">'."\n\r";
$c=count($r);
//print_r ($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $link_film=$r[$k][1];
  $image=$r[$k][2];
  if ($n==0) echo '<TR>';
  $link="filmeto_fs.php?tip=movie&title=".urlencode(fix_t($title))."&link=".urlencode($link_film)."&image=".$image."&sez=&ep=%ep_tit=";
   if ($tast == "NU")
    echo '<td class="mp" align="center" width="25%"><a class="imdb" href="'.$link.'" target="blank"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  else {
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td class="mp" align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="200px" height="278px"><BR>'.$title.'</a></TD>';
  $w++;
  }
   $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '</TABLE>';
}
if ($tip=="release") {
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a class="nav" href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
echo '</TABLE>';
}
?>
</div></body>
</html>
