<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="Filme favorite";
$width="200px";
$height="278px";
$add_target="sitefilme_add.php";
$fs_target="filme_link.php";
$file=$base_fav."sitefilme.dat";
$f_TMDB=$base_pass."tmdb.txt";
$key = file_get_contents($f_TMDB);
if (isset($_GET['fix']))
 $fix="yes";
else
 $fix="no";
$fav_target_fix="sitefilme_fav.php?fix=yes";
set_time_limit(0);
?>
<html><head>
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
      location.reload();
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
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
function rep_img($tit,$img,$key) {
  $year="";
  unfix_t($tit);
  preg_match("/\(([^\)]*)\)$/",$tit,$y);
  //print_r ($y);
  if (isset($y[0])) {
    $title=trim(preg_replace("/\(([^\)]*)\)$/","",$tit));
    if (preg_match("/[1|2]\d{3}/",$y[1],$z))
     $year=$z[0];
  }
  $tit=trim(preg_replace("/\(([^\)]*)\)$/","",$tit));
  if (preg_match("/\s-\s/",$tit)) {
   $t1=explode(" - ",$tit);
   $tit=$t1[0];
  }
  //echo $tit."\n";
 if ($year)
   $l="https://www.themoviedb.org/search/movie?query=".rawurlencode($tit)."%20y:".$year;
 else
   $l="https://www.themoviedb.org/search/movie?query=".rawurlencode($tit);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/117.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
  if (preg_match_all("/movie\/(\d+)\-/",$h,$m)) {
  //print_r ($m);
  $tmdb = $m[1][0];
  $l="https://api.themoviedb.org/3/movie/".$tmdb."?api_key=".$key."&append_to_response=credits";
  //echo $l;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $l);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_REFERER, "https://api.themoviedb.org");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $h = curl_exec($ch);
  curl_close($ch);
   $p=json_decode($h,1);
   if ($p["poster_path"])
    $img="http://image.tmdb.org/t/p/w500".$p["poster_path"];

 }
 return $img;
}
$w=0;
$n=0;
echo '<H2>'.$page_title.' <a href="'.$fav_target_fix.'">(fix image)</a></H2>';
//echo '<H2>'.$page_title.'</H2>';
$arr=array();
$h="";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("\r\n",$h);
  $bfound=0;
  for ($k=0;$k<count($t1) -1;$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $tit=trim($a[0]);
      //$tit=prep_tit($tit);
      $l=trim($a[1]);
      $img=trim($a[2]);
      //if ($k>300) {
      if (preg_match("/sitefilme/",$img) && $fix=="yes") {
       $img=rep_img($tit,$img,$key);
       $bfound=1;
       //if (!preg_match("/sitefilme/",$img)) {
       
       //}
      }
      //}
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$k]=array($tit,$l,$img);
    }
  }
}
if ($bfound==1) {
  $out="";
  //print_r ($arr);
  foreach($arr as $key => $value) {
    //$out =$out.$key."#separator".$arr[$key]["link"]."#separator".$arr[$key]["image"]."\r\n";
    $out =$out.$arr[$key][0]."#separator".$arr[$key][1]."#separator".$arr[$key][2]."\r\n";
  }
  file_put_contents($file,$out);
}
//print_r ($arr);
if ($arr) {
asort($arr);
$n=0;
$w=0;
$p=0;
$nn=count($arr);
$k=intval($nn/10) + 1;
echo '<table border="1px" width="100%"><tr>'."\n\r";
for ($m=1;$m<$k;$m++) {
if ($p==0) echo '<TR>';
   echo '<TD align="center"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></td>';
   $p++;
  if ($p == 14) {
  echo '</tr>';
  $p=0;
  }
}
echo '</TR></table>';
echo '<table border="1px" width="100%">'."\n\r";
foreach($arr as $key => $value) {
    $year="";
    $imdb="";
	$link = urldecode($arr[$key][1]);
    $title = unfix_t(urldecode($arr[$key][0]));
/////////////////////////////////////////
  preg_match("/\(([^\)]*)\)$/",$title,$y);
  //print_r ($y);
  if (isset($y[0])) {
    $tit_imdb=trim(preg_replace("/\(([^\)]*)\)$/","",$title));
    if (preg_match("/[1|2]\d{3}/",$y[1],$z))
     $year=$z[0];
  }
  $tit_imdb=trim(preg_replace("/\(([^\)]*)\)$/","",$tit_imdb));
  if (preg_match("/\s-\s/",$tit_imdb)) {
   $t1=explode(" - ",$tit_imdb);
   $tit_imdb=$t1[0];
  }
/////////////////////////////////////////
    $image=urldecode($arr[$key][2]);
    //$image="blank.jpg";
    $link_f=$fs_target.'?file='.urlencode($link).'&title='.urlencode(fix_t($title));
  if ($n==0) echo '<TR>'."\r\n";
  $val_imdb="tip=movie&title=".urlencode(fix_t($tit_imdb))."&year=".$year."&imdb=".$imdb;
  $fav_link="file=&mod=del&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&year=".$year;
  if ($tast == "NU") {
    echo '<td class="mp" width="25%"><a href="'.$link_f.'" id="myLink'.$w.'" target="_blank" onmousedown="isKeyPressed(event)">
    <img id="myLink'.$w.'" src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
    echo '<a onclick="ajaxrequest('."'".$fav_link."'".')" style="cursor:pointer;">*</a>'."\r\n";
    echo '</TD>'."\r\n";
  } else {
    echo '<td class="mp" width="25%"><a class ="imdb" id="myLink'.$w.'" href="'.$link_f.'" target="_blank">
    <img src="'.$image.'" width="'.$width.'" height="'.$height.'"><BR>'.$title.'</a>
    <input type="hidden" id="imdb_myLink'.$w.'" value="'.$val_imdb.'">'."\r\n";
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
echo '</TABLE>';
}
?>
</body>
</html>
