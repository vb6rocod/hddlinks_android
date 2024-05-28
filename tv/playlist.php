<!DOCTYPE html>
<?php
error_reporting(0);
//ini_set('memory_limit', '44M');
ini_set('memory_limit', '-1');
if (isset($_GET['link'])) {
  $link= $_GET['link'];
} else
 $link="";
$pg_tit=urldecode($_GET["title"]);
if (isset($_GET['page']))
 $page=$_GET['page'];
else
 $page=0;
if (isset($_GET['group']))
 $group=$_GET['group'];
else
 $group="no";
$step=200;
////////////////////////
$base=basename($_SERVER['SCRIPT_FILENAME']);
$p=$_SERVER['QUERY_STRING'];
parse_str($p, $output);

if (isset($output['page'])) unset($output['page']);
$p = http_build_query($output);

$next=$base."?page=".($page+1)."&".$p;
$prev=$base."?page=".($page-1)."&".$p;
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
      if (s.match(/mod=del/gi)) {
      location.reload();
      }
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
<body width="100%">
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



<?php
if ($group <> "no")
 echo '<h2>'.$pg_tit." (".$group.")</H2>";
else
 echo '<h2>'.$pg_tit.'</h2>';
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

$n=0;
$w=0;
function remove_empty_lines($string) {
    $lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $string));
    //print_r ($lines);
    $lines = array_map('trim', $lines);
    //print_r ($lines);
    $lines = array_filter($lines, function($value) {
        return $value !== '';
    });
    return implode("\n", $lines);
    //return $lines;
}
$m3uFile="pl/".$pg_tit;
if ($link) {
  //echo $link;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $link);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:64.0) Gecko/20100101 Firefox/64.0');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_TIMEOUT, 15);
  $m3ufile = curl_exec($ch);
  //echo $m3ufile;
  curl_close($ch);
} else {
$m3ufile = file_get_contents($m3uFile);
}
//echo $m3uFile;
//echo urlencode($m3uFile);
$m3ufile = remove_empty_lines($m3ufile);

$re = '/#EXTINF:(.+?)\n(#.*?\n)?((http|rtmp)\S+)/m';
preg_match_all($re, $m3ufile, $matches);
$tot=count($matches[0]);
//echo $tot;
$rr=array();
$rrr=array();

  for ($z=0;$z<count($matches[1]);$z++) {
   $file=$matches[3][$z];
   $line=$matches[1][$z];
   if (preg_match("/tvg-name\=\"([^\"]+)\"/",$line,$x)) {
     $title=trim($x[1]);
   } else {
     $t=explode(",",$line);
     $title=trim($t[count($t)-1]);
   }
   if (preg_match("/group-title\=\"([^\"]+)\"/",$line,$s))
    $group1=$s[1];
   else
    $group1="no";
   //echo $title."\n".$file."\n";
  $rr[$group1][]=array($title,$file);
  }
  $rrr=array_keys($rr);
//print_r ($rrr);
if ($group <> "no")
  $tot=count($rr[$group]);
if ($tot>$step) {
$k=intval($tot/$step) + 1;
echo '<table border="1px" width="100%"><tr>'."\n\r";
for ($m=0;$m<$k;$m++) {
   if ($m%40 == 0 && $m>0) echo '</TR><TR>';
   echo '<TD align="center"><a href="'.($base."?page=".($m*1)."&".$p).'">'.($m+1).'</a></td>';

}
echo '</TR></table>';
}
if ($group=="no" && count($rrr)>1 && $page==0) {
 echo '<table border="1px" width="100%">';
 echo "<TR><TD class='mp'><a href=".'"playlist_group.php?title='.urlencode($pg_tit)."&link=".urlencode($link).'">ByGroup</a></TD></TR>';
 echo '</table>';
}

echo '<table border="1px" width="100%">';
//print_r ($matches);
//die();
//print_r ($m3uFile);
if ($tot>$step) {
echo '<TR>';
 if ($page>0)
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
 else
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>';
}
if ($group <> "no") {
for ($z=$step*$page;$z<min($step*($page+1),count($rr[$group]));$z++) {
    $file=$rr[$group][$z][1];
    $title=$rr[$group][$z][0];

    $mod="direct";
    $from="fara";
    $t1=preg_replace("/^\|?RO\s*\|\s*/","",$title);
    //echo $t1;
    $val_prog="link=".urlencode(fix_t($t1));
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($file);
    $title=wordwrap($title,25,"\n",true);
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash")
    echo '<TD class="cat" width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    else
    echo '<TD class="cat" width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    $n++;
    $w++;

    //if ($tip_stream == "http" || $tip_stream == "https" || $tip_stream=="acestream" || $tip_stream=="sop") {
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
   //}
  //}
}
/////////////////////////////////////////////////////////////////////
} else {
for ($z=$step*$page;$z<min($step*($page+1),count($matches[2]));$z++) {
   $file=$matches[3][$z];
   $line=$matches[1][$z];
   if (preg_match("/tvg-name\=\"([^\"]+)\"/",$line,$x)) {
     $title=trim($x[1]);
   } else {
     $t=explode(",",$line);
     $title=trim($t[count($t)-1]);
   }

    $mod="direct";
    $from="fara";
    $t1=preg_replace("/^\|?RO\s*\|\s*/","",$title);
    //echo $t1;
    $val_prog="link=".urlencode(fix_t($t1));
    $link="direct_link.php?link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=direct";
    $l="link=".urlencode(fix_t($file))."&title=".urlencode(fix_t($title))."&from=".$from."&mod=".$mod;
    $fav_link="mod=add&title=".urlencode(fix_t($title))."&link=".urlencode($file);
    $title=wordwrap($title,25,"\n",true);
    if ($n == 0) echo "<TR>"."\n\r";
    //if ($tast == "NU")
    if ($flash == "flash")
    echo '<TD class="cat" width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" href="'.$link.'" target="_blank">'.$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    else
    echo '<TD class="cat" width="25%">'.'<a class ="imdb" id="myLink'.($w*1).'" onclick="ajaxrequest('."'".$l."')".'"'." style='cursor:pointer;'>".$title
    .'<input type="hidden" id="imdb_myLink'.($w*1).'" value="'.$val_prog.'">
    <input type="hidden" id="fav_myLink'.($w*1).'" value="'.$fav_link.'">
    </a>';
    $n++;
    $w++;

    //if ($tip_stream == "http" || $tip_stream == "https" || $tip_stream=="acestream" || $tip_stream=="sop") {
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
   //}
  //}
}
}

if ($tot>$step) {
echo '<TR>';
 if ($page>0)
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$prev.'">&nbsp;&lt;&lt;&nbsp;</a> | <a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
 else
   echo '<TD class="nav" colspan="4" align="right"><a href="'.$next.'">&nbsp;&gt;&gt;&nbsp;</a></TD>'."\r\n";
echo '</TR>';
}
 echo '</table>';
?>

<div id="overlay"">
  <div id="text">Wait....</div>
</div>
</body>
</html>
