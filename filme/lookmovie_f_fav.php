<!DOCTYPE html>
<?php
include ("../common.php");
set_time_limit(300);
$host=$_GET['host'];
$page_title="Filme favorite";
$width="200px";
$height="278px";
$add_target="lookmovie_f_add.php";
$fs_target="lookmovie_fs.php";
$file=$base_fav."lookmovie_f.dat";
if (isset($_GET['fix']))
 $fix="yes";
else
 $fix="no";
if (file_exists($base_pass."tmdb.txt"))
  $api_key=file_get_contents($base_pass."tmdb.txt");
else
  $api_key="";
$fav_target_fix="lookmovie_f_fav.php?host=".$host."&fix=yes";
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
    } else if  (charCode == "52") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="imdb.php?" + val_imdb;
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
       //$.fancybox.close();
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
  } else if (event.shiftKey) {
    id = "imdb_" + event.target.id;
    //alert (id);
    val_imdb=document.getElementById(id).value;
    msg="imdb.php?" + val_imdb;
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
function str_between($string, $start, $end){
	$string = " ".$string; $ini = strpos($string,$start);
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}
$w=0;
$n=0;
echo '<H2>'.$page_title.' <a href="'.$fav_target_fix.'">(fix image)</a></H2>';
$h="";
if (file_exists($file)) {
  $h=file_get_contents($file);
  $t1=explode("\r\n",$h);
  for ($k=0;$k<count($t1) -1;$k++) {
    $a=explode("#separator",$t1[$k]);
    if ($a) {
      $tit=trim($a[0]);
      $l=trim($a[1]);
      $img=trim($a[2]);
      //$arr[$tit]["link"]=$l;
      //$arr[$tit]["image"]=$img;
      $arr[$k]=array($tit,$l,$img);
    }
  }
}
if ($arr) {
asort($arr);
$n=0;
$w=0;
$nn=count($arr);

$k=intval($nn/10) + 1;
$p=0;
echo '<table border="1px" width="100%">'."\n\r";
for ($m=1;$m<$k;$m++) {
if ($p==0) echo '<TR>';
   echo '<TD align="center"><a href="#myLink'.($m*10).'">Salt:'.($m*10).'</a></td>';
   $p++;
  if ($p == 14) {
  echo '</tr>';
  $p=0;
  }
}
echo '</table>';
echo '<table border="1px" width="100%">'."\n\r";
foreach($arr as $key => $value) {
    $imdb="";
	$link = urldecode($arr[$key][1]);
    $title = unfix_t(urldecode($arr[$key][0]));
    $image=urldecode($arr[$key][2]);
    // ="/images/b/w780/db88ada5d2083f50593d65b61d54d6a0.jpg"
    // https://lookmovie.ag/p/w300/05dee075be60af0893aa926c3977c38a.jpg
    $image=str_replace("////","//",$image);
    $image=str_replace("image.lookmovie.ag/p","lookmovie.ag/images/p",$image);
    $image=str_replace("lookmovie.ag","lookmovie.io",$image);
    $image=str_replace("lookmovie.io","lookmovie2.to",$image);
    if (strpos($image,"http") === false) $image="https:".$image;
    if (preg_match("/tmdb\.org/",$image) && $fix=="yes" && $api_key) {
    $x=implode(",",get_headers($image));
    if (preg_match("/404 Not Found/",$x)) {
       $rest = substr($title, -6);
       if (preg_match("/\(?((1|2)\d{3})\)?/",$rest,$m)) {
       //$year=$m[1];
       $title1=trim(str_replace($m[0],"",$title));
    } else {
      //$year="";
      $title1=$title;
    }
      $l="https://api.themoviedb.org/3/search/movie?api_key=".$api_key."&query=".urlencode($title1);
      //echo $l;
      $ch = curl_init($l);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      $h_img = curl_exec($ch);
      curl_close ($ch);
      $result=json_decode($h_img,1);
      //print_r ($result);
      $r=$result['results'];
      if (isset($r[0]['poster_path'])) {
        $last = substr(strrchr($image, "/"), 1);
        //$new_image="https://image.tmdb.org/t/p/w185".$r[0]['poster_path'];
        $new_image=str_replace($last,$r[0]['poster_path'],$image);
        $h=str_replace($image,$new_image,$h);
        file_put_contents($file,$h);
        $image=$new_image;
      }
    }
    }
    //$image=$host.parse_url($image)['path'];
  $rest = substr($title, -6);
  if (preg_match("/\((\d+)\)/",$rest,$m)) {
   $year=$m[1];
   $tit_imdb=trim(str_replace($m[0],"",$title));
  } else {
   $year="";
   $tit_imdb=$title;
  }
    $link=$host.parse_url($link)['path'];
    $link_f=$fs_target.'?tip=movie&link='.urlencode($link).'&title='.urlencode(fix_t($title)).'&image='.$image."&sez=&ep=&ep_tit=&year=".$year;
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
<div id="overlay">
  <div id="text">Wait....</div>
</div>
</body>
</html>
