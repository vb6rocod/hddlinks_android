<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");

//$tit=$_GET["title"];
//$link=$_GET["link"];
$width="100px";
$height="140px";
$tit="Classic SciFi Movies";
$tip="search";
/* ==================================================== */
$has_fav="no";
$has_search="no";
$has_add="no";
$has_fs="yes";
$fav_target="";
$add_target="";
$add_file="";
$fs_target="moviewetrust_f.php";
$target="best_cs.php";
/* ==================================================== */
$next="";
$prev="";
$page=1;
/* ==================================================== */
$tit=unfix_t(urldecode($tit));
//$link=unfix_t(urldecode($link));
/* ==================================================== */
if (file_exists($base_cookie."filme.dat"))
  $val_search=file_get_contents($base_cookie."filme.dat");
else
  $val_search="";
$form='<form action="'.$target.'" target="_blank">
Cautare film:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="">
<input type="submit" id="send" value="Cauta...">
</form>';
/* ==================================================== */

  $page_title=$tit;
/* ==================================================== */

?>
<html>
<head>
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
var id_link="";
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file='<?php echo $add_target; ?>';
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
function isValid(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode,
    self = evt.target;
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "51") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      ajaxrequest(val_fav);
    }
    return true;
}
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
function isKeyPressed(event) {
  if (event.ctrlKey) {
    id = "imdb_" + event.target.id;
    val_imdb=document.getElementById(id).value;
    msg="imdb.php?" + val_imdb;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
  }
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>
</head>
<body>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<?php
$w=0;
$n=0;
echo '<H2>'.$page_title.'</H2>'."\r\n";

echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";

$f=array();
$ua="Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/109.0";
$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate');
  $h="";
  $ch = curl_init();
  for ($k=1;$k<3;$k++) {
  $l="https://www.imdb.com/list/ls063938357/?sort=list_order,asc&st_dt=&mode=detail&page=".$k;
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h .= curl_exec($ch);
  }
  curl_close($ch);
  //echo $h;
  //die();
  $videos=explode('<div class="lister-item-image',$h);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2=explode('>',$t1[2]);
  $t3=explode('<',$t2[1]);
  $title=$t3[0];
  $t1=explode('<p class="">',$video);
  $t2=explode("<",$t1[1]);
  $desc=$t2[0];
  $title=strip_tags($title);
  $link=$title;
  $t1=explode('loadlate="',$video);
  $t2=explode('"',$t1[1]);
  $image=$t2[0];
  $t1=explode('data-tconst="',$video);
  $t2=explode('"',$t1[1]);
  $imdb=$t2[0];
  $t1=explode('text-muted unbold">(',$video);
  $t2=explode(')',$t1[1]);
  $year=$t2[0];
  $tit_imdb=$title;
  $link_f=$fs_target.'?tip=search&page=1&link=&title='.urlencode($title);
  if ($title) {
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  //$image="r_m.php?file=".$image;
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>
    '.$title.' ('.$year.')</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($has_add=="yes")
      echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>
    '.$title.' ('.$year.')</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($has_add == "yes")
      echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'"></a>'."\r\n";
    echo '</TD>'."\r\n";
  }
  $n++;
  echo '<td class="mp">'.$desc.'</TD>'."\r\n";
  $w++;
  $n++;
  if ($n == 2) {
  echo '</tr>'."\r\n";
  $n=0;
  }
  }
 }

/* bottom */
  if ($n < 2 && $n > 0) {
    for ($k=0;$k<2-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }

echo "</table>"."\r\n";
echo "</table>";
?></body>
</html>
