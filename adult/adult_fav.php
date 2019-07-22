<!DOCTYPE html>
<?php
include ("../common.php");
$page_title="Adult favorite";
$width="";
$height="";
$file=$base_fav."adult_fav.dat";
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
      location.reload();
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
echo '<H2>'.$page_title.'</H2>';
$arr=array();
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
      $wi=trim($a[3]);
      $he=trim($a[4]);
      $tar=trim($a[5]);
      $arr[$tit]["link"]=$l;
      $arr[$tit]["image"]=$img;
      $arr[$tit]["width"]=$wi;
      $arr[$tit]["height"]=$he;
      $arr[$tit]["target"]=$tar;
    }
  }
}
//print_r ($arr);
if ($arr) {
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
echo '</TR></table>';
echo '<table border="1px" width="100%" style="table-layout:fixed;">'."\r\n";
foreach($arr as $key => $value) {
  $link = urldecode($arr[$key]["link"]);
  $title = unfix_t(urldecode($key));
  $image=urldecode($arr[$key]["image"]);
  $width=urldecode($arr[$key]["width"]);
  $height=urldecode($arr[$key]["height"]);
  $target=urldecode($arr[$key]["target"]);
  if ($target=="adult_link.php")
    $has_fs = "no";
  else
    $has_fs = "yes";
  
  if ($has_fs =="no")
  $fav_link="mod=del&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&width=".$width."&height=".$height."&file=adult_link.php";
  else
  $fav_link="mod=del&title=".urlencode(fix_t($title))."&link=".urlencode($link)."&image=".urlencode($image)."&width=".$width."&height=".$height."&file=filme_link.php";
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
echo '</TABLE>';
}

?>
<div id="overlay"">
  <div id="text">Wait....</div>
</div>

</body>
</html>
