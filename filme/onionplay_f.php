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
$ref="https://ww1.onionplay.to";
$ref="https://onionplay.is";
$ref="https://onionplay.uk";
$ref="https://onionplay.se";
//$ref="https://onionplay.club";
/*
https://onionplay.network/ main
onionplay.se
onionplay.re
onionplay.co
onionplay.org
onionplay.net
*/
$host=parse_url($ref)['host'];
$fav_target="onionplay_f_fav.php?host=".$ref."&fix=no";
$fav_target1="onionplay_f_fav.php?host=".$ref."&fix=yes";
$add_target="onionplay_f_add.php";
$add_file="";
$fs_target="onionplay_fs.php";
$target="onionplay_f.php";
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
     //alert (charCode);
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     } else if (charCode == "53" && e.target.type != "text") {
      document.getElementById("send").click();
     } else if (charCode == "50" && e.target.type != "text") {
      document.getElementById("fav").click();
     } else if (charCode == "48" && e.target.type != "text") {
      window.open("<?php echo $fav_target1;?>");
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
if (file_exists($base_pass."player.txt")) {
$flash=trim(file_get_contents($base_pass."player.txt"));
} else {
$flash="direct";
}

if ($flash=="mp")
    $cf = '<a href="intent:http://127.0.0.1:8080/scripts/filme/cf.php?site='.$ref.'/?s=xxx#Intent;package=org.mozilla.firefox;S.title=Cloudflare;end">*</a>';
else
    $cf = '<a href="cf.php?site='.$ref.'/?s=dune" target="_blank">*</a>';
$cb='<a href="'.$ref.'" target="_blank">*</a>';
echo '<H2>'.$page_title.' ('.$cf.')</H2>'."\r\n";
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

if($tip=="release") {
  if ($page==1)
   $l="https://".$host."/movies/";
  else
   $l="https://".$host."/movies/page/".$page."/";
} else {
  $search=str_replace(" ","+",$tit);
  if ($page==1)
  $l="https://".$host."/search/".$search;
  else
  $l="https://".$host."/search/".$search."/page/".$page."/";
}
$host=parse_url($l)['host'];
$firefox = $base_pass."firefox.txt";
if (file_exists($firefox))
 $ua=file_get_contents($firefox);
else
 $ua="Mozilla/5.0 (Windows NT 10.0; rv:80.0) Gecko/20100101 Firefox/80.0";
$cookie=$base_cookie."onionplay.txt";
 if (file_exists("/storage/emulated/0/Download/cookies.txt")) {
  $h1=file_get_contents("/storage/emulated/0/Download/cookies.txt");
  file_put_contents($cookie,$h1);
  unlink ("/storage/emulated/0/Download/cookies.txt");
 } elseif (file_exists($base_cookie."cookies.txt")) {
  $h1=file_get_contents($base_cookie."cookies.txt");
  file_put_contents($cookie,$h1);
  unlink ($base_cookie."cookies.txt");
 }
 //echo $h1;
$cc="";
if (file_exists($cookie)) {
$x=file_get_contents($cookie);
//echo $x;
//file_put_contents($base_cookie."onionplay.txt",$x);
$y=preg_quote($host,"/");
//unlink ($cookie);
if (preg_match("/".$y."	\w+	\/	\w+	\d+	cf_clearance	([\w\-\.\_]+)/",$x,$m))
 $cc=trim($m[1]);
else
 $cc="";
}

//echo $ua;
//$cc="fKvTQ6Xci3NmG46VHQjprf4cTslb0YIebX7.mbl.Tyg-1636452405-0-150";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:94.0) Gecko/20100101 Firefox/94.0";
//echo $cc;
if ($tip=="release") {
//$ua = $_SERVER['HTTP_USER_AGENT'];
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:94.0) Gecko/20100101 Firefox/94.0";
//$ua="Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0";

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$ref."\r\n"
  )
);
//$context = stream_context_create($opts);
//$html=@file_get_contents($l,false,$context);
$head=array('User-Agent: Mozilla/5.0 (Windows NT 10.0; rv:95.0) Gecko/20100101 Firefox/95.0',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
'Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2',
'Accept-Encoding: deflate',
'Connection: keep-alive',
'Cookie: _ga=GA1.1.238192412.1599210100; _js_datr=UiDGXydiuVrQoP7WfPCjEPFp; __atuvc=0%7C41%2C0%7C42%2C0%7C43%2C0%7C44%2C1%7C45; _ym_uid=1582124103637524699; _ym_d=1635325499; _ga_NHDEZ3LMR5=GS1.1.1608507498.1.0.1608507498.60; _hjid=5dad3d29-5449-41ee-b759-461662a02d3c; cX_P=kixru44z4zqi4e91; sc_is_visitor_unique=rx11849134.1609874042.F52CF4D32A424F9BB6E9BC378CA70AF9.2.2.2.2.2.2.2.2.2-12096247.1609499921.3.3.3.3.2.1.1.1.1; __gads=ID=9404faa27f9171f8-22d8b4b15dba004c:T=1612557705:S=ALNI_MbHte3wNPzuvHG76vr9bLfqn21_3A; _ga_4Y92J21ZFR=GS1.1.1630144471.3.1.1630144497.0; _ga_9ZBLTKLKK0=GS1.1.1615106758.1.1.1615108141.0; _ga_5S4R5BDE8S=GS1.1.1633897355.2.0.1633897365.0; _ga_0WGNHNHYZS=GS1.1.1615711605.1.0.1615711609.0; _ga_PVLYD1EH1L=GS1.1.1616408660.1.0.1616408668.0; HstCfa4529398=1618562419370; HstCla4529398=1619980849748; HstCmu4529398=1618562419370; HstPn4529398=1; HstPt4529398=2; HstCnv4529398=2; HstCns4529398=2; ai_user=5CFRa|2021-04-25T08:45:14.198Z; _clck=1w9rrjr; cto_bundle=lnKgWF9EazRTJTJGQ0ZIbldHMVdRZWxiSTZLcE5CZnNlQ1I1V2hpWTl4UTlYcnc2JTJGWTdvSVYyRFF3bGRTOUlwZnFpcWs5QUdhUmlVNEQ4VDNOSzNiaGtQNTg2QWhma1NNNWVnc0MycENIeHFqV3F4UVhoM0VzTjNyVk55eUV4Tk5CTTQ5bDhyZXRoYUV3MFZLV0p1RHpqZUhTYXZTSlFLdGZtSTNnZUFvcWpEZUZTRXRjJTNE; HstCfa4533164=1621364947339; HstCla4533164=1621364947339; HstCmu4533164=1621364947339; HstPn4533164=1; HstPt4533164=1; HstCnv4533164=1; HstCns4533164=1; _ga_0CLRKRJFKB=GS1.1.1621364947.1.0.1621364949.0; __utma=111872281.238192412.1599210100.1627208922.1627208922.1; __utmz=111872281.1627208922.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); csm-hit=tb:s-XAFQYFK0V4EPBB1DJH2N|1630147890762&t:1630147899688&adb:adblk_no; HstCfa4518200=1630264093671; HstCla4518200=1630580782689; HstCmu4518200=1630264093671; HstPn4518200=1; HstPt4518200=11; HstCnv4518200=6; HstCns4518200=6; _ga_TNN38RF6S1=GS1.1.1636455736.1.1.1636455754.0; _ga_JJ8C3FEJHM=GS1.1.1637323126.1.1.1637323343.0; dom3ic8zudi28v8lr6fgphwffqoz0j6c=17b722ad-50b5-4b00-88fe-10bc19ad3b5c%3A1%3A1',
'Upgrade-Insecure-Requests: 1',
'Sec-Fetch-Dest: document',
'Sec-Fetch-Mode: navigate',
'Sec-Fetch-Site: none',
'Sec-Fetch-User: ?1');
  $ch = curl_init($l);
  //curl_setopt($ch, CURLOPT_USERAGENT, $ua);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HTTPHEADER,$head);
  curl_setopt($ch, CURLOPT_ENCODING,"");
  //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
  //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
  //curl_setopt($ch, CURLOPT_HEADER,1);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close ($ch);
} else {
$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"User-Agent: ".$ua."\r\n".
              "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n" .
              "Accept-Language: ro-RO,ro;q=0.8,en-US;q=0.6,en-GB;q=0.4,en;q=0.2\r\n" .
              "Accept-Encoding: deflate\r\n" .
              "Connection: keep-alive\r\n" .
              "Cookie: cf_clearance=".$cc."\r\n".
              "Referer: ".$ref."\r\n"
  )
);
//$cf="https://embed.smashystream.com/cors.php?";
//$l=$cf.$l;
//echo $l;
$context = stream_context_create($opts);
$html=@file_get_contents($l,false,$context);
}
//echo $html;
$r=array();
if ($tip=="release") {
  $videos = explode('article id="post-',$html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1 = explode('"',$video);
   $link1 = $t1[0];
   $t1=explode('href="',$video);
   $t2=explode('"',$t1[1]);
   $link=$t2[0];
   if (strpos($link,"http") === false) $link="https://".$host.$link;
   $t3 = explode('>', $t1[2]);
   $t4 = explode('<', $t3[1]);
   $title = trim($t4[0]);
   $title=prep_tit($title);
   $t1 = explode('src="', $video);
   $t2 = explode('"', $t1[1]);
   $image = $t2[0];
  $rest = substr($title, -6);
  if (preg_match("/\(?(\d{4})\)?/",$rest,$m)) {
   $year=$m[1];
   $title=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $title=$title;
  }
   if (!preg_match("/featured/",$link1) && !preg_match("/\/tvshows/",$link)) $r[]=array($link,$title,$image,$year);
  }
} else {
  $videos = explode('<article',$html);
  unset($videos[0]);
  $videos = array_values($videos);
  foreach($videos as $video) {
   $t1 = explode('href="',$video);
   $t2=explode('"',$t1[1]);
   $link = $t2[0];
   if (strpos($link,"http") === false) $link="https://".$host.$link;
   $t3 = explode('class="title">', $video);
   $t4 = explode('>', $t3[1]);
   $t5=explode('<',$t4[1]);
   $title = trim($t5[0]);
   $title=prep_tit($title);
   $t1 = explode('src="', $video);
   $t2 = explode('"', $t1[1]);
   $image = $t2[0];
  $rest = substr($title, -6);
  if (preg_match("/\(?(\d{4})\)?/",$rest,$m)) {
   $year=$m[1];
   $title=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $title=$title;
  }
   if (strpos($image,"http") === false) $image="https://".$host.$image;
   if (!preg_match("/\/tvshows/",$link)) $r[]=array($link,$title,$image,$year);
  }
}
for ($k=0; $k<count($r);$k++) {
  $link=$r[$k][0];
  $title=$r[$k][1];
  $image=$r[$k][2];
  $year=$r[$k][3];
  $imdb="";
  $tit_imdb=$title;
  $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  if ($title) {
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
