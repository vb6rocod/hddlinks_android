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
$fav_target_f="123files_f.php";
$fav_target_s="123files_s.php";
$add_target_f="123files_f_add.php";
$add_target_s="123files_s_add.php";
$add_file="";
$fs_target="123files_fs.php";
$fs_target_ep="123files_ep.php";
$target="123files.php";
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
Cautare film/serial:  <input type="text" id="title" name="title" value="'.$val_search.'">
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
function ajaxrequest(link,target) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file=target;
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
      //alert (self.id);
      id = "fav_" + self.id; // fav_myLink_tip
      id1 = "fav_" + self.id + "_tip"; // fav_myLink_tip
      val_fav=document.getElementById(id).value;
      target=document.getElementById(id1).value;
      ajaxrequest(val_fav,target);
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
      document.getElementById("fav_f").click();
     } else if (charCode == "52" && e.target.type != "text") {
      document.getElementById("fav_s").click();
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
if ($page==1) {
echo '<TR>'."\r\n";

   if ($tip == "release") {
   if ($has_fav=="yes" && $has_search=="yes") {
     echo '<TD class="form" colspan="4">'.$form.'</TD>'."\r\n";
   }
   }
echo '</TR>'."\r\n";
}
if ($page == 1 && $tip=="release") {
echo '<TR>'."\r\n";
echo '<TD class="mp" colspan="2"><a id="fav_f" href="'.$fav_target_f.'" target="_blank">Filme favorite</a></TD>'."\r\n";
echo '<TD class="mp"colspan="2"><a id="fav_s" href="'.$fav_target_s.'" target="_blank">Seriale favorite</a></TD>'."\r\n";
echo '</TR>'."\r\n";
}
if ($tip == "search") {
$q=urlencode($tit);
$l="https://www.imdb.com/find?q=".$q."&s=tt";
  $ch = curl_init($l);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  //curl_setopt($ch,CURLOPT_REFERER,$l);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
  //curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $h = curl_exec($ch);
  curl_close ($ch);
//echo $h;
$r=array();
$videos=explode('td class="primary_photo"',$h);
unset($videos[0]);
$videos = array_values($videos);
foreach($videos as $video) {
 $t1=explode('="/title/',$video);
 $t2=explode("/",$t1[1]);
 $imdb=$t2[0];
 $t1=explode('src="',$video);
 $t2=explode('"',$t1[1]);
 $image=$t2[0];
 $t1=explode("_V1",$image);
 $image=$t1[0]."_V1_UY268_CR9,0,182,268_AL_.jpg";
 $t1=explode('class="result_text">',$video);
 $t2=explode(">",$t1[1]);
 $t3=explode("<",$t2[1]);
 $title=trim($t3[0]);
 $t4=explode('a>',$t1[1]);
 $t5=explode('<',$t4[1]);
 $info=trim($t5[0]);
 if (preg_match("/\((\d{4})\)/",$info,$m))
   $year=$m[1];
 else
   $year="";
 //$tip=trim(preg_replace("/\((\d{4})\)/","",$info));
 if (preg_match("/TV Mini-Series|TV Series/",$info))
  $tip="series";
 else
  $tip="movie";
 if (!preg_match("/TV Episode|Short|Video Game|in development|TV Special|Video/",$info))
 $r[]=array($imdb,$image,$title,$info,$year,$tip);
}
//print_r ($r);
$n=0;
$w=0;
for ($k=0;$k<count($r);$k++) {
  $media_type=$r[$k][5];
  $title=$r[$k][2];
  $image=$r[$k][1];
  $imdb=$r[$k][0];
  $year=$r[$k][4];
  $link=$imdb;
//////////////////////////////////////////////
$tit_imdb=$title;
/////////////////////////////////////////////
  if ($n==0) echo '<TR>'."\r\n";
  //echo $media_type;
  if ($media_type == "movie") {
  $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  } else {
  $link_f=$fs_target_ep.'?tip=series&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
  $val_imdb="tip=series&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  }
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.' ('.$year.')</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($media_type == "movie") {
    echo '<input type="hidden" id="fav_myLink'.$w.'_tip" value="123files_f_add.php">'."\r\n";
    echo '<a onclick="ajaxrequest('."'".$fav_link."'".','."'123files_f_add.php'".')" style="cursor:pointer;">*</a>'."\r\n";
    } else {
    echo '<input type="hidden" id="fav_myLink'.$w.'_tip" value="123files_s_add.php">'."\r\n";
    echo '<a onclick="ajaxrequest('."'".$fav_link."'".','."'123files_s_add.php'".')" style="cursor:pointer;">*</a>'."\r\n";
    }
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.' ('.$year.')</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    if ($media_type == "movie")
    echo '<input type="hidden" id="fav_myLink'.$w.'_tip" value="123files_f_add.php">'."\r\n";
    else
    echo '<input type="hidden" id="fav_myLink'.$w.'_tip" value="123files_s_add.php">'."\r\n";
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
//}
//}

echo "</table>"."\r\n";
echo "</table>";
if ($tip=="release") {
if ($tast=="DA") {
echo '
Scurtaturi:<BR>
Folositi tasta 3 pentru a adauga/sterge la favorite.<BR>
Folositi tasta 2 pentru a accesa direct pagina de "Filme Favorite".<BR>
Folositi tasta 4 pentru a accesa direct pagina de "Seriale Favorite".<BR>
Folositi tasta 1 pentru informatii despre film/serial. Apasati "OK" pentru a inchide info.<BR>
Folositi tasta 5 pentru a simula butonul de cautare.</p>';
} else {
echo '
Folositi ctrl+click pentru informatii despre film/serial. Apasati "Esc" pentru a inchide info.<BR>
Folosti "*" pentru a adauga/sterge la favorite.';
}
}
?></body>
</html>
