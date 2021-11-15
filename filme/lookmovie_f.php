<?php
error_reporting(0);
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}
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
$last_good="https://lookmovie.ag";
$last_good="https://lookmovie.io";
$fav_target="lookmovie_f_fav.php?host=".$last_good;
$add_target="lookmovie_f_add.php";
$add_file="";
$fs_target="lookmovie_fs.php";
$target="lookmovie_f.php";
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
$ua = $_SERVER['HTTP_USER_AGENT'];
$ua="Mozilla/5.0 (Windows NT 10.0; rv:86.0) Gecko/20100101 Firefox/86.0";
if($tip=="release") {
 if (strpos($ua,"Windows") !== false)
  $l=$last_good."/?p=".$page."&r=1";
 else
  $l=$last_good."/page/".$page;
} else {
  $search=str_replace(" ","%20",$tit);
  $l=$last_good."/movies/search/?p=".$page."&q=".$search;
}
$host=parse_url($l)['host'];
$cookie=$base_cookie."lookmovie.txt";
 if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
  $h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
  file_put_contents($cookie,$h1);
  unlink ("/storage/emulated/0/Download/cookies.txt");
 } elseif (file_exists($base_cookie."cookies.txt")) {
  $h1=file_get_contents($base_cookie."cookies.txt");
  file_put_contents($cookie,$h1);
  unlink ($base_cookie."cookies.txt");
 }
if (file_exists($base_pass."firefox.txt"))
 $ua=file_get_contents($base_pass."firefox.txt");
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:75.0) Gecko/20100101 Firefox/75.0";
//echo $h;

$head=array('Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_REFERER,$last_good);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  //die();
if (preg_match("/invisible\-recaptcha\-key/",$html)) {
//echo '<a href="look_captcha.php?host=https://lookmovie.io&cookie=lookmovie.txt&target=lookmovie_f.php&title=lookmovie" target="_blank"><b>GET COOKIE</b></a>';
//header ("Location: look_captcha1.php");
   if ($flash=="mp")
    echo '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site=https://lookmovie.io/account/login&cookie='.$cookie.'#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">GET cloudflare cookie</a>';
   else
    header('Location: cf.php?site=https://lookmovie.io/account/login&cookie='.$cookie);
exit ;
}
?>
<!DOCTYPE html>
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
   } else {
     echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
   }
} else {
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
}
echo '</TR>'."\r\n";
if (strpos($html,'div class="movie-list-detailed mobile') === false)
$videos = explode('div class="movie-', $html);
else
$videos=explode('div class="movie-list-detailed mobile',$html);
//print_r ($videos);
unset($videos[0]);
$videos = array_values($videos);
//print_r ($videos);
foreach($videos as $video) {
  $t1 = explode('href="',$video);
  $t2=explode('"',$t1[1]);
  $link = $t2[0];
  if (strpos($link,"http") === false) $link="https://".$host.$link;
  if (strpos($video,'detailed__title">') === false) {
  $t3 = explode('>', $t1[3]);
  $t4 = explode('<', $t3[1]);
  } else {
  $t3=explode('detailed__title">',$video);
  $t4=explode('<',$t3[1]);
  }
  $title = trim($t4[0]);
  $title=prep_tit($title);
  $imamge="";
  $t1 = explode('data-src="', $video);
  $t2 = explode('"', $t1[1]);
  $image=$t2[0];
  if (strpos($image,"http") === false && $image) $image="https://".$host.$image;
  if (!$image) $image="blank.jpg";
  //$t1=explode('data-src="'
  //$image=str_replace("w342","w300",$image);
  //$image="https://image.tmdb.org/t/p/w342/zfxZSe4cGPYj3XUOgJO8OBRy47n.jpg";
  $rest = substr($title, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }
  if (strpos($video,'year-intitle">') !== false) {
  $t1=explode('class="year">',$video);
  $t2=explode('<',$t1[1]);
  } else {
  $t1=explode('year-intitle">',$video);
  $t2=explode('<',$t1[1]);
  }
  $year=$t2[0];
  $imdb="";
  $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  if ($title && strpos($link,"/movie") !== false) {
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  //$image="r_m.php?file=".$image;
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
  $n++;
  if ($n == 4) {
  echo '</tr>'."\r\n";
  $n=0;
  }
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
?></body>
</html>
