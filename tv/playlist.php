<!DOCTYPE html>
<?php
error_reporting(0);
if (isset($_GET['link'])) {
  $link= $_GET['link'];
}
$pg_tit=urldecode($_GET["title"]);
?>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $pg_tit; ?></title>
<script type="text/javascript" src="//code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="../jquery.fancybox.min.js"></script>
<link rel="stylesheet" type="text/css" href="../jquery.fancybox.min.css">
<link rel="stylesheet" type="text/css" href="../custom.css" />

<script type="text/javascript">
var id_link="";
function ajaxrequest(link) {
  var request =  new XMLHttpRequest();
  on();
  var the_data = link;
  var php_file='direct_link.php';
  request.open('POST', php_file, true);			// set the request

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
function addfav(link) {
  var request =  new XMLHttpRequest();
  var the_data = link;
  var php_file='playlist_add.php';
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
    if (charCode == "49") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     msg="prog.php?" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    } else if  (charCode == "50") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      //alert (val_fav);
      addfav(val_fav);
    } else if  (charCode == "52") {
      id = "fav_" + self.id;
      val_fav=document.getElementById(id).value;
      val_fav = val_fav.replace("mod=add","mod=del");
      //alert (val_fav);
      addfav(val_fav);
    } else if (charCode == "51") {
     id = "imdb_" + self.id;
     id_link=self.id;
     val_imdb=document.getElementById(id).value;
     val_imdb = val_imdb.replace("link=","title=");
     msg="../filme/imdb.php?" + val_imdb;
     document.getElementById("fancy").href=msg;
     document.getElementById("fancy").click();
    }
    return true;
}
   function zx(e){
     var instance = $.fancybox.getInstance();
     var charCode = (typeof e.which == "number") ? e.which : e.keyCode
     if (charCode == "13"  && instance !== false) {
       $.fancybox.close();
       setTimeout(function(){ document.getElementById(id_link).focus(); }, 500);
     }
   }
function isKeyPressed(event) {
  if (event.ctrlKey) {
    id = "imdb_" + event.target.id;
    val_imdb=document.getElementById(id).value;
    msg="prog.php?" + val_imdb;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
  }
}
function prog(link) {
    msg="prog.php?" + link;
    document.getElementById("fancy").href=msg;
    document.getElementById("fancy").click();
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
<a href='' id='mytest1'></a>
<a id="fancy" data-fancybox data-type="iframe" href=""></a>
<h2><?php echo $pg_tit; ?> (2=add,4=del)</H2>

<table border="1px" width="100%">
<?php
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
if (file_exists($base_pass."mx.txt")) {
$mx=trim(file_get_contents($base_pass."mx.txt"));
} else {
$mx="ad";
}
if (file_exists($base_pass."tastatura.txt")) {
$tast=trim(file_get_contents($base_pass."tastatura.txt"));
} else {
$tast="NU";
}
$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
if ($flash != "mp") {
if (preg_match("/android|ipad/i",$user_agent) && preg_match("/chrome|firefox|mobile/i",$user_agent)) $flash="chrome";
}
$n=0;
$w=0;
$m3uFile="pl/".$pg_tit;
if (isset($_GET['link'])) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $html = curl_exec($ch);
  curl_close($ch);
  $m3uFile=explode("\n",$html);
} else {
$m3uFile = file($m3uFile);
}
foreach($m3uFile as $key => $line) {
  $line=trim($line);
  if(strtoupper(substr($line, 0, 7)) === "#EXTINF") {
    if (preg_match("/tvg\-name\=\"(.*?)\"/i",$line,$m)) {
      $title=$m[1];
      if (!$title) {
        $t1=explode(",",$line);
        $title=trim($t1[1]);
      }
    } else {
    $t1=explode(",",$line);
    $title=trim($t1[1]);
    }
    $file = trim($m3uFile[$key + 1]);
    if ($file[0]=="#")  $file = trim($m3uFile[$key + 2]);
    if ($pg_tit=="alltvn.m3u")
      $tip_stream="http";
    else {
    $u=parse_url($file);
    $tip_stream=$u["scheme"];
    }
    $mod="direct";
    $from="fara";
    $t1=preg_replace("/^\|?RO\s*\|\s*/","",$title);
    //echo $t1;
    $val_prog="link=".urlencode(fix_t($t1));
    if (substr($file, 0, 4) == "http" || $pg_tit == "alltvn.m3u") {
    if (preg_match("/\.m3u8|\.mp4|\.flv|\.ts/",$file))
      $mod="direct";
    else
      $mod="indirect";
    if (strpos($file,"telekomtv.ro") !== false) {
      $mod="indirect";
      $from="fara";
    } elseif ($pg_tit=="alltvn.m3u") {
      $mod="direct";
      $from="gazw";
    }
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($file);
    if (strpos($link,"html")=== false) {
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash != "mp")
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    else
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    $n++;
    $w++;
    }
  } elseif ($tip_stream=="acestream") {
    if ($n == 0) echo "<TR>"."\n\r";
    $link2="intent:".$file."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
    if ($flash == "mp" || $flash=="chrome")
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link2.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a>';
    else
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$file.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a>';
    $n++;
    $w++;
  } elseif ($tip_stream=="sop") {
    if ($n == 0) echo "<TR>"."\n\r";
    //$link2="intent:".$file."#Intent;package=org.acestream.media.atv;S.title=".urlencode($title).";end";
    $link2=$file;
    $link3="sop_link.php?file=".$file."&title=".urlencode($title);
    if ($flash == "mp" || $flash=="chrome" || $flash=="direct")
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link2.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a>';
    else
    echo '<TD class="cat" width="20%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link3.'" target="_blank">'.$title.'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'"></a>';
    $n++;
    $w++;
  }
    if ($tip_stream == "http" || $tip_stream == "https" || $tip_stream=="acestream" || $tip_stream=="sop") {
    $t1=preg_replace("/^\|?RO\s*\|\s*/","",$title);
    $l_prog="link=".urlencode(fix_t($t1));
    if ($tast == "NU") {
   	echo '<a onclick="prog('."'".$l_prog."')".'"'." style='cursor:pointer;'>"." *".'</a>
   	<a onclick="addfav('."'".$fav_link."')".'"'." style='cursor:pointer;'>"." A".'</a>
       </TD>';
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
    } else {
    echo '</TD>';
    if ($n > 3) {
     echo '</TR>'."\n\r";
     $n=0;
    }
    }
   }
  }
}

 echo '</table>';
?>

<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
