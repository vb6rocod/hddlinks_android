<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];
$link=$_GET["link"];
$width="200px";
$height="100px";
/* ==================================================== */
$has_fav="no";
$has_search="no";
$has_add="no";
$has_fs="no";
$fav_target="";
$add_target="";
$add_file="";
$fs_target="";
$target="clicksud.php";
/* ==================================================== */
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
/* ==================================================== */
$tit=unfix_t(urldecode($tit));
$link=unfix_t(urldecode($link));

$page_title=$tit;
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

$ua = $_SERVER['HTTP_USER_AGENT'];
//echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://www.clicksud.org");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
if (preg_match("/content\=\'(.*?)\'\s+property\=\'og\:image/mei",$html,$m))
 $image=$m[1];
else
 $image="blank.jpg";
$n=0;
$last_tit="aaaa";
$videos = explode('href="', $html);

unset($videos[0]);
$videos = array_values($videos);

foreach($videos as $video) {
  $t2 = explode('"', $video);
  $link = $t2[0];
  if (preg_match("/title\=\'(.*?)\'/mei",$video,$m)) {
    //echo $m[1]."\n".$last_tit."\n";
    //if ($m[1] != $last_tit)
    //print_r ($m);
    $pat="/".$last_tit."/";
    if (!preg_match($pat,$m[1]))
      $title=$m[1];
    else
      $title="";
  } else {
   $t1=explode(">",$video);
   $t2=explode("<",$t1[1]);
   $title=$t2[0];
  }
  $title=urldecode(str_replace("%0A","",urlencode($title)));
  //$title=prep_tit($title);
  if (preg_match("/data-img\=\'(.*?)\'/",$video,$m))
    $image=$m[1];
  if (($title && !preg_match("/mai multe|share|link|Seriale online/i",$title) && preg_match("/\-episodul\-\d+/",$link)) || ($title && !preg_match("/mai multe|share|link|Seriale online/i",$title) && preg_match("/sezonul\-\d+\-episodul(\-|\_)\d+/",$link)) || (preg_match("/episodul\-\d+\-online\-\d+/",$link) && !preg_match("/mai multe/",$title) && preg_match("/episod/i",$title))) {
  $last_tit=$title;
  if ($has_fs == "no")
    $link_f='filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title));
  else
    $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($tit)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  if ($n==0) echo '<TR>'."\r\n";
  $imdb="";
  $year="";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit))."&year=".$year."&imdb=".$imdb;
  $fav_link="file=".$add_file."&mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.prep_tit($title).'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($has_add=="yes")
      echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.prep_tit($title).'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
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
 }
  if ($n < 4 && $n > 0) {
    for ($k=0;$k<4-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }

echo "</table>"."\r\n";
?>
<br></body>
</html>
