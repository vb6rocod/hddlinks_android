<!DOCTYPE html>
<?php
error_reporting(0);
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
$height=intval(200*(232/208))."px";
/* ==================================================== */
$has_main="yes";
$has_fav="no";
$has_search="yes";
$has_add="yes";
$has_fs="no";
$fav_target="adult_fav.php";
$add_target="adult_add.php";
$add_file="";
$fs_target="filme_link.php";
$target="youjizz.php";
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
/* ==================================================== */
if (file_exists($base_cookie."adult.dat"))
  $val_search=file_get_contents($base_cookie."adult.dat");
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
if ($tip=="search") {
  $page_title = "Cautare: ".$tit;
  if ($page == 1) file_put_contents($base_cookie."adult.dat",$tit);
} else
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
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = link;
  var php_file="adult_link.php";
  request.open("POST", php_file, true);			// set the request

  // adds a header to tell the PHP script to recognize the data as is sent via POST
  request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  request.send(the_data);		// calls the send() method with datas as parameter

  // Check request status
  // If the response is received completely, will be transferred to the HTML tag with tagID
  request.onreadystatechange = function() {
    if (request.readyState == 4) {
    off();
    document.getElementById("mytest1").href=request.responseText;
      document.getElementById("mytest1").click();
    }
  }
}
function add_fav(link) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file='adult_add.php';
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
    var charCode = (evt.which) ? evt.which : evt.keyCode,
    self = evt.target;
    if  (charCode == "51" && evt.target.type != "text") {   // add to fav
      id_link=self.id;
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      add_fav(val_fav);
    }
    return true;
}
   function zx(e){
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
     }
   }
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>
</head>
<body>
<script>
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
</script>
<?php
$c="";
echo "<a href='".$c."' id='mytest1'></a>";
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
if ($flash=="chrome") $flash="mp";
$w=0;
$n=0;
if ($tast=="NU")
echo '<H2><a href="adult_fav.php" target="_blank">'.$page_title.'</a></H2>'."\r\n";
else
echo '<H2>'.$page_title.'</H2>'."\r\n";

echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
echo '<TR>'."\r\n";
if ($has_main == "no") {
if ($page==1) {
   if ($tip == "release") {
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
     echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   }
} else {
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
}
} else {
if ($page == 1)
  echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
else
  echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
}
echo '</TR>'."\r\n";

if($tip=="release") {
  if ($page>1)
    $l = "https://www.youjizz.com".$link.$page.".html";
  else
    $l = "https://www.youjizz.com".$link.$page.".html";
} else {
  $search=str_replace(" ","-",$tit);
  if ($page > 1)
    $l="https://www.youjizz.com/search/".$search."-".$page.".html?";
  else
    $l="https://www.youjizz.com/search/".$search."-".$page.".html?";
}
$host=parse_url($l)['host'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:55.0) Gecko/20100101 Firefox/55.0');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
//echo $html;
$r=array();
$videos = explode('div class="video-item"',$html);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
  $t1=explode('href="',$video);
  $t2 = explode('"', $t1[1]);
  $link = "https://www.youjizz.com".$t2[0];
  $t1=explode('class="video-title">',$video);
  $t2=explode(">",$t1[1]);
  $t3=explode("<",$t2[1]);
  $title=$t3[0];
  $title = trim(strip_tags($title));
  $title = prep_tit($title);
  $t0=explode('class="img-responsive',$video);
  $t1 = explode('src="', $t0[1]);
  $t2 = explode('"', $t1[1]);
  $image = $t2[0];
  if (strpos($image,"http") === false) $image="https:".$image;
  $t1 = explode('class="time">',$video);
  $t2 = explode ('<',$t1[1]);
  $durata=trim($t2[0]);
  $durata = preg_replace("/\n|\r/"," ",strip_tags($durata));
  if ($durata) $title=$title." (".$durata.')';
  if ($title) array_push($r ,array($title,$link, $image));
}
$c=count($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $link=$r[$k][1];
  $image=$r[$k][2];
  if ($has_fs =="no")
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&width=".$width."&height=".$height."&file=adult_link.php";
  else
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&width=".$width."&height=".$height."&file=filme_link.php";
  if (true) {
  if ($n==0) echo '<TR>'."\r\n";
  if ($tast == "NU" && $flash !="mp") {
   if ($has_fs=="no")
    $link_f='adult_link.php?link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
   else
    $link_f='../filme/filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<a onclick="add_fav('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else if ($tast == "NU" && $flash == "mp") {
   if ($has_fs=="yes") {
    $link_f='../filme/filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" href="'.$link_f.'" id="myLink'.$w.'" target="_blank">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<a onclick="add_fav('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
   } else {
    $link_f='link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" id="myLink'.$w.'" onclick="ajaxrequest('."'".$link_f."'".')" style="cursor:pointer;">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<a onclick="add_fav('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
   }
  } else if ($tast == "DA" && $flash !="mp") {
   if ($has_fs=="no")
    $link_f='adult_link.php?link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
   else
    $link_f='../filme/filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" href="'.$link_f.'" id="myLink'.$w.'" target="_blank">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'">'."\r\n";
    echo '</TD>'."\r\n";
  } else { // tast="DA" && flash=="mp"
   if ($has_fs=="yes") {
    $link_f='../filme/filme_link.php?file='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" href="'.$link_f.'" id="myLink'.$w.'" target="_blank">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'">'."\r\n";
    echo '</TD>'."\r\n";
   } else {
    $link_f='link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image;
    echo '<td class="mp" width="25%"><a class="imdb" id="myLink'.$w.'" onclick="ajaxrequest('."'".$link_f."'".')" style="cursor:pointer;">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>'."\r\n";
    echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'">'."\r\n";
    echo '</TD>'."\r\n";
   }
  }
  $w++;
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
  } // end preg_match title
 }

/* bottom */
  if ($n < 4 && $n > 0) {
    for ($k=0;$k<4-$n;$k++) {
      echo '<TD></TD>'."\r\n";
    }
    echo '</TR>'."\r\n";
  }
echo '<tr>
<TD class="nav" colspan="4" align="right">'."\r\n";
if ($page > 1)
  echo '<a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
else
  echo '<a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>'."\r\n";
echo "</table>"."\r\n";
echo "</table>";
?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
