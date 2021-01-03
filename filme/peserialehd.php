<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");

$tit=$_GET["title"];
$link=$_GET["link"];
$width="200px";
$height="278px";    //(851/351)
$height=intval(200*(351/851))."px";
/* ==================================================== */
$has_fav="no";
$has_search="no";
$has_add="yes";
$has_fs="no";
$fav_target="";
$add_target="peserialehd_add.php";
$add_file="";
$fs_target="";
$target="peserialehd.php";
/* ==================================================== */

/* ==================================================== */
$tit=unfix_t(urldecode($tit));
if (preg_match("/filme/i",$tit))
 $tip="movie";
else
 $tip="serie";
$link=unfix_t(urldecode($link));
$page_title = $tit;
$ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
$link=str_replace(" ","%20",$link);
$link=str_replace("+","%2B",$link);
//echo $link;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
curl_setopt($ch, CURLOPT_USERAGENT, $ua);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_REFERER, "https://www.peserialehd.us");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
$html = curl_exec($ch);
curl_close($ch);
//echo $html;
//die();
$t1=explode("class='blog-pager-older-link btn'",$html);
$t2=explode("href='",$t1[1]);
$t3=explode("'",$t2[1]);
$next=$target."?link=".urlencode(fix_t($t3[0]))."&title=".urlencode(fix_t($tit));
if (preg_match("/class\=\'blog\-pager\-newer\-link btn/",$html)) {
$t1=explode("class='blog-pager-newer-link btn",$html);
$t2=explode("href='",$t1[1]);
$t3=explode("'",$t2[1]);
$prev=$target."?link=".urlencode(fix_t($t3[0]))."&title=".urlencode(fix_t($tit));
} else {
$prev="";
}
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
    if  (charCode == "51") {
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
echo '<TR>'."\r\n";
if ($prev=="") {
   if ($has_fav=="yes" && $has_search=="yes") {
     echo '<TD class="nav"><a id="fav" href="'.$fav_target.'" target="_blank">Favorite</a></TD>'."\r\n";
     echo '<TD class="form" colspan="2">'.$form.'</TD>'."\r\n";
     echo '<TD class="nav" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   } else if ($has_fav=="no" && $has_search=="yes") {
     echo '<TD class="nav"><a id="fav" href="">Reload...</a></TD>'."\r\n";
     echo '<TD class="form" colspan="2">'.$form.'</TD>'."\r\n";
     echo '<TD class="nav" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   } else if ($has_fav=="yes" && $has_search=="no") {
     echo '<TD class="nav"><a id="fav" href="'.$fav_target.'" target="_blank">Favorite</a></TD>'."\r\n";
     echo '<TD class="nav" colspan="3" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   } else if ($has_fav=="no" && $has_search=="no") {
     echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   }
} else {
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
}
echo '</TR>'."\r\n";


$videos = explode('<article', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $year="";
  $t1=explode("href='",$video);
  $t2=explode("'",$t1[1]);
  $link=$t2[0];
  $t1=explode("data-img='",$video);
  $t2=explode("'",$t1[1]);
  $image=$t2[0];
  $t1=explode("title='",$video);
  $t2=explode("'",$t1[1]);
  $title=$t2[0];
  $title=prep_tit($title);
  if ($tip=="movie")
    $link_f='filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title));
  else
    $link_f='peserialehd_ep.php?file='.urlencode($link).'&title='.urlencode(fix_t($title));
  if ($n==0) echo '<TR>'."\r\n";
  $fav_link="tip=".$tip."&mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  $image="r_m.php?file=".$image;
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    '."\r\n";
    if ($has_add=="yes")
      echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    '."\r\n";
    if ($has_add == "yes")
      echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'"></a>'."\r\n";
    echo '</TD>'."\r\n";
  }
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
}
  if ($n < 4 && $n > 0) {
    for ($k=0;$k<4-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
echo '<tr>
<TD class="nav" colspan="4" align="right">'."\r\n";
if ($prev <>"")
  echo '<a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
else
  echo '<a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>'."\r\n";
echo "</table>"."\r\n";
?>
<br></body>
</html>
