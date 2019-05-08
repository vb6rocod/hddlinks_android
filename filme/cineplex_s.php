<!DOCTYPE html>
<?php
error_reporting(0);
include ("../common.php");
$cookie=$base_cookie."cineplex.dat";
$title = $_GET["title"];
$page = $_GET["page"];
$tip=$_GET["tip"];
$gen=$_GET["gen"];
$token=$_GET["token"];
if ($tip=="search") {
  $page_title="Cautare: ".urldecode($title);
  file_put_contents($base_cookie."seriale.dat",urldecode($title));
} else
  $page_title=urldecode($title);
?>
<html><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Expires" content="0"/>
      <title><?php echo $page_title; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />
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
  var php_file="cineplex_s_add.php";
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
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
    }
   }
var id_link="";
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?tip=series&" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
function mouseOver(n) {
    zz="myLink" + n;
    //alert (zz);
    document.getElementById(zz).focus();
}
</script>

</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<div id="mainnav">
<H2></H2>
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
$w=0;
$n=0;
$w=0;
$cookie=$base_cookie."cineplex.dat";
$n=0;
echo '<H2>'.$page_title.'</H2>';

echo '<table border="1px" width="100%">'."\n\r";
if ($tip=="release") {
echo "<TR>";
if ($page > 1) {
echo '<tr><TD colspan="4" align="right">';
echo '<a class="nav" href="cineplex_s.php?page='.($page-1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="cineplex_s.php?page='.($page+1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}else {
echo '<TD colspan="4" align="right">
<a class="nav" href="cineplex_s.php?page='.($page+1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
}
if (file_exists($base_pass."cineplex_host.txt"))
  $host=file_get_contents($base_pass."cineplex_host.txt");
else
  $host="cinogen.net";
if ($tip=="search") {
//https://cineplex.to/index/loadmoviesnew
//loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=tv&q=star trek&token=f7f9ijktnc3blmeelnvo2n6j75
//$l="https://cineplex.to/search/auto?q=".str_replace("+","%20",urlencode($title))."&_=";
$l="https://".$host."/index/loadmovies";
$post="loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=tv&q=".str_replace("+","%20",urlencode($title))."&token=".$token;
$head=array('Accept-Language: ro-ro,ro;q=0.8,en-us;q=0.6,en-gb;q=0.4,en;q=0.2','Accept-Encoding: deflate','Content-Type: application/x-www-form-urlencoded','X-Requested-With: XMLHttpRequest','Content-Length: '.strlen($post));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://cineplex.to");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
} else {
//https://cineplex.to/index/loadmoviesnew
//loadmovies=showData&page=1&abc=All&genres=&sortby=Recent&quality=All&type=tv&q=&token=f7f9ijktnc3blmeelnvo2n6j75
  $l="https://".$host."/index/loadmoviesnew";
  $post="loadmovies=showData&page=".$page."&abc=All&genres=".$gen."&sortby=Recent&quality=All&type=tv&q=&token=".$token;
  //echo $post;
  $head=array('Accept-Language: ro-ro,ro;q=0.8,en-us;q=0.6,en-gb;q=0.4,en;q=0.2','Accept-Encoding: deflate','Content-Type: application/x-www-form-urlencoded','X-Requested-With: XMLHttpRequest','Content-Length: '.strlen($post));

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch,CURLOPT_REFERER,"https://cineplex.to");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
}
//echo $l;
if ($tip=="search") {
  $videos = explode('class="item">', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('movie-title">',$video);
    $t2=explode('<',$t1[1]);
    $title1= $t2[0];
    $t1=explode('movie-date">',$video);
    $t2=explode("<",$t1[1]);
    $year = trim($t2[0]);
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image= $t2[0];
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $imdb="https://".$host.$t2[0];
    $link_fs='cineplex_s_ep.php?tip=series&imdb='.$imdb.'&title='.urlencode(fix_t($title1)).'&image='.$image."&year=".$year."&token=".$token;

  $fav_link="mod=add&title=".urlencode(fix_t($title1))."&imdb=".$imdb."&year=".$year."&image=".$image;
  $val_imdb="title=".unfix_t(urldecode($title1))."&year=".$year."&imdb=";
  if ($n==0) echo '<TR>';
  if (strpos($link_fs,"/series") !== false) {
  if ($tast == "NU") {
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" onmouseover="mouseOver('.($p*1).')" href="'.$link_fs.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title1.' ('.$year.')<input type="hidden" id="imdb_myLink'.($p*1).'" value="'.$val_imdb.'"></a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  } else {

  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" href="'.$link_fs.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.unfix_t(urldecode($title1)).' ('.$year.')<input type="hidden" id="imdb_myLink'.($p*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($p*1).'" value="'.$fav_link.'"></a></TD>';

  }
  $p++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
  }
}
} else {
  $videos = explode('class="item">', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1=explode('movie-card-title">',$video);
    $t2=explode('<',$t1[1]);
    $title1= $t2[0];
    $t1=explode('mf-year ng-binding">',$video);
    $t2=explode("<",$t1[1]);
    $year = trim($t2[0]);
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image= $t2[0];
    $t1=explode('href="',$video);
    $t2=explode('"',$t1[1]);
    $imdb="https://".$host.$t2[0];
    $link_fs='cineplex_s_ep.php?tip=series&imdb='.$imdb.'&title='.urlencode(fix_t($title1)).'&image='.$image."&year=".$year."&token=".$token;
  //if ($title11 && strpos($link1,"/tv") !== false) {
  if ($n==0) echo '<TR>';
  $fav_link="mod=add&title=".urlencode(fix_t($title1))."&imdb=".$imdb."&year=".$year."&image=".$image;
  $val_imdb="title=".unfix_t(urldecode($title1))."&year=".$year."&imdb=";
  if ($tast == "NU") {
  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" onmouseover="mouseOver('.($p*1).')" href="'.$link_fs.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.$title1.' ('.$year.')<input type="hidden" id="imdb_myLink'.($p*1).'" value="'.$val_imdb.'"></a> <a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a></TD>';
  } else {

  echo '<td class="mp" align="center" width="25%"><a class="imdb" id="myLink'.($p*1).'" href="'.$link_fs.'" target="_blank"><img src="'.$image.'" width="200px" height="280px"><BR>'.unfix_t(urldecode($title1)).' ('.$year.')<input type="hidden" id="imdb_myLink'.($p*1).'" value="'.$val_imdb.'"><input type="hidden" id="fav_myLink'.($p*1).'" value="'.$fav_link.'"></a></TD>';

  }
  $p++;
  $n++;
  if ($n == 4) {
  echo '</tr>';
  $n=0;
  }
//}
}
}
if ($tip=="release") {
echo '<tr><TD colspan="4" align="right">';
if ($page > 1)
echo '<a class="nav" href="cineplex_s.php?page='.($page-1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&lt;&lt;&nbsp;</a> | <a class="nav" href="cineplex_s.php?page='.($page+1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
else
echo '<a class="nav" href="cineplex_s.php?page='.($page+1).'&gen='.$gen.'&token='.$token.'&tip='.$tip.'&title='.$title.'">&nbsp;&gt;&gt;&nbsp;</a></TD></TR>';
}
echo "</table>";
?>
<br></div></body>
</html>
