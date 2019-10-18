<!DOCTYPE html>
<?php
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
include ("../common.php");
include ("../util.php");
$page = $_GET["page"];
$tip= $_GET["tip"];
$tit=$_GET["title"];
$link=$_GET["link"];
$width="200px";
$height="107px";
/* ==================================================== */
$has_fav="yes";
$has_search="yes";
$has_add="yes";
$has_fs="yes";
$fav_target="tvhub_s_fav.php?host=https://tvhub.ro";
$add_target="tvhub_s_add.php";
$add_file="";
$fs_target="tvhub_s_ep.php";
$target="tvhub_s.php";
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
if (file_exists($base_cookie."seriale.dat"))
  $val_search=file_get_contents($base_cookie."seriale.dat");
else
  $val_search="";
$form='<form action="'.$target.'" target="_blank">
Cautare serial:  <input type="text" id="title" name="title" value="'.$val_search.'">
<input type="hidden" name="page" id="page" value="1">
<input type="hidden" name="tip" id="tip" value="search">
<input type="hidden" name="link" id="link" value="">
<input type="submit" id="send" value="Cauta...">
</form>';
/* ==================================================== */
if ($tip=="search") {
  $page_title = "Cautare: ".$tit;
  if ($page == 1) file_put_contents($base_cookie."seriale.dat",$tit);
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
$ua = $_SERVER['HTTP_USER_AGENT'];
$cookie=$base_cookie."hdpopcorns.dat";
$requestLink="https://tvhub.org/";
$requestLink="https://tvhub.ro/";
if ($page==1 && $tip !="search") {
if (file_exists($cookie)) unlink ($cookie);
$head=array(
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
'Accept-Language: en-US,en;q=0.5',
'Accept-Encoding: deflate, br',
'Connection: keep-alive',
'Upgrade-Insecure-Requests: 1');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $requestLink);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch,CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
  curl_setopt($ch, CURLOPT_HTTPGET, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_HEADER,1);
  $h = curl_exec($ch);
 if (strpos($h,"503 Service") !== false) {
  if (strpos($h,'id="cf-dn') === false)
   $q= getClearanceLink_old($h,$requestLink);
  else
   $q= getClearanceLink($h,$requestLink);

  curl_setopt($ch, CURLOPT_URL, $q);
  $h = curl_exec($ch);
  curl_close($ch);
 } else {
    curl_close($ch);
 }
}
if ($tip=="search") {
  if ($page == 1)
    $requestLink = "https://tvhub.ro/?s=".str_replace(" ","+",$tit);
  else
    $requestLink  = "https://tvhub.ro/page/".$page."/?s=".str_replace(" ","+",$tit);
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://tvhub.org/");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
} else {
  $requestLink="https://www1.tvhub.ro/wp-admin/admin-ajax.php";
  $post="action=load_more&page=".($page-1)."&template=cactus-channel%2Fcontent-listing&vars%5Bpost_type%5D=ct_channel&vars%5Bposts_per_page%5D=20&vars%5Bpost_status%5D=publish&vars%5Bignore_sticky_posts%5D=1&vars%5Bpaged%5D=1&id_playlist=";
  //$post="action=load_more&page=1&template=cactus-channel/content-listing&vars[post_type]=ct_channel&vars[posts_per_page]=20&vars[post_status]=publish&vars[ignore_sticky_posts]=1&vars[paged]=1&id_playlist=";
  $ch = curl_init($requestLink);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch,CURLOPT_REFERER,"https://tvhub.org/");
  curl_setopt ($ch, CURLOPT_POST, 1);
  curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
}
//echo $html;
$r=array();
  if ($tip=="release") {
  $videos = explode('div class="entry-content"', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
    $t1 = explode('href="',$video);
    $t2 = explode('"', $t1[1]);
    $link=$t2[0];
    $t1=explode('title="',$video);
    $t2=explode('"',$t1[1]);
    $title=$t2[0];
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    $image = "r_m.php?file=".$image;
    if (strpos($link,"/serial") !== false) array_push($r ,array($title,$link, $image));
  }
  } else {
  //echo $html;
  $videos = explode('div id="post', $html);
  unset($videos[0]);
  $videos = array_values($videos);
  //print_r ($videos);
  foreach($videos as $video) {
    $t1 = explode('href="',$video);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];

    $t1 = explode('title="', $video);
    $t2 = explode('"', $t1[1]);
    $title=$t2[0];
    $t1=explode('src="',$video);
    $t2=explode('"',$t1[1]);
    $image=$t2[0];
    $image = "r_m.php?file=".$image;
    if (strpos($link,"/serial") !== false) array_push($r ,array($title,$link, $image));
  }
  }
$c=count($r);
for ($k=0;$k<$c;$k++) {
  $title=$r[$k][0];
  $title=prep_tit($title);
  $link=$r[$k][1];
  $image=$r[$k][2];
  $rest = substr($title, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }

  $imdb="";
  $link_f=$fs_target.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  if ($title && strpos($link,"/serial") !== false) {
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=series&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
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
