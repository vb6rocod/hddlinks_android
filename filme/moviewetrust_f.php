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
$height="278px";
/* ==================================================== */
$has_fav="yes";
$has_search="yes";
$has_add="yes";
$has_fs="yes";
$fav_target="moviewetrust_f_fav.php?host=";
$add_target="moviewetrust_f_add.php";
$add_file="";
$fs_target="moviewetrust_fs.php";
$target="moviewetrust_f.php";
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
if ($tip=="search") {
  $page_title = "Cautare: ".$tit;
  if ($page == 1) file_put_contents($base_cookie."filme.dat",$tit);
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
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function openlink1(link) {
  msg="link1.php?file=" + link;
  window.open(msg);
}
function openlink(link) {
  on();
  var request =  new XMLHttpRequest();
  var the_data = "link=" + link;
  //alert (the_data);
  var php_file="link1.php";
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
     // alert (msg); pt.mp
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "52") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="http://imdb.com/imdb.php&" + val_imdb;
     openlink (msg);
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
    //alert (id);
    val_imdb=document.getElementById(id).value;
    msg="imdb.php?" + val_imdb;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
  } else if (event.shiftKey) {
    id = "imdb_" + event.target.id;
    //alert (id);
    val_imdb=document.getElementById(id).value;
    msg="http://imdb.com/imdb.php&" + val_imdb;
    openlink1(msg);
  }
}
function on() {
    document.getElementById("overlay").style.display = "block";
}

function off() {
    document.getElementById("overlay").style.display = "none";
}
$(document).on('keyup', '.imdb', isValid);
document.onkeypress =  zx;
</script>
</head>
<body>
<a href='' id='mytest1'></a>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<?php
$w=0;
$n=0;

if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
echo '<H2>'.$page_title.'</H2>'."\r\n";

echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
echo '<TR>'."\r\n";
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
   // tip
   // moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=popular
   } else {
     echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   }
} else {
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
}
echo '</TR>'."\r\n";
if ($page==1000 && $tip=="release") {
echo '<TR>'."\r\n";
$a="Popular";
$b="moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=popular";
echo '<TD class="cat"><a href="'.$b.'">'.$a.'</a></TD>'."\r\n";
$a="Now Playing";
$b="moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=now_playing";
echo '<TD class="cat"><a href="'.$b.'">'.$a.'</a></TD>'."\r\n";
$a="Top Rated";
$b="moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=top_rated";
echo '<TD class="cat"><a href="'.$b.'">'.$a.'</a></TD>'."\r\n";
$a="Upcoming";
$b="moviewetrust_f.php?page=1&tip=release&title=moviewetrust&link=upcoming";
echo '<TD class="cat"><a href="'.$b.'">'.$a.'</a></TD>'."\r\n";
echo '</TR>'."\r\n";
}
if($tip=="release") {
  $l="https://api.themoviedb.org/3/movie/".$link."?api_key=".$api_key."&language=en-US&page=".$page;
} else {
  $search=str_replace(" ","+",$tit);
  $l="https://api.themoviedb.org/3/search/multi?api_key=".$api_key."&language=en-US&query=".$search."&page=".$page."&include_adult=false";
}
///////////////////////////////////////////////
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:88.0) Gecko/20100101 Firefox/88.0";
//echo $l;
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close ($ch);
  $x=json_decode($html,1);
  //print_r ($x);
  $r=array();
if ($tip=="release") {
 for ($k=0;$k<count($x['results']);$k++) {
   $title=$x['results'][$k]['title'];
   if (isset($x['results'][$k]['poster_path']))  // backdrop_path
    $image="http://image.tmdb.org/t/p/w500".$x['results'][$k]['poster_path'];
   else
    $image="blank.jpg";
   $link=$x['results'][$k]['id'];
   $r[]=array($link,$title,$image,"");
 }
} else {
 for ($k=0;$k<count($x['results']);$k++) {
  if ($x['results'][$k]['media_type'] == "movie") {
   $title=$x['results'][$k]['title'];
   if (isset($x['results'][$k]['poster_path']))  // backdrop_path
    $image="http://image.tmdb.org/t/p/w500".$x['results'][$k]['poster_path'];
   else
    $image="blank.jpg";
   $link=$x['results'][$k]['id'];
   $r[]=array($link,$title,$image,"");
  } elseif ($x['results'][$k]['media_type'] == "person") {
    $link=$x['results'][$k]['id'];
    $title=$x['results'][$k]['name'];
    if ($x['results'][$k]['profile_path'])
    $image="http://image.tmdb.org/t/p/w500".$x['results'][$k]['profile_path'];
    else
    $image="blank.jpg";
    $r[]=array($link,$title,$image,"p");
    for ($kk=0;$kk<count($x['results'][$k]['known_for']);$kk++) {
     if ($x['results'][$k]['known_for'][$kk]['media_type']== "movie") {
      $title=$x['results'][$k]['known_for'][$kk]['title'];
      if (isset($x['results'][$k]['known_for'][$kk]['poster_path']))  // backdrop_path
       $image="http://image.tmdb.org/t/p/w500".$x['results'][$k]['known_for'][$kk]['poster_path'];
      else
       $image="blank.jpg";
      $link=$x['results'][$k]['known_for'][$kk]['id'];
      $r[]=array($link,$title,$image,"");
     }
    }
  }
 }
}
for ($k=0; $k<count($r);$k++) {
  $link=$r[$k][0];
  $title=$r[$k][1];
  $image=$r[$k][2];
  $person=$r[$k][3];
  $tit_imdb=$title;
  $imdb="";
  $year="";
  $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;

  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb."&tmdb=".$link;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  if ($person == "") {
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($has_add=="yes")
      echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($has_add == "yes")
      echo '<input type="hidden" id="fav_myLink'.$w.'" value="'.$fav_link.'"></a>'."\r\n";
    echo '</TD>'."\r\n";
  }
  $w++;
  } else {
    $link_f="moviewetrust_p.php?page=1&link=".$link."&title=".urlencode($title);
    echo '<td class="mp" width="25%"><a class ="imdb" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.' (person)</a>';
    echo '</TD>'."\r\n";
  }
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
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
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
