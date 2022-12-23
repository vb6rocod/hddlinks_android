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
//$height="200*(450/253";
$height=intval(200*(253/450))."px";
$last_good="https://ask4movie.co";
$last_good="https://ask4movie.cc";
$last_good="https://ask4movie.io";
$last_good="https://ask4movie.me";
$last_good="https://ask4movie.mx";
$host=parse_url($last_good)['host'];
/* ==================================================== */
$has_fav="yes";
$has_search="yes";
$has_add="yes";
$has_fs="yes";
$fav_target="ask4movie_f_fav.php?host=".$last_good;
$add_target="ask4movie_f_add.php";
$add_file="";
$fs_target="ask4movie_fs.php";
$target="ask4movie_f.php";
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
$f=array();
if ($tip=="search") {
 $search = str_replace(" ","+",$tit);
 $l="https://".$host."/?s=".$search;
} else {
  $post="action=load_categories&category_offset=0&last_row_number=0&page=0&vars[error]=&vars[m]=&vars[p]=2003&vars[post_parent]=&vars[subpost]=&vars[subpost_id]=&vars[attachment]=&vars[attachment_id]=0&vars[name]=&vars[pagename]=&vars[page_id]=2003&vars[second]=&vars[minute]=&vars[hour]=&vars[day]=0&vars[monthnum]=0&vars[year]=0&vars[w]=0&vars[category_name]=&vars[tag]=&vars[cat]=&vars[tag_id]=&vars[author]=&vars[author_name]=&vars[feed]=&vars[tb]=&vars[paged]=0&vars[meta_key]=&vars[meta_value]=&vars[preview]=&vars[s]=&vars[sentence]=&vars[title]=&vars[fields]=&vars[menu_order]=&vars[embed]=&vars[ignore_sticky_posts]=false&vars[suppress_filters]=false&vars[cache_results]=false&vars[update_post_term_cache]=true&vars[lazy_load_term_meta]=true&vars[update_post_meta_cache]=true&vars[post_type]=&vars[posts_per_page]=300&vars[nopaging]=false&vars[comments_per_page]=50&vars[no_found_rows]=false&vars[order]=DESC";
  $l="https://".$host."/wp-admin/admin-ajax.php";
  //$l="https://ask4movie.co/wp-admin/admin-ajax.php";
  //$post="action=load_categories&category_offset=6&last_row_number=6&hidden_categories[]=4,5,6,7,8,9,10,12,13,14,15,16,17,18,19,97,99,89,96,91,1&page=0&vars[error]=&vars[m]=&vars[p]=2003&vars[post_parent]=&vars[subpost]=&vars[subpost_id]=&vars[attachment]=&vars[attachment_id]=0&vars[name]=&vars[pagename]=&vars[page_id]=2003&vars[second]=&vars[minute]=&vars[hour]=&vars[day]=0&vars[monthnum]=0&vars[year]=0&vars[w]=0&vars[category_name]=&vars[tag]=&vars[cat]=&vars[tag_id]=&vars[author]=&vars[author_name]=&vars[feed]=&vars[tb]=&vars[paged]=0&vars[meta_key]=&vars[meta_value]=&vars[preview]=&vars[s]=&vars[sentence]=&vars[title]=&vars[fields]=&vars[menu_order]=&vars[embed]=&vars[ignore_sticky_posts]=false&vars[suppress_filters]=false&vars[cache_results]=false&vars[update_post_term_cache]=true&vars[lazy_load_term_meta]=true&vars[update_post_meta_cache]=true&vars[post_type]=&vars[posts_per_page]=300&vars[nopaging]=false&vars[comments_per_page]=50&vars[no_found_rows]=false&vars[order]=DESC";
}
$ua="Mozilla/5.0 (Windows NT 10.0; rv:79.0) Gecko/20100101 Firefox/79.0";
if ($tip=="release") {
$head=array('Accept: text/plain, */*; q=0.01',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
'X-Requested-With: XMLHttpRequest',
'Content-Length: '.strlen($post).'',
'Origin: https://ask4movie.co',
'Connection: keep-alive',
'Cookie: a1=uqg2dKT6biKoDW7Lyk5DHHo4dW74b61s; token_QpUJAAAAAAAAGu98Hdz1l_lcSZ2rY60Ajjk9U1c=BAoAX-B03AFf4HVVgAGBAsAAICjp96898j_nB14OPYLoqD3iLjC997M_w7tEzvmxpkd-wQBGMEQCIGej65gMODhW-D1A0ORK3siU4eWoUAYN3F5-NbUDp_PmAiBn-uLgGmOEWuU8wMIUkNnJU11JXau3KGJBx6qudbKhVsIAIGUyA4WGPL3lFEOzjw-_UZ1yoHb7cVS64FjE_TzQbjp2xAAQKgQkEQJAuYD5hA1RVxI9e8UAELevwj4MHg9M_yZffKb1va_DAEYwRAIgVN91lVmH1bRZtmYVreL6C4s8F8jSe0W-MzHEkH6Zi08CIH6JImYXpirQkN_tk8g9Ns2BU9guTA4HtiXUU4kp9GTR; _popfired=2; _popfired_expires=Invalid%20Date; lastOpenAt_=1608545618341; 6US=1',
'Referer: https://ask4movie.co/');
//$l="https://ask4movie.co/?s=Instant+Family";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  $html = curl_exec($ch);
  curl_close($ch);
  //echo $html;
  $h=json_decode($html,1)['data'];
} else {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_REFERER,"https://ask4movie.co");
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,0);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 25);
  $h = curl_exec($ch);
  curl_close($ch);
  //echo $h;
}

//echo $h;
$host=parse_url($l)['host'];
if ($tip=="release") {
$videos = explode('div class="slide-item', $h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 $link=$t2[0];
 $t3=explode('>',$t1[2]);
 $t4=explode('<',$t3[1]);
 $title=$t4[0];
 $t1=explode('url(',$video);
 $t2=explode(')"',$t1[1]);
 $image=$t2[0];
  if ($title) $f[] = array($title,$link,$image);
}
} else {
$videos = explode('div class="item', $h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('href="',$video);
 $t2=explode('"',$t1[1]);
 $link=$t2[0];
 $t3=explode('>',$t1[2]);
 $t4=explode('<',$t3[1]);
 $title=$t4[0];
 $t1=explode('url(',$video);
 $t2=explode(')"',$t1[1]);
 $image=$t2[0];
  if ($title) $f[] = array($title,$link,$image);
}
}
//echo $html;
//print_r ($f);
foreach($f as $key => $value) {
  $title=$value[0];
  //$title=prep_tit($title);
  $link=$value[1];
  $image=$value[2];
  $year="";
  $imdb="";
  $year="";
  $rest = substr($title, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }
  $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  if ($title) {
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  $image="r_m.php?file=".$image;
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
