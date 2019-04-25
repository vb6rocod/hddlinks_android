<!DOCTYPE html>
<?php
error_reporting(0);
function decode_entities($text) {
    $text= html_entity_decode($text,ENT_QUOTES,"ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
    $text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
    return $text;
}
//https://www4.the123movieshub.net/tv-series.html
include ("../common.php");
$base_serv="https://www4.the123movieshub.net";
$y=file_get_contents($base_pass."height.txt");
$y=($y-150)."px";
//$y=($y-250)."px";
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];

if ($tip=="search")
$page_title = "Cautare: ".$tit;
else
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
Cautare serial:  <input type="text" id="title" name="title" value="">
<input type="hidden" id="page" name="page" value="'.$page.'">
<input type="hidden" id="tip" name="tip" value="search">
<input type="submit" value="Cauta !"></form>';
$r=array();
$head=array('Accept: text/html, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Referer: https://www.filme-online.to/film/gan/Perdida',
'X-Requested-With: XMLHttpRequest');
if ($tip=="release") {
  $l=$base_serv."/tv-series.html?page=".$page;
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
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
  $l=$base_serv."/movie/search/".$search;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_ENCODING, "");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  //curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  $html = curl_exec($ch);
  curl_close($ch);

}
  //echo $html;
  $videos = explode('<div class="ml-item', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $link=$t2[0];
    $t1=explode('mli-info"><h2>',$video);
    $t2=explode("<",$t1[1]);
    $title=decode_entities($t2[0]);
    $t1=explode('&url=',$video);
    $t2=explode('"',$t1[1]);
    $image="http:".$t2[0];
    if (strpos($link,"season") !== false) array_push($r ,array($title,$link, $image));
  }
  //print_r ($r);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="text/javascript">
function get_XmlHttp() {
  // create the variable that will contain the instance of the XMLHttpRequest object (initially with null value)
  var xmlHttp = null;
  if(window.XMLHttpRequest) {		// for Forefox, IE7+, Opera, Safari, ...
    xmlHttp = new XMLHttpRequest();
  }
  else if(window.ActiveXObject) {	// for Internet Explorer 5 or 6
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  return xmlHttp;
}

function ajaxrequest(link) {
  var request =  get_XmlHttp();		// call the function for the XMLHttpRequest instance

  // create pairs index=value with data that must be sent to server
  //var the_data = {mod:add,title:title, link:link}; //Array
  //link=document.getElementById('server').innerHTML;
  var the_data = link;
  //alert(the_data);
  var php_file="123movies_s_add.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
       alert (request.responseText);
    }
  }
}
</script>
<script type="text/javascript">
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
        self = evt.target;
        //self = document.activeElement;
        //self = evt.currentTarget;
    //console.log(self.value);
       //alert (charCode);
    if (charCode == "97" || charCode == "49") {
     //alert (self.id);
     id = "imdb_" + self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=series&" + val_imdb;
     window.open(msg);
    } else if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
//$(document).on('keydown', '.imdb', isValid);
</script>
<script type="text/javascript">
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "50") {
      document.getElementById("fav").click();
    }
   }
document.onkeypress =  zx;
</script>
<link rel="stylesheet" type="text/css" href="../custom.css" />
<style>
nav ul{height:<?php echo $y; ?> ; width:100%;}
nav ul{overflow:hidden; overflow-y:scroll;}
</style>

</head>
<body><div id="mainnav">
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
/*
function fromRGB($R, $G, $B)
{

    $R = dechex($R);
    if (strlen($R)<2)
    $R = '0'.$R;

    $G = dechex($G);
    if (strlen($G)<2)
    $G = '0'.$G;

    $B = dechex($B);
    if (strlen($B)<2)
    $B = '0'.$B;

    return '#' . $R . $G . $B;
}

*/
//echo fromRGB("10","105","150");
//echo fromRGB("100","200","255");
//#0a6996#64c8ff
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
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD width="25%"><a id="fav" a href="123movies_s_fav.php">Favorite</a></TD><TD style="height:10px;text-align:left" colspan="2">'.$form.'</TD>';
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
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
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".$link_film."&image=".urlencode($image);
  if ($n==0) echo '<TR>';
  $link="123movies_s_ep.php?tip=tv&title=".urlencode(fix_t($title))."&link=".urlencode($link_film)."&image=".$image."&sez=&ep=%ep_tit=";
   if ($tast == "NU")
    echo '<td align="center" width="25%"><a href="'.$link.'" target="blank"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.$title.'</font></a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  else {
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  echo '<td align="center" width="25%"><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><img src="'.$image.'" width="160px" height="200px"><BR><font size="4">'.$title.'</font><input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'"></a></TD>';
  $w++;
  }
   $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
}
echo '</TABLE>';
} else {
echo '<table border="0px" width="100%"><TR>';
echo '<td width=50%" style="vertical-align:top">
        <nav>
            <ul>';
$c=count($r);
//print_r ($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $link_film=$r[$k][1];
  $image=$r[$k][2];
  $link="123movies_fs.php?tip=movie&title=".urlencode(fix_t($title))."&link=".urlencode($link_film)."&image=".$image."&sez=&ep=%ep_tit=";
  $year="";
  $val_imdb="title=".$title."&year=".$year."&imdb=";
  $val_img=$image;
  echo '<li><a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="blank"><input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_imdb.'"><input type="hidden" id="img_myLink'.($w*1).'" value="'.$val_img.'"><font size="4">'.$title.'</font></a></li>';
  $w++;

}
echo '
            </ul>
        </nav>
</TD>
<td width="310px" align="center"><img id="p4" src="blank.jpg" width="300px" height="420px">
</TR>
<tr><TD colspan="2"><label id="desc"></label></TD></TR></TABLE>';
}
if ($tip=="release") {
echo '<table border="1px" width="100%"><TR>'."\n\r";
echo '<TD style="height:10px;text-align:right">';
if ($page > 1)
echo '<a href="'.$prev.'"><font size="4">&nbsp;&lt;&lt;&nbsp;</font></a> | <a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
else
echo '<a href="'.$next.'"><font size="4">&nbsp;&gt;&gt;&nbsp;</font></a></TD></TR>';
echo '</TABLE>';
}
?>
</div></body>
</html>
